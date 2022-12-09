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

    public function __construct(
        $stripe,
        private EntityManagerInterface $em,
        private StripePaymentMethod $paymentMethod,
    ) {
        $this->stripe = \Stripe\Stripe::setApiKey($stripe);
        $this->secret = $stripe;
    }

    public function process(array $expenses, bool $reimburse = true, $setState = false): array {

        $total = array_reduce($expenses, function($a, $elem) {
            /** @var $elem Expense */
            return $a + $elem->getAmount();
        }, 0);



        $expense = array_values($expenses)[0];

        /** @var $expense Expense **/
        $user = $expense->getUser();
        // if owner is user
        if (false === array_search('ROLE_MEMBER', $user->getRoles())) {
            // on lance le débit
            if( $total > 0 ) {
                $stripe = new \Stripe\StripeClient($this->secret);
                $paymentMethodIds = $this->paymentMethod->get($user);
                if (count($paymentMethodIds) == 0) {
                    return [false, $total > 0 , null, 'Pas de moyent de paiement enregistré'];
                }
                $index = 0;
                do {
                    $paymentMethodId = $paymentMethodIds[$index];
                    list( $success , $id , $error ) = $this->payExpense($expenses, $expense, $total, $paymentMethodId);
                    $index++;
                } while (!$success && $index < count($paymentMethodIds));
                // TODO : do something with the IncomeData.
                // TODO allow payment only once the incomeData is generated. api.
                // set Expense status.
                array_map(function ($elem) use ($success, $id, $setState) {
                    /** @var $elem Expense */
                    if ($success) {
                        $elem->setStatus('for-bill');
                        $elem->setPaymentIntentId($id);
                        $reservation = $elem->getReservation();
                        if($setState) {
                            $reservation->setState(-1);
                            $this->em->merge($reservation);
                        }
                    } else {
                        $elem->setStatus('error');
                    }
                    $this->em->merge($elem);
                    $this->em->flush();
                }, $expenses);
            } elseif( $total < 0 ) {

                $paymentIntentId = array_reduce($expense->getReservation()->getExpenses()->toArray(), function($a, $expense) {
                    /** @var $expense Expense */
                    return $a ? $a : $expense->getPaymentIntentId();
                }, null);

                $stripe = new \Stripe\StripeClient($this->secret);
                $success = false;
                $id = null;
                $refundId = null;
                try {
                    $refund = $stripe->refunds->create(['payment_intent' => $paymentIntentId, 'amount' => -1 * $total]);
                    $data = $refund->toArray();
                    $id = $refund->toArray()['id'];
                    $success = true;

                } catch(\Exception $e) {
                    $error = $e->getMessage();

                }

                array_map(function ($elem) use ($success, $id) {
                    /** @var $elem Expense */
                    if ($success) {
                        $elem->setStatus('for-bill');
                        $elem->setStripeRefundId($id);
                    } else {
                        $elem->setStatus('error');
                    }
                    $this->em->merge($elem);
                    $this->em->flush();
                }, $expenses);
            }

            array_map(function($elem) use($stripe, $reimburse){
                /** @var $elem Expense */
                $this->payIncomeForExpense($stripe, $elem, $reimburse);
            }, $expenses);

            return  [$success ,  $total > 0,  $id, $error];


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

            return [ true ,  null,  null, null];
        }

        return [];
    }

    public function payExpense($expenses, $expense, $total , $paymentMethodId) {

        try {
            $intent = \Stripe\PaymentIntent::create([
                'amount' => $total,
                'currency' => 'eur',
                'customer' => $expense->getUser()->getStripeCustomerId(),
                'payment_method' => $paymentMethodId,
                'off_session' => true,
                'confirm' => true,
                'metadata' => [
                    'ids' => implode(',',array_map(function($elem) {return $elem->getId();}, $expenses))
                ]
            ]);

            $data = $intent->toArray();
            return [  true , $data['id'], null];


        } catch (\Stripe\Exception\CardException $e) {
            // Error code will be authentication_required if authentication is needed
            $payment_intent_id = $e->getError()->payment_intent->id;
            $payment_intent = \Stripe\PaymentIntent::retrieve($payment_intent_id);

            return [ false, $payment_intent->client_secret, $e->getMessage()];
        }

    }

    private function payIncomeForExpense($stripe, $expense, $reimburse) {

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
            if ($available > $incomeAmount && $reimburse) {

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