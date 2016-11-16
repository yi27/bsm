<?php
/**
 * @Author: Rnb-3Ds
 * @Date:   2016-11-02 17:12:35
 * @Last Modified by:   Rnb-3Ds
 * @Last Modified time: 2016-11-04 14:16:57
 */

      header('Content-Type:text/html;charset=UTF-8');
      include_once "connectdb.php";

      $query_lb = "SELECT `sername`,`serip`,`optlasttime`,`optlastname`,`groupid`,`cfgefflasttime`,`cfgstatus` FROM `$table_lb` WHERE `cfgstatus`='1' OR `cfgstatus`='2'";
      $result_lb = $pdo -> query($query_lb);
      $data_lb =array();
      while($info_lb = $result_lb -> fetch(2))
      {
      	$data_lb[] = $info_lb;
      }

      $query_oper = "SELECT * FROM `$table_oper` ORDER BY `optime`";
      $result_oper = $pdo -> query($query_oper);
      $data_oper = array();
      while($info_oper = $result_oper -> fetch(2))
      {
            $data_oper[] = $info_oper;
      }
      $count = count($data_oper);

      // 加载分页类，实现分页
      include_once "page.class.php";
      @$perPage = $_GET['page']? $_GET['page'] : 1;
      // 当前页的起始位置
      $start = ($perPage-1)*5;
      $page = new Page($count,5);
      // 获取当前页的数据
      $query_oper  = "SELECT * FROM `$table_oper` ORDER BY `optime` DESC limit $start,5";
      $result_oper = $pdo -> query($query_oper);
      $data_oper   = array();
      while($info_oper = $result_oper -> fetch(2))
      {
            $data_oper[] = $info_oper;
      }
      // 获取分页信息
      $showPage = $page->showpage();

      include_once "zxfzjhcz.html";
