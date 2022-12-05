<?php

namespace App\Controller;

use App\Entity\Expense;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WebhookController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em, $stripeSecretIntent) {
        $this->secret = $stripeSecretIntent;
    }

    #[Route('/webhook/intentSucceed', name: 'app_webhook', methods: ['POST'])]
    public function index(): Response
    {

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $this->secret
            );
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            return new Response('', 400);

        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return new Response('', 400);
        }

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $ids = explode(',', $paymentIntent->metadata->ids);
                array_map(function($id) {
                    $expense = $this->em->getRepository(Expense::class)->find($id);
                    /** @var $expense Expense */
                    $expense->setStatus('paid');
                    $this->em->merge($expense);
                    $this->em->flush();
                }, $ids);
            default:
                echo 'Received unknown event type ' . $event->type;
        }

        // Handle the event
        echo 'Received unknown event type ' . $event->type;

        http_response_code(200);
    }
}
