<?php
require_once __DIR__ . '/vendor/autoload.php';
use Dotenv\Dotenv;
use app\datetime\DateTimeClass;
use app\log\Log;
use app\logic\Logic;
use app\database\DbConnection;
use app\models\Queries;
use app\exceptions\PrepareQueryFailedException;
use app\exceptions\QueryExecuteFailedException;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

new DateTimeClass('Asia/Colombo');
Log::config();
DbConnection::getDbConnectionInstance([
    'SERVER' => $_ENV['SERVER'],
    'USER_NAME' => $_ENV['USER_NAME'],
    'PASSWORD' => $_ENV['PASSWORD'],
    'DB_NAME' => $_ENV['DB_NAME'],
    'PORT' => $_ENV['PORT']
]);
$queries = new Queries($_ENV['DB_NAME'], $_ENV['BATCH_SIZE'], $_ENV['SLEEP_TIME_SEC']);
$currentDate = DateTimeClass::getCurrentDateTime("Y-m-d");
if ($argc === 2) {
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $argv[1])) {
        $currentDate = $argv[1];
    }
}

$tables = [
    'activity_log' => [["created_at", "updated_at"], "id"],
    'alarms_carrier_history' => [["date_time"], "id"],
    'alarms_country_history' => [["date_time"], "id"],
    'alarms_network_history' => [["date_time"], "id"],
    'cdr_dialogs' => [["time", "ringing", "answered", "end"], "id"],
    'cr_acd_mou' => [["report_date"], "id"],
    'cr_cc_asr_mou' => [["report_date"], "id"],
    'ct_mou_top_dest' => [["date"], "id"],
    'ct_mou_variance' => [["date"], "id"],
    'flash_call_labels' => [["update_time"], "cli"],
    'idd_hourly_carrier_level_quality_score' => [["date_time"]],
    'idd_hourly_country_level_quality_score' => [["date_time"]],
    'idd_hourly_network_level_quality_score' => [["date_time"]],
    'idd_hourly_stats' => [["date_"]],
    'idd_hourly_trend_outlier_carrier_level' => [["date_time"]],
    'idd_hourly_trend_outlier_country_level' => [["date_time"]],
    'idd_hourly_trend_outlier_network_level' => [["date_time"]],
    'idd_outlier_alarm_history' => [["created_at"], "id"],
    'idd_outlier_hourly' => [["running_time"]],
    'idd_outlier_ten_min' => [["running_time"]],
    'nw_cc_top_dest' => [["date"], "id"],
    'report_daily_72_78_outgoing_traffic' => [["date"], "date"],
    'report_daily_country_carrier_wise_traffic' => [["date"]],
    'report_daily_country_carrier_wise_traffic_quality' => [["date"], "id"],
    'report_hourly_country_carrier_wise_traffic' => [["hour"], "id"],
    'report_hourly_country_carrier_wise_traffic_test' => [["hour"], "id"],
    'report_hourly_country_network_carrier_wise_traffic' => [["hour"], "id"],
    'total_mou_on_wk_day' => [["report_date"], "id"]
];

try {
    // latest cdr_call table
    $latestCdrCallTable = $queries->getLatestTableNames("cdr_call_", "_bkp", ['start' => 10, 'length' => 8], limit: 1);
    // latest cdr_sip table
    $latestCdrSipTable = $queries->getLatestTableNames("cdr_sip_", "_bkp", ['start' => 9, 'length' => 8], 1);

    $dateDiffGolbal;

    if ($latestCdrCallTable) {
        $dateLatestCdrCallTable = substr($latestCdrCallTable[0]['table_name'], 9);
        $dateDiffData = Logic::findDateDifferenceData($currentDate, $dateLatestCdrCallTable);

        $dateDiffGolbal = $dateDiffData;
        Log::logInfo("no controller, but in index file", "no function", "set value to dateDiffGloabal variable", "success", "date difference - {$dateDiffGolbal['diff']} ; direction - {$dateDiffGolbal['direction']}");

        if ($dateDiffData['diff'] > 0 && $dateDiffData['diff'] < $_ENV['TABLE_LIMIT'] && $dateDiffData['direction'] === 1) {
            $latestCdrCallTables = $queries->getLatestTableNames("cdr_call_", "_bkp", ['start' => 10, 'length' => 8], limit: $dateDiffData['diff']);
        } elseif ($dateDiffData['diff'] > 0 && $dateDiffData['diff'] >= $_ENV['TABLE_LIMIT'] && $dateDiffData['direction'] === 1) {
            $latestCdrCallTables = $queries->getLatestTableNames("cdr_call_", "_bkp", ['start' => 10, 'length' => 8], limit: $_ENV['TABLE_LIMIT']);
        }
        if (isset($latestCdrCallTables)) {
            $date = new DateTime($currentDate);
            foreach ($latestCdrCallTables as $table) {
                $dateFor = $date->format('Ymd');
                $queries->copyTableStructure($table['table_name'], "cdr_call_{$dateFor}");
                $queries->copyTableData($table['table_name'], "cdr_call_{$dateFor}");
                $tables["cdr_call_{$dateFor}"] = [["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"], "id"];
                $date->modify('-1 day');
            }
        }
    }
    if ($latestCdrSipTable) {
        $dateLatestCdrSipTable = substr($latestCdrSipTable[0]['table_name'], 8);
        $dateDiffData = Logic::findDateDifferenceData($currentDate, $dateLatestCdrSipTable);

        if ($dateDiffData['diff'] > 0 && $dateDiffData['diff'] < 7 && $dateDiffData['direction'] === 1) {
            $latestCdrSipTables = $queries->getLatestTableNames("cdr_sip_", "_bkp", ['start' => 9, 'length' => 8], $dateDiffData['diff']);
        } elseif ($dateDiffData['diff'] > 0 && $dateDiffData['diff'] >= 7 && $dateDiffData['direction'] === 1) {
            $latestCdrSipTables = $queries->getLatestTableNames("cdr_sip_", "_bkp", ['start' => 9, 'length' => 8], 7);
        }
        if (isset($latestCdrSipTables)) {
            $date = new DateTime($currentDate);
            foreach ($latestCdrSipTables as $table) {
                $dateFor = $date->format('Ymd');
                $queries->copyTableStructure($table['table_name'], "cdr_sip_{$dateFor}");
                $queries->copyTableData($table['table_name'], "cdr_sip_{$dateFor}");
                $tables["cdr_sip_{$dateFor}"] = [["time"], "id"];
                $date->modify('-1 day');
            }
        }
    }
    
    foreach ($tables as $tableName => $values) {
        $columnsWithData = [];
        $columns = $values[0];
        foreach ($columns as $column) {
            // for cdr_call and cdr_sip tables
            if (substr($tableName, 0, 8) === "cdr_sip_" || substr($tableName, 0, 9) === "cdr_call_") {
                $recentDateArray = $queries->getRecentDate($tableName, $column);
                $recentDate = $recentDateArray[0]['latest_order_date'];
                if (!$recentDate) {
                    Log::logInfo("no controller, but in index file", "no function", "column has no values in cdr_sip or cdr_call", "success", "table - $tableName; column - $column");
                    continue;
                }
                $recentDate = substr_replace($recentDate, '', 10);

                if (substr($tableName, 0, 8) === "cdr_sip_") {
                    $dateToAdd = substr($tableName, 8, 4) . "-" . substr($tableName, 12, 2) . "-" . substr($tableName, 14, 2);
                    $dateDiffData = Logic::findDateDifferenceData($dateToAdd, $recentDate);
                } else {
                    $dateToAdd = substr($tableName, 9, 4) . "-" . substr($tableName, 13, 2) . "-" . substr($tableName, 15, 2);
                    $dateDiffData = Logic::findDateDifferenceData($dateToAdd, $recentDate);
                }

            } else {  // for other tables 
                    $dateDiffData = $dateDiffGolbal;
            }

            if ($dateDiffData['diff'] === 0) {
                Log::logInfo("no controller, but in index file", "no function", "no difference between recent date from column and provided date", "success", "table - $tableName; column - $column");
                return;
            }
            if ($dateDiffData['direction'] === 1) {
                $columnsWithData[$column] = ['modifier' => "add", 'dateDiff' => $dateDiffData['diff']];
            }
            Log::logInfo("no controller, but in index file", "no function", "there is difference between recent date from column and provided date", "success", "table - $tableName; column - $column; diff - {$dateDiffData['diff']}; direction - {$dateDiffData['direction']}");

        }
        if (count($columnsWithData) === 0) {
            continue;
        }
        
        isset($values[1]) ? $queries->updateDates($tableName, $columnsWithData, $values[1]) :
            $queries->updateDates($tableName, $columnsWithData);

    }

} catch (PrepareQueryFailedException $exception) {
    Log::logError("no controller, in index file", "no function", "Exception raised...", "failed", $exception->getMessage());
} catch (QueryExecuteFailedException $exception) {
    Log::logError("no controller, in index file", "no function", "Exception raised...", "failed", $exception->getMessage());
} catch (Exception $exception) {
    Log::logError("no controller, in index file", "no function", "Exception raised...", "failed", $exception->getMessage());
}









