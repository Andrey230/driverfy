<?php

namespace App\Service;

interface ParserInterface
{
    public function parse(string $file): array;
}
