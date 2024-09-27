<?php
require_once __DIR__ . '/vendor/autoload.php';
use Dotenv\Dotenv;
use app\datetime\DateTime;
use app\log\Log;
use app\logic\Logic;
use app\database\DbConnection;
use app\models\Queries;
use app\exceptions\PrepareQueryFailedException;
use app\exceptions\QueryExecuteFailedException;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

new DateTime('Asia/Colombo');
Log::config();
DbConnection::getDbConnectionInstance([
    'SERVER' => $_ENV['SERVER'],
    'USER_NAME' => $_ENV['USER_NAME'],
    'PASSWORD' => $_ENV['PASSWORD'],
    'DB_NAME' => $_ENV['DB_NAME'],
    'PORT' => $_ENV['PORT']
]);
$queries = new Queries();
$currentDate = DateTime::getCurrentDateTime("Y-m-d");

$tables = [
    'activity_log' => ["created_at", "updated_at"],
    'alarms_carrier_history' => ["date_time"],
    'alarms_country_history' => ["date_time"],
    'alarms_network_history' => ["date_time"],
    'carrier_wise_traffic1' => ["date"],
    'cdr_dialogs' => ["time", "ringing", "answered", "end"],
    'cr_acd_mou' => ["report_date"],
    'cr_cc_asr_mou' => ["report_date"],
    'ct_mou_top_dest' => ["date"],
    'ct_mou_variance' => ["date"],
    'flash_call_labels' => ["update_time"],
    'idd_carrier_level_quality_hourly_v3' => ["date_time"],
    'idd_hourly_carrier_level_quality_score' => ["date_time"],
    'idd_hourly_country_level_quality_score' => ["date_time"],
    'idd_hourly_network_level_quality_score' => ["date_time"],
    'idd_hourly_stats' => ["date_"],
    'idd_hourly_stats_v1' => ["date_"],
    'idd_hourly_trend_outlier_carrier_level' => ["date_time"],
    'idd_hourly_trend_outlier_country_level' => ["date_time"],
    'idd_hourly_trend_outlier_network_level' => ["date_time"],
    'idd_outlier_alarm_history' => ["created_at"],
    'idd_outlier_hourly' => ["running_time"],
    'idd_outlier_hourly_tmp' => ["running_time"],
    'idd_outlier_ten_min' => ["running_time"],
    'j_cdr_sip_20211001' => ["time"],
    'j_idd_hourly_stats' => ["date_"],
    'j_network_data' => ["created_at", "updated_at", "deleted_at"],
    'j_networks' => ["created_at", "updated_at", "deleted_at"],
    'nw_cc_top_dest' => ["date"],
    'report_daily_72_78_outgoing_traffic' => ["date"],
    'report_daily_country_carrier_wise_traffic' => ["date"],
    'report_daily_country_carrier_wise_traffic1' => ["date"],
    'report_daily_country_carrier_wise_traffic2' => ["date"],
    'report_daily_country_carrier_wise_traffic_quality' => ["date"],
    'report_hourly_country_carrier_wise_traffic' => ["hour"],
    'report_hourly_country_carrier_wise_traffic_old' => ["hour"],
    'report_hourly_country_carrier_wise_traffic_test' => ["hour"],
    'report_hourly_country_carrier_wise_traffic_test_2' => ["hour"],
    'report_hourly_country_network_carrier_wise_traffic' => ["hour"],
    'system_parameters' => ["created_at", "updated_at", "deleted_at"],
    'temp1_cdr_call_20210217' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'temp1_cdr_sip_20210217' => ["time"],
    'temp_cdr_call_20210216' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'temp_cdr_call_20210217' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'temp_cdr_sip_20210217' => ["time"],
    'total_mou_on_wk_day' => ["report_date"]
];

try {
    // latest cdr_call tables names
    $latestCdrCallTables = $queries->getLatestTableName($_ENV['DB_NAME'], "cdr_call_", "_bkp", ['start' => 10, 'length' => 8]);
    // latest cdr_sip tables names
    $latestCdrSipTables = $queries->getLatestTableName($_ENV['DB_NAME'], "cdr_sip_", "_bkp", ['start' => 9, 'length' => 8]);

    if ($latestCdrCallTables) {
        foreach ($latestCdrCallTables as $table) {
            $tables[$table['table_name']] = ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"];
        }
    }
    if ($latestCdrSipTables) {
        foreach ($latestCdrSipTables as $table) {
            $tables[$table['table_name']] = ["time"];
        }
    }

    foreach ($tables as $tableName => $columns) {
        $columnsWithData = [];
        foreach ($columns as $column) {
            $recentDateArray = $queries->getRecentDate($tableName, $column);
            $recentDate = $recentDateArray[0]['latest_order_date'];
            if (!$recentDate) {
                Log::logInfo("no controller", "no function", "column has no values", "success", "table - $tableName; column - $column");
                continue;
            }
            $dateDiffData = Logic::findDateDifferenceData($currentDate, $recentDate);
            if ($dateDiffData['diff'] === 0) {
                Log::logInfo("no controller", "no function", "no difference between recent date from column and provided date", "success", "table - $tableName; column - $column");
                continue;
            }
            if ($dateDiffData['direction'] === 1) {
                $columnsWithData[$column] = ['modifier' => "add", 'dateDiff' => $dateDiffData['diff']];
            } else {
                $columnsWithData[$column] = ['modifier' => "sub", 'dateDiff' => $dateDiffData['diff']];
            }
            Log::logInfo("no controller", "no function", "there is difference between recent date from column and provided date", "success", "table - $tableName; column - $column; diff - {$dateDiffData['diff']}; direction - {$dateDiffData['direction']}");

        }
        if (count($columnsWithData) === 0) {
            continue;
        }

        $queries->updateDates($tableName, $columnsWithData);
    }

} catch (PrepareQueryFailedException $exception) {
    Log::logError("no controller", "no function", "Exception raised...", "failed", $exception->getMessage());
} catch (QueryExecuteFailedException $exception) {
    Log::logError("no controller", "no function", "Exception raised...", "failed", $exception->getMessage());
} catch (Exception $exception) {
    Log::logError("no controller", "no function", "Exception raised...", "failed", $exception->getMessage());
}









