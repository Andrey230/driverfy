<?php

namespace App\Command\UploadDriver;

use App\Command\CommandHandlerInterface;
use App\Entity\Driver;
use App\Service\DriverCreatorInterface;


class UploadDriverCommandHandler implements CommandHandlerInterface
{
    public function __construct(private DriverCreatorInterface $driverCreator)
    {

    }

    public function __invoke(UploadDriverCommand $uploadDriverCommand): array
    {
        $driver = $this->driverCreator->create($uploadDriverCommand->file);

        return [
            'name' => $driver->getName(),
            'carNumber' => $driver->getCarNumber(),
            'cardId' => $driver->getId(),
            'user' => $driver->getUserId()->getId(),
        ];
    }
}
