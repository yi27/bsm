<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/15
 * Time: 16:59
 */

include_once "../include/classes/core/CSession.php";
    $CSession = new CSession;
    $CSession->destroy();
    unset($_COOKIE['bs_sessionid']);
    var_dump($_REQUEST);
$del_session = "DELETE FROM `sessions` WHERE status='1' AND userid='.bs_dbstr($session['userid'])";
DBexecute('UPDATE sessions SET status='1' WHERE sessionid='.bs_dbstr($sessionId));*/