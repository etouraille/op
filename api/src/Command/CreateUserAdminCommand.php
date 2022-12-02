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
    name: 'app:create-user',
    description: 'Creates a new admin.',
    hidden: false,
    aliases: ['app:add-user']
)]
class CreateUserAdminCommand extends Command
{

    public function __construct(
        protected EntityManagerInterface $em,
        protected UserPasswordHasherInterface $hasher) {

            parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        $user = new User();

        $encryptedPassword = $this->hasher->hashPassword(
            $user,
            $password
        );

        $user->setEmail($email);
        $user->setPassword($encryptedPassword);
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $this->em->persist($user);
        $this->em->flush();

        return Command::SUCCESS;

    }

    protected function configure() : void {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'what is user email')
            ->addArgument('password', InputArgument::REQUIRED, 'What is your passwor')
        ;
    }
}