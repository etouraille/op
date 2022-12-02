<?php


namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Entity\Coin;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'app:add-coin',
    description: 'Add coins',
    aliases: ['app:add-coin'],
    hidden: false
)]
class AddCoinCommand extends Command
{

    private $secret;

    public function __construct(
        protected EntityManagerInterface $em,

    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

        $coin = new Coin();
        $coin->setAmount(100000);
        $coin->setReason(Coin::REASON_PROVIDE);
        $coin->setOwner($user);

        $this->em->persist($coin);
        $this->em->flush();


        return Command::SUCCESS;

    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'what is user email');
    }
}