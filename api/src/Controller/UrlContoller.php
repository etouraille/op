<?php

namespace App\Controller;

use App\Entity\Thing;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class UrlContoller extends AbstractController
{
    public function __construct(private EntityManagerInterface $em) {}

    public function __invoke() : array {
        return $this->em->getRepository(Thing::class)->findAllExceptPending();
    }
}