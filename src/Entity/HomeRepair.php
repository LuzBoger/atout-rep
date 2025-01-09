<?php

namespace App\Entity;

use App\Repository\HomeRepairRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HomeRepairRepository::class)]
#[ORM\InheritanceType("JOINED")]
#[ORM\DiscriminatorColumn("repair_type", "string")]
#[ORM\DiscriminatorMap(["painting" => Painting::class, "roofing" => Roofing::class])]
abstract class HomeRepair extends Request
{
    #[ORM\Column(length: 500)]
    private ?string $description = null;

    /**
     * @var Collection<int, Photos>
     */
    #[ORM\OneToMany(targetEntity: Photos::class, mappedBy: 'HomeRepair')]
    private Collection $photos;

    public function __construct()
    {
        $this->photos = new ArrayCollection();
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

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
            $photo->setHomeRepair($this);
        }

        return $this;
    }

    public function removePhoto(Photos $photo): static
    {
        if ($this->photos->removeElement($photo)) {
            // set the owning side to null (unless already changed)
            if ($photo->getHomeRepair() === $this) {
                $photo->setHomeRepair(null);
            }
        }

        return $this;
    }
}
