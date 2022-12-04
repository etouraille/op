<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Thing;
use Doctrine\ORM\EntityManagerInterface;

class LastStateProvider implements ProviderInterface
{

    public function __construct(private EntityManagerInterface $em) {

    }
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $filter = isset($context['filters']) ? $context['filters']['filter'] : null;
        return $this
            ->em
            ->getRepository(Thing::class)
            ->findLasts($filter)
            ;
    }
}