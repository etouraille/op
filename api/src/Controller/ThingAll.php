<?php
namespace App\Controller;

use App\Entity\Thing;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class ThingAll extends AbstractController {

    public function __construct(
        private EntityManagerInterface $em
    ) {

    }

    public function __invoke(Request $request) : array {
        return $this->em->getRepository(Thing::class)->findAll();

    }
}