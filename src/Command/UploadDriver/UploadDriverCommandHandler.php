<?php

namespace App\Command\UploadDriver;

use App\Command\CommandHandlerInterface;
use App\Entity\Driver;
use App\Service\DriverCreatorInterface;


class UploadDriverCommandHandler implements CommandHandlerInterface
{
    public function __construct(private readonly DriverCreatorInterface $driverCreator)
    {

    }

    public function __invoke(UploadDriverCommand $uploadDriverCommand): array
    {
        $driver = $this->driverCreator->create($uploadDriverCommand->file);

        return $driver->toArray();
    }
}
