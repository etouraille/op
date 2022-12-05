<?php


namespace App\Command;

use App\Entity\Thing;
use App\Entity\User;
use App\Service\UrlGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Entity\Coin;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'app:url',
    description: 'Make url',
    aliases: ['app:url'],
    hidden: false
)]
class MakeUrlCommand extends Command
{


    public function __construct(
        protected EntityManagerInterface $em,
        protected UrlGenerator $service,

    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $things = $this->em->getRepository(Thing::class)->findAll();
        $name = $things[0]->getShop()->getName();
        foreach($things as $thing) {
            $url = $this->service->makeUrl($name, $thing->getName());
            $thing->setUrl($url);
            $this->em->merge($thing);
            $this->em->flush();
        }


        return Command::SUCCESS;

    }


}