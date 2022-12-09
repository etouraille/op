<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\PayReturn;
use App\Entity\Thing;
use App\Entity\User;
use App\Service\CacheService;
use App\Service\UrlGenerator;
use Doctrine\ORM\EntityManagerInterface;

class ThingStateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private UrlGenerator $service,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ?PayReturn
    {
        /** @var $data Thing */
        $name = $data->getShop()->getName();
        $url = $this->service->makeUrl($name, $data->getName());
        $data->setUrl($url);

        try {
            CacheService::purge();
        } catch (\Exception $e) {
            return new PayReturn(false, false, null, $e->getMessage());
        }

        if ($operation instanceof Post) {
            //$this->em->merge($data->getShop());
            $this->em->persist($data);
            $this->em->flush();
        } elseif($operation instanceof Patch || $operation instanceof Put) {
            $this->em->merge($data);
            $this->em->flush();
        }


        if($operation instanceof Patch || $operation instanceof Put || $operation instanceof Post) {
            $user = $data->getOwner();
            // si active l'objet et que son propriétaire est un membre.
            if(
                false!== array_search('ROLE_MEMBER', $user->getRoles())
                && $data->getStatus() === 'active') {
                    $user->setIsMemberValidated(true);
                    $this->em->merge($user);
            } elseif(false!== array_search('ROLE_MEMBER', $user->getRoles())
                && $data->getStatus() === 'inactive') {
                // si il n'a plus d'objet
                // on le passe à inactif.
                if(!$this->em->getRepository(User::class)->hasActiveThings($user)) {
                    $user->setIsMemberValidated(false);
                    $this->em->merge($user);
                }
            }
            if($data->getStatus() === 'active') {
                $data->setActivationDate(new \DateTime());
            } elseif($data->getStatus() === 'inactive') {
                $data->setActivationDate(null);
            }
        }
        // remove old pictures
        if($operation instanceof Put || $operation instanceof Patch) {
            $oldThing = clone $this->em->getRepository(Thing::class)->findOneBy(['id'=> $data->getId()]);
            $pictures = $oldThing->getPictures();
            // check which one are preserved
            foreach($pictures as $oldPicture) {
                $preserved = false;
                foreach($data->getPictures() as $newPic) {
                    if($newPic->getId() === $oldPicture->getId() && $newPic->getId() && $oldPicture->getId()) {
                        $preserved = true;
                    }
                }
                if(!$preserved) $this->em->remove($oldPicture);
            }

        }

        if ($operation instanceof Post) {
            $this->em->persist($data);
            $this->em->flush();
        } elseif($operation instanceof Patch || $operation instanceof Put) {
            $this->em->merge($data);
            $this->em->flush();
        }

        return new PayReturn(true, false, null, null);
    }
}