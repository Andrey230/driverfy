<?php

namespace App\Service;

interface ParserInterface
{
    public function parse(string $file, int $fullDayStart, int $fullDayEnd): array;
}
