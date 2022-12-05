<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\ThingTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ThingTypeRepository::class)]
#[GetCollection(normalizationContext: ['groups' => ['type']])]
#[Post]
#[Patch]
class ThingType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['type', 'list', 'get'])]
    private ?int $id = null;

    #[Groups(['type', 'list', 'get'])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'type', targetEntity: Thing::class)]
    private Collection $things;

    public function __construct()
    {
        $this->things = new ArrayCollection();
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
            $thing->setType($this);
        }

        return $this;
    }

    public function removeThing(Thing $thing): self
    {
        if ($this->things->removeElement($thing)) {
            // set the owning side to null (unless already changed)
            if ($thing->getType() === $this) {
                $thing->setType(null);
            }
        }

        return $this;
    }
}
