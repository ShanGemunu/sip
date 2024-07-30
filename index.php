<?php
require_once __DIR__ . '/Connecter.php';

$connecter = Connecter::getConneterInstance();
$conn = $connecter->getDbConnection();

$msrnRanges = null;

function getMsrnRanges($conn)
{
    $query = "SELECT from_msisdn,to_msisdn FROM msrn_ranges";
    $statement = $conn->prepare($query);
    if (!($statement->execute())) {
        throw new Exception('exception occured in getMsrnRanges');
    }

    $result = $statement->get_result();
    $msrnRanges = $result->fetch_all(MYSQLI_ASSOC);

    return $msrnRanges;
}

function isMsrn($called, $msrnRanges)
{
    foreach ($msrnRanges as $msrn) {
        if ($called >= $msrn['from_msisdn'] && $called <= $msrn['to_msisdn'])
            return true;
    }

    return false;
}

function getRoamingStatus($traffic_type, $calling, $called, $msrnRanges)
{
    $roaming = 0;

    switch ($traffic_type) {
        case 'incoming':

            $prefix = substr($calling, 0, 4);
            if ($prefix == 9472 || $prefix == 9478) {
                $roaming = 2;
            } else {
                $isMsrn = isMsrn($called, $msrnRanges);
                if ($isMsrn) {
                    $roaming = 1;
                }
            }

            break;
        case 'outgoing':

            if (substr($calling, 0, 3) != 947) {
                $roaming = 1;
            }

            break;
    }

    return $roaming;
}






try {
// ------------------------------------------------------------------------------------------------------------
    // "ALTER TABLE `nw_cc_top_dest` ADD `roaming` TINYINT NULL DEFAULT '0' AFTER `traffic_type`",
    // "ALTER TABLE `ct_mou_top_dest` ADD `roaming` TINYINT NULL DEFAULT '0' AFTER `traffic_type`",
    // "ALTER TABLE `ct_mou_variance` ADD `roaming` TINYINT NULL DEFAULT '0' AFTER `traffic_type`",
    // "ALTER TABLE `cr_acd_mou` ADD `roaming` TINYINT NULL DEFAULT '0' AFTER `traffic_type`",
    // "ALTER TABLE `cr_cc_asr_mou` ADD `roaming` TINYINT NULL DEFAULT '0' AFTER `traffic_type`",
    // "ALTER TABLE `total_mou_on_wk_day` ADD `roaming` TINYINT NULL DEFAULT '0' AFTER `traffic_type`",
    // "ALTER TABLE `ct_mou_top_dest` DROP INDEX `ct_mou_top_dest_date_country_id_traffic_type_unique`, ADD UNIQUE `ct_mou_top_dest_date_country_id_traffic_type_roaming_unique` (`date`, `country_id`, `traffic_type`, `roaming`)",
    // "ALTER TABLE `ct_mou_variance` DROP INDEX `ct_mou_variance_date_country_id_traffic_type_unique`, ADD UNIQUE `ct_mou_variance_date_country_id_traffic_type_roaming_unique` (`date`,`country_id`,`traffic_type`,`roaming`)",
    // "ALTER TABLE `nw_cc_top_dest` DROP INDEX `date_country_id_network_id_plmn_id_traffic_type_unique`, ADD UNIQUE `date_country_id_network_id_plmn_id_traffic_type_roaming_unique` (`date`,`country_id`,`network_id`,`plmn_id`,`traffic_type`,`roaming`)",
    // "ALTER TABLE `cr_acd_mou` DROP INDEX `unique_report_date_carrier_id_traffic_type`, ADD UNIQUE `unique_report_date_carrier_id_traffic_type_roaming_type` (`report_date`,`carrier_id`,`traffic_type`,`roaming`)",
    // "ALTER TABLE `cr_cc_asr_mou` DROP INDEX `unique_report_date_carrier_id_traffic_type`, ADD UNIQUE `unique_report_date_carrier_id_traffic_type_roaming` (`report_date`,`carrier_id`,`traffic_type`,`roaming`)",
    // "ALTER TABLE `total_mou_on_wk_day` DROP INDEX `total_mou_on_wk_day_date_traffic_type_unique`, ADD UNIQUE `total_mou_on_wk_day_date_traffic_type_roaming_unique` (`report_date`,`traffic_type`,`roaming`)"
// -------------------------------------------------------------------------------------------------------------




    $alterQueries = [
        "ALTER TABLE `report_hourly_country_network_carrier_wise_traffic` DROP INDEX `hour`, ADD UNIQUE `hour` (`hour`, `traffic_type`, `network_id`, `carrier_id`, `country_id`, `roaming`)",
    ];


    foreach ($alterQueries as $query) {
        $statement = $conn->prepare($query);
        if (!($statement->execute())) {
            throw new Exception('exception occured in alterQueries');
        }

        $statement->close();
    }

    $insertIntoMsrn = "INSERT INTO msrn_ranges (from_msisdn, to_msisdn) VALUES (94783502000, 94783502999), (94783503000, 94783503999), (94783506000, 94783506999),  (94783507000, 94783507999), (94780057000, 94780057999), (94780058000, 94780058999), (94780059000, 94780059999), (94780060000, 94780060999)";

    $statement = $conn->prepare($insertIntoMsrn);
    if (!($statement->execute())) {
        throw new Exception('exception occured in insertIntoMsrn');
    }
    $statement->close();

    $alterquery_cdr_call_1 = "ALTER TABLE `cdr_call_test_01` ADD `roaming` TINYINT NULL DEFAULT '0' AFTER `network_id`";
    $alterquery_cdr_call_2 = "ALTER TABLE `report_hourly_country_network_carrier_wise_traffic` ADD INDEX `idx_roaming` (`roaming`)";

    // $statement = $conn->prepare($alterquery_cdr_call_1);
    // if (!($statement->execute())) {
    //     throw new Exception('exception occured in alterquery_cdr_call_1');
    // }
    // $statement->close();

    // $statement = $conn->prepare($alterquery_cdr_call_2);
    // if (!($statement->execute())) {
    //     throw new Exception('exception occured in alterquery_cdr_call_2');
    // }
    // $statement->close();

    $createTableSystemParameters = "
    CREATE TABLE `system_parameters` (
      `id` int(11) NOT NULL,
      `param` varchar(200) NOT NULL,
      `value` text NOT NULL,
      `description` varchar(256) DEFAULT NULL,
      `created_at` datetime DEFAULT NULL,
      `updated_at` datetime DEFAULT NULL,
      `deleted_at` datetime DEFAULT NULL,
      `created_by` int(11) DEFAULT NULL,
      `updated_by` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
    ";

    // $statement = $conn->prepare($createTableSystemParameters);
    // if (!($statement->execute())) {
    //     throw new Exception('exception occured in createTableSystemParameters');
    // }
    // $statement->close();

    $alterTableSystemParamOne = "
        ALTER TABLE `system_parameters`
        ADD PRIMARY KEY (`id`),
        ADD KEY `param` (`param`)
    ";

    // $statement = $conn->prepare($alterTableSystemParamOne);
    // if (!($statement->execute())) {
    //     throw new Exception('exception occured in alterTableSystemParamOne');
    // }
    // $statement->close();

    // $alterTableSystemParamTwo = "ALTER TABLE `system_parameters` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT";

    // $statement = $conn->prepare($alterTableSystemParamTwo);
    // if (!($statement->execute())) {
    //     throw new Exception('exception occured in alterTableSystemParamTwo');
    // }
    // $statement->close();



    $insertIntoSystemParamOne = "INSERT INTO `system_parameters` (`param`, `value`, `description`) SELECT ('rotation_chart_list_on_traffic_trends', 'carrier_wise_total_attempts_last_3_days-tab,last_hour_traffic-tab,number_of_attempts-tab,carrier_wise_average_call_duration_last_3_days-tab,carrier_wise_answer_seizure_ratio_last_3_days-tab,carrier_wise_mou_last_3_days-tab', 'traffic_trends_dashboard')
        WHERE NOT EXISTS (
        SELECT 1 FROM system_parameters 
        WHERE `param` = 'rotation_chart_list_on_traffic_trends'
        AND `value` = 'rotation_chart_list_on_traffic_trends', 'carrier_wise_total_attempts_last_3_days-tab,last_hour_traffic-tab,number_of_attempts-tab,carrier_wise_average_call_duration_last_3_days-tab,carrier_wise_answer_seizure_ratio_last_3_days-tab,carrier_wise_mou_last_3_days-tab'
        AND `description` = 'traffic_trends_dashboard'
        )
    ";
    $insertIntoSystemParamTwo = "INSERT INTO `system_parameters` (`param`, `value`, `description`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) SELECT ('rotation_delay_on_traffic_trends', '10000', 'traffic_trends_charts_rotation_delay', NULL, NULL, NULL, NULL, NULL)
        WHERE NOT EXISTS (
        SELECT 1 FROM system_parameters
        WHERE `param` = 'rotation_delay_on_traffic_trends'
        AND `value` = '10000'
        AND `description` = 'traffic_trends_charts_rotation_delay'
        AND `created_at` = NULL
        AND `updated_at` = NULL
        AND `deleted_at` = NULL
        AND `created_by` = NULL
        AND `updated_by` = NULL
        )
    ";


    $insertIntoPermissions = "
        INSERT INTO `permissions` (`name`, `guard_name`, `enabled`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) 
        VALUES 
        ('system-parameter index', 'web', '1', NULL, NULL, NULL, NULL, NULL),
        ('system-parameter create', 'web', '1', NULL, NULL, NULL, NULL, NULL),
        ('system-parameter edit', 'web', '1', NULL, NULL, NULL, NULL, NULL),
        ('system-parameter delete', 'web', '1', NULL, NULL, NULL, NULL, NULL)
    ";

    // -----------??????????????????????????-------------------------------
    // $statement = $conn->prepare($insertIntoSystemParamOne);
    // if (!($statement->execute())) {
    //     throw new Exception('exception occured in insertIntoSystemParamOne');
    // }
    // $statement->close();

    // $statement = $conn->prepare($insertIntoSystemParamTwo);
    // if (!($statement->execute())) {
    //     throw new Exception('exception occured in alterTableSystemParamTwo');
    // }
    // $statement->close();

    // $statement = $conn->prepare($insertIntoPermissions);
    // if (!($statement->execute())) {
    //     throw new Exception('exception occured in insertIntoPermissions');
    // }
    // $statement->close();



    $offset = 0;
    $batchRecords = [];

    $msrnRanges = getMsrnRanges($conn);
    var_dump($msrnRanges);

    do {
        $query_ = "SELECT * FROM cdr_call_test_01 LIMIT 10000 OFFSET ?";

        $statement_ = $conn->prepare($query_);

        if ($statement_ === false) {
            throw new Exception('exception occored.');
        }

        $statement_->bind_param("i", $offset);

        if ($statement_->execute() === false) {
            throw new Exception('exception occored.');
        }

        $result = $statement_->get_result();
        $batchRecords = $result->fetch_all(MYSQLI_ASSOC);

        $statement_->close();

        if (0 < count($batchRecords)) {
            $query__ = "UPDATE cdr_call_test_01 SET roaming=? WHERE id=?";
            $statement__ = $conn->prepare($query__);
            if ($statement__ === false) {
                throw new Exception("failed prepair");
            }
            foreach ($batchRecords as $record) {
                $trafficType = $record['traffic_type'];
                $calling = $record['calling'];
                $called = $record['called'];
                $roaming = getRoamingStatus($trafficType, $calling, $called, $msrnRanges);
                $statement__->bind_param("ii", $roaming, $record['id']);
            }
        }

        $offset += 10000;
    } while (0 < count($batchRecords));

throw new Exception("custom error!");


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



