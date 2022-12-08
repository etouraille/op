<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Coin;
use App\Entity\User;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use Symfony\Component\Security\Core\Security;

class UserStateProcessor implements ProcessorInterface
{

    protected $stripe;
    private $secret;

    public function __construct(
        private UserPasswordHasherInterface $hasher,
        private EntityManagerInterface $em,
        private UserService $service,
        $stripe
    ) {
        $this->stripe = \Stripe\Stripe::setApiKey($stripe);
        $this->secret = $stripe;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        // Handle the state
        if($data instanceof User) {
            if($operation instanceof Post) {

                //create stripe user:
                $data = $this->service->create(
                    $data,
                    $data->getEmail(),
                    $data->getPassword(),
                    $data->getRoles(),
                    $data->getFirstname(),
                    $data->getLastname(),
                    $data->getAddress(),
                    $data->getZipcode(),
                    $data->getCity()
                );

            } elseif ($operation instanceof Put) {
                if($data->getPassword()) {
                    $hashedPassword = $this->hasher->hashPassword($data,$data->getPassword());
                    $data->setPassword($hashedPassword);
                    $roles = $data->getRoles();
                    $roles = array_unique($roles);
                    // is user is not admin remove role_admin from roles.
                    $roles = $this->service->removeRoleAdminIfIsNotAdmin($roles);
                    $data->setRoles($roles);
                    $this->em->merge($data);
                    $this->em->flush();
                }
            }
        }
    }
}
