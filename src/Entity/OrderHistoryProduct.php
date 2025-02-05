<?php

namespace App\Entity;

use App\Repository\OrderHistoryProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderHistoryProductRepository::class)]
class OrderHistoryProduct
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $productName = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $quantity = null;

    #[ORM\Column(nullable: true)]
    private ?float $price = null;

    #[ORM\ManyToOne(inversedBy: 'orderProducts')]
    private ?OrderHistory $orderHistory = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductName(): ?string
    {
        return $this->productName;
    }

    public function setProductName(?string $productName): static
    {
        $this->productName = $productName;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getOrderHistory(): ?OrderHistory
    {
        return $this->orderHistory;
    }

    public function setOrderHistory(?OrderHistory $orderHistory): static
    {
        $this->orderHistory = $orderHistory;

        return $this;
    }
}
