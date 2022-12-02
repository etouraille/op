<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class SetUpIntentController extends AbstractController
{
    #[Route('/api/set-up-intent')]
    public function setUpIntent(Security $security): JsonResponse
    {
        try {

            \Stripe\Stripe::setApiKey($this->getParameter('app.secret_stripe'));
            /** @var  $user User */
            $user = $security->getUser();
            $stripeCustomerId = $user->getStripeCustomerId();

            // Create a PaymentIntent with amount and currency
            $paymentIntent = \Stripe\SetupIntent::create([
                'customer' => $stripeCustomerId,
                'payment_method_types' => ['card'],
            ]);

            $output = [
                'clientSecret' => $paymentIntent->client_secret,
            ];

            return new JsonResponse($output);
        } catch (\Error $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}
