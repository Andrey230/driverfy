<?php

namespace App\Query\UsersByMonth;

use App\Entity\MonthActivity;
use App\Query\QueryHandlerInterface;
use App\Repository\MonthActivityRepository;
use Symfony\Bundle\SecurityBundle\Security;

class UsersByMonthHandler implements QueryHandlerInterface
{
    public function __construct(
        private MonthActivityRepository $monthActivityRepository,
        private Security $security,
    )
    {
    }

    public function __invoke(UsersByMonthQuery $usersByMonthQuery)
    {
        /** @var MonthActivity[] $result */
        $result = $this->monthActivityRepository->findDriversByMonthAndUserId(
            $usersByMonthQuery->month,
            $this->security->getUser()->getId()
        );



        return array_map(function ($monthActivity) {
            return $monthActivity->toArray();
        }, $result);
    }
}
