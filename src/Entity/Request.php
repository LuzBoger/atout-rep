<?php

namespace App\Entity;

use App\Enum\StatusRequest;
use App\Repository\RequestRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RequestRepository::class)]
#[ORM\InheritanceType("JOINED")]
#[ORM\DiscriminatorColumn("demande_type", "string")]
#[ORM\DiscriminatorMap(["objectHS" => ObjectHS::class, "homeRepair" => HomeRepair::class])]
class Request
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'requests')]
    private ?Customer $client = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $ModificationDate = null;

    #[ORM\Column(enumType: StatusRequest::class)]
    private ?StatusRequest $status = null;

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
}
