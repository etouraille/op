<?php

namespace App\Controller;

use App\Entity\Expense;
use App\Entity\IncomeData;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Snappy\Pdf;


#[AsController]
class ExportIncomeController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em) {

    }

    public function __invoke(): IncomeData
    {

        $userId = $_GET['userId'];

        $expenses = $this->em->getRepository(Expense::class)->findForIncome($userId);

        $snappy = new Pdf('/usr/bin/wkhtmltopdf');

        $file = 'facture_user_' . $userId . '_id_' . rand(1,1000) . '.pdf';
        $input = $this->getParameter('app.api_url') . 'income/' . $userId;
        $output = "/cdn/"  . $file;
        $snappy->generate($input, $output);

        $total = array_reduce($expenses, function ( $a , $elem) {
            return $a + $elem->getAmount();
        }, 0);



        $income = new IncomeData();
        $income->setAmount($total);
        $income->setDate(new \DateTime());
        $income->setFile($this->getParameter('app.cdn_url') . $file );

        $user = $this->em->getRepository(User::class)->findOneBy(['id' => $userId]);

        $income->setUser($user);

        foreach($expenses as $expense) {
            /** @var $expense Expense */
            $expense->setIncomeData($income);
            $this->em->merge($expense);
        }
        $this->em->persist($income);
        $this->em->flush();

        return $income;

    }
}
