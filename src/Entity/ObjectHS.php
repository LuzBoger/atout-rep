<?php

namespace App\Entity;

use App\Enum\StateObject;
use App\Repository\ObjectHSRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ObjectHSRepository::class)]
class ObjectHS extends Request
{
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(enumType: StateObject::class)]
    private ?StateObject $state = null;

    #[ORM\Column]
    private ?int $age = null;

    #[ORM\Column(length: 500)]
    private ?string $Details = null;

    /**
     * @var Collection<int, Photos>
     */
    #[ORM\OneToMany(targetEntity: Photos::class, mappedBy: 'ObjectHS')]
    private Collection $photos;

    public function __construct()
    {
        $this->photos = new ArrayCollection();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getState(): ?StateObject
    {
        return $this->state;
    }

    public function setState(StateObject $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): static
    {
        $this->age = $age;

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->Details;
    }

    public function setDetails(string $Details): static
    {
        $this->Details = $Details;

        return $this;
    }

    /**
     * @return Collection<int, Photos>
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(Photos $photo): static
    {
        if (!$this->photos->contains($photo)) {
            $this->photos->add($photo);
            $photo->setObjectHS($this);
        }

        return $this;
    }

    public function removePhoto(Photos $photo): static
    {
        if ($this->photos->removeElement($photo)) {
            // set the owning side to null (unless already changed)
            if ($photo->getObjectHS() === $this) {
                $photo->setObjectHS(null);
            }
        }

        return $this;
    }
}
