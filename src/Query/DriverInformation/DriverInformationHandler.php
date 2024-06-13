<?php

namespace App\Query\DriverInformation;

use App\Entity\Driver;
use App\Query\QueryHandlerInterface;
use App\Repository\DriverRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DriverInformationHandler implements QueryHandlerInterface
{
    public function __construct(private DriverRepository $driverRepository)
    {
    }


    public function __invoke(DriverInformationQuery $driverInformationQuery): array
    {
        /** @var Driver $driver */
        $driver = $this->driverRepository->find($driverInformationQuery->id);

        if(empty($driver)){
            throw new NotFoundHttpException('Driver not found');
        }

        return $driver->toArray(true);
    }
}
