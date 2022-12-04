<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Coin;
use App\Entity\User;
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
        private Security $security,
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
                $stripeCustomer = \Stripe\Customer::create(['email' => $data->getEmail()]);
                $data->setStripeCustomerId($stripeCustomer->id);
                // encode password
                if($data->getPassword()) {
                    $plainPassword = $data->getPassword();
                    $hashedPassword = $this->hasher->hashPassword($data, $plainPassword);
                    $data->setPassword($hashedPassword);
                } else {
                    $plainPassword = rand(1, 10000000000000000);
                    $hashedPassword = $this->hasher->hashPassword($data, $plainPassword);
                    $data->setPassword($hashedPassword);
                }
                // roles are unique
                $roles = $data->getRoles();
                $roles = array_unique($roles);
                // if user is not admin remove role_admin from roles
                $roles = $this->removeRoleAdminIfIsNotAdmin($roles);
                // si l'user est member on ajoute un compte stripe pour pouvoir faire les virements.
                if (false !== array_search('ROLE_MEMBER', $roles)) {
                    $stripe = new \Stripe\StripeClient($this->secret);
                    $account = $stripe->accounts->create(['type' => 'express', 'email' => $data->getEmail()]);
                    $data->setStripeAccountId($account->id);
                }
                $data->setRoles($roles);
                // persist
                $this->em->persist($data);
                $this->em->flush();

                // add coins
                $coin = new Coin();
                $coin->setAmount(100000);
                $coin->setReason(Coin::REASON_PROVIDE);
                $coin->setOwner($data);

                $this->em->persist($coin);
                $this->em->flush();

            } elseif ($operation instanceof Put) {
                if($data->getPassword()) {
                    $hashedPassword = $this->hasher->hashPassword($data,$data->getPassword());
                    $data->setPassword($hashedPassword);
                    $roles = $data->getRoles();
                    $roles = array_unique($roles);
                    // is user is not admin remove role_admin from roles.
                    $roles = $this->removeRoleAdminIfIsNotAdmin($roles);
                    $data->setRoles($roles);
                    $this->em->merge($data);
                    $this->em->flush();
                }
            }
        }
    }

    // Here for security Reason, because some user might log with no admin rights
    // even more, in app when créate user they log with no right
    // prevent from malicious usage of the api.
    private function removeRoleAdminIfIsNotAdmin($roles) {
        // TODO à revoir.
        $isAdmin = $this->security?->getUser()?->getRoles();
        if(!$isAdmin) {
            if($key = array_search('ROLE_ADMIN',$roles) !== false ) {
                unset($roles[$key]);
            }
        }
        return $roles;
    }
}
