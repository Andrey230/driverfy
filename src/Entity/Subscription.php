<?php

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubscriptionRepository::class)]
class Subscription
{
    const BASE_SUBSCRIPTION = 'base';
    const BEGINNER_SUBSCRIPTION = 'beginner';
    const PRO_SUBSCRIPTION = 'pro';

    const BASE_MAX_DRIVERS = 5;
    const BEGINNER_MAX_DRIVERS = 15;
    const PRO_MAX_DRIVERS = 50;

    const BASE_PRICE = 50;
    const BEGINNER_PRICE = 100;
    const PRO_PRICE = 500;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column]
    private ?int $max_drivers = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'subscription_id')]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getMaxDrivers(): ?int
    {
        return $this->max_drivers;
    }

    public function setMaxDrivers(int $max_drivers): static
    {
        $this->max_drivers = $max_drivers;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setSubscriptionId($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getSubscriptionId() === $this) {
                $user->setSubscriptionId(null);
            }
        }

        return $this;
    }
}
