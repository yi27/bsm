<?php
/**
 * @Author: Rnb-3Ds
 * @Date:   2016-11-03 17:54:46
 * @Last Modified by:   Rnb-3Ds
 * @Last Modified time: 2016-11-04 00:59:32
 */

header('Content-Type:text/html;charset=UTF-8');
      include_once "connectdb.php";


      $query_lb = "SELECT `ID`, `sername`,`serip`,`sersshacc`,`sersshpwd`,`sersshport` FROM `$table_lb`";
      $result_lb = $pdo -> query($query_lb);

      $data_lb = array();
      while($info_lb = $result_lb -> fetch(2))
      {
      	$data_lb[] = $info_lb;
      }
      $query_reorder = "SELECT * FROM `$table_reorder` WHERE `editcfg` = '1'";
      $result_reorder = $pdo -> query($query_reorder);
      $data_reorder = array();
      if(!$result_reorder)
      {
            echo "命令表中无数据！请检查数据库！";
      }
      while ($info_reorder = $result_reorder -> fetch(2))
      {
      	 $data_reorder[] = $info_reorder;
      }


include_once "ycml.html";