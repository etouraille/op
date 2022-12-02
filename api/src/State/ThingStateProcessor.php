<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Thing;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Metadata\Put;

class ThingStateProcessor implements ProcessorInterface
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        // Handle the state
        if($data instanceof Thing) {
            if($operation instanceof Put) {
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
                $this->em->flush();
                $this->em->merge($data);
                $this->em->flush();
            }
        }
    }
}
