<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/9
 * Time: 10:16
 */

//请先开启PDO

include_once "../conf/bsm.conf.php";
$dbtype = strtolower($DB['TYPE']);
$dsn = $dbtype.":host=127.0.0.1;port=".$DB['PORT'].";dbname=".$DB['DATABASE'].";charset=utf8;";
$pdo = new PDO($dsn,$DB['USER'],$DB['PASSWORD']);
//通过PDO连接数据库并返回一个pdo对象
$table_lb = "bsm_cfg_lb";
$table_lbgroup = "bsm_cfg_lbgroup";
$table_vip = "bsm_cfg_vip";
$table_rs = "bsm_cfg_vip_rs";
$table_oper = "bsm_log_oper";
$table_session = "sessions";
$table_user = "users";
$table_reorder = "bsm_type_reorder";
////////////////
//
$table_hosts = "hosts";
//负载监控服务器表bsm_cfg_jks
$table_jks = "bsm_cfg_jks";
//负载监控子表bsm_cfg_jks_items
$table_jks_items = "bsm_cfg_jks_items";
//建议报告子表bsm_log_jks_report
$table_jks_report = "bsm_log_jks_report";

//表的变量定义↑↑↑↑↑↑↑↑





