<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Reservation;
use App\Entity\Thing;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Metadata\Put;

class ThingBackStateProcessor implements ProcessorInterface
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        /*
        foreach($data->getReservations() as $reservation) {
            if ($reservation->getId() === (int)$id_reservation) {

                $_currentR = $reservation;
                $con = $this->em->getConnection();
                $date = new \DateTime();
                $sql = sprintf("UPDATE reservation SET back_date = '%s' , state = 2 WHERE id = %d ", $date->format('Y-m-d H:i:s'), $_currentR->getId());
                $stmp = $con->prepare($sql);
                $stmp->executeQuery();
                //$_currentR->setState(2);
                $_currentR->setBackDate($date);
                //$this->em->merge($_currentR);
                //$this->em->flush();

                $startDate = $_currentR->getStartDate();
                $backDate = $_currentR->getBackDate();
                $startDate->setTime(0, 0, 0);
                $backDate->setTime(0, 0, 0);
                $delta = 1 + (int)$startDate->diff($backDate)->format("%r%a");
                $price = round($thing->getDailyPrice() * $delta * 100);
                if ($price > 0) {
                    //$expense = new Expense();
                    // reservation
                    $sql = sprintf("INSERT INTO expense ( reservation_id , user_id, owner_id, amount , status , thing_id )
                                        VALUES ( %d, %d, %d, %d , 'pending', %d)
                            ", $_currentR->getId(), $_currentR->getOwner()->getId(), $thing->getOwner()->getId(),$price, $thing->getId());
                    $stmp = $con->prepare($sql);
                    $stmp->executeQuery();
                    /*
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
        }*/
    }
}
