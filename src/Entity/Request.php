<?php

namespace App\Entity;

use App\Enum\StatusRequest;
use App\Repository\RequestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RequestRepository::class)]
#[ORM\InheritanceType("JOINED")]
#[ORM\DiscriminatorColumn(name: "demande_type", type: "string")]
#[ORM\DiscriminatorMap(["objectHS" => ObjectHS::class, "painting" => Painting::class, "roofing" => Roofing::class])]
class Request
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'requests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Customer $client = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $ModificationDate = null;

    #[ORM\Column(enumType: StatusRequest::class)]
    private ?StatusRequest $status = null;

    /**
     * @var Collection<int, Dates>
     */
    #[ORM\OneToMany(targetEntity: Dates::class, mappedBy: 'Request')]
    private Collection $dates;

    public function __construct()
    {
        $this->dates = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?Customer
    {
        return $this->client;
    }

    public function setClient(?Customer $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getModificationDate(): ?\DateTimeInterface
    {
        return $this->ModificationDate;
    }

    public function setModificationDate(\DateTimeInterface $ModificationDate): static
    {
        $this->ModificationDate = $ModificationDate;

        return $this;
    }

    public function getStatus(): ?StatusRequest
    {
        return $this->status;
    }

    public function setStatus(StatusRequest $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Dates>
     */
    public function getDates(): Collection
    {
        return $this->dates;
    }

    public function addDate(Dates $date): static
    {
        if (!$this->dates->contains($date)) {
            $this->dates->add($date);
            $date->setRequest($this);
        }

        return $this;
    }

    public function removeDate(Dates $date): static
    {
        if ($this->dates->removeElement($date)) {
            // set the owning side to null (unless already changed)
            if ($date->getRequest() === $this) {
                $date->setRequest(null);
            }
        }

        return $this;
    }
}
