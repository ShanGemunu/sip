<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Log\Logger;
use App\Models\Queries;

try {
    $queries = new Queries();
   
    $queries->alterTableReportHourlyCountryCarrierWiseTraffic();
    $queries->alterReportHourlyCountryNetworkCarrierWiseTraffic();
    $queries->createMsrnRangesTable();
    $queries->insertIntoMsrn();
    $queries->alterTableCdrCall();
    $queries->createTableSystemParameters();
    $queries->insertIntoSystemParam();
    $queries->insertIntoPermissions();
    $queries->addValuesForCdrCall();

} catch (Exception $e) {
    $logger = new Logger();
    $logger->createExceptionLog($e->getMessage(), $e->getLine(), $e->getFile());
}

