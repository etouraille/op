<?php

namespace App\Controller;

use App\Entity\Thing;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class Pending extends AbstractController
{


    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function __invoke(Request $request) : array {
        return $this->em->getRepository(Thing::class)->findPending($request->get('delta', 2));
    }
}