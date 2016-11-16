<?php
/**
 * @Author: Rnb-3Ds
 * @Date:   2016-10-28 10:12:17
 * @Last Modified by:   yi27
 * @Last Modified time: 2016-11-04 11:55:26
 */

      header('Content-Type:text/html;charset=UTF-8');
      include_once "connectdb.php";

      // 拼接搜索where条件

      @$serip = trim($_POST['serip']) ? $_POST['serip'] : '';
      @$groupid = trim($_POST['groupid']) ? $_POST['groupid'] : '';//归属组
      @$cfgstatus = trim($_POST['cfgstatus']) ? $_POST['cfgstatus'] : '5';
	   // 因为0在三目运算是为假，因此换成4，而数据库字段为0，所以进行重新赋值。
      if($cfgstatus==4){
            $cfgstatus=0;
      }
      static $where = '';
      if(!empty($serip)){
          $where .="where serip = '$serip'";
      }
      if(!empty($groupid) && !empty($where)){
          $where .= " and groupid = $groupid";
      }elseif(empty($where) && !empty($groupid)){
          $where .= "where groupid = $groupid";
      }
      if(!empty($cfgstatus) && !empty($where)  && $cfgstatus!=5){
          $where .= " and cfgstatus=$cfgstatus";
      }elseif(empty($where) && in_array($cfgstatus,array(0,1,2,3,4))){

          $where .="where cfgstatus = $cfgstatus";
      }

      $query_lb = "SELECT `ID`,`sername`,`serip`,`optlasttime`,`optlastname`,`groupid`,`cfgefflasttime`,`cfgstatus` FROM `$table_lb` $where";
      //var_dump($query_lb);die;
      $result_lb = $pdo -> query($query_lb);
      if($result_lb)
      {
            $arr_lvs = array();
            while ($data_lb = $result_lb -> fetch(2))
            {
                 $arr_lvs[] = $data_lb;
            }
      }


      include_once "fzjhqgl.html";