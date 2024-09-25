<?php

namespace app\models;

use app\database\DbConnection;
use mysqli_stmt;
class Queries
{

    /** 
     *    prepare query
     *    returns a statement object or false if an error occurred.
     *    @param string
     *    @return mysqli_stmt|bool
     */
    private function prepareQuery(string $query): mysqli_stmt|bool
    {

        return DbConnection::getDbConnectionInstance()->getDbConnection()->prepare($query);
    }

    /** 
     *    bind parameters to query
     *    Returns true on success or false on failure.
     *    @param mysqli_stmt $statement 
     *    @param array $parameters 
     *    @return bool  
     */
    private function bindParameters(mysqli_stmt $statement, array $parameters): bool
    {
        // assoiative array
        $types = array_map(function ($param) {

            return $param[1];
        }, $parameters);

        $concatTypes = implode('', $types);

        // assoiative array
        $values = array_map(function ($param) {

            return $param[0];
        }, $parameters);

        // array 
        $values = array_values($values);

        return $statement->bind_param($concatTypes, ...$values);
    }

    /** 
     *    get the recent date inserted from a table
     *    @param string $table 
     *    @param string $column 
     *    @return array  
     */
    public function getRecentDate(string $table, string $column): array
    {
        $query = "SELECT MAX($column) AS latest_order_date
                FROM $table";

        $statement = $this->prepareQuery($query);

        if ($statement === false)
            throw new PrepareQueryFailedException("data - $logAndExceptionData", BaseModel::class, "insert");

        if ($statement->execute() === false) {
            throw new QueryExecuteFailedException("data - $logAndExceptionData", BaseModel::class, "insert");
        }
        $result = $statement->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);

    }

    public function updateDates(string $table, array $columnsWithData)
    {
        $setData = "";

        foreach ($columnsWithData as $columnName => $data) {
            $modifier = ($data['modifier'] === "add") ? "DATE_ADD" : "DATE_SUB";
            $dateDiff = $data['dateDiff'];
            $setData .= "$columnName = $modifier($columnName, INTERVAL $dateDiff DAY),";
        }

        $query = "
            UPDATE $table SET 
                $setData 
        ";

        
    }
}


$tables = [
    'activity_log' => ["created_at", "updated_at"],
    'alarms_carrier-histoty' => ["date_time"],
    'alarms_country_history' => ["date_time"],
    'alarms_network_history' => ["date_time"],
    'carrier_wise_traffic1' => ["date"],
    'cdr_call' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20210823' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20210824' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20210825' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20210826' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20210827' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20210828' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20210829' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20210830' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20210831' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20210901' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20210902' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20210903' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20210904' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20210905' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20210906' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20210907' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20210908' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20210909' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20210910" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20210911" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20210912" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20210913" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20210914" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20210915" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20210916" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20210917" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20210918" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20210919" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20210920" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240620" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240621" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240622" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240623" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240624" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240625" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240626" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240627" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240628" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240629" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240630" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240701" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240702" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240703" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240704" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240705" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240706" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240707" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240708" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240709" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240710" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240711" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240712" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240713" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240714" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240715" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240716" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240717" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240718" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240719" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240720" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240721" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240722" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240723" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240724" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240725" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240726" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240727" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240728" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240729" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240730" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240731" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240801" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240802" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240803" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240804" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240805" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240806" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240807" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240808" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240809" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240810" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240811" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240812" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240813" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240814" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240815" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240816" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    "cdr_call_20240817" => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20240818' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20240819' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20240820' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20240821' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20240822' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20240823' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20240824' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20240825' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20240826' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20240827' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20240828' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20240829' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20240830' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20240831' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20240901' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20240902' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20240903' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20240904' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20240905' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20240906' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20240907' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20240908' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20240909' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20240910' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20240911' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20240912' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20240913' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20240914' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20240915' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20240916' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20240917' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20240918' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_call_20240919' => ["invite_time", "ringing_time", "answered_time", "ack_time", "bye_time", "cancel_time"],
    'cdr_dialogs' => ["time", "ringing", "answered", "end"],
    'cdr_sip_20240620' => ["time"],
    'cdr_sip_20240621' => ["time"],
    'cdr_sip_20240622' => ["time"],
    'cdr_sip_20240623' => ["time"],
    'cdr_sip_20240624' => ["time"],
    'cdr_sip_20240625' => ["time"],
    'cdr_sip_20240626' => ["time"],
    'cdr_sip_20240627' => ["time"],
    'cdr_sip_20240628' => ["time"],
    'cdr_sip_20240629' => ["time"],
    'cdr_sip_20240630' => ["time"],
    'cdr_sip_20240701' => ["time"],
    'cdr_sip_20240702' => ["time"],
    'cdr_sip_20240703' => ["time"],
    'cdr_sip_20240704' => ["time"],
    'cdr_sip_20240705' => ["time"],
    'cdr_sip_20240706' => ["time"],
    'cdr_sip_20240707' => ["time"],
    'cdr_sip_20240708' => ["time"],
    'cdr_sip_20240709' => ["time"],
    'cdr_sip_20240710' => ["time"],
    'cdr_sip_20240711' => ["time"],
    'cdr_sip_20240712' => ["time"],
    'cdr_sip_20240713' => ["time"],
    'cdr_sip_20240714' => ["time"],
    'cdr_sip_20240715' => ["time"],
    'cdr_sip_20240716' => ["time"],
    'cdr_sip_20240717' => ["time"],
    'cdr_sip_20240718' => ["time"],
    'cdr_sip_20240719' => ["time"],
    'cdr_sip_20240720' => ["time"],
    'cdr_sip_20240721' => ["time"],
    'cdr_sip_20240722' => ["time"],
    'cdr_sip_20240723' => ["time"],
    'cdr_sip_20240724' => ["time"],
    'cdr_sip_20240725' => ["time"],
    'cdr_sip_20240726' => ["time"],
    'cdr_sip_20240727' => ["time"],
    'cdr_sip_20240728' => ["time"],
    'cdr_sip_20240729' => ["time"],
    'cdr_sip_20240730' => ["time"],
    'cdr_sip_20240731' => ["time"],
    'cdr_sip_20240801' => ["time"],
    'cdr_sip_20240802' => ["time"],
    'cdr_sip_20240803' => ["time"],
    'cdr_sip_20240804' => ["time"],
    'cdr_sip_20240805' => ["time"],
    'cdr_sip_20240806' => ["time"],
    'cdr_sip_20240807' => ["time"],
    'cdr_sip_20240808' => ["time"],
    'cdr_sip_20240809' => ["time"],
    'cdr_sip_20240810' => ["time"],
    'cdr_sip_20240811' => ["time"],
    'cdr_sip_20240812' => ["time"],
    'cdr_sip_20240813' => ["time"],
    'cdr_sip_20240814' => ["time"],
    'cdr_sip_20240815' => ["time"],
    'cdr_sip_20240816' => ["time"],
    'cdr_sip_20240817' => ["time"],
    'cdr_sip_20240818' => ["time"],
    'cdr_sip_20240819' => ["time"],
    'cdr_sip_20240820' => ["time"],
    'cdr_sip_20240821' => ["time"],
    'cdr_sip_20240822' => ["time"],
    'cdr_sip_20240823' => ["time"],
    'cdr_sip_20240824' => ["time"],
    'cdr_sip_20240825' => ["time"],
    'cdr_sip_20240826' => ["time"],
    'cdr_sip_20240827' => ["time"],
    'cdr_sip_20240828' => ["time"],
    'cdr_sip_20240829' => ["time"],
    'cdr_sip_20240830' => ["time"],
    'cdr_sip_20240831' => ["time"],
    'cdr_sip_20240901' => ["time"],
    'cdr_sip_20240902' => ["time"],
    'cdr_sip_20240903' => ["time"],
    'cdr_sip_20240904' => ["time"],
    'cdr_sip_20240905' => ["time"],
    'cdr_sip_20240906' => ["time"],
    'cdr_sip_20240907' => ["time"],
    'cdr_sip_20240908' => ["time"],
    'cdr_sip_20240909' => ["time"],
    'cdr_sip_20240910' => ["time"],
    'cdr_sip_20240911' => ["time"],
    'cdr_sip_20240912' => ["time"],
    'cdr_sip_20240913' => ["time"],
    'cdr_sip_20240914' => ["time"],
    'cdr_sip_20240915' => ["time"],
    'cdr_sip_20240916' => ["time"],
    'cdr_sip_20240917' => ["time"],
    'cdr_sip_20240918' => ["time"],
    'cdr_sip_20240919' => ["time"],
    'cdr_sip_lahiru' => ["time"],
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