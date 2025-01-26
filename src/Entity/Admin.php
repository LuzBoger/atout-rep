<?php

namespace App\Entity;

use App\Repository\AdminRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdminRepository::class)]
class Admin extends Account
{
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $accountCreationDate = null;

    public function getAccountCreationDate(): ?\DateTimeInterface
    {
        return $this->accountCreationDate;
    }

    public function setAccountCreationDate(\DateTimeInterface $accountCreationDate): static
    {
        $this->accountCreationDate = $accountCreationDate;

        return $this;
    }
}
