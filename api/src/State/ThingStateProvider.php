<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Thing;
use Doctrine\ORM\EntityManagerInterface;

final class ThingStateProvider implements ProviderInterface
{
    public function __construct(
        private EntityManagerInterface $em,
    )
    {

    }

    /**
     * {@inheritDoc}
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $filter = isset($context['filters']) ? $context['filters']['name'] : null;
        return $this
            ->em
            ->getRepository(Thing::class)
            ->findAllExceptPending($filter)
            ;
    }
}