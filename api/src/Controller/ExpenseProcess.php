<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Expense;
use App\Service\ExpenseService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class ExpenseProcess extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $em,
        private ExpenseService $service
    ){
    }

    public function __invoke(Request $request)
    {

        $userId = $request->get('userId');

        if ($userId) {
            $ret =  $this->service->process(
                $this->em->getRepository(Expense::class)->findForIncome($userId)
            );
            return [$ret];
        } else {
            return [];
        }
    }
}