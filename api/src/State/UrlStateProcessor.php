<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Service\UrlGenerator;
use Doctrine\ORM\EntityManagerInterface;

class UrlStateProcessor
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private UrlGenerator $service,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $name = $data->getShop()->getName();
        $url = $this->service->makeUrl($name, $data->getName());
        $data->setUrl($url);

        if ($operation instanceof Post) {
            $this->em->persist($data);
            $this->em->flush();
        } elseif($operation instanceof Patch || $operation instanceof Put) {
            $this->em->merge($data);
            $this->em->flush();
        }
    }
}