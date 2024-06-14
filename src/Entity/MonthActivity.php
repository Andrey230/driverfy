<?php

namespace App\Entity;

use App\Repository\MonthActivityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MonthActivityRepository::class)]
class MonthActivity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'monthActivities')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Driver $driver_id = null;

    #[ORM\Column(length: 255)]
    private ?string $month = null;

    #[ORM\Column]
    private ?int $total_points = null;

    #[ORM\Column]
    private ?int $total_distance = null;

    #[ORM\Column]
    private ?int $average_distance = null;

    #[ORM\Column]
    private ?int $total_work_days = null;

    #[ORM\Column]
    private ?int $total_drive = null;

    #[ORM\Column]
    private ?int $total_work = null;

    #[ORM\Column]
    private array $days = [];

    #[ORM\Column]
    private ?int $count_eight = null;

    #[ORM\Column]
    private ?int $count_nine = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDriverId(): ?Driver
    {
        return $this->driver_id;
    }

    public function setDriverId(?Driver $driver_id): static
    {
        $this->driver_id = $driver_id;

        return $this;
    }

    public function getMonth(): ?string
    {
        return $this->month;
    }

    public function setMonth(string $month): static
    {
        $this->month = $month;

        return $this;
    }

    public function getTotalPoints(): ?int
    {
        return $this->total_points;
    }

    public function setTotalPoints(int $total_points): static
    {
        $this->total_points = $total_points;

        return $this;
    }

    public function getTotalDistance(): ?int
    {
        return $this->total_distance;
    }

    public function setTotalDistance(int $total_distance): static
    {
        $this->total_distance = $total_distance;

        return $this;
    }

    public function getAverageDistance(): ?int
    {
        return $this->average_distance;
    }

    public function setAverageDistance(int $average_distance): static
    {
        $this->average_distance = $average_distance;

        return $this;
    }

    public function getTotalWorkDays(): ?int
    {
        return $this->total_work_days;
    }

    public function setTotalWorkDays(int $total_work_days): static
    {
        $this->total_work_days = $total_work_days;

        return $this;
    }

    public function getTotalDrive(): ?int
    {
        return $this->total_drive;
    }

    public function setTotalDrive(int $total_drive): static
    {
        $this->total_drive = $total_drive;

        return $this;
    }

    public function getTotalWork(): ?int
    {
        return $this->total_work;
    }

    public function setTotalWork(int $total_work): static
    {
        $this->total_work = $total_work;

        return $this;
    }

    public function getDays(): array
    {
        return $this->days;
    }

    public function setDays(array $days): static
    {
        $this->days = $days;

        return $this;
    }

    public function toArray(bool $withDriver = false): array
    {
        $data = [
            'month' => $this->getMonth(),
            'totalPoints' => $this->getTotalPoints(),
            'totalDistance' => $this->getTotalDistance(),
            'averageDistance' => $this->getAverageDistance(),
            'totalWorkDays' => $this->getTotalWorkDays(),
            'totalDrive' => $this->getTotalDrive(),
            'totalWork' => $this->getTotalWork(),
            'countEight' => $this->getCountEight(),
            'countNine' => $this->getCountNine(),
            'days' => $this->getDays(),
        ];

        if($withDriver){
            $data['driver'] = $this->getDriverId()->toArray();
        }

        return $data;
    }

    public function getCountEight(): ?int
    {
        return $this->count_eight;
    }

    public function setCountEight(int $count_eight): static
    {
        $this->count_eight = $count_eight;

        return $this;
    }

    public function getCountNine(): ?int
    {
        return $this->count_nine;
    }

    public function setCountNine(int $count_nine): static
    {
        $this->count_nine = $count_nine;

        return $this;
    }
}
