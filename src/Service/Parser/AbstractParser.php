<?php

namespace App\Service\Parser;

abstract class AbstractParser implements ParserInterface
{
    public function __construct(
        public array $data,
        public int $fullDayStart,
        public int $fullDayEnd
    )
    {
    }

    protected function convertMinutesToTime($totalMinutes): string
    {
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        return sprintf('%02d:%02d', $hours, $minutes);
    }
}
