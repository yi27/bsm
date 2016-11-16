<?php
/**
 * @Author: Rnb-3Ds
 * @Date:   2016-11-01 17:03:47
 * @Last Modified by:   yi27
 * @Last Modified time: 2016-11-07 15:00:35
 */
header('Content-Type:text/html;charset=UTF-8');
        include_once "connectdb.php";

      // 搜索功能
      // 接受搜索条件
      @$groupid1 = trim($_POST['groupid']) ? trim($_POST['groupid']) : '';
      @$sername1 = trim($_POST['sername']) ? trim($_POST['sername']) : '';
      @$virtual_server1 = trim($_POST['virtual_server']) ? trim($_POST['virtual_server']) : '';
      // 拼接where条件
      $where = '';
      if($groupid1){
            $where .= "where groupid = $groupid1";
      }
      if($sername1 && $where){
            $where .=" and sername = $sername1";
      }elseif ($sername1 && !$where) {
            $where .="where sername = $sername1";
      }
      if($virtual_server1 && $where){
            $where .= " and virtual_server = $virtual_server1";
      }elseif($virtual_server1 && !$where){
            $where .= "where virtual_server = $virtual_server1";
      }

      $query_vip = "SELECT * FROM `$table_vip` $where";


      $result_vip = $pdo -> query($query_vip);
      if($result_vip)
      {
      	$data_vip = array();
      	while($info_vip = $result_vip -> fetch(2))
      	{
      		$groupid = $info_vip['groupid'];
      		$query_lbgroup = "SELECT `groupid`,`groupname`,`grouptype` FROM `$table_lbgroup` WHERE `groupid` = '$groupid'";
      		$result_lbgroup = $pdo -> query($query_lbgroup);
      		$info_lbgroup = $result_lbgroup -> fetch(2);
      		$info_vip['grouptype'] = $info_lbgroup['grouptype'];
      		$info_vip['groupname'] = $info_lbgroup['groupname'];

      		$query_lb = "SELECT ID FROM `$table_lb` WHERE `groupid` = '$groupid'";
      		$result_lb = $pdo ->query($query_lb);
      		$data_lb = array();
      		while($info_lb = $result_lb -> fetch(2))
      		{
      			$data_lb[]= $info_lb;
      		}
      		$info_vip['countlb'] = count($data_lb);

      		$virtual_server = $info_vip['virtual_server'];
      		$query_rs = "SELECT ID FROM `$table_rs` WHERE `virtual_server` = '$virtual_server'";
      		$result_rs = $pdo -> query($query_rs);
      		$data_rs = array();
      		while($info_rs = $result_rs -> fetch(2))
      		{
      			$data_rs[] = $info_rs;
      		}
      		$info_vip['countrs'] = count($data_rs);
      		$data_vip[] = $info_vip;
      	}
      }

      include_once "ywfwqdy.html";