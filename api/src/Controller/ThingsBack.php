<?php
namespace App\Controller;

use App\Entity\Expense;
use App\Entity\Reservation;
use App\Entity\Thing;
use App\Entity\User;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[AsController]
class ThingsBack extends AbstractController {

    public function __construct(
        private Security $security,
        private EntityManagerInterface $em
    ) {

    }

    #[Route(
        path: '/api/things/{id}/reservations/{id_reservation}',
        name: 'thingsBack',
        defaults: [
            '_api_resource_class' => Thing::class,
            //'_api_operation_name' => '_api_/books/{id}/publication_post',
        ],
        methods: ['PUT'],
    )]
    public function __invoke($id, $id_reservation) : JsonResponse {

        $thing = $this->em->getRepository(Thing::class)->find($id);

        foreach($thing->getReservations() as $reservation) {
            /** @var $reservation Reservation */
            if ($reservation->getId() === (int)$id_reservation) {

                $_currentR = $reservation;
                //$con = $this->em->getConnection();
                $date = new \DateTime();
                //$sql = sprintf("UPDATE reservation SET back_date = '%s' , state = 2 WHERE id = %d ", $date->format('Y-m-d H:i:s'), $_currentR->getId());
                //$stmp = $con->prepare($sql);
                //$stmp->executeQuery();
                $_currentR->setState(2);
                $_currentR->setBackDate($date);
                $this->em->merge($_currentR);
                $this->em->flush();

                $startDate = $_currentR->getStartDate();
                $backDate = $_currentR->getBackDate();
                $startDate->setTime(0, 0, 0);
                $backDate->setTime(0, 0, 0);
                $delta = 1 + (int)$startDate->diff($backDate)->format("%r%a");
                $price = round($thing->getDailyPrice() * $delta * 100);
                if ($price > 0) {
                    $expense = new Expense();
                    // reservation
                    //$sql = sprintf("INSERT INTO expense ( reservation_id , user_id, owner_id, amount , status , thing_id )
                    //                    VALUES ( %d, %d, %d, %d , 'pending', %d)
                     //       ", $_currentR->getId(), $_currentR->getOwner()->getId(), $thing->getOwner()->getId(),$price, $thing->getId());
                    //$stmp = $con->prepare($sql);
                    //$stmp->executeQuery();

                    $expense->setReservation($_currentR);
                    // user
                    $expense->setUser($_currentR->getOwner());
                    // owner
                    $owner = $_currentR->getOwner();
                    $this->em->merge($_currentR->getOwner());
                    $expense->setOwner($thing->getOwner());
                    $this->em->merge($thing->getOwner());
                    // amount
                    $expense->setAmount($price);
                    // status
                    $expense->setStatus('pending');
                    // thing
                    $expense->setThing($thing);
                    $this->em->persist($expense);
                    $this->em->flush();

                }
            }
        }

        return new JsonResponse(['id' => $thing->getId()]);
    }
}