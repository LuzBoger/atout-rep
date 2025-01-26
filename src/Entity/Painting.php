<?php

namespace App\Entity;

use App\Enum\PaintType;
use App\Repository\PaintingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaintingRepository::class)]
class Painting extends HomeRepair
{

    #[ORM\Column]
    private ?int $surfaceArea = null;

    #[ORM\Column(enumType: PaintType::class)]
    private ?PaintType $paintType = null;

    public function getType(): string
    {
        return 'Painting';
    }

    public function getSurfaceArea(): ?int
    {
        return $this->surfaceArea;
    }

    public function setSurfaceArea(int $surfaceArea): static
    {
        $this->surfaceArea = $surfaceArea;

        return $this;
    }

    public function getPaintType(): ?PaintType
    {
        return $this->paintType;
    }

    public function setPaintType(PaintType $paintType): static
    {
        $this->paintType = $paintType;

        return $this;
    }
}
