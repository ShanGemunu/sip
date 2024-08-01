<?php

namespace App\Log;

class Logger
{
    function createLog(string $message, int $line, string $file):void
    {
        $filePath = __DIR__ . '/logs/logs.csv';
        $fileHandle = fopen($filePath, 'a');

        if ($fileHandle) {
            date_default_timezone_set('Asia/Colombo');
            $log = [date('Y-m-d H:i:s'), "exception", $message, $line, $file];

            // Write the data to the CSV file
            fputcsv($fileHandle, $log);

            // Close the file
            fclose($fileHandle);
        } else {
            exit;
        }
    }

}

