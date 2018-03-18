<?php
require __DIR__. '/set.php';
ini_set('date.timezone','Asia/Shanghai');
$servername = "localhost";
$username = "jack";
$password = "350166483Qp!";
$dbname = "bitcoin";
$userid=$_GET['userid'];


$conn = new mysqli($servername, $username, $password, $dbname);
// 检测连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}
$time=time();
try {
    $conn->query('BEGIN');
    $conn->query('set names utf8');
    $result = $conn->query("SELECT `first_name`,`last_name`,`username`,`banlance`,`socked`,`walletid` 
                    FROM `' . TB_USER . '`
                    WHERE `id` = $userid 
                    LIMIT 1");
    while($result && $row = $result->fetch_assoc()) {
        $row=$row_;
    }

    $walletId=$row['walletid'];
    $yueinfo = yue($walletId);

    $banlanc=$yueinfo['balance']+$row['banlance'];
    $walletbanlanc=$yueinfo['balance'];
    $accoutbanlanc=$row['banlance'];
    $username=$row['first_name']."      ".$row['last_name']."       ".$row['username'];
    //-------------------------------------------------------------------------------------
    $orderinfo=[];
    $result = $conn->query("SELECT * FROM `bitorder` WHERE (`owner`=$userid and `state`!=-1) or ($time-`start_time`<1800 and (`buyer_id`=$userid or `seller_id`=$userid) and `state` =1) or ((`buyer_id`=$userid or `seller_id`=$userid) and `state` =2) or  ((`buyer_id`=$userid or `seller_id`=$userid) and `state` =3) or ((`buyer_id`=$userid or `seller_id`=$userid) and `state` =4) order by id desc");
    while($result && $row = $result->fetch_assoc()) {
        if(($time - $row['start_time']>1800) && $row['state'] ==1 ){
            $row['state']=0;
        }
        if($row['state'] ==0){
            if($row['buy_sell'] == 1){
                $temp['des']='买入'.$row['num'];
                $temp['state']='等待交易';
                $temp['fuhao']="+".$row['num'];
                $temp['over']=0;
            }else{
                $temp['des']='卖出'.$row['num'];
                $temp['fuhao']="-".$row['num'];
                $temp['state']='等待交易';
                $temp['over']=0;
            }
        }else if($row['state'] ==1){
            if($userid == $row['buyer_id']){
                $temp['des']='买入'.$row['num'];
                $temp['fuhao']="+".$row['num'];
                $temp['state']='等待您付款';
                $temp['over']=0;
            }else{
                $temp['des']='卖出'.$row['num'];
                $temp['fuhao']="-".$row['num'];
                $temp['state']='等待 '.$row['buyer_id'].' 付款';
                $temp['over']=0;
            }
        }else if($row['state'] ==2){
            if($userid == $row['buyer_id']){
                $temp['des']='买入'.$row['num'];
                $temp['fuhao']="+".$row['num'];
                $temp['state']='等待 '.$row['buyer_id'].' 放行';
                $temp['over']=0;
            }else{
                $temp['des']='卖出'.$row['num'];
                $temp['fuhao']="-".$row['num'];
                $temp['state']='等待您放行';
                $temp['over']=0;
            }
        }else if($row['state'] ==3){
            if($row['buy_sell'] == 2){
                $temp['des']='收入'.$row['num'];
                $temp['fuhao']="+".$row['num'];
                $temp['state']='交易完成';
                $temp['over']=1;

            }else if($userid == $row['buyer_id']){
                $temp['des']='买入'.$row['num'];
                $temp['fuhao']="+".$row['num'];
                $temp['state']='交易完成 '.$row['seller_id'];
                $temp['over']=1;
            }else{
                $temp['des']='卖出'.$row['num'];
                $temp['fuhao']="-".$row['num'];
                $temp['state']='交易完成 '.$row['buyer_id'];
                $temp['over']=1;
            }
        }else if($row['state'] ==4){
            if($userid == $row['buyer_id']){
                $temp['des']='买入'.$row['num'];
                $temp['fuhao']="+".$row['num'];
                $temp['state']='等待审核 '.$row['seller_id'];
                $temp['over']=0;
            }else{
                $temp['des']='卖出'.$row['num'];
                $temp['fuhao']="-".$row['num'];
                $temp['state']='等待审核 '.$row['buyer_id'];
                $temp['over']=0;
            }
        }
        $temp['orderid']=date("Ymd",strtotime($row['create_time'])).$row['id'];
        $temp['time']=$row['create_time'];
        $orderinfo[]=$temp;
    }
   
    $conn->query('COMMIT');
    
} catch (Exception $e) {
    $conn->query('ROLLBACK');
}
$conn->close();
foreach ($orderinfo as $key => $value) {
    $content.="<tr>";
    $content.="<th>".$value['orderid']."</th>";
    $content.="<th>".$value['time']."</th>";
    $content.="<th>".$value['des']."</th>";
    $content.="<th>".$value['state']."</th>";
    if($value['over']){
        if($value['fuhao'] <0 ){
            $content.="<th style='color:red'>".$value['fuhao']."</th>";
        }else{
            $content.="<th style='color:green'>".$value['fuhao']."</th>";
        }
    }else{
        $content.="<th>".$value['fuhao']."</th>";
    }
    $content.="</tr>";
    
}


$html="<html>
<body>
<h>用户名：$username</h>
<h>钱包余额：$walletbanlanc</h>
<h>账户余额：$accoutbanlanc</h>
<h>全部资产：$banlanc</h>


<table border='1'>
  <tr>
    <th>单号</th>
    <th>时间</th>
    <th>说明</th>
    <th>状态</th>
    <th>数量</th>
  </tr>
 $content
</table>

</body>
</html>";
echo $html;




