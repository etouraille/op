<?php
namespace App\Controller;

use App\Entity\Book;
use App\Entity\Thing;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetOutThingsForUser extends AbstractController
{

    public function __construct(private EntityManagerInterface $em)
    {

    }

    public function __invoke(Request $request)
    {

        $userId = $request->get('userId');

        return $this
            ->em
            ->getRepository(Thing::class)
            ->getOutThingsForUser($userId)
        ;
    }
}