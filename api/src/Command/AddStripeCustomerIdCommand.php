<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'app:add-stripe-customer-id',
    description: 'Add stripe customer id.',
    hidden: false,
    aliases: ['app:add-stripe-customer-id']
)]
class AddStripeCustomerIdCommand extends Command
{

    private $secret;

    public function __construct(
        protected EntityManagerInterface $em,
        $stripe
    ) {
            \Stripe\Stripe::setApiKey($stripe);
            $this->secret = $stripe;
            parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $email = $input->getArgument('email');
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        /** @var  $user User **/
        $stripeCustomer = \Stripe\Customer::create(['email' => $user->getEmail()]);

        if(!$user->getStripeCustomerId()) $user->setStripeCustomerId($stripeCustomer->id);

        if (!$user->getStripeAccountId() && false !== array_search('ROLE_MEMBER', $user->getRoles())) {
            $stripe = new \Stripe\StripeClient($this->secret);
            $account = $stripe->accounts->create(['type' => 'express', 'email' => $user->getEmail()]);
            $user->setStripeAccountId($account->id);
        }

        $this->em->persist($user);
        $this->em->flush();

        return Command::SUCCESS;

    }

    protected function configure() : void {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'what is user email')
        ;
    }
}