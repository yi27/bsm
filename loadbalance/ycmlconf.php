<?php
/**
 * @Author: Rnb-3Ds
 * @Date:   2016-11-03 20:35:16
 * @Last Modified by:   Rnb-3Ds
 * @Last Modified time: 2016-11-16 19:58:44
 */

header('Content-Type:text/html;charset=UTF-8');
include_once "connectdb.php";
$sessionid = empty($_COOKIE['bs_sessionid']) ? '' : $_COOKIE['bs_sessionid'];
if (!$sessionid)
{
    $user = "guest";
} else
{
    $query_session = "SELECT `userid` FROM `$table_session` WHERE `sessionid` = '$sessionid'";
    $result_session = $pdo->query($query_session);
    $info_session = $result_session->fetch(3);
    $userid = $info_session[0];
    $query_user = "SELECT `alias` FROM `$table_user` WHERE `userid`='$userid'";
    $result_user = $pdo->query($query_user);
    $info_user = $result_user->fetch(2);
    $user = $info_user['alias'];

}

$serid = empty($_POST['param']) ?'': trim($_POST['param']);
$ordertype = $_POST['param1'] ? trim($_POST['param1']) : '';
$editcfg = empty($_POST['param2']) ? '' : trim($_POST['param2']);
$query_lb = "SELECT `serip`,`sersshacc`,`sersshpwd`,`sersshport` FROM `$table_lb` WHERE `ID` = '$serid'";
$result_lb = $pdo->query($query_lb);
$info_lb = $result_lb->fetch(2);
$query_reorder = "SELECT `ordertype` FROM `$table_reorder` WHERE `editcfg` = '1'";
$result_reorder = $pdo -> query($query_reorder);
$data_reorder = array();
while ($info_reorder = $result_reorder -> fetch(2))
{
    $data_reorder[] = $info_reorder;
}
// 判断用户命令是否存在
if(!in_array($ordertype,$data_reorder)){
    echo 4;exit;
}
$data_ordertype = array();
foreach ($data_reorder as $k=> $v)
{
    $data_ordertype[] = $v['ordertype'];
}
if(!in_array($ordertype,$data_ordertype))
{
    echo "2";
    exit;
}
$reg = "/(?:;|>|&|\|\|)+/";

if (!$serid)
{
    echo "3";
    exit;
}

if (preg_match($reg, $editcfg))
{
    echo "1";

} else {
    $orderstring = $ordertype . " " . $editcfg;
    $serip = $info_lb['serip'];
    $sersshport = $info_lb['sersshport'];
    $sersshacc = $info_lb['sersshacc'];
    $password = $info_lb['sersshpwd'];
    $string = substr($password, '0', "-32");
    $string2 = explode("h", $string);
    array_pop($string2); //删除数组中最后一个空元素
    $sersshpwd = "";
    for ($i = 0; $i < count($string2); $i++) {
        $sersshpwd .= chr($string2[$i]);
    }
    if (!is_dir("../AuxiliaryCommand")) {
        mkdir("../AuxiliaryCommand");
    }

    $localfile = '/var/www/html/zabbix/AuxiliaryCommand/order.php';//本地生成目录
    $recvfile = '/var/www/html/zabbix/AuxiliaryCommand/result.php';//本地接收目录
    $remotefile = "/var/www/html/zabbix/loadbalance/order.php";//需要放在用户安装zabbix的目录下
    $current = "<?php
            \$arr = array();
            \$what = system('$orderstring');
           ";

    file_put_contents($localfile, $current);//生成本地命令文件

    @$connection = ssh2_connect($serip, $sersshport);
    @$index = ssh2_auth_password($connection, $sersshacc, $sersshpwd);
    if(!$index)
    {
        echo "2";exit;
    }

    ssh2_scp_send($connection, $localfile, $remotefile);//发送命令文件

    ssh2_exec($connection, 'php -f /var/www/html/zabbix/loadbalance/order.php > /var/www/html/zabbix/loadbalance/result.php');//远程执行命令-》linux下执行php文件

    ssh2_scp_recv($connection, '/var/www/html/zabbix/loadbalance/result.php', $recvfile);

    $reusltstring = file_get_contents($recvfile);
    //echo 1000211;die;
    //echo $reusltstring;die;
    echo json_encode($reusltstring);
    // 记录此次执行到数据库
    $sql_oper_add = "INSERT INTO `table_oper` (`optype`, `opname`, `optime`, `result`) VALUES ('1', '$user', now(), '执行辅助命令')";

    $pdo -> exec($sql_oper_add);
}