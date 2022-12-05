<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Thing;
use Doctrine\ORM\EntityManagerInterface;

class SearchStateProvider implements ProviderInterface
{

    public function __construct(private EntityManagerInterface $em) {

    }
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $name = $context['filters']['name'];
        return $this
            ->em
            ->getRepository(Thing::class)
            ->search($name)
        ;
    }
}