<?php


namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class ReservationStateProcessor implements ProcessorInterface
{

    public function __construct(private Security $security, private EntityManagerInterface $em) {

    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if($data instanceof Reservation && $operation instanceof Post) {
            // set owner as current user only when it's not defined.
            // on the app side of the api
            if(!$data->getOwner()) {
                $data->setOwner($this->security->getUser());
            }
            $this->em->persist($data);
            $this->em->flush();
        }
    }
}