<?php

namespace App\Service\MonthActivityCreator;

use App\Entity\Driver;
use App\Entity\MonthActivity;

interface MonthActivityCreatorInterface
{
    public function create(Driver $driver, string $month, array $activities): MonthActivity;
}
