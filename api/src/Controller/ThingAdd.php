<?php
namespace App\Controller;

use App\Entity\Thing;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[AsController]
class ThingAdd extends AbstractController {

    public function __construct(
        private Security $security,
        private EntityManagerInterface $em,
    ) {

    }

    public function __invoke(Thing $thing) : Thing {
        $user = $this->security?->getUser();
        $thing->setOwner($user);
        $this->em->persist($thing);
        $this->em->flush();
        return $thing;
    }
}