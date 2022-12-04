<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\Current;
use App\Controller\Done;
use App\Controller\GetOutThingsForUser;
use App\Controller\PayController;
use App\Controller\Pending;
use App\Controller\SetThingBack;
use App\Controller\ThingAdd;
use App\Controller\ThingAll;
use App\Controller\ThingsBack;
use App\Controller\UrlContoller;
use App\Controller\Waiting;
use App\Filter\SearchOrFIlter;
use App\Repository\ThingRepository;
use App\State\LastStateProvider;
use App\State\ProposedStateProvider;
use App\State\SearchStateProvider;
use App\State\StarStateProvider;
use App\State\StartStateProvider;
use App\State\ThingBackStateProcessor;
use App\State\ThingStateProcessor;
use App\State\ThingStateProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: ThingRepository::class)]
#[ORM\Index(name: "search_index", columns: ["name", 'description'])]
#[ORM\Index(name: "search_index_name", columns: ["name"])]
#[ORM\Index(name: "search_index_description", columns: ['description'])]
#[ApiResource(operations: [
    new GetCollection(
        uriTemplate: '/things/search',
        normalizationContext: ['groups' => ['search']],
        name: 'search things',
        provider: SearchStateProvider::class,
    )
])]
#[ApiResource(operations: [
    new GetCollection(
        uriTemplate: '/things-out',
        controller: GetOutThingsForUser::class,
        normalizationContext: ['groups' => ['out']],
        name: 'thingsOut'
    )
])]
#[ApiResource(operations: [
    new Post(
        uriTemplate: '/thing/add',
        controller: ThingAdd::class,
        denormalizationContext: ['groups' => ['add']],
        name: 'thingAddFromApp'
    )
])]
#[ApiResource(operations: [
    new GetCollection(
        uriTemplate: '/thing/all',
        controller: ThingAll::class,
        normalizationContext: ['groups' => ['list']],
        denormalizationContext: ['groups' => ['collection']],
        name: 'thingsAll'
    )
])]

#[ApiResource(operations: [
    new GetCollection(
        uriTemplate: '/pending',
        controller: Pending::class,
        normalizationContext: ['groups' => ['pending', 'reservation']],
        name: 'Les objets en retard'
    )
])]
#[ApiResource(operations: [
    new GetCollection(
        uriTemplate: '/waiting',
        controller: Waiting::class,
        normalizationContext: ['groups' => ['pending', 'reservation']],
        name: 'mes reservationq en attente'
    )
])]
#[ApiResource(operations: [
    new GetCollection(
        uriTemplate: '/current',
        controller: Current::class,
        normalizationContext: ['groups' => ['pending', 'reservation']],
        name: 'mes objets en cours de réservation'
    )
])]
#[ApiResource(operations: [
    new GetCollection(
        uriTemplate: '/done',
        controller: Done::class,
        normalizationContext: ['groups' => ['pending', 'reservation']],
        name: 'Mes objets réservés par la passé'
    )
])]


#[ApiResource(operations: [
    new GetCollection(
        uriTemplate: '/url',
        controller: UrlContoller::class,
        normalizationContext: ['groups' => ['url']],
        name: 'mes url disponibles'
    )
])]
#[ApiResource(operations: [
    new GetCollection(
        uriTemplate: '/stars',
        normalizationContext: ['groups' => ['list', 'reservation']],
        name: 'mes objets stars',
        provider: StarStateProvider::class
    )
])]

#[ApiResource(operations: [
    new GetCollection(
        uriTemplate: '/lasts',
        normalizationContext: ['groups' => ['list', 'reservation']],
        name: 'mes objets dernièrement ajoutés',
        provider: LastStateProvider::class
    )
])]
#[ApiResource(operations: [
    new GetCollection(
        uriTemplate: '/proposed',
        normalizationContext: ['groups' => ['list', 'reservation']],
        name: 'Proposés par les membre',
        provider: ProposedStateProvider::class
    )
])]
#[ApiResource(operations: [
    new GetCollection(
        uriTemplate: '/pay',
        controller: PayController::class,
        normalizationContext: ['groups' => ['list', 'reservation']],
        name: 'Payer depuis le site',

    )
])]
#[GetCollection(normalizationContext: ['groups' => ['search']], provider: ThingStateProvider::class)]
#[Get(normalizationContext: ['groups' => ['get', 'reservation']])]
#[Post(denormalizationContext: ['groups' => ['post']], processor: ThingStateProcessor::class)]
#[Put(denormalizationContext: ['groups' => ['post', 'put']], processor: ThingStateProcessor::class)]
#[Patch(denormalizationContext: ['groups' => ['post', 'put']], processor: ThingStateProcessor::class)]
#[ApiFilter(SearchOrFIlter::class, properties: ['name', 'description'])]

class Thing
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['post', 'collection', 'get', 'put', 'tback', 'out', 'search', 'expense', 'list', 'pending', 'incomes', 'coins', 'url'])]
    private ?int $id = null;

    #[Groups(['post', 'collection', 'get', 'add', 'tback', 'out', 'search','expense', 'list','pending', 'incomes', 'coins'])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Groups(['post', 'collection', 'get', 'add', 'list','pending'])]
    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[Groups(['post', 'collection', 'get', 'add', 'list'])]
    #[ORM\Column]
    private ?float $price = null;

    #[Groups(['post', 'collection', 'get', 'list'])]
    #[ORM\ManyToOne(inversedBy: 'things')]
    #[ORM\JoinColumn(name: "thing_id", referencedColumnName: "id", nullable:true)]
    private ?ThingType $type = null;

    #[Groups(['post', 'collection', 'get', 'add', 'search', 'list'])]
    #[ORM\OneToMany(mappedBy: 'thing', targetEntity: Picture::class, cascade: ['persist', 'merge'], fetch: 'EAGER', orphanRemoval: true)]
    private Collection $pictures;

    #[MaxDepth(2)]
    #[Groups(['post', 'collection', 'get', 'tback', 'list'])]
    #[ORM\ManyToOne(cascade: ['persist'], fetch: 'EAGER', inversedBy: 'things')]
    private ?User $owner = null;

    #[Groups(['put','collection', 'back', 'add', 'tback', 'out', 'search', 'reservation'])]
    #[ORM\OneToMany(mappedBy: 'thing', targetEntity: Reservation::class, orphanRemoval: true)]
    private Collection $reservations;

    #[Groups(['post', 'collection', 'get', 'put', 'expense', 'pending', 'list'])]
    #[ORM\Column(nullable: true)]
    private ?float $dailyPrice = null;

    #[MaxDepth(2)]
    #[Groups(['post', 'collection', 'put'])]
    #[ORM\OneToMany(mappedBy: 'thing', targetEntity: Expense::class, cascade: ['persist'])]
    private Collection $expenses;

    #[MaxDepth(2)]
    #[ORM\OneToMany(mappedBy: 'thing', targetEntity: Income::class)]
    private Collection $incomes;

    #[ORM\OneToMany(mappedBy: 'thing', targetEntity: Coin::class)]
    private Collection $coins;

    #[Groups(['post', 'collection', 'get', 'put', 'add', 'tback', 'list', 'pending'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    #[Groups(['add'])]
    #[ORM\ManyToOne(inversedBy: 'things')]
    private ?Shop $shop = null;

    #[Groups(['url'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $url = null;

    #[Groups(['put'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $activationDate = null;

    public function __construct()
    {
        $this->pictures = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->expenses = new ArrayCollection();
        $this->incomes = new ArrayCollection();
        $this->coins = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getType(): ?ThingType
    {
        return $this->type;
    }

    public function setType(?ThingType $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, Picture>
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures->add($picture);
            $picture->setThing($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): self
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getThing() === $this) {
                $picture->setThing(null);
            }
        }

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
            $reservation->setThing($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getThing() === $this) {
                $reservation->setThing(null);
            }
        }

        return $this;
    }

    public function getDailyPrice(): ?float
    {
        return $this->dailyPrice;
    }

    public function setDailyPrice(?float $dailyPrice): self
    {
        $this->dailyPrice = $dailyPrice;

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
            $expense->setThing($this);
        }

        return $this;
    }

    public function removeExpense(Expense $expense): self
    {
        if ($this->expenses->removeElement($expense)) {
            // set the owning side to null (unless already changed)
            if ($expense->getThing() === $this) {
                $expense->setThing(null);
            }
        }

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
            $income->setThing($this);
        }

        return $this;
    }

    public function removeIncome(Income $income): self
    {
        if ($this->incomes->removeElement($income)) {
            // set the owning side to null (unless already changed)
            if ($income->getThing() === $this) {
                $income->setThing(null);
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
            $coin->setThing($this);
        }

        return $this;
    }

    public function removeCoin(Coin $coin): self
    {
        if ($this->coins->removeElement($coin)) {
            // set the owning side to null (unless already changed)
            if ($coin->getThing() === $this) {
                $coin->setThing(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getShop(): ?Shop
    {
        return $this->shop;
    }

    public function setShop(?Shop $shop): self
    {
        $this->shop = $shop;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getActivationDate(): ?\DateTimeInterface
    {
        return $this->activationDate;
    }

    public function setActivationDate(?\DateTimeInterface $activationDate): self
    {
        $this->activationDate = $activationDate;

        return $this;
    }
}
