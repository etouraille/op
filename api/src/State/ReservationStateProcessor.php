<?php


namespace App\State;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Reservation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Security;

class ReservationStateProcessor implements ProcessorInterface
{

    public function __construct(private Security $security, private EntityManagerInterface $em) {

    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        // if user is role_member it must be isMemberValidated
        $user = $this->security->getUser();
        /** @var $user User */
        if(false !== array_search('ROLE_MEMBER', $user->getRoles()) && !$user->isIsMemberValidated()) {
            throw new UnauthorizedHttpException('Member is not Authorized');
        }
        if($data instanceof Reservation && $operation instanceof Post) {
            // set owner as current user only when it's not defined.
            // on the app side of the api
            if(!$data->getOwner()) {
                $data->setOwner($this->security->getUser());
            }
            $this->em->persist($data);
            $this->em->flush();
        } elseif($data instanceof Reservation && $operation instanceof Patch) {
            $this->em->merge($data);
            $this->em->flush();
        } elseif($data instanceof Reservation && $operation instanceof Delete) {
            $this->em->remove($data);
            $this->em->flush();
        }
    }
}