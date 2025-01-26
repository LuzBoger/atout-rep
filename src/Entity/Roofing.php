<?php

namespace App\Entity;

use App\Enum\RoofMaterial;
use App\Repository\RoofingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoofingRepository::class)]
class Roofing extends HomeRepair
{

    #[ORM\Column(enumType: RoofMaterial::class)]
    private ?RoofMaterial $roofMaterial = null;

    #[ORM\Column]
    private ?bool $needInsulation = null;

    public function getType(): string
    {
        return 'Roofing';
    }

    public function getRoofMaterial(): ?RoofMaterial
    {
        return $this->roofMaterial;
    }

    public function setRoofMaterial(RoofMaterial $roofMaterial): static
    {
        $this->roofMaterial = $roofMaterial;

        return $this;
    }

    public function isNeedInsulation(): ?bool
    {
        return $this->needInsulation;
    }

    public function setNeedInsulation(bool $needInsulation): static
    {
        $this->needInsulation = $needInsulation;

        return $this;
    }
}
