<?php
/**
 * @Author: Rnb-3Ds
 * @Date:   2016-10-27 16:48:22
 * @Last Modified by:   Rnb-3Ds
 * @Last Modified time: 2016-10-27 17:12:07
 */
	//辅助命令

	  header('Content-Type:text/html;charset=UTF-8');
      //请先开启PDO
      include_once "../conf/htbsm.conf.php";
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

      $serid = trim($_REQUEST['serid']) ? trim($_REQUEST['serid']) : '';
      $ordertype = trim($_REQUEST['ordertype']) ? trim($_REQUEST['ordertype']) : '';
      $Editcfg = trim($_REQUEST['Editcfg']) ? trim($_REQUEST['Editcfg']) : '';
      $a = array("/%/","/@/","/&/","/>/","/</","/||/","/;/"); //屏蔽字库 （注意：英文字符格式：/英文字符/i） i：忽略大小写
	  $out = preg_replace($a,"*",$Editcfg);
	  echo $out ;
