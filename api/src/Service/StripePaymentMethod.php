<?php

namespace App\Service;

use App\Entity\User;

class StripePaymentMethod
{
    private $secret;

    public function __construct($stripe) {
        $this->secret = $stripe;
    }

    public function get(User $user)
    {

        $stripe = new \Stripe\StripeClient($this->secret);
        $paymentMethod = $stripe->paymentMethods->all(
            [
                'customer' => $user->getStripeCustomerId(),
                'type' => 'card'
            ]
        );
        $data = $paymentMethod->toArray();
        if (count($data['data']) == 0) {
            return [];
        } else {
            return array_map(function ($elem) {
                return $elem['id'];
            }, $data['data']);
        }
    }
}