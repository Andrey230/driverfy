<?php

namespace App\Entity;

use App\Repository\DriverRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DriverRepository::class)]
class Driver
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $carNumber = null;

    #[ORM\Column(length: 255)]
    private ?string $cardId = null;

    #[ORM\ManyToOne(inversedBy: 'drivers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userId = null;

    /**
     * @var Collection<int, MonthActivity>
     */
    #[ORM\OneToMany(targetEntity: MonthActivity::class, mappedBy: 'driver_id', orphanRemoval: true)]
    private Collection $monthActivities;

    public function __construct()
    {
        $this->monthActivities = new ArrayCollection();
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

    public function getCarNumber(): ?string
    {
        return $this->carNumber;
    }

    public function setCarNumber(string $carNumber): static
    {
        $this->carNumber = $carNumber;

        return $this;
    }

    public function getCardId(): ?string
    {
        return $this->cardId;
    }

    public function setCardId(string $cardId): static
    {
        $this->cardId = $cardId;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->userId;
    }

    public function setUserId(?User $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'carNumber' => $this->getCarNumber(),
            'cardId' => $this->getCardId(),
            //'activities' => $activities,
            //'user' => $this->getUserId()->getId(),
        ];
    }

    /**
     * @return Collection<int, MonthActivity>
     */
    public function getMonthActivities(): Collection
    {
        return $this->monthActivities;
    }

    public function addMonthActivity(MonthActivity $monthActivity): static
    {
        if (!$this->monthActivities->contains($monthActivity)) {
            $this->monthActivities->add($monthActivity);
            $monthActivity->setDriverId($this);
        }

        return $this;
    }

    public function removeMonthActivity(MonthActivity $monthActivity): static
    {
        if ($this->monthActivities->removeElement($monthActivity)) {
            // set the owning side to null (unless already changed)
            if ($monthActivity->getDriverId() === $this) {
                $monthActivity->setDriverId(null);
            }
        }

        return $this;
    }
}
