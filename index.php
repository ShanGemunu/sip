<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Log\Logger;
use App\Models\Queries;

try {
    $queries = new Queries();
   
    // $queries->alterTableReportHourlyCountryCarrierWiseTraffic();
    // $queries->alterReportHourlyCountryNetworkCarrierWiseTraffic();
    // $queries->insertIntoMsrn();
    // $queries->alterTableCdrCall();
    // $queries->createTableSystemParameters();
    // $queries->insertIntoSystemParamOne();
    // $queries->insertIntoSystemParamTwo();
    // $queries->insertIntoPermissions();
    // $queries->addValuesForCdrCall();

} catch (Exception $e) {
    $logger = new Logger();
    $logger->createLog($e->getMessage(), $e->getLine(), $e->getFile());
}

