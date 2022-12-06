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

        $fb = new \JanuSoftware\Facebook\Facebook([
            'app_id' => '1867394790282222',
            'app_secret' => 'd082cb017a76c6323ee4ceaabc08e9be',
            'default_graph_version' => 'v15.0',
            //'default_access_token' => '{access-token}', // optional
        ]);

        try {
            // If you provided a 'default_access_token', the '{access-token}' is optional.
            $response = $fb->get('/me', $data['token']);
        } catch(\JanuSoftware\Facebook\Exception\ResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(\JanuSoftware\Facebook\Exception\SDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        $me = $response->getGraphNode();

        $fields = $me->getFieldNames();
        $noEmail = false;
        if(false === array_search('email', $fields)) {
            $noEmail = true;
            $email = str_replace(' ', '_',  $me->getField('name')) . '@facebook.com';
        } else {
            $email = $me->getField('email');
        }



        if ($me) {
            if($noEmail) {
                $user = $this->em->getRepository(User::class)->findOneBy(['facebookId' => $me->getField('id')]);
            } else {
                $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
            }
            if(!$user) {
                $user = $this->userService->create(
                    $email,
                    null,
                    isset($data['roles']) ? $data['roles'] : [],
                    $me->getField('name'),
                    null,
                    null,
                    null,
                    null,
                    null,
                    $me->getField('id')

                );
            }
            return new JsonResponse(['token' => $this->JWTManager->create($user), 'id' => $user->getId(), 'email' => $email, 'fields'=> $me->getFieldNames()]);
        } else {
            return new JsonResponse(['token' => null], 401);
        }
    }
}
