<?php

namespace App\Command;

interface CommandBusInterface
{
    public function execute(CommandInterface $command): mixed;
}
