<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Expense;
use Doctrine\ORM\EntityManagerInterface;

class ExpenseStateProvider implements ProviderInterface
{

    public function __construct(private EntityManagerInterface $em) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return $this->em->getRepository(Expense::class)->findForIncome($_GET['userId']);
    }
}
