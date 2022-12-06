<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class SocialSigninController extends AbstractController
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
    public function google(): JsonResponse
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

    #[Route('/facebook/signin', name: 'app_google_signin')]
    public function facebook(): JsonResponse
    {
        $payload = @file_get_contents('php://input');
        $data = json_decode($payload, true);

        $provider = new \League\OAuth2\Client\Provider\Facebook([
            'clientId'          => '1867394790282222',
            'clientSecret'      => 'd082cb017a76c6323ee4ceaabc08e9be',
            //'redirectUri'       => 'https://example.com/callback-url',
            'graphApiVersion'   => 'v2.10',
        ]);

        $token = $provider->getAccessToken('authorization_code', [
            'code' => $data['token']
        ]);

        try {

            // We got an access token, let's now get the user's details
            $me = $provider->getResourceOwner($token);



        } catch (\Exception $e) {

            // Failed to get user details
            exit('Oh dear...');
        }


        if ($me) {
            $email = $me->getEmail();
            $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
            if(!$user) {
                $user = $this->userService->create($email, null, isset($data['roles']) ? $data['roles'] : [], $me->getFirstName(), $me->getLastName());
            }
            return new JsonResponse(['token' => $this->JWTManager->create($user), 'id' => $user->getId(), 'email' => $email]);
        } else {
            return new JsonResponse(['token' => null], 401);
        }
    }
}
