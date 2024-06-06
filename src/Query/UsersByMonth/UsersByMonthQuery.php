<?php

namespace App\Query\UsersByMonth;

use App\Query\QueryInterface;

class UsersByMonthQuery implements QueryInterface
{
    public function __construct(public string $month)
    {
    }
}
