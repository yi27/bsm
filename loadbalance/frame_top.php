<?php
          header('Content-Type:text/html;charset=UTF-8');
          include_once "connectdb.php";
          $sessionid = empty($_COOKIE['bs_sessionid'])?'':$_COOKIE['bs_sessionid'];

            if(!$sessionid){
				//echo"<script>$(document).ready(function(){layer.alert('请登录！',function(){location.href='index.php';});})</script>";
				$user = 'guest';
            }else{
                  $query_session = "SELECT `userid` FROM sessions WHERE `sessionid` = '$sessionid'";
                  $result_session = $pdo -> query($query_session);
                  $info_session = $result_session -> fetch(3);
                  $userid = $info_session[0];
                  $query_user = "SELECT `alias` FROM users WHERE `userid`='$userid'";
                  $result_user = $pdo -> query($query_user);
                  $info_user = $result_user -> fetch(2);
                  $user = $info_user['alias'];
            }

			if($user=='guest'){
				echo"<script>window.parent.frames.location.href='/index.php'</script>";
			}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
     <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script type="text/javascript" src="http://netdna.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
        <link href="../styles/blue-theme.css" rel="stylesheet" type="text/css">
    <link href="../styles/bootstrap.css" rel="stylesheet" type="text/css">

    <link href="../styles/style.css" rel="stylesheet" type="text/css">
    <style>
@font-face {
  font-family: 'Glyphicons Halflings';
  src: url('../fonts/glyphicons-halflings-regular.eot');
  src: url('../fonts/glyphicons-halflings-regular.eot?#iefix') format('embedded-opentype'), url('../fonts/glyphicons-halflings-regular.woff') format('woff'), url('../fonts/glyphicons-halflings-regular.ttf') format('truetype'), url('../fonts/glyphicons-halflings-regular.svg#glyphicons_halflingsregular') format('svg');
}
.glyphicon {
  position: relative;
  top: 1px;
  display: inline-block;
  font-family: 'Glyphicons Halflings';
  -webkit-font-smoothing: antialiased;
  font-style: normal;
  font-weight: normal;
  line-height: 1;
  -moz-osx-font-smoothing: grayscale;
}
</style>
    </head>
    <body><header role="banner">
    <div class="nav" role="navigation">
    <div class="top-nav-container" id="mmenu">
    	<a href="/bsm.php?action=dashboard.view" target="_parent"><div class="logo"></div></a>
    <ul class="top-nav">
    	<li class="" id="jkt" onclick="MMenu.mouseOver('view');"><a tabindex="0">监控台</a></li>
            <?php if($user=='guest'){}else{?>
    	<li id="ywgl"  class=""><a tabindex="0">运维管理</a></li>
            <?php }?>
    	<li id="fzjhgl"  class="selected"><a tabindex="0">负载均衡管理</a></li>
    	<li id="cmdb"  class=""><a tabindex="0">CMDB</a></li>
    	<li id="tjfx"  class=""><a tabindex="0">统计分析</a></li>
            <?php if($user=='guest'){}else{?>
    	<li id="xtgl"  class=""><a tabindex="0">系统管理</a></li>
            <?php }?>
    </ul>
    <ul class="top-nav-icons">
<!--    <li>
	<form method="get" action="http://www.baidu.com/baidu" target="_blank"   accept-charset="utf-8">
	    <input type="hidden" id="sid" name="sid" value="0b082d620d897ec5">
	    <input type="hidden" id="form_refresh" name="form_refresh" value="1">
	    <input type="text" id="search" name="word"  value="" maxlength="255" autocomplete="off" class="search" style="height:20px">
	    <button type="submit" class="btn-search" >&nbsp;</button>
	</form>
    </li>-->

<!--    <li>
		<a class="top-nav-signout" title="Sign out" href="logout.php" target="_parent">&nbsp;</a>
    </li>-->
    </ul>
    </div>
    <div class="top-subnav-container" onmouseover="MMenu.submenu_mouseOver();" onmouseout="MMenu.mouseOut();">
    <ul class="top-subnav" id="_jkt" style="display: none;">
	    <li style="font-size:12px;padding-bottom:10px"><a class="selected" href="/bsm.php?action=dashboard.view&amp;ddreset=1" target="_parent">概要展示</a></li>
	    <li style="font-size:12px;padding-bottom:10px"><a href="/screens.php?ddreset=1" target="_parent">大屏展示</a></li>
	    <li style="font-size:12px;padding-bottom:10px"><a href="/overview.php?ddreset=1" target="_parent">告警事件概览列表</a></li>
	    <li style="font-size:12px;padding-bottom:10px"><a href="/latest.php?ddreset=1" target="_parent">维度-监控项当前状态</a></li>
	    <li style="font-size:12px;padding-bottom:10px"><a href="/tr_status.php?ddreset=1" target="_parent">维度-触发器事件</a></li>
	    <li style="font-size:12px;padding-bottom:10px"><a href="/events.php?ddreset=1" target="_parent">维度-监控项事件</a></li>
	    <li style="font-size:12px;padding-bottom:10px"><a href="/charts.php?ddreset=1" target="_parent">指标趋势图表展示</a></li>
	    <li style="font-size:12px;padding-bottom:10px"><a href="/bsm.php?action=map.view&amp;ddreset=1">拓扑图展示与管理</a></li>
	    <li style="font-size:12px;padding-bottom:10px"><a href="/bsm.php?action=web.view&amp;ddreset=1">WEB主动拨测状态总览</a></li>
	    <li style="font-size:12px;padding-bottom:10px"><a href="/srv_status.php?ddreset=1" target="_parent">IT服务SLA报告</a></li>
    </ul>

    <ul class="top-subnav" id="_ywgl" style="display: none;">
	    <li style="font-size:12px;padding-bottom:10px"><a href="/hostgroups.php?ddreset=1" target="_parent">单元分组管理</a></li>
	    <li style="font-size:12px;padding-bottom:10px"><a href="/templates.php?ddreset=1" target="_parent">监控模板管理</a></li>
	    <li style="font-size:12px;padding-bottom:10px"><a href="/hosts.php?ddreset=1" target="_parent">监控单元与WEB拨测管理</a></li>
	    <li style="font-size:12px;padding-bottom:10px"><a href="/maintenance.php?ddreset=1" target="_parent">单元监控周期排除定义</a></li>
	    <li style="font-size:12px;padding-bottom:10px"><a href="/actionconf.php?ddreset=1" target="_parent">报警动作管理</a></li>
	    <li style="font-size:12px;padding-bottom:10px"><a href="/discoveryconf.php?ddreset=1" target="_parent">服务拨测管理</a></li>
	    <li style="font-size:12px;padding-bottom:10px"><a href="/services.php?ddreset=1" target="_parent">IT服务SLA定义</a></li>
    </ul>

    <ul class="top-subnav" id="_fzjhgl" style="display: block;">
    	<li style="font-size:12px;padding-bottom:10px"><a href="/loadbalance/iframset-main.html?ddreset=1" target="_top" >负载均衡器配置</a></li>
    </ul>

    <ul class="top-subnav" id="_cmdb" style="display: none;">
    	<li style="font-size:12px;padding-bottom:10px"><a href="/hostinventoriesoverview.php?ddreset=1" target="_parent">资产记录概览</a></li>
    	<li style="font-size:12px;padding-bottom:10px"><a href="/hostinventories.php?ddreset=1" target="_parent">资产明细数据查看</a></li>
    </ul>

    <ul class="top-subnav" id="_tjfx" style="display: none;">
	<li style="font-size:12px;padding-bottom:10px"><a href="/bsm.php?action=report.status&amp;ddreset=1" target="_parent">当前系统状态总览</a></li>
	<li style="font-size:12px;padding-bottom:10px"><a href="/queue.php?ddreset=1" target="_parent">系统队列</a></li>
	<li style="font-size:12px;padding-bottom:10px"><a href="/report4.php?ddreset=1" target="_parent">报警途径分析</a></li>
	<li style="font-size:12px;padding-bottom:10px"><a href="/auditacts.php?ddreset=1" target="_parent">系统动作日志</a></li>
	<li style="font-size:12px;padding-bottom:10px"><a href="/auditlogs.php?ddreset=1" target="_parent">系统操作报告</a></li>
    </ul>

    <ul class="top-subnav" id="_xtgl" style="display: none;">
	    <li style="font-size:12px;padding-bottom:10px"><a href="/adm.gui.php?ddreset=1" target="_parent">系统常规管理</a></li>
	    <li style="font-size:12px;padding-bottom:10px"><a href="/bsm.php?action=proxy.list&amp;ddreset=1" target="_parent">代理程序管理</a></li>
                <li><a href="/authentication.php?ddreset=1" target="_parent">Authentication</a></li>
	    <li style="font-size:12px;padding-bottom:10px"><a href="/usergrps.php?ddreset=1" target="_parent">用户角色定义</a></li>
	    <li style="font-size:12px;padding-bottom:10px"><a href="/users.php?ddreset=1" target="_parent">用户管理</a></li>
	    <li style="font-size:12px;padding-bottom:10px"><a href="/bsm.php?action=mediatype.list&amp;ddreset=1" target="_parent">报警途径定义</a></li>
	    <li style="font-size:12px;padding-bottom:10px"><a href="/bsm.php?action=script.list&amp;ddreset=1" target="_parent">通用脚本定义</a></li>
    </ul>

    </div></div></header></body>
</html>
<script src="../js/jquery.js" type="text/javascript"></script>
<script src="../js/common.js" type="text/javascript"></script>
<script src="../js/main.js" type="text/javascript"></script>

<script>

	$("#jkt").click(function() {
		$("#fzjhgl").attr('class','');
                         $("#ywgl").attr('class','');
                         $("#cmdb").attr('class','');
                         $("#tjfx").attr('class','');
                         $("#xtgl").attr('class','');
                         $("#jkt").attr('class','selected');

                         $("#_jkt").css({
                                     display: 'block',
                         });
		$("#_ywgl").css({
			display: 'none',
		});
		$("#_fzjhgl").css({
			display: 'none',
		});
		$("#_cmdb").css({
			display: 'none',
		});
		$("#_tjfx").css({
			display: 'none',
		});
		$("#_xtgl").css({
			display: 'none',
		});
	});
	$("#ywgl").click(function() {
                         $("#jkt").attr('class','');
                         $("#fzjhgl").attr('class','');
                         $("#cmdb").attr('class','');
                         $("#tjfx").attr('class','');
                         $("#xtgl").attr('class','');
                         $("#ywgl").attr('class','selected');//显示三角形

                         $("#_ywgl").css({
                                     display: 'block',
                         });
                         $("#_jkt").css({
			display: 'none',
		});
		$("#_fzjhgl").css({
			display: 'none',
		});
		$("#_cmdb").css({
			display: 'none',
		});
		$("#_tjfx").css({
			display: 'none',
		});
		$("#_xtgl").css({
			display: 'none',
		});
	});
	$("#fzjhgl").click(function() {
                         $("#jkt").attr('class','');
                         $("#ywgl").attr('class','');
                         $("#cmdb").attr('class','');
                         $("#tjfx").attr('class','');
                         $("#xtgl").attr('class','');
                         $("#fzjhgl").attr('class','selected');

		$("#_fzjhgl").css({
			display: 'block',
		});
		$("#_ywgl").css({
			display: 'none',
		});
		$("#_jkt").css({
			display: 'none',
		});
		$("#_cmdb").css({
			display: 'none',
		});
		$("#_tjfx").css({
			display: 'none',
		});
		$("#_xtgl").css({
			display: 'none',
		});
	});
	$("#cmdb").click(function() {
                         $("#jkt").attr('class','');
                         $("#ywgl").attr('class','');
                         $("#fzjhgl").attr('class','');
                         $("#tjfx").attr('class','');
                         $("#xtgl").attr('class','');
                         $("#cmdb").attr('class','selected');

                         $("#_cmdb").css({
			display: 'block',
		});
		$("#_ywgl").css({
			display: 'none',
		});
		$("#_fzjhgl").css({
			display: 'none',
		});
		$("#_jkt").css({
			display: 'none',
		});
		$("#_tjfx").css({
			display: 'none',
		});
		$("#_xtgl").css({
			display: 'none',
		});
	});
	$("#tjfx").click(function() {
                         $("#jkt").attr('class','');
                         $("#ywgl").attr('class','');
                         $("#cmdb").attr('class','');
                         $("#fzjhgl").attr('class','');
                         $("#xtgl").attr('class','');
                         $("#tjfx").attr('class','selected');

                         $("#_tjfx").css({
			display: 'block',
		});
		$("#_ywgl").css({
			display: 'none',
		});
		$("#_fzjhgl").css({
			display: 'none',
		});
		$("#_cmdb").css({
			display: 'none',
		});
		$("#_jkt").css({
			display: 'none',
		});
		$("#_xtgl").css({
			display: 'none',
		});
	});
	$("#xtgl").click(function() {
                         // 三角形指向
                         $("#jkt").attr('class','');
                         $("#ywgl").attr('class','');
                         $("#cmdb").attr('class','');
                         $("#tjfx").attr('class','');
                         $("#fzjhgl").attr('class','');
                         $("#xtgl").attr('class','selected');
                         // 切换模块
		$("#_xtgl").css({
			display: 'block',
		});
		$("#_ywgl").css({
			display: 'none',
		});
		$("#_fzjhgl").css({
			display: 'none',
		});
		$("#_cmdb").css({
			display: 'none',
		});
		$("#_tjfx").css({
			display: 'none',
		});
		$("#_jkt").css({
			display: 'none',
		});
	});

$(function(){

                        // alert(1);


                         $("#_jkt").css({
			display: 'none',
		});
		$("#_ywgl").css({
			display: 'none',
		});
		$("#_fzjhgl").css({
			display: 'block',
		});
		$("#_cmdb").css({
			display: 'none',
		});
		$("#_tjfx").css({
			display: 'none',
		});
		$("#_xtgl").css({
			display: 'none',
		});


})
</script>