<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class AccountIsActiveController extends AbstractController
{

    #[Route('/api/account/is/active', name: 'app_account_is_active', methods:['GET'])]
    public function index(Security $security): JsonResponse
    {

        $user = $security->getUser();
        /** @var  $user User */
        $stripeAccountId = $user->getStripeAccountId();
        if($stripeAccountId) {
            $stripe = new \Stripe\StripeClient($this->getParameter('app.secret_stripe'));
            $account = $stripe->accounts->retrieve(
                $stripeAccountId
            );
            $data = $account->toArray();
            $isActive = $data['capabilities']['transfers'] === 'active';

            return new JsonResponse(['isActive' => $isActive]);
        } else {
            return new JsonResponse(['isActive' => false]);
        }
    }
}
