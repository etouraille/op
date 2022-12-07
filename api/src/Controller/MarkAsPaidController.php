<?php

namespace App\Controller;
use App\Entity\Expense;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class MarkAsPaidController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em) {

    }

    public function __invoke(Request $request): array {
        $expenses = $this->em->getRepository(Expense::class)->findForIncome($request->get('userId'))
        array_map(function($expense) {
            /** @var $expense Expense */
            $expense->setStatus('paid');
            $this->em->merge($expense);
        }, $expenses);

        $this->em->flush();

        return $expenses;
    }

}