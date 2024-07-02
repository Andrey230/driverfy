<?php

namespace App\Service\Parser;

use App\Dictionary\NationNumericDictionary;

abstract class AbstractParser implements ParserInterface
{
    private array $countries;
    private array $countriesRange;

    public function __construct(
        public array $data,
        public int $fullDayStart,
        public int $fullDayEnd
    )
    {
        $this->countries = $this->getCountries();
        $this->countriesRange = $this->getCountriesRange();
    }

    public function parse(): array
    {
        $result = [];

        $months = $this->getDriverActivities();
        $this->dataAnalysis($months);

        $result['driverInfo'] = $this->getDriverIdentification();
        $result['driverInfo']['carNumber'] = $this->getCarNumber();

        $result['months'] = array_reverse($months);

        return $result;
    }

    private function getCountryByDate(int $date): ?string
    {
        $country = null;
        foreach ($this->countriesRange as $record){
            if($date >= $record['start'] && $date < $record['end']){
                $country = $record['country'];
            }
        }

        return $country;
    }


    private function getCountriesRange(): array
    {
        $visits = [];

        $countryBlock = $this->getBlockData('DriverCardPlaces');

        $countryRecords = $countryBlock['CardPlaceDailyWorkPeriod']['PlaceRecordRecords'] ?? [];

        $firstRecord = array_shift($countryRecords);

        $currentCountry = NationNumericDictionary::getCountryCode($firstRecord['DailyWorkPeriodCountry']);
        $currentTime = strtotime($firstRecord['EntryTime']);

        foreach ($countryRecords as $record) {

            $recordCountry = NationNumericDictionary::getCountryCode($record['DailyWorkPeriodCountry']);

            if($recordCountry == $currentCountry){
                continue;
            }

            $closeTime = strtotime($record['EntryTime']);

            $visits[] = [
                'country' => $currentCountry,
                'start' => $currentTime,
                'end' => $closeTime - 1,
            ];

            $currentCountry = $recordCountry;
            $currentTime = $closeTime;
        }

        return $visits;
    }


    protected function convertMinutesToTime($totalMinutes): string
    {
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    protected function getCountries(): array
    {
        $countryBlock = $this->getBlockData('DriverCardPlaces');

        $countryRecords = $countryBlock['CardPlaceDailyWorkPeriod']['PlaceRecordRecords'] ?? [];

        if(empty($countryRecords)){
            throw new \Exception('Country block is empty');
        }

        $countries = [];

        foreach ($countryRecords as $record){
            $date = substr($record['EntryTime'], 0, 10);
            $country = NationNumericDictionary::NATION_NUMERIC_LIST[$record['DailyWorkPeriodCountry']];

            if (!isset($countries[$date])) {
                $countries[$date] = [$country['code']];
            }else{
                if(!in_array($country['code'] ,$countries[$date])){
                    $countries[$date][] = $country['code'];
                }
            }
        }

        return $countries;
    }

    protected function getDriverActivities(): array
    {
        $activitiesBlock = $this->getBlockData('DriverCardDriverActivityData');

        $records = $activitiesBlock['CardDriverActivity']['CardActivityDailyRecordRecords'] ?? [];

        if(empty($records)){
            throw new \Exception('empty block with driver activities');
        }

        $months = [];

        foreach ($records as $record) {
            $date = new \DateTime($record['ActivityRecordDate']);
            $monthKey = $date->format('Y-m');
            $nightDrive = false;

            $dayData = [
                'date' => $date,
                'distance' => (float)$record['ActivityDayDistance'],
                'activities' => [
                    'REST' => 0,
                    'WORK' => 0,
                    'DRIVING' => 0,
                    'AVAILABILITY' => 0,
                ]
            ];

            $dayActivities = $record['ActivityChangeInfoRecords'];

            $dayType = 'DAY_OFF';

            $activeFirstActivity = $record['ActivityChangeInfoRecords'][0]['CardStatus'] == '0x00';

            $countries = [];

            if(count($record['ActivityChangeInfoRecords']) === 1){
                if($activeFirstActivity){
                    $dayType = 'FULL';
                }else{

                    $currentCountry = $this->getCountryByDate($date->getTimestamp());

                    if($currentCountry && $currentCountry !== 'PL'){
                        $dayType = 'UNKNOWN';
                    }

                    $countries[] = $currentCountry ?? [];
                }
            }else{
                if(!$activeFirstActivity){
                    $startOfWork = $record['ActivityChangeInfoRecords'][1]['Minutes'];

                    if($startOfWork >= $this->fullDayStart){
                        $dayType = 'HALF';
                    }else{
                        $dayType = 'FULL';
                    }
                }else{
                    $lastActivity = end($record['ActivityChangeInfoRecords']);
                    $dayIsEnd = $lastActivity['CardStatus'] == '0x01';

                    if($dayIsEnd){
                        $dayType = $lastActivity['Minutes'] <= $this->fullDayEnd ? 'HALF' : 'FULL';
                    }else{
                        $dayType = 'FULL';
                    }
                }
            }

            $dayData['dayType'] = $dayType;

            $refactorActivities = [];


            foreach ($dayActivities as $i => $currentActivity) {

                switch ($currentActivity['Activity']) {
                    case "0x03":
                        $activityStatus = "DRIVING";
                        break;
                    case "0x02":
                        $activityStatus = "WORK";
                        break;
                    case "0x01":
                        $activityStatus = "AVAILABILITY";
                        break;
                    case "0x00":
                        $activityStatus = "REST";
                        break;
                }


                $refactorActivities[] = [
                    'activity' => $activityStatus,
                    'cardStatus' => $currentActivity['CardStatus'] == '0x01' ? 'inactive' : 'active',
                    'time' => $this->convertMinutesToTime($currentActivity['Minutes']),
                    'minutes' => $currentActivity['Minutes'],
                ];

                $nextActivity = $dayActivities[$i + 1] ?? null;
                $currentTime = (int)$currentActivity['Minutes'];
                $nextTime = $nextActivity ? (int)$nextActivity['Minutes'] : 24 * 60;

                $timeSpent = $nextTime - $currentTime;

                $dayData['activities'][$activityStatus] += $timeSpent;

                if($currentTime <= 240 && $activityStatus === 'DRIVING'){
                    $nightDrive = true;
                }
            }

            $dayData['refactorActivities'] = $refactorActivities;
            $dayData['nightDrive'] = $nightDrive;
            $dayData['countries'] = !empty($countries) ? $countries : $this->countries[substr($record['ActivityRecordDate'], 0, 10)] ?? [];

            $totalDistance = $months[$monthKey]['totalDistance'] ?? 0;
            $months[$monthKey]['days'][] = $dayData;
            $months[$monthKey]['totalDistance'] = $totalDistance + $dayData['distance'];
            $months[$monthKey]['label'] = $date->format('F Y');

        }

        return $months;
    }

    protected function getCarNumber(): string
    {
        $carBlock = $this->getBlockData('DriverCardVehiclesUsed');

        return end($carBlock['CardVehiclesUsed']['CardVehicleRecordRecords'])['VehicleRegistration']['VehicleRegistrationNumber'];
    }

    protected function getDriverIdentification(): array
    {
        $idBlock = $this->getBlockData('DriverCardIdentification');

        return [
            'name' => $idBlock['HolderIdentification']['CardHolderName']['HolderSurname'].' '.$idBlock['HolderIdentification']['CardHolderName']['HolderFirstNames'],
            'id' => $idBlock['Identification']['CardNumber']['Full']
        ];
    }

    protected function getBlockData(string $key): array
    {
        foreach ($this->data['Blocks'] as $block){
            if($block['Type'] === $key){
                return $block;
                break;
            }
        }

        throw new \Exception('Block not found by '.$key);
    }

    protected function dataAnalysis(&$months): void
    {
        foreach ($months as &$monthObject) {
            $points = (int) $monthObject['totalDistance'] / 100;

            $totalWorkDays = 0;
            $totalDriveTime = 0;
            $totalWorkTime = 0;
            $countEightPlus = 0;
            $countNinePlus = 0;
            $totalUnknownDays = 0;

            foreach ($monthObject['days'] as $day){

                switch ($day['dayType']){
                    case "FULL":
                        $totalWorkDays++;
                        break;
                    case "HALF":
                        $totalWorkDays+= 0.5;
                        break;
                    case "UNKNOWN":
                        $totalUnknownDays++;
                        break;
                }


                if($day['nightDrive']){
                    $points += 20;
                }

                if($day['distance'] > 0){

                    $totalWork = $day['activities']['DRIVING'] + $day['activities']['WORK'];

                    $totalWorkTime += $day['activities']['WORK'];
                    $totalDriveTime += $day['activities']['DRIVING'];

                    if($totalWork >= 780){
                        $points += 10;
                    }

                    if($day['activities']['DRIVING'] >= 540){
                        $points += 10;
                        $countNinePlus++;
                    }elseif ($day['activities']['DRIVING'] >= 480){
                        $points += 10;
                        $countEightPlus++;
                    }
                }
            }

            if($totalWorkDays > 22){
                $points += ($totalWorkDays - 22) * 20;
            }

            $monthObject['totalWorkDays'] = $totalWorkDays;

            if ($totalWorkDays > 0) {
                $avgDistance = $monthObject['totalDistance'] / $totalWorkDays;
                $monthObject['averageDistance'] = $avgDistance;
                $points += (int) $avgDistance / 100;
            }else{
                $monthObject['averageDistance'] = 0;
            }

            $monthObject['totalPoints'] = $points;
            $monthObject['totalWorkTime'] = $totalWorkTime;
            $monthObject['totalDriveTime'] = $totalDriveTime;
            $monthObject['countEightPlus'] = $countEightPlus;
            $monthObject['countNinePlus'] = $countNinePlus;
            $monthObject['totalUnknownDays'] = $totalUnknownDays;
        }
    }
}
