<?php

namespace App\Entity;

use ApiPlatform\Metadata\GetCollection;
use App\Controller\CoinController;
use App\Repository\CoinRepository;
use App\State\CoinStateProvider;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CoinRepository::class)]
#[GetCollection(normalizationContext: ['groups' => ['coins']], provider: CoinStateProvider::class)]
class Coin
{

    const REASON_PROVIDE = 0;
    const REASON_ADD = 1;
    const REASON_REMOVE = 2;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['collection', 'coins'])]
    #[ORM\ManyToOne(inversedBy: 'coins')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[Groups(['collection', 'coins'])]
    #[ORM\Column]
    private ?int $amount = null;

    #[Groups(['collection', 'coins'])]
    #[ORM\Column]
    private ?int $reason = null;

    #[Groups(['collection', 'coins'])]
    #[ORM\ManyToOne(inversedBy: 'coins')]
    private ?Thing $thing = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getReason(): ?int
    {
        return $this->reason;
    }

    public function setReason(int $reason): self
    {
        $this->reason = $reason;

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
}
