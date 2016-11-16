<?php
/**
 * @Author: Rnb-3Ds
 * @Date:   2016-10-28 16:06:10
 * @Last Modified by:   yi27
 * @Last Modified time: 2016-11-10 21:46:13
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

      @$serid = $_POST['param'];
      if(!$serid){
      		echo "3";exit;
      }
      $query_lb = "SELECT `serip`,`sersshport`,`sersshacc`,`sersshpwd`,`sersshpath` FROM $table_lb WHERE ID = '$serid'";
      $result_lb = $pdo -> query($query_lb);

      $data_lb = $result_lb -> fetch(2);

      		//远程登录
      $serip = $data_lb['serip'];
			$sersshport = $data_lb['sersshport'];
			$sersshacc = $data_lb['sersshacc'];
      		$password = $data_lb['sersshpwd'];
      		$uploadpath = $data_lb['sersshpath'];
			$string = substr($password,'0',"-32");
			$string2 = explode("h", $string);
			$sersshpwd="";
			//密码转换
			for($i=0;$i<count($string2);$i++)
			{
				$sersshpwd.=chr($string2[$i]);
			}
			$connection = @ssh2_connect($serip, $sersshport);
		    $index = @ssh2_auth_password($connection, $sersshacc, $sersshpwd);
		    if($index)
		    {
		    	$path = str_replace("/", "\\", $_SERVER["DOCUMENT_ROOT"]);
		      	$file1 = $path."/none/keepalived.conf";
		      	$file2 = $path."/none/ospfd.conf";
		      	$uploadfile1 = $uploadpath."keepalived.conf";
		      	$uploadfile2 = $uploadpath."ospfd.conf";


      	  		ssh2_scp_send($connection , $file1 , $uploadfile1); //上传文件 上传成功会对原本件覆盖
      	  		ssh2_scp_send($connection , $file2 , $uploadfile2); //上传文件 上传成功会对原本件覆盖
          		ssh2_exec ($connection , 'service keepalived reload'); //执行命令
          		//sleep(2);
          		ssh2_exec ($connection , 'service zebra reload'); //执行命令
          		ssh2_exec ($connection , 'service ospf reload'); //执行命令


		    }
		    $del_lb = "DELETE FROM $table_lb WHERE `ID` = '$serid'";
          	if($pdo -> exec($del_lb))
          	{
          		$sql_oper_add = "INSERT INTO `$table_oper` (`optype`, `opname`, `optime`, `result`) VALUES ('1', '$user', now(), '删除LVS服务器')";
          		$pdo -> exec($sql_oper_add);
          		echo "1";

          	}else
          	{
          		echo "2";
          	}
