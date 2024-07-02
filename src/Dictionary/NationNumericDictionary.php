<?php

namespace App\Dictionary;

class NationNumericDictionary
{
    const NATION_NUMERIC_LIST = [
        '0x00' => [
            'code' => '-',
            'country' => 'Unknown'
        ],
        '0x02' => [
            'code' => 'AL',
            'country' => 'Albania'
        ],
        '0x03' => [
            'code' => 'AND',
            'country' => 'Andorra'
        ],
        '0x04' => [
            'code' => 'ARM',
            'country' => 'Armenia'
        ],
        '0x05' => [
            'code' => 'AZ',
            'country' => 'Azerbaijan'
        ],
        '0x09' => [
            'code' => 'BY',
            'country' => 'Belarus'
        ],
        '0x06' => [
            'code' => 'B',
            'country' => 'Belgium'
        ],
        '0x08' => [
            'code' => 'BIH',
            'country' => 'Bosnia Herzegovina'
        ],
        '0x07' => [
            'code' => 'BG',
            'country' => 'Bulgaria'
        ],
        '0x19' => [
            'code' => 'HR',
            'country' => 'Croatia'
        ],
        '0x0B' => [
            'code' => 'CY',
            'country' => 'Cyprus'
        ],
        '0x0C' => [
            'code' => 'CZ',
            'country' => 'Czech Republic'
        ],
        '0x0E' => [
            'code' => 'DK',
            'country' => 'Denmark'
        ],
        '0x10' => [
            'code' => 'EST',
            'country' => 'Estonia'
        ],
        '0x12' => [
            'code' => 'FIN',
            'country' => 'Finland'
        ],
        '0x11' => [
            'code' => 'F',
            'country' => 'France'
        ],
        '0x16' => [
            'code' => 'GE',
            'country' => 'Georgia'
        ],
        '0x0D' => [
            'code' => 'D',
            'country' => 'Germany'
        ],
        '0x17' => [
            'code' => 'GR',
            'country' => 'Greece'
        ],
        '0x18' => [
            'code' => 'H',
            'country' => 'Hungary'
        ],
        '0x1C' => [
            'code' => 'IS',
            'country' => 'Iceland'
        ],
        '0x1B' => [
            'code' => 'IRL',
            'country' => 'Ireland'
        ],
        '0x1A' => [
            'code' => 'I',
            'country' => 'Italy'
        ],
        '0x1D' => [
            'code' => 'KZ',
            'country' => 'Kazakhstan'
        ],
        '0x38' => [
            'code' => 'KG',
            'country' => 'Kyrgyz Republic'
        ],
        '0x20' => [
            'code' => 'LV',
            'country' => 'Latvia'
        ],
        '0x13' => [
            'code' => 'FL',
            'country' => 'Liechtenstein'
        ],
        '0x1F' => [
            'code' => 'LT',
            'country' => 'Lithuania'
        ],
        '0x1E' => [
            'code' => 'L',
            'country' => 'Luxembourg'
        ],
        '0x21' => [
            'code' => 'M',
            'country' => 'Malta'
        ],
        '0x23' => [
            'code' => 'MD',
            'country' => 'Moldova'
        ],
        '0x22' => [
            'code' => 'MC',
            'country' => 'Monaco'
        ],
        '0x34' => [
            'code' => 'MNE',
            'country' => 'Montenegro'
        ],
        '0x26' => [
            'code' => 'NL',
            'country' => 'Netherlands'
        ],
        '0x24' => [
            'code' => 'MK',
            'country' => 'North Macedonia'
        ],
        '0x25' => [
            'code' => 'N',
            'country' => 'Norway'
        ],
        '0x28' => [
            'code' => 'PL',
            'country' => 'Poland'
        ],
        '0x27' => [
            'code' => 'P',
            'country' => 'Portugal'
        ],
        '0x29' => [
            'code' => 'RO',
            'country' => 'Romania'
        ],
        '0x2B' => [
            'code' => 'RUS',
            'country' => 'Russia'
        ],
        '0x2A' => [
            'code' => 'RSM',
            'country' => 'San Marino'
        ],
        '0x35' => [
            'code' => 'SRB',
            'country' => 'Serbia'
        ],
        '0x2D' => [
            'code' => 'SK',
            'country' => 'Slovakia'
        ],
        '0x2E' => [
            'code' => 'SLO',
            'country' => 'Slovenia'
        ],
        '0x0F' => [
            'code' => 'E',
            'country' => 'Spain'
        ],
        '0x2C' => [
            'code' => 'S',
            'country' => 'Sweden'
        ],
        '0x0A' => [
            'code' => 'CH',
            'country' => 'Switzerland'
        ],
        '0x37' => [
            'code' => 'TJ',
            'country' => 'Tajikistan'
        ],
        '0x30' => [
            'code' => 'TR',
            'country' => 'Turkey'
        ],
        '0x2F' => [
            'code' => 'TM',
            'country' => 'Turkmenistan'
        ],
        '0x31' => [
            'code' => 'UA',
            'country' => 'Ukraine'
        ],
        '0x15' => [
            'code' => 'UK',
            'country' => 'United Kingdom'
        ],
        '0x36' => [
            'code' => 'UZ',
            'country' => 'Uzbekistan'
        ],
        '0x32' => [
            'code' => 'V',
            'country' => 'Vatican City'
        ],
        '0x33' => [
            'code' => 'YU',
            'country' => 'Yugoslavia'
        ],
        '0x39' => [
            'code' => 'RFU',
            'country' => 'Reserved for Future Use'
        ],
        '0xFD' => [
            'code' => 'EC',
            'country' => 'European Community'
        ],
        '0xFE' => [
            'code' => 'EUR',
            'country' => 'Rest of Europe'
        ],
        '0xFF' => [
            'code' => 'WLD',
            'country' => 'Rest of the World'
        ]
    ];

    public static function getCountryCode(string $code): string
    {
        return self::NATION_NUMERIC_LIST[$code]['code'];
    }
}
