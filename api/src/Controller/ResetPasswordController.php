<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\MailerService;
use App\Service\UUID;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ResetPasswordController extends AbstractController
{
    public function __construct(
        private $app_url,
        private MailerService $service,
        private UserPasswordHasherInterface $hasher,
    ) {}

    #[Route('/reset/password/{email}', name: 'app_reset_password', methods: ['GET'])]
    public function index($email, EntityManagerInterface $em): JsonResponse
    {

        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
        /** @var $user User */
        if ($user) {
            $uuid = UUID::v4();
            $user->setToken($uuid);
            $em->merge($user);
            $em->flush();
            $email = $user->getEmail();
            $name = $user->getFirstname();

            $content = $this->renderView('reset_password/index.html.twig', [
                'name' => $name,
                'app_url' => $this->app_url,
                'token' => $uuid,
            ]);

            $subject = 'Réinitialisation de votre mot de passe sur queel.io';

            list( $success, $error ) = $this->service->send($email, $name, $subject, $content);

            return new JsonResponse(['success' => $success, 'error' => $error], $success ? 200: 500);

        } else {
            return new JsonResponse(['success' => false, 'error' => 'l\'email n\'existe pas'], 400);
        }
    }

    #[Route('/reset/password', name: 'app_reset_password_post', methods: ['POST'])]
    public function post(EntityManagerInterface $em, Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);
        $user = $em->getRepository(User::class)->findOneBy(['token' => $data['token']]);
        /** @var $user User */
        if ($user && $data['token']) {

            $plainPassword = $data['password'];
            $encodedPassword = $this->hasher->hashPassword($user, $plainPassword);
            $user->setPassword($encodedPassword);
            $user->setToken(null);
            $em->merge($user);
            $em->flush();

            return new JsonResponse(['success' => true]);

        } else {
            return new JsonResponse(['success' => false, 'error' => 'le token n existe pas'], 500);
        }
    }
}
