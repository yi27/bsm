<?php
/**
 * @Author: Rnb-3Ds
 * @Date:   2016-10-30 17:51:55
 * @Last Modified by:   yi27
 * @Last Modified time: 2016-11-07 09:53:15
 */

      header('Content-Type:text/html;charset=UTF-8');
      include_once "connectdb.php";

      // 拼接搜索where条件,post接收
      //1,组id 2,组名称 3，组类型 （组备，集群）4，负载均衡器（有，没有） 5.业务服务（有,没有）
      @$groupid = trim($_POST['groupid'])?$_POST['groupid'] : '';
      @$groupname = trim($_POST['groupname'])?$_POST['groupname'] : '';
      @$grouptype = trim($_POST['grouptype']) ? $_POST['grouptype'] : '';
      @$lbcount = trim($_POST['lbcount']) ? (int)$_POST['lbcount'] : 3;
      @$vipcount = trim($_POST['vipcount']) ? (int)$_POST['vipcount'] : 3;

      static $where = '';
      if(!empty($groupid)){
            $where .="where groupid = $groupid";
      }

       if(!empty($groupname) && !empty($where)){
            $where .= " and groupid = '$groupid'";
      }elseif(empty($where) && !empty($groupname)){
            $where .= "where groupname = '$groupname'";
      }

      if(!empty($grouptype) && !empty($where)){
            $where .= " and grouptype = '$grouptype'";
      }elseif(empty($where) && !empty($grouptype)){
            $where .= "where grouptype = '$grouptype'";
      }


      $query_lbgroup = "SELECT * FROM `$table_lbgroup` $where";
      $data_lbgroup = array();
      $result_lbgroup = $pdo -> query($query_lbgroup);
      if($result_lbgroup)
      {
             while ($info_lbgroup = $result_lbgroup->fetch(2))
            {
                  //bsm_cfg_lbgroup表的groupid
                  $groupid1 = $info_lbgroup['groupid'];
                  $query_lb = "SELECT ID FROM `$table_lb` WHERE `groupid` = '$groupid1'";
                  $data_lb = array();
                  $result_lb = $pdo -> query($query_lb);
                  while ($info_lb = $result_lb->fetch(2))
                  {
                        $data_lb[] = $info_lb;
                  }
                  // 负载均衡器数量
                  $info_lbgroup['lbcount'] = count($data_lb);

                  $query_vip = "SELECT ID FROM `$table_vip` WHERE `groupid` = '$groupid1'";
                  $data_vip = array();
                  $result_vip = $pdo -> query($query_vip);
                  while ($info_vip = $result_vip->fetch(2))
                  {
                        $data_vip[] = $info_vip;
                  }
                  $info_lbgroup['vipcount'] = count($data_vip);


                 //echo $lbcount.'--',$info_lbgroup['lbcount'].'--',$vipcount.'--',$info_lbgroup['vipcount'].'--';die;
                 //$lbcount   $vipcount 判断条件1==存在，2==不存在，3==全部
                 //可用switch优化
                  if($lbcount===1 && $info_lbgroup['lbcount']>=1 && $vipcount===1 && $info_lbgroup['vipcount']>=1){
                        $data_lbgroup[] = $info_lbgroup;
                  }
                  if($lbcount===1 && $info_lbgroup['lbcount']>=1 && $vipcount===2 && $info_lbgroup['vipcount']==0){
                        $data_lbgroup[] = $info_lbgroup;
                  }
                  if($lbcount===1 && $info_lbgroup['lbcount']>=1 && $vipcount===3){
                         $data_lbgroup[] = $info_lbgroup;
                  }
                  if($lbcount===2 && $info_lbgroup['lbcount']==0 && $vipcount===1 && $info_lbgroup['vipcount']>=1){
                        $data_lbgroup[] = $info_lbgroup;
                  }
                  if($lbcount===2 && $info_lbgroup['lbcount']==0 && $vipcount===3){
                         $data_lbgroup[] = $info_lbgroup;
                  }
                  if($lbcount===2 && $info_lbgroup['lbcount']==0 && $vipcount===2 && $info_lbgroup['vipcount']==0){
                        $data_lbgroup[] = $info_lbgroup;
                  }
                  if($lbcount===3 && $vipcount===1 && $info_lbgroup['vipcount']>=1){
                        $data_lbgroup[] = $info_lbgroup;
                  }
                  if($lbcount===3 && $vipcount===2 && $info_lbgroup['vipcount']==0){
                        $data_lbgroup[] = $info_lbgroup;
                  }
                  if($lbcount===3 && $vipcount===3){
                        $data_lbgroup[] = $info_lbgroup;
                  }
            }
      }

      include_once "fzjhzdy.html";
