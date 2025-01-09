<?php

namespace App\Entity;

use App\Repository\PhotosRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PhotosRepository::class)]
class Photos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $photoPath = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $uploadDate = null;

    #[ORM\ManyToOne(inversedBy: 'photos')]
    private ?ObjectHS $ObjectHS = null;

    #[ORM\ManyToOne(inversedBy: 'photos')]
    private ?HomeRepair $HomeRepair = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPhotoPath(): ?string
    {
        return $this->photoPath;
    }

    public function setPhotoPath(string $photoPath): static
    {
        $this->photoPath = $photoPath;

        return $this;
    }

    public function getUploadDate(): ?\DateTimeInterface
    {
        return $this->uploadDate;
    }

    public function setUploadDate(\DateTimeInterface $uploadDate): static
    {
        $this->uploadDate = $uploadDate;

        return $this;
    }

    public function getObjectHS(): ?ObjectHS
    {
        return $this->ObjectHS;
    }

    public function setObjectHS(?ObjectHS $ObjectHS): static
    {
        $this->ObjectHS = $ObjectHS;

        return $this;
    }

    public function getHomeRepair(): ?HomeRepair
    {
        return $this->HomeRepair;
    }

    public function setHomeRepair(?HomeRepair $HomeRepair): static
    {
        $this->HomeRepair = $HomeRepair;

        return $this;
    }
}
