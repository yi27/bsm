<?php
/**
 * @Author: Rnb-3Ds
 * @Date:   2016-10-28 11:36:34
 * @Last Modified by:   Rnb-3Ds
 * @Last Modified time: 2016-10-28 15:16:09
 */

header('Content-Type:text/html;charset=UTF-8');
		include_once "connectdb.php";


      $serid = $_POST['param1'];

      $query_lb = "SELECT `serip`,`sersshport`,`sersshacc`,`sersshpwd` FROM $table_lb WHERE ID = '$serid'";
      $result_lb = $pdo -> query($query_lb);

      $data_lb = $result_lb -> fetch(2);

      		//远程登录
      		$serip = $data_lb['serip'];
			$sersshport = $data_lb['sersshport'];
			$sersshacc = $data_lb['sersshacc'];

      		$password = $data_lb['sersshpwd'];
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
		    	echo 1;
		    }else
		    {
		    	echo 0;
		    }


