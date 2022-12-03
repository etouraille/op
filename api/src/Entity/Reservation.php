<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\ReservationRepository;
use App\State\ReservationStateProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ApiResource]
#[Post(
    normalizationContext: ['groups' => ['reservation']],
    denormalizationContext: ['groups' => ['post']],
    processor: ReservationStateProcessor::class
)]
#[Patch(denormalizationContext: ['groups' => ['post']], processor: ReservationStateProcessor::class)]
#[Delete(processor: ReservationStateProcessor::class)]
class Reservation
{
    const STATE_BOOKED = null; // or null mostly not set.
    const STATE_OUT = 1;
    const STATE_BACK = 2;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['collection', 'put', 'get', 'add', 'tback', 'out', 'reservation'])]
    private ?int $id = null;

    #[Groups(['collection', 'get', 'put', 'post', 'add', 'tback', 'expense', 'reservation'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startDate = null;

    #[Groups(['collection', 'get', 'put', 'post','add', 'tback','expense', 'reservation'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $endDate = null;

    #[Groups(['post', 'collection'])]
    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Thing $thing = null;

    #[Groups(['collection', 'get', 'put', 'post', 'tback', 'search', 'reservation'])]
    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[Groups(['collection', 'get', 'put', 'post', 'back', 'add', 'tback', 'out', 'search', 'reservation'])]
    #[ORM\Column(nullable: true)]
    private ?int $state = null;

    #[Groups(['collection', 'put', 'get', 'post', 'add', 'tback', 'reservation', 'expense'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $backDate = null;

    #[Groups(['back'])]
    #[ORM\OneToOne(mappedBy: 'reservation', cascade: ['persist', 'remove'])]
    private ?Expense $expense = null;

    #[Groups(['collection', 'put', 'get', 'post', 'add', 'reservation', 'expense'])]
    private ?int $delta;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

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

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getState(): ?int
    {
        return $this->state;
    }

    public function setState(?int $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getBackDate(): ?\DateTimeInterface
    {
        return $this->backDate;
    }

    public function setBackDate(?\DateTimeInterface $backDate): self
    {
        $this->backDate = $backDate;

        return $this;
    }

    public function getExpense(): ?Expense
    {
        return $this->expense;
    }

    public function setExpense(Expense $expense): self
    {
        // set the owning side of the relation if necessary
        if ($expense->getReservation() !== $this) {
            $expense->setReservation($this);
        }

        $this->expense = $expense;

        return $this;
    }

    public function getDelta() {
        $delta = null;
        if ($this->startDate && $this->backDate) {
            $this->startDate->setTime(0, 0, 0);
            $this->backDate->setTime(0, 0, 0);
            $delta = 1 + $this->startDate->diff($this->backDate)->format("%r%a");
        }
        return $delta;

    }
}
