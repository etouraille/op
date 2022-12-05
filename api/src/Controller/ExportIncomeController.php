<?php

namespace App\Controller;

use App\Entity\Expense;
use App\Entity\IncomeData;
use App\Entity\User;
use App\Service\GenerateBill;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Snappy\Pdf;


#[AsController]
class ExportIncomeController extends AbstractController
{

    public function __construct(private GenerateBill $service) {

    }

    public function __invoke(): IncomeData
    {

        $userId = $_GET['userId'];

        return $this->service->process($userId);
    }
}
