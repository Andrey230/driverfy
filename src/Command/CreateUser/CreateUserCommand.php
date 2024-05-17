<?php

namespace App\Command\CreateUser;

use App\Command\CommandInterface;

class CreateUserCommand implements CommandInterface
{
    public function __construct(
        public string $email,
        public string $password,
        public string $name,
    )
    {
    }
}
