<?php

namespace App\Entity;

use App\Repository\AddressRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(length: 10)]
    private ?string $PostalCode = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    /**
     * @var Collection<int, Dates>
     */
    #[ORM\OneToMany(targetEntity: Dates::class, mappedBy: 'Address')]
    private Collection $dates;

    #[ORM\ManyToOne(inversedBy: 'addresses')]
    private ?Customer $customer = null;

    /**
     * @var Collection<int, Dates>
     */
    #[ORM\OneToMany(targetEntity: Dates::class, mappedBy: 'address')]
    private Collection $Dates;

    public function __construct()
    {
        $this->dates = new ArrayCollection();
        $this->Dates = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->PostalCode;
    }

    public function setPostalCode(string $PostalCode): static
    {
        $this->PostalCode = $PostalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

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
            $date->setAddress($this);
        }

        return $this;
    }

    public function removeDate(Dates $date): static
    {
        if ($this->dates->removeElement($date)) {
            // set the owning side to null (unless already changed)
            if ($date->getAddress() === $this) {
                $date->setAddress(null);
            }
        }

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    public function __toString(): string
    {
        return sprintf('%s (%s)', $this->getAddress(), $this->getPostalCode());
    }
}
