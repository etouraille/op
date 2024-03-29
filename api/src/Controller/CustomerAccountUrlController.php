<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class CustomerAccountUrlController extends AbstractController
{
    #[Route('api/customer/account/url', name: 'app_customer_account_url', methods: ['GET'])]
    public function index(Security $security): JsonResponse
    {
        $stripe = new \Stripe\StripeClient($this->getParameter('app.secret_stripe'));
        $user = $security->getUser();
        /** @var $user User */
        if($user->getStripeAccountId()) {
            $link = $stripe->accountLinks->create(
                [
                    'account' => $user->getStripeAccountId(),
                    'refresh_url' => $this->getParameter('app.app_url') . 'income',
                    'return_url' => $this->getParameter('app.app_url') . 'income',
                    'type' => 'account_onboarding',
                ]
            );

            return new JsonResponse(['url' => $link->url]);
        } else {
            return new JsonResponse(['success' => false, "error" => 'Stripe account id not defined'], 500);
        }
    }
}
