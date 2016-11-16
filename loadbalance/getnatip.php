<?php
/**
 * @Author: Rnb-3Ds
 * @Date:   2016-11-03 09:59:04
 * @Last Modified by:   Rnb-3Ds
 * @Last Modified time: 2016-11-03 10:03:56
 */
header('Content-Type:text/html;charset=UTF-8');
      include_once "connectdb.php";

      $groupid = $_REQUEST['param'] ? trim($_REQUEST['param']) : '';
      $query_lbnat = "SELECT `nat_ip` FROM `$table_lb` WHERE `groupid` = '$groupid'";
      $result_lbnat = $pdo -> query($query_lbnat);
      $info_lbnat = $result_lbnat -> fetch (2);
      echo json_encode($info_lbnat);