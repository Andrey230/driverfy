<?php

namespace App\Service;

class DriverParser implements ParserInterface
{
    const SCRIPT_PATH = '../src/Fixtures/dddxml.ubuntu';

    const FULL_DAY_START = 1410;
    const FULL_DAY_END = 600;

    public function parse(string $file): array
    {
        $scriptPath = self::SCRIPT_PATH;
        $tempFile = $this->getTempFile($file);

        chmod($scriptPath, 0755);

        $command = escapeshellcmd("$scriptPath -j $tempFile");

        $output = [];
        $return_var = 0;
        exec($command . ' 2>&1', $output, $return_var);

        $json_string = implode("\n", $output);
        $data = json_decode($json_string, true);

        if(empty($data)){
            throw new \Exception('Invalid parse date');
        }

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
                            'DRIVING' => 0,
                            'AVAILABILITY' => 0,
                        ]
                    ];

                    $dayActivities = $record['ActivityChangeInfoRecords'];

                    $dayType = 'DAY_OFF';

                    $activeFirstActivity = $record['ActivityChangeInfoRecords'][0]['CardStatus'] == '0x00';

                    if(count($record['ActivityChangeInfoRecords']) === 1){
                        if($activeFirstActivity){
                            $dayType = 'FULL';
                        }
                    }else{
                        if(!$activeFirstActivity){
                            $startOfWork = $record['ActivityChangeInfoRecords'][1]['Minutes'];

                            if($startOfWork >= self::FULL_DAY_START){
                                $dayType = 'HALF';
                            }else{
                                $dayType = 'FULL';
                            }
                        }else{
                            $lastActivity = end($record['ActivityChangeInfoRecords']);
                            $dayIsEnd = $lastActivity['CardStatus'] == '0x01';

                            if($dayIsEnd){
                                $dayType = $lastActivity['Minutes'] <= self::FULL_DAY_END ? 'HALF' : 'FULL';
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
                            'time' => $this->convertMinutesToTime($currentActivity['Minutes'])
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
            }elseif ($block['Type'] === 'DriverCardCurrentUsage'){
                $result['driverInfo']['carNumber'] = $block['CardCurrentUse']['SessionOpenVehicle']['VehicleRegistrationNumber'];
            }
        }


        foreach ($months as &$monthObject) {
            $points = (int) $monthObject['totalDistance'] / 100;

            $totalWorkDays = 0;
            $totalDriveTime = 0;
            $totalWorkTime = 0;
            $countEightPlus = 0;
            $countNinePlus = 0;

            foreach ($monthObject['days'] as $day){

                switch ($day['dayType']){
                    case "FULL":
                        $totalWorkDays++;
                        break;
                    case "HALF":
                        $totalWorkDays+= 0.5;
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
        }

        $result['months'] = array_reverse($months);

        return $result;
    }

    private function getTempFile(string $file): string
    {
        $fileData = base64_decode($file);

        $tempFileName = tempnam(sys_get_temp_dir(), 'test_') . '.ddd';

        file_put_contents($tempFileName, $fileData);

        if(!file_exists($tempFileName)){
            throw new \Exception('file not saved');
        }

        return $tempFileName;
    }

    private function convertMinutesToTime($totalMinutes): string
    {
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        return sprintf('%02d:%02d', $hours, $minutes);
    }
}
