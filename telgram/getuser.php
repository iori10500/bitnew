<?php
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
    //-------------------------------------------------------------------------------------
    $orderinfo=[];
    $result = $conn->query("SELECT * FROM `bitorder` WHERE (`owner`=$userid and `state`!=-1) or ($time-`start_time`<1800 and (`buyer_id`=$userid or `seller_id`=$userid) and `state` =1) or ((`buyer_id`=$userid or `seller_id`=$userid) and `state` =2) or  ((`buyer_id`=$userid or `seller_id`=$userid) and `state` =3) or ((`buyer_id`=$userid or `seller_id`=$userid) and `state` =4) ");
    while($result && $row = $result->fetch_assoc()) {
        if($time - $row['start_time']>1800){
            $row['state']=0;
        }
        if($row['state'] ==0){
            if($row['buy_sell'] == 1){
                $temp['des']='买入'.$row['num'];
                $temp['state']='等待交易';
                $temp['fuhao']="+";
                $temp['over']=0;
            }else{
                $temp['des']='卖出'.$row['num'];
                $temp['fuhao']="-";
                $temp['state']='等待交易';
                $temp['over']=0;
            }
        }else if($row['state'] ==1){
            if($userid == $row['buyer_id']){
                $temp['des']='买入'.$row['num'];
                $temp['fuhao']="+";
                $temp['state']='等待付款';
                $temp['over']=0;
            }else{
                $temp['des']='卖出'.$row['num'];
                $temp['fuhao']="-";
                $temp['state']='等待付款';
                $temp['over']=0;
            }
        }else if($row['state'] ==2){
            if($userid == $row['buyer_id']){
                $temp['des']='买入'.$row['num'];
                $temp['fuhao']="+";
                $temp['state']='等待放行';
                $temp['over']=0;
            }else{
                $temp['des']='卖出'.$row['num'];
                $temp['fuhao']="-";
                $temp['state']='等待放行';
                $temp['over']=0;
            }
        }else if($row['state'] ==3){
            if($row['buy_sell'] == 2){
                $temp['des']='收入'.$row['num'];
                $temp['fuhao']="+";
                $temp['state']='交易完成';
                $temp['over']=1;

            }else if($userid == $row['buyer_id']){
                $temp['des']='买入'.$row['num'];
                $temp['fuhao']="+";
                $temp['state']='交易完成';
                $temp['over']=1;
            }else{
                $temp['des']='卖出'.$row['num'];
                $temp['fuhao']="-";
                $temp['state']='交易完成';
                $temp['over']=1;
            }
        }else if($row['state'] ==4){
            if($userid == $row['buyer_id']){
                $temp['des']='买入'.$row['num'];
                $temp['fuhao']="+";
                $temp['state']='等待审核';
                $temp['over']=0;
            }else{
                $temp['des']='卖出'.$row['num'];
                $temp['fuhao']="-";
                $temp['state']='等待审核';
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

print_r($orderinfo);




