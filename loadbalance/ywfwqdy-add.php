<?php
/**
 * @Author: Rnb-3Ds
 * @Date:   2016-11-02 11:31:40
 * @Last Modified by:   Rnb-3Ds
 * @Last Modified time: 2016-11-03 15:22:31
 */

      include_once "connectdb.php";

      // 获取负载均衡定义组信息
      $query_lbgroup = "SELECT `groupid`,`grouptype`,`groupname`,`suptramode` FROM `$table_lbgroup`";
      $result_lbgroup = $pdo -> query($query_lbgroup);
      $data_lbgroup = array();
      while ($info_lbgroup = $result_lbgroup -> fetch(2))
      {
      	$data_lbgroup[] = $info_lbgroup;
      }

      // 获取负载均衡器信息，根据groupid
      $groupid = $data_lbgroup[0]['groupid'];
      $query_lb = "SELECT count(ID),`nat_ip` FROM `$table_lb` WHERE `groupid` = '$groupid'";
      //var_dump($query_lb);die;
      $result_lb = $pdo -> query($query_lb);
      $info_lb = $result_lb -> fetch(3);
      $data_lbgroup[0]['countlb'] = $info_lb[0];
      $data_lbgroup[0]['nat_ip'] = $info_lb[1];
      $data_lbgroup[0]['suptramode'] = explode(";", $data_lbgroup[0]['suptramode']);
      //var_dump($info_lb);die;
      //$info_lbgroup = $result_lbgroup -> fetch(2);
      //var_dump($data_lbgroup);die;
      include_once "ywfwqdy-add.html";

