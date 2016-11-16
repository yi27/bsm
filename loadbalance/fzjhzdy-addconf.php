<?php
/**
 * @Author: Rnb-3Ds
 * @Date:   2016-10-31 15:29:46
 * @Last Modified by:   yi27
 * @Last Modified time: 2016-11-11 18:02:53
 */

 header('Content-Type:text/html;charset=UTF-8');
		include_once "connectdb.php";
//var_dump($_COOKIE);die;
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

		$groupid = $_REQUEST['groupid'] ? trim($_REQUEST['groupid']) : '';
		$groupname = $_REQUEST['groupname'] ? trim($_REQUEST['groupname']) : '';
		$grouptype = $_REQUEST['grouptype'] ? $_REQUEST['grouptype'] : '';
		@$suptramode = $_REQUEST['suptramode'] ? $_REQUEST['suptramode'] : '';
		@$lb_id = $_REQUEST['lb_id'] ? $_REQUEST['lb_id'] : '';
	    	@$isMASTER = $_REQUEST['isMASTER'] ? trim($_REQUEST['isMASTER']) : '';

	    //var_dump($suptramode);$_suptramode = implode(";", $suptramode);var_dump($_suptramode);die;
	    if(!$groupid)
	    {
			echo"<script src='../js/jquery.js' type='text/javascript'></script>";
			echo"<script src='../js/layer-v3.0.1/layer/layer.js' type='text/javascript'></script>";
			echo"<script>$(document).ready(function(){layer.alert('组ID不存在！',function(){history.go(-1);});})</script>";exit;

	    }
	    if(!$groupname)
	    {
			echo"<script src='../js/jquery.js' type='text/javascript'></script>";
			echo"<script src='../js/layer-v3.0.1/layer/layer.js' type='text/javascript'></script>";
			echo"<script>$(document).ready(function(){layer.alert('组名不存在！',function(){history.go(-1);});})</script>";exit;
	    }
	    if(!$grouptype)
	    {
			echo"<script src='../js/jquery.js' type='text/javascript'></script>";
			echo"<script src='../js/layer-v3.0.1/layer/layer.js' type='text/javascript'></script>";
			echo"<script>$(document).ready(function(){layer.alert('组类型不正确！',function(){history.go(-1);});})</script>";exit;
	    }
	    if(!$suptramode)
	    {
			echo"<script src='../js/jquery.js' type='text/javascript'></script>";
			echo"<script src='../js/layer-v3.0.1/layer/layer.js' type='text/javascript'></script>";
			echo"<script>$(document).ready(function(){layer.alert('请选择正确的转发模式！',function(){history.go(-1);});})</script>";exit;
	    }

	    if(@$suptramode['NAT']&&$grouptype=='2')
	    {
			echo"<script src='../js/jquery.js' type='text/javascript'></script>";
			echo"<script src='../js/layer-v3.0.1/layer/layer.js' type='text/javascript'></script>";
			echo"<script>$(document).ready(function(){layer.alert('集群模式下不可选择NAT模式！',function(){history.go(-1);});})</script>";exit;
	    }

	    if($grouptype=='1')
	    {

	    	if(!$isMASTER)
	    	{
				echo"<script src='../js/jquery.js' type='text/javascript'></script>";
				echo"<script src='../js/layer-v3.0.1/layer/layer.js' type='text/javascript'></script>";
				echo"<script>$(document).ready(function(){layer.alert('主备模式下请选择一个主要服务器！',function(){history.go(-1);});})</script>";exit;

	    	}

	    	if(@count($lb_id)>3)
	    	{
				echo"<script src='../js/jquery.js' type='text/javascript'></script>";
				echo"<script src='../js/layer-v3.0.1/layer/layer.js' type='text/javascript'></script>";
				echo"<script>$(document).ready(function(){layer.alert('主备模式下最大仅可选择3台负载均衡服务器！',function(){history.go(-1);});})</script>";exit;
	    	}

	    	for ($v=0; $v < count($lb_id); $v++) {
	    		$serid = $lb_id[$v];
	    		$update_backup = "UPDATE `$table_lb` SET `state`='BACKUP',`priority`='255-$v' WHERE (`ID`='$serid')";
	    		$pdo -> exec($update_backup);
	    	}
	    	$update_master = "UPDATE `$table_lb` SET `state`='MASTER',`priority`='255' WHERE (`ID`='$isMASTER')";
	    	$pdo -> exec($update_master);
	    }


	    if(@$suptramode["NAT"])
	    {
	    	if($lb_id)
	    	{
	    		/*for ($i=0; $i < count($lb_id); $i++)
	    		{

		    		$lbid = $lb_id[$i];
		    		$query_lb = "SELECT `nat_ip` FROM `$table_lb` WHERE `ID` = '$lbid'";
		    		$result_lb = $pdo -> query($query_lb);
		    		$info_lb = $result_lb -> fetch(2);
		    		$data_lb[$i] = $info_lb;
	    		}
		    	for($j=0; $j< count($data_lb);$j++)
		    	{
		    		if($data_lb[$j]!=$data_lb[0])
		    		{
		    			 echo"<script>alert('所选负载均衡器的NAT模式网关不一致，请在“负载均衡器管理”中处理');</script>";
		    			 echo "<meta http-equiv='Refresh' content='0;URL=fzjhzdy-add.php'>";exit;
		    		}
		    	}*/
		    	$nat_ip=array();
	    		foreach ($lb_id as $key => $value) {
		    		$query_lb = "SELECT `nat_ip` FROM `$table_lb`  WHERE  `ID`=$value LIMIT 1";
		    		$result_natip = $pdo->query($query_lb);
		    		$ip_array= $result_natip->fetch();
		    		$nat_ip[] = $ip_array[0];
	    		}

	    		if(count(array_unique($nat_ip))>1){
					echo"<script src='../js/jquery.js' type='text/javascript'></script>";
					echo"<script src='../js/layer-v3.0.1/layer/layer.js' type='text/javascript'></script>";
					echo"<script>$(document).ready(function(){layer.alert('eth1(NAT模式网关不一致)！',function(){history.go(-1);});})</script>";exit;
	    		}
	    	}
	    }

	    if(@$suptramode["FNAT"])
	    {
	    	// 勾选FNAT模式下eth1（全NAT模式本地地址）需要唯一
	    	if($lb_id)
	    	{
	    		$data_lb = array();
	    		for ($k=0; $k < count($lb_id); $k++)
	    		{
		    		$lbid = $lb_id[$k];
		    		$query_lb = "SELECT `FNAT_ip` FROM `$table_lb` WHERE `ID` = '$lbid' LIMIT 1";
		    		$result_lb = $pdo -> query($query_lb);
		    		$info_lb = $result_lb -> fetch(2);
		    		// 把ip拆分成开重新放入数组
		    		foreach(explode(';',$info_lb['FNAT_ip']) as $key=>$value){
		    			$data_lb[] = $value;
		    		}

	    		}

		    	if (count($data_lb) != count(@array_unique($data_lb)))
		    	{
					echo"<script src='../js/jquery.js' type='text/javascript'></script>";
					echo"<script src='../js/layer-v3.0.1/layer/layer.js' type='text/javascript'></script>";
					echo"<script>$(document).ready(function(){layer.alert('所选负载均衡器的本地地址存在相同内容，请在“负载均衡器管理”中处理！',function(){history.go(-1);});})</script>";exit;

			}
	    	}
	    }
	    $_suptramode = implode(";", $suptramode);
	    	$insert_lbgroup = "INSERT INTO `$table_lbgroup`  VALUES ('$groupid', '$groupname', '$grouptype', '$_suptramode', now(), '$user')";
		    $pdo -> exec($insert_lbgroup);
		if($lb_id)
	    	{
			for ($f=0; $f < count($lb_id) ; $f++)
			{
			    	$_serid = $lb_id[$f];
			    	$update_lb = "UPDATE `$table_lb` SET `virtual_router_id`='$groupid',`vrrp_instance`='$groupid', `groupid`='$groupid',`cfgstatus`='1',`ingroupidlasttime`=now() WHERE (`ID`='$_serid')";
			    	$pdo -> exec($update_lb);
			    	$query_log_add = "INSERT INTO `$table_oper` (`optype`, `opname`, `optime`, `result`) VALUES ('1', '$user', now(), '新增负载均衡组')";//操作表插入数据
	               		 $pdo -> exec($query_log_add);
		    	}

		}else{
			$query_log_add = "INSERT INTO `$table_oper` (`optype`, `opname`, `optime`, `result`) VALUES ('1', '$user', now(), '新增负载均衡组')";//操作表插入数据
	               	$pdo -> exec($query_log_add);
		}
		echo"<script src='../js/jquery.js' type='text/javascript'></script>";
		echo"<script src='../js/layer-v3.0.1/layer/layer.js' type='text/javascript'></script>";
		echo"<script>$(document).ready(function(){layer.alert('新增负载均衡组定义成功！',function(){location.href='fzjhzdy.php';});})</script>";exit;

