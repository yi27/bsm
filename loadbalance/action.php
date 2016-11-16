<?php
/**

 * @Last Modified by:   Rnb-3Ds
 * @Last Modified time: 2016-11-15 14:14:23


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


$contents2 = ''; //集群的ospfd.conf文件组装
$query_lb = "SELECT * FROM `$table_lb`";
$result_lb = $pdo -> query($query_lb);
$data_lb = array();
while( $info_lb = $result_lb -> fetch(2))
{
	$data_lb[] = $info_lb;
}


foreach($data_lb as $key => $value)
{
	$serid = $value['ID'];
	$serip = $value['serip'];
	$sersshport = $value['sersshport'];
	$sersshacc = $value['sersshacc'];
	$uploadpath = $value['sersshpath'];

	$password = $value['sersshpwd'];
	$string = substr($password,'0',"-32");
	$string2 = explode("h", $string);
	array_pop($string2); //删除数组中最后一个空元素
	$sersshpwd="";
	for($i=0;$i<count($string2);$i++)
	{
		$sersshpwd.=chr($string2[$i]);
	}
	//0无配置；1待执行配置；2待取消配置；3已生效
	if($value['cfgstatus']==="2")
	{			 //ssh密码解密↑↑↑↑↑↑↑↑↑↑↑↑↑
		//登陆ssh并设置上传文件目录↓↓↓↓↓↓↓↓↓↓↓
		$connection = ssh2_connect($serip, $sersshport);
		@$index = ssh2_auth_password($connection, $sersshacc, $sersshpwd);
		$path = str_replace("\\", "/", $_SERVER["DOCUMENT_ROOT"]);
		$file1 = $path."/none/keepalived.conf";
		$file2 = $path."/none/ospfd.conf";
		$uploadfile1 = $uploadpath."keepalived.conf";
		$uploadfile2 = $uploadpath."ospfd.conf";

		if($index)
		{
            //echo $file1,$file2,$uploadfile1,$uploadfile2;die;
			ssh2_scp_send($connection , $file1 , $uploadfile1); //上传文件 上传成功会对原本件覆盖

			ssh2_scp_send($connection , $file2 , $uploadfile2); //上传文件 上传成功会对原本件覆盖

			ssh2_exec ($connection , 'service keepalived reload'); //执行命令
			//sleep(2);

			ssh2_exec ($connection , 'service zebra reload'); //执行命令
			ssh2_exec ($connection , 'service ospf reload'); //执行命令

			$update_lb = "UPDATE `$table_lb` SET `cfgstatus`='0',`groupid`='' WHERE (`ID`='$serid')";
			// var_dump($update_lb);die;
			$pdo -> exec($update_lb);
            //由于循环中会出现冲突，统一在最后提示执行成功
			//echo "<script>alert('执行配置成功！');location.href='/loadbalance/zxfzjhcz.php';</script>";exit;
		}else
		{
			//ssh登陆失败

			echo"<script src='../js/jquery.js' type='text/javascript'></script>";
			echo"<script src='../js/layer-v3.0.1/layer/layer.js' type='text/javascript'></script>";
			echo"<script>$(document).ready(function(){layer.alert('远程登陆失败！请检查服务器是否开启！',function(){location.href='/loadbalance/zxfzjhcz.php';});})</script>";exit;
		}

	}elseif($value['cfgstatus']==="1")
	{
		$groupid = $value['groupid'];
		$query_group = "SELECT * FROM `$table_lbgroup` WHERE groupid = '$groupid'";
		$result_group = $pdo -> query($query_group);
		$info_group = $result_group -> fetch(2);
		//登陆ssh并设置上传文件目录↓↓↓↓↓↓↓↓↓↓↓
		//$connection = ssh2_connect($serip, $sersshport);
		//$index = ssh2_auth_password($connection, $sersshacc, $sersshpwd);
		$path = str_replace("\\", "/", $_SERVER["DOCUMENT_ROOT"]);
		//$file1 = $path."/none/keepalived.conf";

		//组类型 1主备；2集群
		if($info_group['grouptype']=='1')
		{
			$file2 = $path."/none/ospfd.conf";
			$contents = ''; //每份配置文件的开始
			$contents = "
! Configuration File for keepalived
   global_defs {
  notification_email {
   }
   notification_email_from  287186486@qq.com
   smtp_server mail.domob.cn
   smtp_connect_timeout 30
   router_id LVS
 }";//第一个固定不变

			$FNAT_ip = $value['FNAT_ip'];
			//$fullnat_interface = $arr_lb[$i]['fullnat_interface'];
			$FNAT_iparr = explode(";", $FNAT_ip);//把字符串分割成数组
			$FNAT_ip_contents = "";
			$group_fullip_contents = "";
			foreach ($FNAT_iparr as $FNK => $FNV)
			{
				$FNAT_ip_contents .= "    $FNV \n ";
				$group_fullip_contents .= "      $FNV dev eth1 \n  ";
			}
			$contents.="
	  local_address_group laddr_g1 { \n ";
			$contents.=$FNAT_ip_contents;
			$contents.=" }\n";//本地组名与组IP的拼接


			$contents.="
	  vrrp_instance {$value['vrrp_instance']} {//必须配置 标识开启了vrrp协议
	  state {$value['state']}  #标示备份机为BACKUP  实际无主备之分，实际根据priority  判定切换
	  interface eth0  #绑定网卡的接口，是VIP 绑定在哪个接口上
	  virtual_router_id {$value['virtual_router_id']}
	  priority {$value['priority']}   #MASTER权重要高于BACKUP 比如BACKUP为99
	  advert_int {$value['advert_int']}
	  authentication {
	      auth_type {$value['auth_type']} #主从服务器验证方式
	      auth_pass PASS
	  }\n\n";//拼接lb表 服务器配置第一段到此为止，依然在循环里，拼接其他表数据



			//下面开始拼接vip表的数据
			$query_vip = "SELECT * FROM `$table_vip` WHERE `groupid` = '{$value['groupid']}'";//查询所有组相同的vip数据

			$query_vip_fnat = "SELECT * FROM `$table_vip` WHERE `groupid` = '{$value['groupid']}' AND `lb_kind` = 'FNAT'";//查询所有组相同的vip_fullnat数据
			$query_vip_nat = "SELECT * FROM `$table_vip` WHERE `groupid` = '{$value['groupid']}' AND `lb_kind` = 'NAT'";//查询所有组相同的vip_nat数据

			$result_vip = $pdo -> query($query_vip);//执行查询语句
			$result_vip_fnat = $pdo -> query($query_vip_fnat);//执行查询语句
			$result_vip_nat = $pdo -> query($query_vip_nat);//执行查询语句
			if($result_vip_fnat)
			{
				$liparr=array();
			}

			$arr_vip = array();//把数组清空 避免数据重复
			while($data_vip = $result_vip -> fetch(2))
			{
				$arr_vip[] = $data_vip;//对执行结果进行处理转换成数组
			}

			$vip_ip_contents = "";

			if($arr_vip)
			{
				//只允许有服务器组有对应的vip时才允许添加虚拟IP
				foreach ($arr_vip as $vipkey1 => $vipvalue1)
				{
					$vip_ip = $vipvalue1['virtual_server'];

					$vip_ip_contents .= "      $vip_ip dev eth0 \n ";
				}
				$contents .= "virtual_ipaddress { \n $vip_ip_contents ";

				if($result_vip_fnat -> fetch(2))	//当fnat数组不为空时
				{




					$contents .=  $group_fullip_contents;
				}

				if($result_vip_nat -> fetch(2))
				{

					$nat_ip = $value['nat_ip'];
					$contents .= "      $nat_ip dev eth1 \n";
				}
				//取出组名相同的vip的服务IP
			}



			$contents .= "   }\n}\n\n";
			//以上为virtual_ipaddress 的配置文件信息


			//下面拼接表vip与rs的白色位置信息
			//虚拟IP拼接完成，下面拼接表2数据
			foreach ($arr_vip as $vipkey => $vipvalue)
			{
				$contents .= "
  #虚拟服务器 21端口的配置
virtual_server {$vipvalue['virtual_server']} {$vipvalue['virtual_server_port']} {
    delay_loop {$vipvalue['delay_loop']}            #(每隔10秒查询realserver状态)
    lb_algo {$vipvalue['lb_algo']}              #(lvs 算法)
    lb_kind {$vipvalue['lb_kind']}             #(FULLNAT)
    persistence_timeout {$vipvalue['persistence_timeout']}  #(同一IP的连接60秒内被分配到同一台realserver)
    protocol {$vipvalue['protocol']}            #(用TCP协议检查realserver状态)
    lddr_group_name {$value['local_address_group']}    #local address group
  ";
				if($vipvalue['synflood']=='1')
				{
					$contents .="  syn_proxy \n\n";
				}
				$serverip = $vipvalue['virtual_server'];
				$query_rs = "SELECT * FROM $table_rs WHERE `virtual_server` = '$serverip' ";

				$result_rs = $pdo -> query($query_rs);
				$arr_rs = array();//把数组清空 避免数据重复
				while($data_rs = $result_rs->fetch(2))
				{
					$arr_rs[] = $data_rs;//对执行结果进行处理转换成数组
				}
				$rs_contents = "";
				if($arr_rs)
				{
					//只允许有服务器组有对应的vip时才允许添加虚拟IP
					foreach ($arr_rs as $rskey => $rsvalue)
					{
						$rs_contents .= "

    real_server {$rsvalue['real_server']} {$rsvalue['real_server_port']} {
        weight  {$rsvalue['weight']}
        TCP_CHECK {
            connect_timeout 10
            nb_get_retry 3
            delay_before_retry 3
            connect_port {$rsvalue['real_server_port']}
        }
    }\n";
					}
					$contents .= $rs_contents;


				}
				$contents .= "}";
				//以上配置文件已全部配置完成
			}
//----------------------------------上面的为主备配置-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


		}elseif($info_group['grouptype']=='2') //上面的if是主备 下面开始的是集群
		{
			$contents = '';//每份配置文件的开始

			$contents2 .= "
 hostname lb
password liu2016
log stdout
interface eth0
 ip ospf hello-interval 1
 ip ospf dead-interval 3
interface eth1
interface lo
router ospf \n";

			$contents .= "
 global_defs {
  notification_email {
   }
   notification_email_from  287186486@qq.com
   smtp_server mail.domob.cn
   smtp_connect_timeout 30
   router_id LVS
 }\n";
			$FNAT_ip = $value['FNAT_ip'];
			//$fullnat_interface = $arr_lb[$i]['fullnat_interface'];
			$FNAT_iparr = explode(";", $FNAT_ip);//把字符串分割成数组
			$FNAT_ip_contents = "";
			$group_fullip_contents = "";
			$group_fnat_contents2 = "";
			foreach ($FNAT_iparr as $FNK => $FNV)
			{
				$FNAT_ip_contents .= "      $FNV \n ";
				$group_fullip_contents .= "ip addr add {$FNV}/{$value['eth1ipmask']} dev eth1; ";
				$group_fnat_contents2 .= "network {$FNV}/{$value['eth1ipmask']} area 0.0.0.0 \n";
			}
			$contents.="
	  local_address_group laddr_g1 { \n ";
			$contents.=$FNAT_ip_contents;
			$contents.=" }\n";//本地组名与组IP的拼接


			//下面开始拼接vip表的数据
			$query_vip = "SELECT * FROM `$table_vip` WHERE `groupid` = '{$value['groupid']}'";//查询所有组相同的vip数据

			$query_vip_fnat = "SELECT * FROM `$table_vip` WHERE `groupid` = '{$value['groupid']}' AND `lb_kind` = 'FNAT'";//查询所有组相同的vip_fullnat数据
			$query_vip_nat = "SELECT * FROM `$table_vip` WHERE `groupid` = '{$value['groupid']}' AND `lb_kind` = 'NAT'";//查询所有组相同的vip_nat数据

			$result_vip = $pdo -> query($query_vip);//执行查询语句
			$result_vip_fnat = $pdo -> query($query_vip_fnat);//执行查询语句
			$result_vip_nat = $pdo -> query($query_vip_nat);//执行查询语句
			if($result_vip_fnat)
			{
				$liparr=array();
			}

			$arr_vip = array();//把数组清空 避免数据重复
			while($data_vip = $result_vip -> fetch(2))
			{
				$arr_vip[] = $data_vip;//对执行结果进行处理转换成数组
			}

			$vip_ip_contents = "  quorum_up \"";

			if($arr_vip)
			{
				//只允许有服务器组有对应的vip时才允许添加虚拟IP
				foreach ($arr_vip as $vipkey1 => $vipvalue1)
				{
					$vip_ip = $vipvalue1['virtual_server'];

					$vip_ip_contents .= "ip addr add {$vip_ip}/32 dev eth0;";
					$contents2 .= "network {$vip_ip}/24 area 0.0.0.0 \n";
				}

				if($result_vip_fnat -> fetch(2))	//当fnat数组不为空时
				{
					$vip_ip_contents .= $group_fullip_contents;
					$contents2 .= $group_fnat_contents2;
				}

				if($result_vip_nat -> fetch(2))
				{

					$nat_ip = $value['nat_ip'];
					$vip_ip_contents .= "ip addr add {$nat_ip}/{$value['eth1ipmask']} dev eth1;";
					$contents2 .= "network {$nat_ip}/{$value['eth1ipmask']} area 0.0.0.0 \n";
				}


			}



			//$contents .= "   }\n}\n\n";
			//以上为virtual_ipaddress 的配置文件信息


			//下面拼接表vip与rs的白色位置信息
			//虚拟IP拼接完成，下面拼接表2数据
			foreach ($arr_vip as $vipkey => $vipvalue)
			{
				$contents .= "
  #虚拟服务器 21端口的配置
virtual_server {$vipvalue['virtual_server']} {$vipvalue['virtual_server_port']} {
    delay_loop {$vipvalue['delay_loop']}            #(每隔10秒查询realserver状态)
    lb_algo {$vipvalue['lb_algo']}              #(lvs 算法)
    lb_kind {$vipvalue['lb_kind']}             #(FULLNAT)
    persistence_timeout {$vipvalue['persistence_timeout']}  #(同一IP的连接60秒内被分配到同一台realserver)
    protocol {$vipvalue['protocol']}            #(用TCP协议检查realserver状态)
    lddr_group_name {$value['local_address_group']}    #local address group
	alpha
	quorum 1
	hysteresis 0
  ";
				$contents .= $vip_ip_contents;
				$contents .= "\"";

				if($vipvalue['synflood']=='1')
				{
					$contents .="\n    syn_proxy \n";
				}

				$serverip = $vipvalue['virtual_server'];
				$query_rs = "SELECT * FROM $table_rs WHERE `virtual_server` = '$serverip' ";

				$result_rs = $pdo -> query($query_rs);
				$arr_rs = array();//把数组清空 避免数据重复
				while($data_rs = $result_rs->fetch(2))
				{
					$arr_rs[] = $data_rs;//对执行结果进行处理转换成数组
				}
				$rs_contents = "";
				if($arr_rs)
				{
					//只允许有服务器组有对应的vip时才允许添加虚拟IP
					foreach ($arr_rs as $rskey => $rsvalue)
					{
						$rs_contents .= "

    real_server {$rsvalue['real_server']} {$rsvalue['real_server_port']} {
        weight  {$rsvalue['weight']}
        TCP_CHECK {
            connect_timeout 10
            nb_get_retry 3
            delay_before_retry 3
            connect_port {$rsvalue['real_server_port']}
        }
    }\n";
					}
					$contents .= $rs_contents;


				}
				$contents .= "}";

				//以上配置文件已全部配置完成


			}
			$contents2 .= "line vty";

		}//esif的结束


		/*$dir = $value['serip'];
        $path = str_replace("/", "\\", $_SERVER["DOCUMENT_ROOT"]);
        $dir1 = "$path\\$dir";
        $file = $dir1 . "\\sshconfigkeepalived.conf";
        $file2 = $dir1 . "\\ospfd.conf";*/
		$dir = $value['serip'];
		$path = str_replace("\\", "/", $_SERVER["DOCUMENT_ROOT"]);
		$dir1 = "$path/$dir";
		$file = $dir1 . "/sshconfigkeepalived.conf";
		$file2 = $dir1 . "/ospfd.conf";

		if(!$contents2)
		{
			$contents2 = "
hostname lb
password liu2016
log stdout
interface eth0
interface eth1
interface lo
line vty";
		}

		if(is_dir($dir1))
		{
			unlink($file);  //如果目录存在则删除底下文件
			unlink($file2);
			rmdir($dir1);//删除目录
		}

		if(!mkdir($dir1,0777)){
			echo"<script src='../js/jquery.js' type='text/javascript'></script>";
			echo"<script src='../js/layer-v3.0.1/layer/layer.js' type='text/javascript'></script>";
			echo"<script>$(document).ready(function(){layer.alert('创建目录失败！',function(){location.href='/loadbalance/zxfzjhcz.php';});})</script>";exit;
		}  //生成目录

		if(!file_put_contents($file,$contents)){
			echo"<script src='../js/jquery.js' type='text/javascript'></script>";
			echo"<script src='../js/layer-v3.0.1/layer/layer.js' type='text/javascript'></script>";
			echo"<script>$(document).ready(function(){layer.alert('生成文件失败',function(){location.href='/loadbalance/zxfzjhcz.php';});})</script>";exit;

		} //生成文件
		file_put_contents($file2,$contents2); //生成文件


		//下面上传文件
		@$connection = ssh2_connect($serip, $sersshport);
		@$index = ssh2_auth_password($connection, $sersshacc, $sersshpwd);
		$uploadfile1 = $uploadpath."keepalived.conf";
		$uploadfile2 = $uploadpath."ospfd.conf";

		if($index)
		{
			if(!ssh2_scp_send($connection , $file , $uploadfile1)){
				echo"<script src='../js/jquery.js' type='text/javascript'></script>";
				echo"<script src='../js/layer-v3.0.1/layer/layer.js' type='text/javascript'></script>";
				echo"<script>$(document).ready(function(){layer.alert('上传文件失败！',function(){location.href='/loadbalance/zxfzjhcz.php';});})</script>";exit;

			} //上传文件 上传成功会对原本件覆盖
//echo $uploadfile1,$connection,$file,$serip;die;
			ssh2_scp_send($connection , $file2 , $uploadfile2); //上传文件 上传成功会对原本件覆盖
			ssh2_exec ($connection , 'service keepalived reload'); //执行命令
			ssh2_exec ($connection , 'service zebra reload'); //执行命令
			ssh2_exec ($connection , 'service ospf reload'); //执行命令

			//在上传成功后更新LVS服务器数据信息
			$update_lb = "UPDATE `$table_lb` SET `cfgefflasttime`=now(), `cfgstatus`='3' WHERE (`ID`='$serid')";
			$pdo -> exec($update_lb);
			$sql_oper_add = "INSERT INTO `$table_oper` (`optype`, `opname`, `optime`, `result`) VALUES ('2', '$user', now(), '执行配置命令')";
			$pdo -> exec($sql_oper_add);
			//echo "<script>alert('生成配置文件并上传成功!点击返回！');location.href='/loadbalance/zxfzjhcz.php';</script>";exit;
		}else
		{
			echo"<script src='../js/jquery.js' type='text/javascript'></script>";
			echo"<script src='../js/layer-v3.0.1/layer/layer.js' type='text/javascript'></script>";
			echo"<script>$(document).ready(function(){layer.alert('远程登陆失败！请检查服务器是否开启！',function(){location.href='/loadbalance/zxfzjhcz.php';});})</script>";exit;
		}
	}//if $value['cfgstatus']==="1"
}//foreach
//echo "<script>alert('生成配置文件并上传成功!点击返回！');location.href='/loadbalance/zxfzjhcz.php';</script>";exit;
echo"<script src='../js/jquery.js' type='text/javascript'></script>";
echo"<script src='../js/layer-v3.0.1/layer/layer.js' type='text/javascript'></script>";
echo"<script>$(document).ready(function(){layer.alert('执行操作成功！',function(){location.href='/loadbalance/zxfzjhcz.php';});})</script>";exit;