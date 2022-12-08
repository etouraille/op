<?php

namespace App\Service;

use App\Entity\Coin;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;

class UserService
{
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

    public function create($user, $email, $password = null, $roles=[], $firstname = null, $lastname= null, $address = null, $zipcode=null, $city=null, $photo=null, $facebookId = null) {


        $user->setEmail($email);
        //create stripe user:
        $stripeCustomer = \Stripe\Customer::create(['email' => $email]);
        $user->setStripeCustomerId($stripeCustomer->id);

        // encode password
        $plainPassword = $password ? $password : rand(1, 10000000000000000);
        $hashedPassword = $this->hasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        // roles are unique
        $roles = array_unique($roles);

        // if user is not admin remove role_admin from roles
        $roles = $this->removeRoleAdminIfIsNotAdmin($roles);
        $user->setRoles($roles);

        // stripe account to do virements
        $stripe = new \Stripe\StripeClient($this->secret);
        $account = $stripe->accounts->create(['type' => 'express', 'email' => $email]);
        $user->setStripeAccountId($account->id);

        //fields
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setAddress($address);
        $user->setZipcode($zipcode);
        $user->setCity($city);
        $user->setFacebookId($facebookId);

        // persist
        $this->em->persist($user);
        $this->em->flush();

        // add coins
        $coin = new Coin();
        $coin->setAmount(100000);
        $coin->setReason(Coin::REASON_PROVIDE);
        $coin->setOwner($user);

        $this->em->persist($coin);
        $this->em->flush();

        return $user;

    }

    // Here for security Reason, because some user might log with no admin rights
    // even more, in app when créate user they log with no right
    // prevent from malicious usage of the api.
    public function removeRoleAdminIfIsNotAdmin($roles) {
        $hasRoles = $this->security?->getUser()?->getRoles();
        if(!$hasRoles) {
            if($key = array_search('ROLE_ADMIN',$roles) !== false ) {
                unset($roles[$key]);
            }
        }
        return $roles;
    }
}