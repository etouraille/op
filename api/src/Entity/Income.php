<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\IncomeRepository;
use App\State\CoinStateProvider;
use App\State\IncomeStateProvider;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: IncomeRepository::class)]
#[ApiResource(denormalizationContext: ['groups' => ['post']])]
#[GetCollection(normalizationContext: ['groups' => ['incomes']], provider: IncomeStateProvider::class)]

class Income
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['collection', 'incomes'])]
    #[ORM\Column]
    private ?int $amount = null;

    #[Groups(['collection', 'incomes'])]
    #[ORM\ManyToOne(inversedBy: 'incomes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Thing $thing = null;

    #[Groups(['collection', 'incomes'])]
    #[ORM\ManyToOne(inversedBy: 'incomes')]
    #[ORM\JoinColumn(nullable: false)]
    #[ApiProperty(readableLink: false, writableLink: false)]
    private ?User $user = null;

    #[Groups(['collection', 'incomes'])]
    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[Groups(['collection'])]
    #[ORM\ManyToOne(inversedBy: 'income', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Expense $expense = null;

    #[Groups(['incomes'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date = null;

    private $pendingIncomes = [];

    public function getId(): ?int
    {
        return $this->id;
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getExpense(): ?Expense
    {
        return $this->expense;
    }

    public function setExpense(Expense $expense): self
    {
        $this->expense = $expense;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function addPendingIncome(Income $income) {
        $this->pendingIncomes[] = $income;
        return $this;
    }

    public function getPendingIncomes() {
        return $this->pendingIncomes;
    }
}
