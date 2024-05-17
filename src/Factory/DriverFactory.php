<?php

namespace App\Factory;

use App\Entity\Driver;
use App\Entity\User;

class DriverFactory
{
    public function createDriver(
        string $name,
        string $carNumber,
        string $cardId,
        User $user,
    ): Driver
    {
        $driver = new Driver();
        $driver->setName($name);
        $driver->setCarNumber($carNumber);
        $driver->setCardId($cardId);
        $driver->setUserId($user);

        return $driver;
    }
}
