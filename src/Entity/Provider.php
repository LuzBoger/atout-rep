<?php

namespace App\Entity;

use App\Repository\ProviderRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProviderRepository::class)]
class Provider extends Account
{
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $PriceFDP = null;

    public function getPriceFDP(): ?string
    {
        return $this->PriceFDP;
    }

    public function setPriceFDP(string $PriceFDP): static
    {
        $this->PriceFDP = $PriceFDP;

        return $this;
    }
}
