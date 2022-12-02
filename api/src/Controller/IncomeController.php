<?php

namespace App\Controller;

use App\Entity\Expense;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IncomeController extends AbstractController
{
    #[Route('/income/{userId}', name: 'app_income')]
    public function index($userId, EntityManagerInterface $em): Response
    {

        $expenses = $em->getRepository(Expense::class)->findForIncome($userId);

        $total = 0;

        $user = $em->getRepository(User::class)->findOneBy(['id' => $userId]);

        $expenses = array_map(function ($elem) use(&$total) {
            /** @var $elem Expense */
            $elem->setAmount($elem->getAmount()/ 100);
            $total += $elem->getAmount();
            return $elem;
        }, $expenses);

        return $this->render('income/index.html.twig', [
            'expenses' => $expenses,
            'total'  => $total,
            'user' => $user,
            'today' => new \DateTime(),
        ]);
    }
}
