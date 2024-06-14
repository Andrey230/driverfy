<?php

namespace App\DTO;

use App\Entity\Driver;

class MonthActivityDTO
{
    public function __construct(
        public Driver $driver,
        public string $month,
        public int $totalPoints,
        public int $totalDistance,
        public int $averageDistance,
        public int $totalWorkDays,
        public int $totalDrive,
        public int $totalWork,
        public int $countEight,
        public int $countNine,
        public array $days,
    )
    {
    }
}
