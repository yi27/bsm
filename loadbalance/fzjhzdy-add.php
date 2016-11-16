<?php
/**
 * @Author: Rnb-3Ds
 * @Date:   2016-10-30 22:11:29
 * @Last Modified by:   Rnb-3Ds
 * @Last Modified time: 2016-10-31 15:35:50
 */
header('Content-Type:text/html;charset=UTF-8');
      include_once "connectdb.php";

      $query_lbgroup = "SELECT groupid FROM `$table_lbgroup` ORDER BY groupid DESC";
      $result_lbgroup = $pdo -> query($query_lbgroup);
      $info_lbgroup = $result_lbgroup -> fetch(2);
      $groupid = $info_lbgroup['groupid']+1;

      $query_lb = "SELECT `ID`, `sername`,`serip`,`groupid`,`nat_ip`,`FNAT_ip` FROM `$table_lb` WHERE `groupid` = '0'";
      $result_lb = $pdo -> query($query_lb);
      $data_lb = array();
      while($info_lb = $result_lb -> fetch(2))
      {
      	$data_lb[] = $info_lb;
      }
      include_once "fzjhzdy-add.html";