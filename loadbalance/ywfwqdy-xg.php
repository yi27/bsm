<?php
/**
 * @Author: Rnb-3Ds
 * @Date:   2016-11-03 10:23:27
 * @Last Modified by:   Rnb-3Ds
 * @Last Modified time: 2016-11-03 16:45:45
 */

header('Content-Type:text/html;charset=UTF-8');
      include_once "connectdb.php";

      $serid = $_REQUEST['serid'] ? trim($_REQUEST['serid']) : '';
      $query_vip = "SELECT * FROM `$table_vip` WHERE `ID` = '$serid'";
      $result_vip = $pdo -> query($query_vip);
      $info_vip = $result_vip -> fetch(2);
      $query_lbgroup_self = "SELECT `groupid`,`grouptype`,`groupname`,`suptramode` FROM `$table_lbgroup` WHERE `groupid` = '$info_vip[groupid]'";
      $result_lbgroup_self = $pdo -> query($query_lbgroup_self);
      $info_lbgroup_self = $result_lbgroup_self -> fetch(2);

      $query_lbcount_self = "SELECT count(ID),`nat_ip` FROM `$table_lb` WHERE `groupid` = '$info_vip[groupid]'";
      $result_lbcount_self = $pdo -> query($query_lbcount_self);
      $info_lbcount_self = $result_lbcount_self -> fetch(3);

      //$info_vip['lb_kind'] = explode(";", $info_vip['lb_kind']);
      //var_dump($info_lbcount_self);die;
      $groupid = $info_vip['groupid'];
      // 根据groupid获取当前组的所有转发规则
      $query_lbgroup = "SELECT `groupid`,`grouptype`,`groupname`,`suptramode` FROM `$table_lbgroup` WHERE groupid=$groupid";

      $result_lbgroup = $pdo -> query($query_lbgroup);

      $data_lbgroup = array();
      while ($info_lbgroup = $result_lbgroup -> fetch(2))
      {
          $info_lbgroup['suptramode'] =  explode(';',$info_lbgroup['suptramode']);
      	$data_lbgroup[] = $info_lbgroup;
      }

      $query_rs = "SELECT * FROM `$table_rs` WHERE `virtual_server` = '$info_vip[virtual_server]' AND `groupid` = '$info_vip[groupid]'";
      if($result_rs = $pdo -> query($query_rs))
      {
	      $data_rs = array();
	      while ($info_rs = $result_rs -> fetch(2))
	      {
	      	$data_rs[] = $info_rs;
	      }
      }

      include_once "ywfwqdy-xg.html";