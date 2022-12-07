<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\CompensationRepository;
use App\State\CompensationStateProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CompensationRepository::class)]
#[ApiResource]
#[ApiResource(operations: [
        new Post(
            uriTemplate: '/compensations',
            normalizationContext: ['groups' => ['compensation', 'pay' ]],
            denormalizationContext: ['groups' => ['compensation']],
            security: "is_granted('ROLE_ADMIN')",

            processor: CompensationStateProcessor::class
        ),
        new GetCollection(
            uriTemplate: '/compensations',
            normalizationContext: ['groups' => ['compensation']],
            security: "is_granted('ROLE_ADMIN')",

        )
    ]
)]
class Compensation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['compensation'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['compensation'])]
    private ?float $rate = null;

    #[ORM\ManyToOne(inversedBy: 'compensation')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['compensation'])]
    private ?Thing $thing = null;

    #[ORM\ManyToOne(inversedBy: 'compensation')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['compensation'])]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRate(): ?float
    {
        return $this->rate;
    }

    public function setRate(float $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function getThing(): ?Thing
    {
        return $this->thing;
    }

    public function setThing(?Thing $thing): self
    {
        $this->thing = $thing;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
