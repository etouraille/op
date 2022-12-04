<?php
namespace App\Controller;

use App\Entity\Thing;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Security;

#[AsController]
class Current extends AbstractController
{


    public function __construct(
        private EntityManagerInterface $em,
        private Security $security
    ){
    }

    public function __invoke(Request $request): array {
        $userId = $request->get('userId', null);
        if($userId && $this->security->isGranted('ROLE_ADMIN')) {
            return $this->em->getRepository(Thing::class)->currentForUserId($userId);
        } elseif($userId && !$this->security->isGranted('ROLE_ADMIN')) {
            throw new UnauthorizedHttpException('Role admin is not granted');
        } else {
            return $this->em->getRepository(Thing::class)->currentForUser($this->security->getUser());
        }
    }
}