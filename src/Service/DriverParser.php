<?php

namespace App\Service;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DriverParser implements ParserInterface
{
    const FILE_PATH = '../src/Fixtures/data.json';

    public function parse(string $file): array
    {
        if(!file_exists(self::FILE_PATH)){
            throw new NotFoundHttpException('file not found');
        }

        $contentFile = file_get_contents(self::FILE_PATH);
        $data = json_decode($contentFile, true);

        $result = [];
        $months = [];

        $blocks = $data['Blocks'];

        foreach ($blocks as $block) {
            if ($block['Type'] === 'DriverCardDriverActivityData') {
                $records = $block['CardDriverActivity']['CardActivityDailyRecordRecords'];

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
                            'DRIVING' => 0
                        ]
                    ];

                    $dayActivities = $record['ActivityChangeInfoRecords'];

                    foreach ($dayActivities as $i => $currentActivity) {

                        switch ($currentActivity['Activity']) {
                            case "0x03":
                                $activityStatus = "DRIVING";
                                break;
                            case "0x02":
                                $activityStatus = "WORK";
                                break;
                            case "0x00":
                                $activityStatus = "REST";
                                break;
                        }

                        $nextActivity = $dayActivities[$i + 1] ?? null;
                        $currentTime = (int)$currentActivity['Minutes'];
                        $nextTime = $nextActivity ? (int)$nextActivity['Minutes'] : 24 * 60;

                        $timeSpent = $nextTime - $currentTime;

                        $dayData['activities'][$activityStatus] += $timeSpent;

                        if($currentTime <= 240 && $activityStatus === 'DRIVING'){
                            $nightDrive = true;
                        }
                    }

                    $dayData['nightDrive'] = $nightDrive;

                    $totalDistance = $months[$monthKey]['totalDistance'] ?? 0;
                    $months[$monthKey]['days'][] = $dayData;
                    $months[$monthKey]['totalDistance'] = $totalDistance + $dayData['distance'];
                    $months[$monthKey]['label'] = $date->format('F Y');

                }
            }elseif ($block['Type'] === 'DriverCardIdentification'){
                $result['driverInfo'] = [
                    'name' => $block['HolderIdentification']['CardHolderName']['HolderSurname'].' '.$block['HolderIdentification']['CardHolderName']['HolderFirstNames'],
                    'id' => $block['Identification']['CardNumber']['Full']
                ];
            }elseif ($block['Type'] === 'DriverCardEventsData'){
                $result['driverInfo']['carNumber'] = end($block['CardEventData']['CardEventRecordRecords'])['EventVehicleRegistration']['VehicleRegistrationNumber'];
            }
        }


        foreach ($months as &$monthObject) {
            $points = (int) $monthObject['totalDistance'] / 100;

            $totalWorkDays = 0;
            $totalDriveTime = 0;
            $totalWorkTime = 0;

            foreach ($monthObject['days'] as $day){
                if($day['nightDrive']){
                    $points += 20;
                }

                if($day['distance'] > 0){
                    $totalWorkDays++;

                    $totalWork = $day['activities']['DRIVING'] + $day['activities']['WORK'];

                    $totalWorkTime += $day['activities']['WORK'];
                    $totalDriveTime += $day['activities']['DRIVING'];

                    if($totalWork >= 780){
                        $points += 10;
                    }

                    if($day['activities']['DRIVING'] >= 480){
                        $points += 10;
                    }

                    if($day['activities']['DRIVING'] >= 540){
                        $points += 10;
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
            }

            $monthObject['totalPoints'] = $points;
            $monthObject['totalWorkTime'] = $totalWorkTime;
            $monthObject['totalDriveTime'] = $totalDriveTime;
        }

        $result['months'] = array_reverse($months);

        return $result;
    }
}
