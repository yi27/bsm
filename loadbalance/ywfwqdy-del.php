<?php
/**
 * @Author: Rnb-3Ds
 * @Date:   2016-11-04 10:26:42
 * @Last Modified by:   Rnb-3Ds
 * @Last Modified time: 2016-11-04 10:40:53
 */

header('Content-Type:text/html;charset=UTF-8');
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
      //表的变量定义↑↑↑↑↑↑↑↑

      @$serid = $_POST['param'];
      if(!$serid){
      		echo "3";exit;
      }

      $query_vip = "SELECT `virtual_server`,`groupid` FROM `$table_vip` WHERE `ID` = '$serid'";
      $result_vip = $pdo -> query($query_vip);
      $info_vip =$result_vip -> fetch(2);
      $vipip = $info_vip['virtual_server'];
      $groupid = $info_vip['groupid'];
      $del_vip = "DELETE FROM `$table_vip` WHERE `ID` = '$serid'";
      $del_rs = "DELETE FROM `$table_rs` WHERE `virtual_server` = '$vipip' AND `groupid` = '$groupid'";
      if($pdo -> exec($del_vip))
      {
      	$update_lb = "UPDATE `$table_lb` SET `cfgstatus`='1' WHERE (`groupid`='$groupid')";
      	$pdo -> exec ($update_lb);
      	$pdo -> exec($del_rs);
      	echo "1";
      }else
      {
      	echo "2";
      }

