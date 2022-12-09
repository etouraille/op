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
        $filter = isset($context['filters']['name']) ? $context['filters']['name'] : null;
        $typeIds = isset($context['filters']['filter']) ? $context['filters']['filter'] : null;
        $type = isset($context['filters']['type']) ? $context['filters']['type'] : null;
        switch( $type) {
            case 'stars':
                return $this
                    ->em
                    ->getRepository(Thing::class)
                    ->findStars($typeIds)
                    ;
                break;
            case 'rand':
                return $this
                    ->em
                    ->getRepository(Thing::class)
                    ->findRand($typeIds)
                    ;
                break;

            case 'proposed':
                return $this
                    ->em
                    ->getRepository(Thing::class)
                    ->findPendings()
                    ;
                break;
            case 'lasts':
                return $this
                    ->em
                    ->getRepository(Thing::class)
                    ->findLasts($typeIds)
                    ;
                break;

            default:
                return $this
                    ->em
                    ->getRepository(Thing::class)
                    ->findAllExceptPending($filter)
                    ;
                break;
        }

    }
}