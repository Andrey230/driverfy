<?php

namespace App\Command\UploadDriver;

use App\Command\CommandInterface;

class UploadDriverCommand implements CommandInterface
{
    public function __construct(public string $file)
    {
    }
}
