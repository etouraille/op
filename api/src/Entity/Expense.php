<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Controller\ExpenseProcess;
use App\Repository\ExpenseRepository;
use App\State\ExpenseStateProvider;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(operations: [
    new GetCollection(
        uriTemplate: '/expense/process',
        controller: ExpenseProcess::class,
        name: 'expenseProcess',
        normalizationContext: ['groups' => ['expense']]
    ),
])]

#[ORM\Entity(repositoryClass: ExpenseRepository::class)]
#[ORM\Index(name: "find_for_income", columns: ['user_id', 'status'])]
#[GetCollection(
    normalizationContext: ['groups' => ['expense']],
    provider: ExpenseStateProvider::class
)]
class Expense
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['put', 'tback', 'expense'])]
    private ?int $id = null;

    #[Groups(['post', 'collection', 'get', 'tback', 'expense'])]
    #[ORM\OneToOne(inversedBy: 'expense', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Reservation $reservation = null;

    #[Groups(['post', 'collection', 'get'])]
    #[ORM\ManyToOne(inversedBy: 'expenses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[Groups(['post', 'collection', 'get'])]
    #[ORM\ManyToOne(inversedBy: 'expenses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[Groups(['post', 'collection', 'get', 'expense'])]
    #[ORM\Column]
    private ?int $amount = null;

    #[Groups(['post', 'collection', 'get'])]
    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[Groups(['post', 'collection', 'get', 'expense'])]
    #[ORM\ManyToOne(inversedBy: 'expenses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Thing $thing = null;

    #[ORM\OneToOne(mappedBy: 'expense', cascade: ['persist', 'remove'])]
    private ?Income $income = null;

    #[Groups(['collection', 'expense'])]
    #[ORM\ManyToOne(inversedBy: 'expenses')]
    private ?IncomeData $incomeData = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReservation(): ?Reservation
    {
        return $this->reservation;
    }

    public function setReservation(Reservation $reservation): self
    {
        $this->reservation = $reservation;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

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

    public function getIncome(): ?Income
    {
        return $this->income;
    }

    public function setIncome(Income $income): self
    {
        // set the owning side of the relation if necessary
        if ($income->getExpense() !== $this) {
            $income->setExpense($this);
        }

        $this->income = $income;

        return $this;
    }

    public function getIncomeData(): ?IncomeData
    {
        return $this->incomeData;
    }

    public function setIncomeData(?IncomeData $incomeData): self
    {
        $this->incomeData = $incomeData;

        return $this;
    }
}
