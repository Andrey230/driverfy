<?php

namespace App\Service\MonthActivityCreator;

use App\DTO\MonthActivityDTO;
use App\Entity\Driver;
use App\Entity\MonthActivity;
use App\Factory\MonthActivityFactory;
use App\Repository\MonthActivityRepository;
use Doctrine\ORM\EntityManagerInterface;

class MonthActivityCreator implements MonthActivityCreatorInterface
{
    public function __construct(
        private readonly MonthActivityFactory    $activityFactory,
        private readonly EntityManagerInterface  $entityManager,
        private readonly MonthActivityRepository $monthActivityRepository,
    )
    {
    }

    public function create(Driver $driver, string $month, array $activities): MonthActivity
    {
        $dto = new MonthActivityDTO(
            $driver,
            $month,
            $activities['totalPoints'],
            $activities['totalDistance'],
            $activities['averageDistance'],
            $activities['totalWorkDays'],
            $activities['totalDriveTime'],
            $activities['totalWorkTime'],
            $activities['countEightPlus'],
            $activities['countNinePlus'],
            $activities['days'],
        );

        $record = $this->monthActivityRepository->findByMonth($driver, $month);

        if(!$record){
            $record = $this->activityFactory->create($dto);
            $this->entityManager->persist($record);
        }else{
            $record = $this->activityFactory->update($record, $dto);
        }

        return $record;
    }
}
