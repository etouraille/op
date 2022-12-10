<?php


namespace App\State;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Expense;
use App\Entity\Reservation;
use App\Entity\User;
use App\Service\CacheService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Security;

class ReservationStateProcessor implements ProcessorInterface
{

    private $secret;
    private $app_url;
    public function __construct(
        private Security $security,
        private EntityManagerInterface $em,
        private RequestStack $stack,
        $stripe,
        $app_url,
    ) {
        $this->secret = $stripe;
        $this->app_url = $app_url;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        // if user is role_member it must be isMemberValidated
        $user = $this->security->getUser();
        /** @var $user User */
        //this test is available only in app case.
        if($_SERVER['HTTP_REFERER'] === $this->app_url && false !== array_search('ROLE_MEMBER', $user->getRoles()) && !$user->isIsMemberValidated()) {
            throw new UnauthorizedHttpException('Member is not Authorized');
        }
        CacheService::ban('api/reservations');
        if($data instanceof Reservation && $operation instanceof Post) {
            // set owner as current user only when it's not defined.
            // on the app side of the api
            if(!$data->getOwner()) {
                $data->setOwner($this->security->getUser());
            }
            $this->em->persist($data);
            $this->em->flush();
        } elseif($data instanceof Reservation && $operation instanceof Patch) {
            $this->em->merge($data);
            $this->em->flush();
        } elseif($data instanceof Reservation && $operation instanceof Delete) {
            // paid
            if ($data->getState() === -1) {
                // on rembourse
                // TODO there can only be one reimbursement on a charge.
                /*
                $paymentIntentId = $data->getExpense()->getPaymentIntentId();
                $amount = $data->getExpense()->getAmount();
                $stripe = new \Stripe\StripeClient($this->secret);
                try {
                    $stripe->refunds->create(['payment_intent' => $paymentIntentId, 'amount' => $amount]);
                    $expense = new Expense();
                    $expense->setAmount(-1 * $amount);
                    // TODO status is done only when refund is done, do callback
                    $expense->setStatus('paid');
                    $expense->setUser($data->getExpense()->getUser());
                    $expense->setOwner($data->getExpense()->getOwner());
                    $expense->setThing($data->getExpense()->getThing());
                    $expense->setReservation($data->getExpense()->getReservation());
                    $this->em->persist($expense);
                    $this->em->flush();
                } catch(\Exception $e) {
                    $message = $e->getMessage();
                    $message;
                }
                */
            }
            $this->em->remove($data);
            $this->em->flush();
        }
    }
}