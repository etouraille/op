<?php
namespace App\Controller;

use App\Entity\Thing;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Core\Security;

#[AsController]
class Done extends AbstractController
{


    public function __construct(
        private EntityManagerInterface $em,
        private Security $security
    ){
    }

    public function __invoke(): array {
        return $this->em->getRepository(Thing::class)->doneForUser($this->security->getUser());
    }
}