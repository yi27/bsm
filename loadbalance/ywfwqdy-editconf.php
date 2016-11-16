<?php
/**
 * @Author: Rnb-3Ds
 * @Date:   2016-10-14 15:59:52
 * @Last Modified by:   Rnb-3Ds
 * @Last Modified time: 2016-11-04 23:42:38
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
			//接收表单传递的数据

			$serid = trim($_REQUEST['serid']) ? trim($_REQUEST['serid']) : '';
			$groupid = trim($_REQUEST['groupid']) ? trim($_REQUEST['groupid']) : '';
			$sername = trim($_REQUEST['sername']) ? trim($_REQUEST['sername']) : '';
			$virtual_server = trim($_REQUEST['virtual_server']) ? trim($_REQUEST['virtual_server']) : '';
			$virtual_server_port = trim($_REQUEST['virtual_server_port']) ? trim($_REQUEST['virtual_server_port']) : '';
			$delay_loop = trim($_REQUEST['delay_loop']) ? trim($_REQUEST['delay_loop']) : '';
			$lb_algo = trim($_REQUEST['lb_algo']) ? trim($_REQUEST['lb_algo']) : '';
			$lb_kind = trim($_REQUEST['lb_kind']) ? trim($_REQUEST['lb_kind']) : '';
			$persistence_timeout = trim($_REQUEST['persistence_timeout']) ? trim($_REQUEST['persistence_timeout']) : '';
			$protocol = $_REQUEST['protocol'];
			@$synflood = trim($_REQUEST['synflood']) ? trim($_REQUEST['synflood']) : '';

			$real_server = $_REQUEST['real_server'];
			$real_server_port = $_REQUEST['real_server_port'];
			$weight = $_REQUEST['weight'];
			$check_type = "TCP_CHECK";

            if($delay_loop<1){
                echo "<script>alert('健康检查间隔需大于零!');history.go(-1);</script>";exit;
            }
            if($persistence_timeout<1){
                echo "<script>alert('会话保持时间需大于零！');history.go(-1);</script>";exit;
            }

			if(!$sername||!$virtual_server||!$virtual_server_port||!$delay_loop||!$persistence_timeout)
			{
				echo "<script>alert('你有信息漏填!请确认！');history.go(-1);</script>";exit;
			}


				//信息入库
				//$sql是插入语句，$query是查询语句,$result资源集,$data 是数据数组
				$query_vip = "SELECT * FROM `$table_vip` WHERE `ID` = '$serid'";
				$result_vip = $pdo -> query($query_vip);
				$info_vip = $result_vip -> fetch (2);
				//查出原vip业务服务器的数据，找出groupid和vipip，作为删除rs表的依据
				$rs_groupid = $info_vip['groupid'];
				$rs_virtualserver = $info_vip['virtual_server'];

				$update_vip="UPDATE `$table_vip` SET `sername`='$sername' , `virtual_server_port`='$virtual_server_port' ,`virtual_server`='$virtual_server', `delay_loop`='$delay_loop' , `lb_algo`='$lb_algo' , `lb_kind`='$lb_kind' , `persistence_timeout`='$persistence_timeout' , `protocol`='$protocol' , `groupid`='$groupid' WHERE `ID` = '$serid' ";
//echo $update_vip;die;
				if($pdo -> exec($update_vip)!==false)
				{	//更新成功在操作表中插入一条数据

					$sql_log_1 = "INSERT INTO `$table_oper` VALUES (DEFAULT,'1','','$user',now(),'修改业务服务器')";
                	$pdo -> exec($sql_log_1);
                	//然后对原有rs进行删除
                	$sql_del_rs = "DELETE FROM `$table_rs` WHERE `groupid`='$rs_groupid' AND `virtual_server` = '$rs_virtualserver'";
                	//var_dump($sql_del_rs);die;
                	$pdo -> exec($sql_del_rs);
                	$sql_rs[]=array();//rs配置信息可能有多个，需要拼接sql进行入库处理
					for ($i=0; $i < count($weight); $i++)
					{
						$sql_rs[$i] = "INSERT INTO `$table_rs` VALUES(DEFAULT,'$groupid','$virtual_server','$real_server[$i]','$real_server_port[$i]','$weight[$i]','$check_type')";
						$pdo -> exec($sql_rs[$i]);
						//插入成功在操作表中插入一条数据
					}	//真实服务器数据入库

						$sql_log_2 = "INSERT INTO `bsm_log_oper` VALUES (DEFAULT,'1','','$user',now(),'添加业务服务器源主机')";
		                $pdo -> exec($sql_log_2);

		                //入表后把该业务服务器所在的组下面的负载均衡器数据的“当前配置文件状态”中改为待执行配置↓↓↓↓↓↓↓↓↓↓↓
				        $sql_lb_update = "UPDATE `$table_lb` SET `cfgstatus`='1' WHERE (`groupid`='$groupid')";
				        $pdo -> exec($sql_lb_update);
				}else
				{
					echo "<script>alert('新增业务服务器失败!请确认信息是否正确！');history.go(-1);</script>";exit;
				}



				echo "<script>alert('修改VIP服务器成功!点击返回！');location.href='/loadbalance/ywfwqdy.php';</script>";exit;