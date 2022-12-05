<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GoogleSigninController extends AbstractController
{

    private $secret;

    public function __construct(
        $googleClientId,
        private EntityManagerInterface $em,
        private UserService $userService,
        private JWTTokenManagerInterface $JWTManager
    ) {
        $this->secret = $googleClientId;
    }

    #[Route('/google/signin', name: 'app_google_signin')]
    public function index(): JsonResponse
    {
        $payload = @file_get_contents('php://input');
        $data = json_decode($payload, true);
        $client = new \Google_Client(['client_id' => $this->secret]);  // Specify the CLIENT_ID of the app that accesses the backend
        $payload = $client->verifyIdToken($data['token']);
        if ($payload) {
            $email = $payload['email'];
            $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
            if(!$user) {
                $user = $this->userService->create($email, null, isset($data['roles']) ? $data['roles'] : [], $data['given_name'], $data['family_name']);
            }
            return new JsonResponse(['token' => $this->JWTManager->create($user)]);
        } else {
            return new JsonResponse(['token' => null], 401);
        }
    }
}
