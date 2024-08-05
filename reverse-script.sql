alter table `report_hourly_country_network_carrier_wise_traffic` drop index `idx_roaming`;

alter table `report_hourly_country_network_carrier_wise_traffic` drop index `hour`;

alter table `report_hourly_country_network_carrier_wise_traffic` drop `roaming`;

ALTER TABLE `report_hourly_country_network_carrier_wise_traffic` add UNIQUE `hour` (`hour`, `traffic_type`, `network_id`, `carrier_id`, `country_id`);


ALTER TABLE `ct_mou_top_dest` add INDEX `ct_mou_top_dest_date_country_id_traffic_type_unique` (`date`, `country_id`, `traffic_type`), drop key `ct_mou_top_dest_date_country_id_traffic_type_roaming_unique`;
ALTER TABLE `ct_mou_variance` add INDEX `ct_mou_variance_date_country_id_traffic_type_unique` (`date`,`country_id`,`traffic_type`), drop key `ct_mou_variance_date_country_id_traffic_type_roaming_unique`;
ALTER TABLE `nw_cc_top_dest` add INDEX `date_country_id_network_id_plmn_id_traffic_type_unique` (`date`,`country_id`,`network_id`,`plmn_id`,`traffic_type`), drop key `date_country_id_network_id_plmn_id_traffic_type_roaming_unique`;
ALTER TABLE `cr_acd_mou` add INDEX `unique_report_date_carrier_id_traffic_type` (`report_date`,`carrier_id`,`traffic_type`), drop key `unique_report_date_carrier_id_traffic_type_roaming_type`;
ALTER TABLE `cr_cc_asr_mou` add INDEX `unique_report_date_carrier_id_traffic_type` (`report_date`,`carrier_id`,`traffic_type`), drop key `unique_report_date_carrier_id_traffic_type_roaming`;
ALTER TABLE `total_mou_on_wk_day` add INDEX `total_mou_on_wk_day_date_traffic_type_unique` (`report_date`,`traffic_type`), drop key `total_mou_on_wk_day_date_traffic_type_roaming_unique`;

ALTER TABLE `ct_mou_top_dest` drop `roaming`;
ALTER TABLE `nw_cc_top_dest` drop `roaming`;
ALTER TABLE `ct_mou_variance` drop `roaming`;
ALTER TABLE `cr_acd_mou` drop `roaming`;
ALTER TABLE `cr_cc_asr_mou` drop `roaming`;
ALTER TABLE `total_mou_on_wk_day` drop `roaming`;

drop table msrn_ranges;

 -- $cdrTableName = "cdr_call_" . date('Ymd');
-- ALTER cdr_call_20240803 drop `roaming`;

DELETE from system_parameters
where (`param` = 'rotation_chart_list_on_traffic_trends' and `value` = 'carrier_wise_total_attempts_last_3_days-tab,last_hour_traffic-tab,number_of_attempts-tab,carrier_wise_average_call_duration_last_3_days-tab,carrier_wise_answer_seizure_ratio_last_3_days-tab,carrier_wise_mou_last_3_days-tab' and `description` = 'traffic_trends_dashboard')
OR (`param` = 'rotation_delay_on_traffic_trends' and `value`= '10000' and `description`='traffic_trends_charts_rotation_delay');

delete from permissions 
where (`name`='system-parameter index' and `guard_name`='web' and `enabled`='1')
or (`name`='system-parameter create' and `guard_name`='web' and `enabled`='1')
or (`name`='system-parameter edit' and `guard_name`='web' and `enabled`='1')
or (`name`='system-parameter delete' and `guard_name`='web' and `enabled`='1');

