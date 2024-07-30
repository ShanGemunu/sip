
ALTER TABLE `report_hourly_country_network_carrier_wise_traffic` DROP INDEX `hour`, ADD UNIQUE `hour` (`hour`, `traffic_type`, `network_id`, `carrier_id`, `country_id`, `roaming`);

ALTER TABLE `ct_mou_top_dest` ADD `roaming` TINYINT NULL DEFAULT '0' AFTER `traffic_type`;
ALTER TABLE `ct_mou_variance` ADD `roaming` TINYINT NULL DEFAULT '0' AFTER `traffic_type`;
ALTER TABLE `nw_cc_top_dest` ADD `roaming` TINYINT NULL DEFAULT '0' AFTER `traffic_type`;
ALTER TABLE `cr_acd_mou` ADD `roaming` TINYINT NULL DEFAULT '0' AFTER `traffic_type`;
ALTER TABLE `cr_cc_asr_mou` ADD `roaming` TINYINT NULL DEFAULT '0' AFTER `traffic_type`;
ALTER TABLE `total_mou_on_wk_day` ADD `roaming` TINYINT NULL DEFAULT '0' AFTER `traffic_type`;


ALTER TABLE `ct_mou_top_dest` DROP INDEX `ct_mou_top_dest_date_country_id_traffic_type_unique`, ADD UNIQUE `ct_mou_top_dest_date_country_id_traffic_type_roaming_unique` (`date`, `country_id`, `traffic_type`, `roaming`);
ALTER TABLE `ct_mou_variance` DROP INDEX `ct_mou_variance_date_country_id_traffic_type_unique`, ADD UNIQUE `ct_mou_variance_date_country_id_traffic_type_roaming_unique` (`date`,`country_id`,`traffic_type`,`roaming`);
ALTER TABLE `nw_cc_top_dest` DROP INDEX `date_country_id_network_id_plmn_id_traffic_type_unique`, ADD UNIQUE `date_country_id_network_id_plmn_id_traffic_type_roaming_unique` (`date`,`country_id`,`network_id`,`plmn_id`,`traffic_type`,`roaming`);
ALTER TABLE `cr_acd_mou` DROP INDEX `unique_report_date_carrier_id_traffic_type`, ADD UNIQUE `unique_report_date_carrier_id_traffic_type_roaming_type` (`report_date`,`carrier_id`,`traffic_type`,`roaming`);
ALTER TABLE `cr_cc_asr_mou` DROP INDEX `unique_report_date_carrier_id_traffic_type`, ADD UNIQUE `unique_report_date_carrier_id_traffic_type_roaming` (`report_date`,`carrier_id`,`traffic_type`,`roaming`);
ALTER TABLE `total_mou_on_wk_day` DROP INDEX `total_mou_on_wk_day_date_traffic_type_unique`, ADD UNIQUE `total_mou_on_wk_day_date_traffic_type_roaming_unique` (`report_date`,`traffic_type`,`roaming`);
