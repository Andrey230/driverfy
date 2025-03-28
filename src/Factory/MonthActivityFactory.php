<?php

namespace App\Factory;

use App\DTO\MonthActivityDTO;
use App\Entity\MonthActivity;

class MonthActivityFactory
{
    public function create(MonthActivityDTO $activityDTO): MonthActivity
    {
        $monthActivity = new MonthActivity();
        $monthActivity->setDriverId($activityDTO->driver);
        $monthActivity->setTotalPoints($activityDTO->totalPoints);
        $monthActivity->setTotalDistance($activityDTO->totalDistance);
        $monthActivity->setAverageDistance($activityDTO->averageDistance);
        $monthActivity->setTotalDrive($activityDTO->totalDrive);
        $monthActivity->setTotalWork($activityDTO->totalWork);
        $monthActivity->setTotalWorkDays($activityDTO->totalWorkDays);
        $monthActivity->setDays($activityDTO->days);
        $monthActivity->setMonth($activityDTO->month);
        $monthActivity->setCountEight($activityDTO->countEight);
        $monthActivity->setCountNine($activityDTO->countNine);
        $monthActivity->setTotalUnknownDays($activityDTO->totalUnknownDays);

        return $monthActivity;
    }

    public function update(MonthActivity $monthActivity, MonthActivityDTO $activityDTO): MonthActivity
    {
        $monthActivity->setTotalPoints($activityDTO->totalPoints);
        $monthActivity->setTotalDistance($activityDTO->totalDistance);
        $monthActivity->setAverageDistance($activityDTO->averageDistance);
        $monthActivity->setTotalDrive($activityDTO->totalDrive);
        $monthActivity->setTotalWork($activityDTO->totalWork);
        $monthActivity->setTotalWorkDays($activityDTO->totalWorkDays);
        $monthActivity->setTotalUnknownDays($activityDTO->totalUnknownDays);
        $monthActivity->setDays($activityDTO->days);
        $monthActivity->setCountEight($activityDTO->countEight);
        $monthActivity->setCountNine($activityDTO->countNine);

        return $monthActivity;
    }
}
