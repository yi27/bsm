<?php
/**
 * @Author: Rnb-3Ds
 * @Date:   2016-11-02 00:42:41
 * @Last Modified by:   Rnb-3Ds
 * @Last Modified time: 2016-11-02 10:49:03
 */

header('Content-Type:text/html;charset=UTF-8');
		include_once "connectdb.php";

  	    $serid = $_REQUEST['serid'] ? trim($_REQUEST['serid']) : '';
  	    $query_lb = "SELECT * FROM `$table_lb` WHERE `ID` = '$serid'";
  	    $result_lb = $pdo -> query($query_lb);
  	    $info_lb = $result_lb -> fetch(2);
 	    $password = $info_lb['sersshpwd'];
		$string = substr($password,'0',"-32");
		//var_dump($string);die;
		$string2 = explode("h", $string);
		array_pop($string2);

		$sersshpwd="";
		for($i=0;$i<count($string2);$i++)
		{
			$sersshpwd.=chr($string2[$i]);
		}
		$info_lb['sersshpwd'] = $sersshpwd;

        include_once "fzjhqgl-xg.html";