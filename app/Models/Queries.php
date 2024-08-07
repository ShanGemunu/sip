<?php

namespace App\Models;

use Dotenv\Dotenv;
// use Exception;
use App\Database\DbConnection;
use Exception;
use App\Log\Logger;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

class Queries
{
    private $conn;
    private $logger;

    public function __construct()
    {
        $dbConnection = DbConnection::getDbConnectionInstance();
        $this->conn = $dbConnection->getDbConnection();
        $this->logger = new Logger();
    }

    private function getMsrnRanges(): array
    {
        $query = "SELECT from_msisdn,to_msisdn FROM msrn_ranges";
        $statement = $this->conn->prepare($query);
        if (!($statement->execute())) {
            throw new Exception('exception occured in getMsrnRanges');
        }

        $result = $statement->get_result();
        $msrnRanges = $result->fetch_all(MYSQLI_ASSOC);

        $this->logger->createSuccessLog($query, 'getMsrnRanges', 'Queries');
        return $msrnRanges;
    }

    private function isMsrn(string $called, array $msrnRanges): bool
    {
        foreach ($msrnRanges as $msrn) {
            if ($called >= $msrn['from_msisdn'] && $called <= $msrn['to_msisdn'])
                return true;
        }
        return false;
    }

    private function getRoamingType(string $traffic_type, string $calling, string $called, array $msrnRanges): int
    {
        $roaming = 0;

        switch ($traffic_type) {
            case 'incoming':

                $prefix = substr($calling, 0, 4);
                if ($prefix == 9472 || $prefix == 9478) {
                    $roaming = 2;
                } else {
                    $isMsrn = $this->isMsrn($called, $msrnRanges);
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

    // report_hourly_country_network_carrier_wise_traffic
    public function alterTableReportHourlyCountryCarrierWiseTraffic(): void
    {
        $query = "ALTER TABLE `report_hourly_country_network_carrier_wise_traffic` ADD `roaming` TINYINT(1) NOT NULL AFTER `network_id`, ADD INDEX `idx_roaming` (`roaming`)";

        $statement = $this->conn->prepare($query);
        if (!($statement->execute())) {
            throw new Exception('exception occured in alterTableReportHourlyCountry__Carrier_Wise_Traffic');
        }

        $this->logger->createSuccessLog($query, 'alterTableReportHourlyCountryCarrierWiseTraffic', 'Queries');
    }

    // report_hourly_country_network_carrier_wise_traffic
    public function alterReportHourlyCountryNetworkCarrierWiseTraffic(): void
    {
        $alterQueries = [
            "ALTER TABLE `report_hourly_country_network_carrier_wise_traffic` DROP INDEX `hour`, ADD UNIQUE `hour` (`hour`, `traffic_type`, `network_id`, `carrier_id`, `country_id`, `roaming`)",
            "ALTER TABLE `nw_cc_top_dest` ADD `roaming` TINYINT NULL DEFAULT '0' AFTER `traffic_type`",
            "ALTER TABLE `ct_mou_top_dest` ADD `roaming` TINYINT NULL DEFAULT '0' AFTER `traffic_type`",
            "ALTER TABLE `ct_mou_variance` ADD `roaming` TINYINT NULL DEFAULT '0' AFTER `traffic_type`",
            "ALTER TABLE `cr_acd_mou` ADD `roaming` TINYINT NULL DEFAULT '0' AFTER `traffic_type`",
            "ALTER TABLE `cr_cc_asr_mou` ADD `roaming` TINYINT NULL DEFAULT '0' AFTER `traffic_type`",
            "ALTER TABLE `total_mou_on_wk_day` ADD `roaming` TINYINT NULL DEFAULT '0' AFTER `traffic_type`",

            "ALTER TABLE `ct_mou_top_dest` DROP INDEX `ct_mou_top_dest_date_country_id_unique`, ADD UNIQUE `ct_mou_top_dest_date_country_id_traffic_type_roaming_unique` (`date`, `country_id`, `traffic_type`, `roaming`)",
            "ALTER TABLE `ct_mou_variance` DROP INDEX `ct_mou_variance_date_country_id_traffic_type_unique`, ADD UNIQUE `ct_mou_variance_date_country_id_traffic_type_roaming_unique` (`date`,`country_id`,`traffic_type`,`roaming`)",
            "ALTER TABLE `nw_cc_top_dest` DROP INDEX `date_country_id_network_id_plmn_id_traffic_type_unique`, ADD UNIQUE `date_country_id_network_id_plmn_id_traffic_type_roaming_unique` (`date`,`country_id`,`network_id`,`plmn_id`,`traffic_type`,`roaming`)",
            "ALTER TABLE `cr_acd_mou` DROP INDEX `unique_report_date_carrier_id_traffic_type`, ADD UNIQUE `unique_report_date_carrier_id_traffic_type_roaming_type` (`report_date`,`carrier_id`,`traffic_type`,`roaming`)",
            "ALTER TABLE `cr_cc_asr_mou` DROP INDEX `unique_report_date_carrier_id_traffic_type`, ADD UNIQUE `unique_report_date_carrier_id_traffic_type_roaming` (`report_date`,`carrier_id`,`traffic_type`,`roaming`)",
            "ALTER TABLE `total_mou_on_wk_day` DROP INDEX `total_mou_on_wk_day_date_traffic_type_unique`, ADD UNIQUE `total_mou_on_wk_day_date_traffic_type_roaming_unique` (`report_date`,`traffic_type`,`roaming`)"
        ];

        foreach ($alterQueries as $query) {

            $statement = $this->conn->prepare($query);
            if (!($statement->execute())) {
                throw new Exception('exception occured in alterQueries');
            }

            $this->logger->createSuccessLog($query, 'alterReportHourlyCountryNetworkCarrierWiseTraffic', 'Queries');

        }
    }


    function createMsrnRangesTable()
    {
        $query = "
        CREATE TABLE msrn_ranges (
        id INT NOT NULL AUTO_INCREMENT,
        from_msisdn BIGINT(20) NOT NULL,
        to_msisdn BIGINT(20) NOT NULL,
        INDEX(from_msisdn, to_msisdn),
        PRIMARY KEY(id)
        )";

        $statement = $this->conn->prepare($query);
        if (!($statement->execute())) {
            throw new Exception('exception occured in createMsrnRangesTable');
        }

        $this->logger->createSuccessLog($query, 'createMsrnRangesTable', 'Queries');
    }


    public function insertIntoMsrn(): void
    {
        $query = "INSERT INTO msrn_ranges (from_msisdn, to_msisdn) VALUES (94783502000, 94783502999), (94783503000, 94783503999), (94783506000, 94783506999),  (94783507000, 94783507999), (94780057000, 94780057999), (94780058000, 94780058999), (94780059000, 94780059999), (94780060000, 94780060999)";

        $statement = $this->conn->prepare($query);
        if (!($statement->execute())) {
            throw new Exception('exception occured in insertIntoMsrn');
        }

        $this->logger->createSuccessLog($query, 'insertIntoMsrn', 'Queries');

        $statement->close();
    }


    public function alterTableCdrCall(): void
    {
        $cdrTableName = "cdr_call";
        $query = "ALTER TABLE " . $cdrTableName . " ADD `roaming` TINYINT NULL DEFAULT '0' AFTER `network_id`";
        $statement = $this->conn->prepare($query);
        if (!($statement->execute())) {
            throw new Exception('exception occured in alterTableCdrCall');
        }

        $this->logger->createSuccessLog($query, 'alterTableCdrCall', 'Queries');
    }


    public function createTableSystemParameters(): void
    {
        $query = "
        CREATE TABLE IF NOT EXISTS `system_parameters` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `param` varchar(200) NOT NULL,
        `value` text NOT NULL,
        `description` varchar(256) DEFAULT NULL,
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL,
        `deleted_at` datetime DEFAULT NULL,
        `created_by` int(11) DEFAULT NULL,
        `updated_by` int(11) DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `param` (`param`)
        )";

        $statement = $this->conn->prepare($query);
        if (!($statement->execute())) {
            throw new Exception('exception occured in createTableSystemParameters');
        }

        $this->logger->createSuccessLog($query, 'createTableSystemParameters', 'Queries');
    }

    public function getCountForSystemParamByColumn(string $valueParam, string $valueValue, string $valueDescription): int
    {
        $query = "SELECT * FROM system_parameters WHERE `param`='{$valueParam}' AND `value` = '{$valueValue}' AND `description`='{$valueDescription}'";

        $statement = $this->conn->query($query);
        if (!$statement) {
            throw new Exception('exception occured in alterTableSystemParamTwo');
        }

        $numberOfRows =  $statement->num_rows;

        $this->logger->createSuccessLog($query, 'getCountForSystemParamByColumn', 'Queries');

        return $numberOfRows;
    }

    public function insertIntoSystemParam(): void
    {
        $value = "";
        $countTerendsDashboard = $this->getCountForSystemParamByColumn('rotation_chart_list_on_traffic_trends', 'carrier_wise_total_attempts_last_3_days-tab,last_hour_traffic-tab,number_of_attempts-tab,carrier_wise_average_call_duration_last_3_days-tab,carrier_wise_answer_seizure_ratio_last_3_days-tab,carrier_wise_mou_last_3_days-tab', 'traffic_trends_dashboard');
        $countRotationDelay = $this->getCountForSystemParamByColumn('rotation_delay_on_traffic_trends', '10000', 'traffic_trends_charts_rotation_delay');

        if (($countTerendsDashboard !== 0 && $countRotationDelay === 0)) {
            $value = "('rotation_delay_on_traffic_trends', '10000', 'traffic_trends_charts_rotation_delay')";
        } elseif ($countRotationDelay !== 0 && $countTerendsDashboard === 0) {
            $value = "('rotation_chart_list_on_traffic_trends', 'carrier_wise_total_attempts_last_3_days-tab,last_hour_traffic-tab,number_of_attempts-tab,carrier_wise_average_call_duration_last_3_days-tab,carrier_wise_answer_seizure_ratio_last_3_days-tab,carrier_wise_mou_last_3_days-tab', 'traffic_trends_dashboard')";
        } elseif ($countRotationDelay === 0 && $countTerendsDashboard === 0) {
            $value = "('rotation_chart_list_on_traffic_trends', 'carrier_wise_total_attempts_last_3_days-tab,last_hour_traffic-tab,number_of_attempts-tab,carrier_wise_average_call_duration_last_3_days-tab,carrier_wise_answer_seizure_ratio_last_3_days-tab,carrier_wise_mou_last_3_days-tab', 'traffic_trends_dashboard'),
            ('rotation_delay_on_traffic_trends', '10000', 'traffic_trends_charts_rotation_delay')";
        } else {
            return;
        }
        $query = "INSERT INTO system_parameters (`param`, `value`, `description`) VALUES $value";

        $statement = $this->conn->prepare($query);
        if (!($statement->execute())) {
            throw new Exception('exception occured @insertIntoSystemParam');
        }

        $this->logger->createSuccessLog($query, 'insertIntoSystemParam', 'Queries');
    }

    public function insertIntoPermissions(): void
    {
        $query = "
            INSERT INTO `permissions` (`name`, `guard_name`, `enabled`) 
            VALUES 
            ('system-parameter index', 'web', '1'),
            ('system-parameter create', 'web', '1'),
            ('system-parameter edit', 'web', '1'),
            ('system-parameter delete', 'web', '1')
        ";

        $statement = $this->conn->prepare($query);
        if (!($statement->execute())) {
            throw new Exception('exception occured in insertIntoPermissions');
        }

        $this->logger->createSuccessLog($query, 'insertIntoPermissions', 'Queries');
    }

    public function addValuesForCdrCall(): void
    {
        $offset = 0;
        $batchRecords = [];
        $limit = $_ENV['LIMIT'];
        $cdrTableName = "cdr_call_" . date('Ymd');

        $msrnRanges = $this->getMsrnRanges();

        do {

            $query_ = "SELECT * FROM " . $cdrTableName . " LIMIT " . $limit . " OFFSET ?";

            $statement_ = $this->conn->prepare($query_);

            if ($statement_ === false) {
                throw new Exception('exception occored.');
            }

            $statement_->bind_param("i", $offset);

            if ($statement_->execute() === false) {
                throw new Exception('exception occored.');
            }

            $result = $statement_->get_result();
            $batchRecords = $result->fetch_all(MYSQLI_ASSOC);


            $logQuery_ = "SELECT * FROM " . $cdrTableName . " LIMIT " . $limit . " OFFSET {$offset}";
            $this->logger->createSuccessLog($logQuery_, 'addValuesForCdrCall', 'Queries');


            $statement_->close();

            if (0 < count($batchRecords)) {
                $query__ = "UPDATE " . $cdrTableName . " SET roaming=? WHERE id=?";
                $statement__ = $this->conn->prepare($query__);
                if ($statement__ === false) {
                    throw new Exception("failed prepair");
                }
                foreach ($batchRecords as $record) {
                    $trafficType = $record['traffic_type'];
                    $calling = $record['calling'];
                    $called = $record['called'];
                    $roaming = $this->getRoamingType($trafficType, $calling, $called, $msrnRanges);
                    $statement__->bind_param("ii", $roaming, $record['id']);
                    if ($statement__->execute() === false) {
                        throw new Exception('exception occored.');
                    }

                    $logQuery__ ="UPDATE " . $cdrTableName . " SET roaming={$roaming} WHERE id={$record['id']}";
                    $this->logger->createSuccessLog($logQuery__, 'addValuesForCdrCall', 'Queries');
                }
            }

            $offset += $limit;
        } while (0 < count($batchRecords));
    }
}