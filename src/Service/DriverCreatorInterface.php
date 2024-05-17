<?php

namespace App\Service;

use App\Entity\Driver;

interface DriverCreatorInterface
{
    public function create(string $file): Driver;
}
