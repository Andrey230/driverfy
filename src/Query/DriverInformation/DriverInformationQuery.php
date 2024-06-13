<?php

namespace App\Query\DriverInformation;

use App\Query\QueryInterface;

class DriverInformationQuery implements QueryInterface
{
    public function __construct(public string $id)
    {
    }
}
