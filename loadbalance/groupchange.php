<?php
/**
 * @Author: Rnb-3Ds
 * @Date:   2016-11-02 14:33:18
 * @Last Modified by:   Rnb-3Ds
 * @Last Modified time: 2016-11-03 16:50:56
 */

header('Content-Type:text/html;charset=UTF-8');
include_once "connectdb.php";

      $groupid = $_POST['groupid'] ? trim($_POST['groupid']) : '';
      $query_lbgroup = "SELECT `grouptype`,`groupname`,`suptramode` FROM `$table_lbgroup` WHERE `groupid` = '$groupid'";

      $result_lbgroup = $pdo -> query($query_lbgroup);
      $info_lbgroup = $result_lbgroup -> fetch(2);
      if($info_lbgroup['grouptype']==1)
      {
            $info_lbgroup['group_type']="主备";
      }else
      {
            $info_lbgroup['group_type']="集群";
      }
      $query_lbcount = "SELECT count(ID) FROM `$table_lb` WHERE `groupid` = '$groupid'";
      $result_lbcount = $pdo -> query($query_lbcount);
      $info_lbcount = $result_lbcount -> fetch(3);
      $info_lbgroup['lbcount'] = $info_lbcount[0];
      $info_lbgroup['suptramode'] = explode(";", $info_lbgroup['suptramode']);

      echo json_encode($info_lbgroup);
