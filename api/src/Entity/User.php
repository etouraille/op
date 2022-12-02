<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\ExpenseProcess;
use App\Controller\PingController;
use App\Filter\SearchOrFIlter;
use App\Repository\UserRepository;
use App\State\UserStateProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[Post(processor: UserStateProcessor::class)]
#[Put(processor: UserStateProcessor::class)]
#[GetCollection(normalizationContext: ['groups' => ['search']])]
#[Get(normalizationContext: ['groups' => ['user']])]
#[Patch]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Index(name: "search_index_email", columns: ['email'])]
#[ORM\Index(name: "search_index_firstname", columns: ['firstname'])]
#[ORM\Index(name: "search_index_lastname", columns: ['lastname'])]
#[ApiFilter(SearchOrFIlter::class, properties: ['email', 'firstname', 'lastname'])]
#[ApiResource(operations: [
    new GetCollection(
        uriTemplate: '/ping',
        controller: PingController::class,
        normalizationContext: ['groups' => ['get']],
        name: 'ping'
    ),
])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['collection', 'get','put','tback', 'search', 'user', 'reservation'])]
    private ?int $id = null;

    #[Groups(['post', 'collection', 'get', 'search', 'user', 'reservation'])]
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[Groups(['post', 'collection', 'get', 'user', 'reservation'])]
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */

    #[ORM\Column]
    private ?string $password = null;

    #[Groups(['post', 'collection'])]
    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Thing::class)]
    private Collection $things;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Reservation::class)]
    private Collection $reservations;

    #[Groups(['post', 'collection', 'get', 'search', 'user', 'reservation'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstname = null;

    #[Groups(['post', 'collection', 'get', 'search', 'user', 'reservation'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastname = null;


    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stripeCustomerId = null;

    #[MaxDepth(1)]
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Expense::class)]
    private Collection $expenses;

    #[MaxDepth(1)]
    #[ORM\Column(length: 255)]
    private ?string $stripeAccountId = null;

    #[MaxDepth(1)]
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Income::class)]
    private Collection $incomes;

    #[MaxDepth(1)]
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: IncomeData::class)]
    private Collection $incomeData;

    #[MaxDepth(1)]
    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Coin::class)]
    private Collection $coins;

    #[Groups(['put', 'user'])]
    #[ORM\Column(nullable: true)]
    private ?bool $isMemberValidated = null;

    public function __construct()
    {
        $this->things = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->expenses = new ArrayCollection();
        $this->incomes = new ArrayCollection();
        $this->incomeData = new ArrayCollection();
        $this->coins = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId($id): self {
        $this->id = $id;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Thing>
     */
    public function getThings(): Collection
    {
        return $this->things;
    }

    public function addThing(Thing $thing): self
    {
        if (!$this->things->contains($thing)) {
            $this->things->add($thing);
            $thing->setOwner($this);
        }

        return $this;
    }

    public function removeThing(Thing $thing): self
    {
        if ($this->things->removeElement($thing)) {
            // set the owning side to null (unless already changed)
            if ($thing->getOwner() === $this) {
                $thing->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setOwner($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getOwner() === $this) {
                $reservation->setOwner(null);
            }
        }

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getStripeCustomerId(): ?string
    {
        return $this->stripeCustomerId;
    }

    public function setStripeCustomerId(?string $stripeCustomerId): self
    {
        $this->stripeCustomerId = $stripeCustomerId;

        return $this;
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
            $expense->setUser($this);
        }

        return $this;
    }

    public function removeExpense(Expense $expense): self
    {
        if ($this->expenses->removeElement($expense)) {
            // set the owning side to null (unless already changed)
            if ($expense->getUser() === $this) {
                $expense->setUser(null);
            }
        }

        return $this;
    }

    public function getStripeAccountId(): ?string
    {
        return $this->stripeAccountId;
    }

    public function setStripeAccountId(string $stripeAccountId): self
    {
        $this->stripeAccountId = $stripeAccountId;

        return $this;
    }

    /**
     * @return Collection<int, Income>
     */
    public function getIncomes(): Collection
    {
        return $this->incomes;
    }

    public function addIncome(Income $income): self
    {
        if (!$this->incomes->contains($income)) {
            $this->incomes->add($income);
            $income->setUser($this);
        }

        return $this;
    }

    public function removeIncome(Income $income): self
    {
        if ($this->incomes->removeElement($income)) {
            // set the owning side to null (unless already changed)
            if ($income->getUser() === $this) {
                $income->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, IncomeData>
     */
    public function getIncomeData(): Collection
    {
        return $this->incomeData;
    }

    public function addIncomeData(IncomeData $incomeData): self
    {
        if (!$this->incomeData->contains($incomeData)) {
            $this->incomeData->add($incomeData);
            $incomeData->setUser($this);
        }

        return $this;
    }

    public function removeIncomeData(IncomeData $incomeData): self
    {
        if ($this->incomeData->removeElement($incomeData)) {
            // set the owning side to null (unless already changed)
            if ($incomeData->getUser() === $this) {
                $incomeData->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Coin>
     */
    public function getCoins(): Collection
    {
        return $this->coins;
    }

    public function addCoin(Coin $coin): self
    {
        if (!$this->coins->contains($coin)) {
            $this->coins->add($coin);
            $coin->setOwner($this);
        }

        return $this;
    }

    public function removeCoin(Coin $coin): self
    {
        if ($this->coins->removeElement($coin)) {
            // set the owning side to null (unless already changed)
            if ($coin->getOwner() === $this) {
                $coin->setOwner(null);
            }
        }

        return $this;
    }

    public function isIsMemberValidated(): ?bool
    {
        return $this->isMemberValidated;
    }

    public function setIsMemberValidated(?bool $isMemberValidated): self
    {
        $this->isMemberValidated = $isMemberValidated;

        return $this;
    }
}
