<?php
/**
 * @Author: Rnb-3Ds
 * @Date:   2016-10-30 19:56:16
 * @Last Modified by:   yi27
 * @Last Modified time: 2016-11-10 21:46:15
 */

header('Content-Type:text/html;charset=UTF-8');

        include_once "connectdb.php";
        $sessionid = empty($_COOKIE['bs_sessionid'])?'':$_COOKIE['bs_sessionid'];
        if(!$sessionid)
        {
            $user = "guest";
        }else
        {
            $query_session = "SELECT `userid` FROM `$table_session` WHERE `sessionid` = '$sessionid'";
            $result_session = $pdo -> query($query_session);
            $info_session = $result_session -> fetch(3);
            $userid = $info_session[0];
            $query_user = "SELECT `alias` FROM `$table_user` WHERE `userid`='$userid'";
            $result_user = $pdo -> query($query_user);
            $info_user = $result_user -> fetch(2);
            $user = $info_user['alias'];

        }


      @$groupid = $_POST['param']?$_POST['param']:"";
      $query_vip = "SELECT ID FROM `$table_vip` WHERE `groupid` = '$groupid'";
      $result_vip = $pdo -> query($query_vip);
      if(!$groupid)
      {
      	echo "3";exit;
      }

      if($result_vip -> fetch(2))
      {
      	echo "1";exit;
      }
      $update_lb = "UPDATE `$table_lb` SET `cfgstatus`='2' WHERE (`groupid`='$groupid')";
      $delete_lbgroup = "DELETE FROM `$table_lbgroup` WHERE (`groupid`='$groupid')";
      //echo $delete_lbgroup; echo $groupid;
      $result_lb = $pdo -> exec($update_lb);$result_lbgroup = $pdo -> exec($delete_lbgroup);
      if($result_lbgroup)
          	{
          		$sql_oper_add = "INSERT INTO `$table_oper` (`optype`, `opname`, `optime`, `result`) VALUES ('1', '$user', now(), '删除负载均衡组')";
          		$pdo -> exec($sql_oper_add);
          		echo "2";exit;

          	}else
          	{
          		echo "3";
          	}


