<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Expense;
use App\Service\ExpenseService;
use App\Service\GenerateBill;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class ExpenseProcess extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $em,
        private ExpenseService $service,
        private GenerateBill $billService,
    ){
    }

    public function __invoke(Request $request)
    {

        $userId = $request->get('userId');

        if ($userId) {
            list($success, $isIntent, $id, $error) =  $this->service->process(
                $this->em->getRepository(Expense::class)->findForIncome($userId),
                false
            );
            if($success) {

                try {
                    $incomeDate = $this->billService->process($userId);
                } catch(\Exception $e ) {
                    $error = $e->getMessage();
                }
            }
            return [['success' => $success, 'isIntent' => $isIntent, 'id' => $id, 'error' => $error, 'bill' => $incomeDate ? $incomeDate->getFile() : null]];
        } else {
            return [];
        }
    }
}