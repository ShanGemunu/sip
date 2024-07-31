<?php
require_once __DIR__ . '/Connecter.php';
require_once __DIR__ . '/Queries.php';

$connecter = Connecter::getConneterInstance();
$conn = $connecter->getDbConnection();


try {

    $queries = new Queries();
    
    $queries->alterTableReportHourlyCountryCarrierWiseTraffic($conn);
    $queries->alterReportHourlyCountryNetworkCarrierWiseTraffic($conn);
    $queries->insertIntoMsrn($conn);
    $queries->alterTableCdrCall($conn);
    $queries->createTableSystemParameters($conn);
    $queries->alterTableSystemParamOne($conn);
    $queries->alterTableSystemParmTwo($conn);
    $queries->insertIntoSystemParamOne($conn);
    $queries->insertIntoSystemParamTwo($conn);
    $queries->insertIntoPermissions($connn);
    $queries->addValuesForCdrCall($conn);


} catch (Exception $e) {
    $filePath = __DIR__ . '/logs.csv';
    $fileHandle = fopen($filePath, 'a');

    if ($fileHandle) {
        date_default_timezone_set('Asia/Colombo');
        $log = [date('Y-m-d , H:i:s'), "exception", $e->getMessage(), $e->getLine(), $e->getFile()];

        // Write the data to the CSV file
        fputcsv($fileHandle, $log);

        // Close the file
        fclose($fileHandle);
    } else {
        echo "Failed to open file!";
    }
}



