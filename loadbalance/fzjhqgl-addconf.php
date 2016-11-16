<?php
/**
 * @Author: Rnb-3Ds
 * @Date:   2016-10-08 10:47:59
 * @Last Modified by:   yi27
 * @Last Modified time: 2016-11-10 21:46:21
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

		$sername = $_REQUEST['sername'] ? trim($_REQUEST['sername']) : '';
		$serip = $_REQUEST['serip'] ? trim($_REQUEST['serip']) : '';
		$sersshport = $_REQUEST['sersshport'] ? trim($_REQUEST['sersshport']) : 22;
		$sersshacc = $_REQUEST['sersshacc'] ? trim($_REQUEST['sersshacc']) : '';
        $sersshpwd = $_REQUEST['sersshpwd'] ? trim($_REQUEST['sersshpwd']) : '';
        $eth0ip = $_REQUEST['eth0ip'] ? trim($_REQUEST['eth0ip']) : '';
        $eth0ipmask = $_REQUEST['eth0ipmask'] ? trim($_REQUEST['eth0ipmask']) : '';
        $eth1ip = $_REQUEST['eth1ip'] ? trim($_REQUEST['eth1ip']) : '';
        $eth1ipmask = $_REQUEST['eth1ipmask'] ? trim($_REQUEST['eth1ipmask']) : '';
        $nat_ip = $_REQUEST['nat_ip'] ? trim($_REQUEST['nat_ip']) : '';
        $FNAT_ip = $_REQUEST['FNAT_ip'] ? trim($_REQUEST['FNAT_ip']) : '';

    //以上为从前台接收的表单数据
    /*****************************************************************************/
    //下面为目前自定义数据
    $interface = "eth0";  //通讯接口
    $nat_interface = "eth1"; //IP登记-eth1(NAT模式网关)接口
    $local_address_group = 'laddr_g1'; //IP登记-eth1(全NAT模式本地地址)组名称
    $FNAT_interface = 'eth1'; //IP登记-eth1(全NAT模式本地地址)接口
    $sersshpath = "/etc/keepalived/"; //上传文件路径
    $auth_type =  'PASS'; //组验证方式
    $advert_int = '1';  //组健康检查时间
    //暂把操作人写成admin

		//判断前台数据接收
      $password = "";
      for($i=0;$i<strlen($sersshpwd);$i++)
      {
        $password.=ord($sersshpwd[$i])."h"; //对密码进行第一层加密 转化为ASCII码后再拼上一个分隔符h
      }
        $password.=md5(md5("bsm")); //对密码进行第二层加密，转化完成后再拼上双md5加密的字符

    /*  //对eth0的子网掩关进行二进制转换↓↓↓
      $eth0ipmaskarr = explode(".",$eth0ipmask);
      $eth0bin = 0;
      foreach($eth0ipmaskarr as $eth0v)
      {
        $v0=substr_count((decbin($eth0v)),"1");
        $eth0bin+=$v0;
      }

      //对eth1的子网掩关进行二进制转换↓↓↓
      $eth1ipmaskarr = explode(".",$eth1ipmask);
      $eth1bin = 0;
      foreach($eth1ipmaskarr as $eth1v)
      {
        $v1=substr_count((decbin($eth1v)),"1");
        $eth1bin+=$v1;
      }*/

		if(!$sername||!$serip||!$sersshport||!$sersshacc||!$sersshpwd||!$eth0ip||!$eth0ipmask||!$eth1ip||!$eth1ipmask||!$nat_ip||!$FNAT_ip)
		{ //前台有必填数据为空的时候返回
			 echo"<script>alert('输入有误！请检查后输入');history.go(-1);</script>";exit;//接收失败返回上一页
		}

    //验证ssh2能否登录，若不能则不保存
      @$connection = ssh2_connect($serip, $sersshport);
      @$index = ssh2_auth_password($connection, $sersshacc, $sersshpwd);
      if(!$index)
      {
          echo"<script src='../js/jquery.js' type='text/javascript'></script>";
          echo"<script src='../js/layer-v3.0.1/layer/layer.js' type='text/javascript'></script>";
          echo"<script>$(document).ready(function(){layer.alert('远程登录失败，请确认账号密码ip等信息是否出错！',function(){history.go(-1);});})</script>";exit;//检测输入的账号密码能否远程登录，若不能则返回
      }

      //$sql是插入语句，$query是查询语句
      $query_lb_sername = "SELECT ID FROM `$table_lb` WHERE sername = '$sername'";
      $select_lb_sername = $pdo->query($query_lb_sername);
      if($select_lb_sername->fetch(2))
      {
          echo"<script src='../js/jquery.js' type='text/javascript'></script>";
          echo"<script src='../js/layer-v3.0.1/layer/layer.js' type='text/javascript'></script>";
          echo"<script>$(document).ready(function(){layer.alert('服务器名已被占用！请检查后输入！',function(){history.go(-1);});})</script>";exit;//检测服务器名是否重复
      }else
      {
          $sql_add = "INSERT INTO `$table_lb` (`ID`, `sername`, `serip`, `sersshport`, `sersshacc`, `sersshpwd`, `nat_ip`, `FNAT_ip`, `eth0ip`, `eth0ipmask`, `eth1ip`, `eth1ipmask`, `cfgstatus`, `optlasttime`, `optlastname`) VALUES (DEFAULT, '$sername', '$serip', '$sersshport', '$sersshacc', '$password', '$nat_ip', '$FNAT_ip', '$eth0ip', '$eth0ipmask', '$eth1ip', '$eth1ipmask',  '0', now(), '$user')";//查找同一组ID里面有没有MASTER角色的语句 在组ID默认为1的情况下也相当于是查找表是否为空
          $pdo->exec($sql_add); //插入数据库

          $sql_oper_add = "INSERT INTO `$table_oper` (`optype`, `opname`, `optime`, `result`) VALUES ('1', '$user', now(), '新增LVS服务器')";

          $pdo -> exec($sql_oper_add);
          echo"<script src='../js/jquery.js' type='text/javascript'></script>";
          echo"<script src='../js/layer-v3.0.1/layer/layer.js' type='text/javascript'></script>";
          echo"<script>$(document).ready(function(){layer.alert('负载均衡器新增成功！',function(){location.href='fzjhqgl.php';});})</script>";

          /*echo "<meta http-equiv='Refresh' content='0;URL=fzjhqgl.php'>";exit;*/
        }







