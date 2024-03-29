<?php

namespace App\Controller;

use App\Entity\Expense;
use App\Entity\Picture;
use App\Entity\Reservation;
use App\Entity\Thing;
use App\Entity\User;
use App\Dto\PayReturn;
use App\Service\ExpenseService;
use App\Service\GenerateBill;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Security;

#[AsController]
class PayController extends AbstractController
{

    private $env;

    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
        private ExpenseService $expenseService,
        private GenerateBill $billService,
        $env
    ) {
        $this->env = $env;
    }

    public function __invoke(): PayReturn {
        $user = $this->security->getUser();
        /** @var $user User */
        if(
            false !== array_search('ROLE_MEMBER', $user->getRoles())
            && !$user->isIsMemberValidated()
        ) {
            throw new UnauthorizedHttpException('Member not valid');
        }
        $things = $this
            ->em
            ->getRepository(Thing::class)
            ->waitingForUser(
                $user,
                $this->getParameter('app.payment_front') == 1
            )
        ;
        $expenses = array_filter(array_reduce($things, function($a, $thing) {

            /** @var $thing Thing */
            $expenses = array_map(function($reservation) use($thing) {
                /** @var $reservation Reservation */
                $state = $reservation->getState();
                $expenses = $reservation->getExpenses();
                if(-2 === $reservation->getState() && count($reservation->getExpenses()) === 0) {
                    $startDate = $reservation->getStartDate();
                    $backDate = $reservation->getEndDate();
                    $startDate->setTime(0, 0, 0);
                    $backDate->setTime(0, 0, 0);
                    $delta = 1 + (int)$startDate->diff($backDate)->format("%r%a");
                    $price = round($thing->getDailyPrice() * $delta * 100);
                    if( $price> 0 ) {
                        $expense = new Expense();
                        $expense->setStatus('pending');
                        $expense->setThing($thing);
                        $expense->setUser($reservation->getOwner());
                        $expense->setOwner($thing->getOwner());
                        $expense->setAmount($thing->getDailyPrice() * 100);
                        $expense->setReservation($reservation);
                        $this->em->persist($expense);
                        $this->em->flush();
                    }
                    $this->em->merge($reservation);
                    $this->em->flush();

                    return $expense;
                } else if(-2 === $reservation->getState() && $reservation->getExpenses() && count($reservation->getExpenses()) === 1) {
                    return $reservation->getExpenses()->toArray()[0];
                }
            }, $thing->getReservations()->toArray());

            return array_merge($a, $expenses);

        },[]));




        //TODO faire la bill après le processus de paiement. Status pre-bill
        list($success, $isIntent, $id, $error) = $this->expenseService->process($expenses, false, true);

        if('dev' == $this->env) {
            try {
                $bill = $this->billService->process($user->getId());
            } catch (\Exception $e) {

            }
        } else {
            $bill = $this->billService->process($user->getId());
        }

        return new PayReturn($success, $isIntent, $id, $error);
    }

}