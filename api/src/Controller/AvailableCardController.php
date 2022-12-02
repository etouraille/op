<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class AvailableCardController extends AbstractController
{
    #[Route('/api/available/card', name: 'app_available_card', methods: ['GET'])]
    public function index(Security $security): JsonResponse
    {
        $user = $security->getUser();
        /** @var $user User */
        $stripe = new \Stripe\StripeClient($this->getParameter('app.secret_stripe'));
        $paymentMethod = $stripe->paymentMethods->all(
            [
                'customer' => $user->getStripeCustomerId(),
                'type' => 'card'
            ]
        );
        $data = $paymentMethod->toArray();
        return new JsonResponse($data['data']);
    }

    #[Route('api/available/card/{id}', name : 'app_available_card_delete', methods: ['DELETE'])]
    public function delete($id, Security $security) {
        $user = $security->getUser();
        /** @var $user User */
        $stripe = new \Stripe\StripeClient($this->getParameter('app.secret_stripe'));
        $paymentMethod = $stripe->paymentMethods->detach($id);
        $data = $paymentMethod->toArray();
        return new JsonResponse($data);
    }
}
