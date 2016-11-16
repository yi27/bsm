<?php
/**
 * @Author: Rnb-3Ds
 * @Date:   2016-11-01 10:03:39
 * @Last Modified by:   Rnb-3Ds
 * @Last Modified time: 2016-11-01 14:42:00
 */
header('Content-Type:text/html;charset=UTF-8');
      include_once "connectdb.php";

      $groupid = $_REQUEST['groupid'] ? trim($_REQUEST['groupid']) : '';
      //var_dump($groupid);die;
      $query_lbgroup = "SELECT * FROM `$table_lbgroup` WHERE `groupid` = '$groupid'";
      $result_lbgroup = $pdo -> query($query_lbgroup);
      $info_lbgroup = $result_lbgroup -> fetch(2);
      $suptramode = array();
      $suptramode = explode(";", $info_lbgroup['suptramode']);
      $query_lb = "SELECT `sername` FROM `$table_lb` WHERE `groupid` ='$groupid'";
      $result_lb = $pdo -> query($query_lb);
      $data_lb = array();
      while($info_lb = $result_lb -> fetch(2))
      {
      		$data_lb[] = $info_lb;
      }
      $query_vip = "SELECT `sername` FROM `$table_vip` WHERE `groupid` ='$groupid'";
      $result_vip = $pdo -> query($query_vip);
      $data_vip = array();
      while($info_vip = $result_vip -> fetch(2))
      {
      		$data_vip[] = $info_vip;
      }

      $query_lb_all = "SELECT `ID`, `sername`,`serip`,`groupid`,`nat_ip`,`FNAT_ip`,`state` FROM `$table_lb` WHERE `groupid` ='$groupid' OR `groupid` = '0' ORDER BY `groupid` DESC";
      $result_lb_all = $pdo -> query($query_lb_all);
      $data_lb_all = array();
      while($info_lb_all = $result_lb_all -> fetch(2))
      {
      		$data_lb_all[] = $info_lb_all;
      }
     // var_dump($suptramode);die;
      include_once "fzjhzdy-xg.html";