<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer extends Account
{
    /**
     * @var Collection<int, Request>
     */
    #[ORM\OneToMany(targetEntity: Request::class, mappedBy: 'client')]
    private Collection $requests;

    /**
     * @var Collection<int, Address>
     */
    #[ORM\OneToMany(targetEntity: Address::class, mappedBy: 'customer')]
    private Collection $addresses;

    /**
     * @var Collection<int, OrderHistory>
     */
    #[ORM\OneToMany(targetEntity: OrderHistory::class, mappedBy: 'Customer')]
    private Collection $orderHistories;

    public function __construct()
    {
        $this->requests = new ArrayCollection();
        $this->addresses = new ArrayCollection();
        $this->orderHistories = new ArrayCollection();
    }

    /**
     * @return Collection<int, Request>
     */
    public function getRequests(): Collection
    {
        return $this->requests;
    }

    public function addRequest(Request $request): static
    {
        if (!$this->requests->contains($request)) {
            $this->requests->add($request);
            $request->setClient($this);
        }

        return $this;
    }

    public function removeRequest(Request $request): static
    {
        if ($this->requests->removeElement($request)) {
            // set the owning side to null (unless already changed)
            if ($request->getClient() === $this) {
                $request->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Address>
     */
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    public function addAddress(Address $address): static
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses->add($address);
            $address->setCustomer($this);
        }

        return $this;
    }

    public function removeAddress(Address $address): static
    {
        if ($this->addresses->removeElement($address)) {
            // set the owning side to null (unless already changed)
            if ($address->getCustomer() === $this) {
                $address->setCustomer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, OrderHistory>
     */
    public function getOrderHistories(): Collection
    {
        return $this->orderHistories;
    }

    public function addOrderHistory(OrderHistory $orderHistory): static
    {
        if (!$this->orderHistories->contains($orderHistory)) {
            $this->orderHistories->add($orderHistory);
            $orderHistory->setCustomer($this);
        }

        return $this;
    }

    public function removeOrderHistory(OrderHistory $orderHistory): static
    {
        if ($this->orderHistories->removeElement($orderHistory)) {
            // set the owning side to null (unless already changed)
            if ($orderHistory->getCustomer() === $this) {
                $orderHistory->setCustomer(null);
            }
        }

        return $this;
    }
}
