<?php

namespace App\Controller;

use App\Entity\IncomeData;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Security;

#[AsController]
class BillsController extends AbstractController
{


    public function __construct(
        private EntityManagerInterface $em,
        private Security $security
    )
    {
    }

    public function __invoke(Request $request) {
        $userId = $request->get('userId');
        if($userId && !$this->security->isGranted('ROLE_ADMIN')) {
            throw new UnauthorizedHttpException('Not allowed');
        } elseif($userId) {
            $user = $this->em->getRepository(User::class)->find($userId);
        } else {
            $user = $this->security->getUser();
        }
        return $this->em->getRepository(IncomeData::class)->findForUser($user);
    }
}