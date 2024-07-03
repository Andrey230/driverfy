<?php

namespace App\Service;
use App\Service\Parser\ParserVersion0;
use App\Service\Parser\ParserVersion1;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DriverParser implements ParserInterface
{
    const SCRIPT_PATH = '../src/Fixtures/dddxml.ubuntu';

    public function parse(string $file, int $fullDayStart, int $fullDayEnd): array
    {
        $scriptPath = self::SCRIPT_PATH;
        $tempFile = $this->getTempFile($file);

        chmod($scriptPath, 0755);

        $command = escapeshellcmd("$scriptPath -j $tempFile");

        $output = [];
        $return_var = 0;
        exec($command, $output, $return_var);

        $json_string = implode("\n", $output);
        $data = json_decode($json_string, true);

        if(empty($data)){
            throw new \Exception('Invalid parse date');
        }

        switch ($data['Format']){
            case "0x00":
                $parser = new ParserVersion0($data, $fullDayStart, $fullDayEnd);
                break;
            case "0x01":
                $parser = new ParserVersion1($data, $fullDayStart, $fullDayEnd);
                break;
            default:
                throw new NotFoundHttpException('parser not found by version');
        }

        return $parser->parse();

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
}
