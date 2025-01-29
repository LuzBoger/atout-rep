<?php

namespace App\Entity;

use App\Enum\StatusCart;
use App\Repository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(enumType: StatusCart::class)]
    private ?StatusCart $statusCart = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Customer $customer = null;

    #[ORM\OneToMany(targetEntity: CartProduct::class, mappedBy: 'cart', cascade: ['persist', 'remove'])]
    private Collection $cartProducts;

    public function __construct()
    {
        $this->cartProducts = new ArrayCollection();
    }

    public function getCartProducts(): Collection
    {
        return $this->cartProducts;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatusCart(): ?StatusCart
    {
        return $this->statusCart;
    }

    public function setStatusCart(StatusCart $statusCart): static
    {
        $this->statusCart = $statusCart;

        return $this;
    }

    /**
     * @return Collection<int, CartProduct>
     */
    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): static
    {
        $this->customer = $customer;

        return $this;
    }
}
