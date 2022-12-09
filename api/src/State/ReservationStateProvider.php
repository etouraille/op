<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Expense;
use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;

class ReservationStateProvider implements ProviderInterface
{

    public function __construct(private EntityManagerInterface $em) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $thingId = $context['filters']['thingId'];
        return $this->em->getRepository(Reservation::class)->findForThingId($thingId);
    }
}
