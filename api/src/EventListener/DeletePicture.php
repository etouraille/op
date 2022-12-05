<?php

namespace App\EventListener;

use App\Entity\Picture;
use App\Entity\User;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class DeletePicture
{

    private $cdn_url;

    public function __construct(private JWTTokenManagerInterface $JWTManager, string $cdn_url) {
        $this->cdn_url = $cdn_url;
    }

    public function postRemove(LifecycleEventArgs $args): void {
        $entity = $args->getObject();

        if(!$entity instanceof Picture) {
            return;
        }
        $file = $entity->getPicture();
        $user = $args->getObjectManager()->getRepository(User::class)->find(1);
        $token = $this->JWTManager->create($user);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->cdn_url . 'delete-picture/' . $file);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_NOBODY, TRUE); // remove body
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer " . $token ));
        $data = curl_exec($ch);
        $data;
        $error = curl_error($ch);
        $error;
    }
}