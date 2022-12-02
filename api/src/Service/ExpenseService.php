<?php

namespace App\Service;

use App\Entity\Coin;
use App\Entity\Expense;
use App\Entity\Income;
use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;

class ExpenseService
{

    protected $stripe;
    protected $secret;

    public function __construct($stripe, private EntityManagerInterface $em) {
        $this->stripe = \Stripe\Stripe::setApiKey($stripe);
        $this->secret = $stripe;
    }

    public function process(array $expenses): array {

        $total = array_reduce($expenses, function($a, $elem) {
            /** @var $elem Expense */
            return $a + $elem->getAmount();
        }, 0);

        $expense = $expenses[0];

        /** @var $expense Expense **/
        $user = $expense->getUser();
        // if owner is user
        if (false === array_search('ROLE_MEMBER', $user->getRoles())) {
            // on lance le débit
            $stripe = new \Stripe\StripeClient($this->secret);
            $paymentMethod = $stripe->paymentMethods->all(
                [
                    'customer' => $user->getStripeCustomerId(),
                    'type' => 'card'
                ]
            );
            $data = $paymentMethod->toArray();
            $index = 0;
            if(count($data['data']) == 0) {
                return ['error' => 'Pas de methode de paiement enregistrée'];
            }
            do {
                $paymentMethodId = $data['data'][$index]['id'];
                $paymentSuccess = $this->payExpense($expense, $total, $paymentMethodId);
                $index ++;
            } while(!$paymentSuccess && $index < count($data['data']));
            // TODO : do something with the IncomeData.
            // TODO allow payment only once the incomeData is generated. api.
            // set Expense status.
            array_map(function($elem) use ($paymentSuccess) {
                /** @var $elem Expense */
                $paymentSuccess ? $elem->setStatus('paid') : $elem->setStatus('error');
                $this->em->merge($elem);
                $this->em->flush();
            }, $expenses);

            array_map(function($elem) use($stripe){
                /** @var $elem Expense */
                $this->payIncomeForExpense($stripe, $elem);
            }, $expenses);

            return ['success' => $paymentSuccess];

        }
        if (false !== array_search('ROLE_MEMBER', $user->getRoles())) {
            // on lance le débit des coin
            array_map(function($elem) {
                /** @var $elem Expense */
                $amount = $elem->getAmount();
                $user = $elem->getUser();
                $thing = $elem->getThing();

                $coin = new Coin();
                $coin->setReason(Coin::REASON_REMOVE);
                $coin->setAmount(-1 * $amount);
                $coin->setOwner($user);
                $coin->setThing($thing);
                $this->em->persist($coin);

                $coin = new Coin();
                $coin->setReason(Coin::REASON_ADD);
                $coin->setAmount($amount);
                $coin->setOwner($elem->getOwner());
                $coin->setThing($thing);
                $this->em->persist($coin);

                $elem->setStatus('paid');
                $this->em->merge($elem);

                $this->em->flush();

            }, $expenses);

            return ['success' => true];
        }

        return [];
    }

    public function payExpense($expense, $total , $paymentMethodId) {

        try {
            \Stripe\PaymentIntent::create([
                'amount' => $total,
                'currency' => 'eur',
                'customer' => $expense->getUser()->getStripeCustomerId(),
                'payment_method' => $paymentMethodId,
                'off_session' => true,
                'confirm' => true,
            ]);

            return true;


        } catch (\Stripe\Exception\CardException $e) {
            // Error code will be authentication_required if authentication is needed
            echo 'Error code is:' . $e->getError()->code;
            $payment_intent_id = $e->getError()->payment_intent->id;
            $payment_intent = \Stripe\PaymentIntent::retrieve($payment_intent_id);

            return false;
        }

    }

    private function payIncomeForExpense($stripe, $expense) {

        try {

            $incomeAmount = (int) round($expense->getAmount() * 0.3);
            $income = new Income();
            $income->setUser($expense->getOwner());
            $income->setAmount($incomeAmount);
            $income->setExpense($expense);
            $income->setThing($expense->getThing());
            $income->setStatus('pending');
            $this->em->persist($income);
            $this->em->flush();

            $balance = $stripe->balance->retrieve([]);
            $dataBalance = $balance->toArray();
            $available = $dataBalance['available'][0]['amount'];
            // TODO when test is over set relation oneToOne.
            if ($available > $incomeAmount) {

                $transfer = \Stripe\Transfer::create([
                    'amount' => $incomeAmount,
                    'currency' => 'eur',
                    'destination' => $expense->getThing()->getOwner()->getStripeAccountId(),
                    'transfer_group' => 'transert pour ' . $expense->getThing()->getName(),
                ]);

                $income->setStatus('paid');
                $this->em->merge($income);
                $this->em->flush();
            } else {
                // balance issue.
                $income->setStatus('error');
                $this->em->merge($income);
                $this->em->flush();
            }

        } catch (\Exception $e ) {
            $error = $e->getMessage();
            $income->setStatus('error');
            $this->em->merge($income);
            $this->em->flush();
        }
    }
}