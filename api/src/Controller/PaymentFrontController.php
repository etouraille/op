<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentFrontController extends AbstractController
{
    #[Route('/payment/front', name: 'app_payment_front', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return new JsonResponse([
            'available' => $this->getParameter('app.payment_front') == 1
        ]);
    }
}
