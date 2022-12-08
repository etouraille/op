<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Controller\BillsController;
use App\Controller\ExpenseProcess;
use App\Controller\ExportIncomeController;
use App\Repository\IncomeDataRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: IncomeDataRepository::class)]
#[ApiResource(operations: [
    new GetCollection(
        uriTemplate: '/export/income',
        controller: ExportIncomeController::class,
        denormalizationContext: ['groups' => ['post', 'put']],
        security: "is_granted('ROLE_ADMIN')",
        name: 'exportIncomeController',
    ),
    new GetCollection(
        uriTemplate: '/bills',
        controller: BillsController::class,
        denormalizationContext:  ['groups' => ['post', 'put']],
        security: "is_granted('ROLE_USER')",
        name: 'Mes facture'
    )
], denormalizationContext: ['groups' => ['post']])]
class IncomeData
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['post'])]
    #[ORM\OneToMany(mappedBy: 'incomeData', targetEntity: Expense::class)]
    private Collection $expenses;

    #[Groups(['post', 'collection'])]
    #[ORM\Column(length: 255)]
    private ?string $file = null;

    #[Groups(['post'])]
    #[ORM\Column]
    private ?float $amount = null;

    #[Groups(['post'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[Groups(['post'])]
    #[ORM\ManyToOne(inversedBy: 'incomeData')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function __construct()
    {
        $this->expenses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Expense>
     */
    public function getExpenses(): Collection
    {
        return $this->expenses;
    }

    public function addExpense(Expense $expense): self
    {
        if (!$this->expenses->contains($expense)) {
            $this->expenses->add($expense);
            $expense->setIncomeData($this);
        }

        return $this;
    }

    public function removeExpense(Expense $expense): self
    {
        if ($this->expenses->removeElement($expense)) {
            // set the owning side to null (unless already changed)
            if ($expense->getIncomeData() === $this) {
                $expense->setIncomeData(null);
            }
        }

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(string $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

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
