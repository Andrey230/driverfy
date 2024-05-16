<?php

namespace App\Query;

interface QueryBusInterface
{
    public function execute(QueryInterface $query): mixed;
}
