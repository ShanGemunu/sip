<?php

namespace app\log;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use app\datetime\DateTime;

class Log
{
    private static $logger = null;
    private static $id;

    public static function config()
    {
        self::$id = uniqid(DateTime::getCurrentDateTime('YmdHis'));
    }

    public static function getLogger()
    {
        if (self::$logger === null) {
            self::$logger = new Logger('app');

            // Define a custom date format and output format
            $dateFormat = 'Y-m-d H:i:s';
            $outputFormat = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";

            // Create a formatter with the custom formats
            $formatter = new LineFormatter($outputFormat, $dateFormat);

            // Create a handler with the custom formatter
            $streamHandler = new StreamHandler(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '/logs/logs/app.log');
            $streamHandler->setFormatter($formatter);

            self::$logger->pushHandler($streamHandler);
        }

        return self::$logger;
    }

    public static function logInfo(string $controllerName, string $functionName, string $stepDescription, string $stepStatus, string $data)
    {
        self::getLogger()->info(self::$id . ", $controllerName, $functionName, $stepDescription, $stepStatus, $data");
    }

    public static function logError(string $controllerName, string $functionName, string $stepDescription, string $stepStatus, string $data)
    {
        self::getLogger()->error(self::$id . ", $controllerName, $functionName, $stepDescription, $stepStatus, $data");
    }
}












