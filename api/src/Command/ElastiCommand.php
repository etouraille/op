<?php


namespace App\Command;

use App\Entity\Thing;
use App\Entity\User;
use App\Service\ElasticService;
use App\Service\UrlGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Entity\Coin;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'app:elastic',
    description: 'Make index',
    aliases: ['app:elastic'],
    hidden: false
)]
class ElastiCommand extends Command
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

        $client = ElasticService::getClient();

        $params = [
            'index' => 'thing',
            'body' => [
                'settings' => [
                    'number_of_shards' => 3,
                    'number_of_replicas' => 2
                ],
                'mappings' => [
                    '_source' => [
                        'enabled' => true
                    ],
                    'properties' => [
                        'id' => [
                            'type' => 'integer'
                        ],
                        'name' => [
                            'type' => 'keyword'
                        ],
                        'type' => [
                            'type' => 'keyword'
                        ],
                        'picture' => [
                            'type' => 'keyword'
                        ]
                    ]
                ]
            ]
        ];

        $things = $this->em->getRepository(Thing::class)->findAll();

        foreach($things as $thing ) {

            $object = ElasticService::getObject($thing);

            $params = [
                'index' => 'thing',
                'id' =>  $thing->getId(),
                'body' => $object,
            ];
            $client->index($params);
        }

        //$client->indices()->create($params);

        return Command::SUCCESS;

    }


}