<?php

namespace App\Command;
use App\Entity\Income;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:pass-income',
    description: 'Process Income (payment from user)',
    aliases: ['app:income'],
    hidden: false
)]
class PassIncomeCommand extends Command
{

    private $secret;

    public function __construct(
        $stripe,
        protected EntityManagerInterface $em,
    )
    {
        $this->secret = $stripe;
        \Stripe\Stripe::setApiKey($stripe);
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $incomes = array_reduce(
            $this->em->getRepository(Income::class)->getErrorsAndPendings(),
            function($a, $income) {
               /** @var $income Income */
               $userId = $income->getUser()->getId();
                if (isset($a[$userId])) {
                    $a[$userId]->setAmount($a[$userId]->getAmount() + $income->getAmount());
                    $a[$userId]->addPendingIncome($income);
                } else {
                    $a[$userId] = $income->addPendingIncome($income);
                }
                return $a;
            },[]);

        $stripe = new \Stripe\StripeClient($this->secret);


        $balance = $stripe->balance->retrieve([]);
        $dataBalance = $balance->toArray();
        $available = $dataBalance['available'][0]['amount'];

        if($available <= 0) {
            return Command::FAILURE;
        }

        $ret = array_reduce($incomes, function($a, $income) {
            /** @var $income Income */

            try {
                // set stripe transfer:
                $transfer = \Stripe\Transfer::create([
                    'amount' => $income->getAmount(),
                    'currency' => 'eur',
                    'destination' => $income->getUser()->getStripeAccountId(),
                    'transfer_group' => 'transert pour ' . $income->getThing()->getName(),
                ]);
                array_map(function($elem) {
                    /** @var $elem Income */
                    $elem->setStatus('paid');
                    $elem->setDate(new \DateTime());
                    $this->em->merge($elem);
                    $this->em->flush();

                }, $income->getPendingIncomes());

                return $a && true;

            } catch (\Exception $e ) {
                return $a && false;
            }
        }, true);

        return $ret ? Command::SUCCESS: Command::FAILURE;
    }
}