<?php
ini_set('date.timezone','Asia/Shanghai');
$servername = "localhost";
$username = "jack";
$password = "350166483Qp!";
$dbname = "bitcoin";

// 创建连接
/*
$conn = new mysqli($servername, $username, $password, $dbname);
// 检测连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
} 
$time=time();

try {
    $conn->query('BEGIN');
    $conn->query('set names utf8');
    $result = $conn->query('SELECT id,num,buyer_id,seller_id from `' . "bitorder" . '` where state=2 and '.$time.'-start_time>=1800  limit 5');
    while($row = $result->fetch_assoc()) {
        $conn->query('update bitorder set state=3 where id='.$row['id'].' and state=2');
        mysqli_query($conn,'update bitorder set state=3 where id='.$row['id'].' and state=2');
        mysqli_query($conn,'update user set banlance=banlance+'.$row['num'].' where id='.$row['buyer_id']);
        $result2 = $conn->query('SELECT parentId,id,first_name from `' . "user" . '` where id in ('.$row['buyer_id'].",".$row['seller_id'].')');
        while($row2 = $result2->fetch_assoc())
        {
            if($row2['parentId'] && ($row2['parentId'] != $row2['id'])){
                mysqli_query($conn,'
                            INSERT INTO `' . "bitorder" . '`
                            (`buy_sell`, `buyer_id`, `price`, `num`,`state`,`owner`,`des`)
                            VALUES
                            (2, '.$row2['parentId'].', 0, 0.00001,3, 0,"'.$row2['first_name'].'")
                        ');
                mysqli_query($conn,'update user set banlance=banlance+0.00001 where id='.$row2['parentId']);
            }
        }
    }
    $conn->query('COMMIT');
    
} catch (Exception $e) {
    $conn->query('ROLLBACK');
}
$conn->close();

*/


//----------------------------------机器人------------------------------



$tempbuy=file_get_contents("https://api-otc.huobi.pro/v1/otc/trade/list/public?coinId=1&tradeType=1&currentPage=1&payWay=&country=&merchant=1&online=1&range=0");
$temp=json_decode($tempbuy,true);
$price=$temp['data'][0]['price'];
if(!$price && file_exists("curl_price")){
    $price=file_get_contents("curl_price");
}
file_put_contents("curl_price",$price);

$time=time();
$timepriceT=date("H:i",$time);
$timeprice=file_exists("timeprice.dat")?json_decode(file_get_contents("timeprice.dat"),true):["price"=>[],"time"=>[]];
if(count($timeprice['price']) < 20){
    $timeprice['price'][]=$price;
    $timeprice['time'][]=$timepriceT; 
}else{
    unset($timeprice['price'][0]);
    unset($timeprice['time'][0]);
    $timeprice['price'][]=$price;
    $timeprice['time'][]=$timepriceT; 
}
$timeprice['price']=array_value($timeprice['price']);
$timeprice['time']=array_value($timeprice['time']);
file_put_contents("timeprice.dat",json_encode($timeprice));

$hour=date("H",$time);
$min=date("i",$time);
if($hour >2 && $hour <6){
    if($min/10 < 5){
        exit();
    }
}
//////////////////////////////////////////////////////////////////////
$conn = new mysqli($servername, $username, $password, $dbname);
// 检测连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}
$shoukuanmark=[
    //-----------------------------------------------------------------------
   // 'jiaojiaoka@gmail.com  支付宝  张娇 备注单号',
   // '支付宝账号   jiaomei1234@gmail.com   王童童  别填写明感词汇',
   // '支付宝：18361084095@163.com  张兴荣  大于5万分开转',
   // 'jackshanyeshuzi@gmail.com   支付宝名  李鸣 备注订单号',
   // '支付宝：  赵建国   bitnnw@gmail.com 大于5万分开转',
        //#'支付宝：63-9458149311  王俊明',
        //#'支付宝:  63-9458149312  柯丰',
        //#'支付宝账号  63-9055596065 张悠然',
        //#'支付宝账号  evalijiajia@gmail.com  李佳',
        //#'支付宝：houxiaojack@gmail.com  郭小琴',
    //-----------------------------------------------------------------------
    '微信： QP350166483 大于5万分开转账，量大私下联系，平台交易',
    '支付宝：1107969784@qq.com  谢天明  大于5万分多次转账',
    '支付宝：15072466127  张武宗 人一直都在，转账备注好订单号',
    '支付宝：18190117297  吴文丰  备注好订单号',
    '搬砖小王子  支付宝：350166483@qq.com  谯鹏 欢迎私下联系 平台交易  量大价格美丽',
    '招商银行  南京月牙湖支行  谯(qiao)鹏  6214830256968529  两大价格美丽',
    '中国银行  南京梅花山庄支行  谯（qiao)鹏  4563511200015916852  两大私下商量',
    '微信：id_tianming  请备注好订单号',//谢天明
    '微信：zwz_07  大于5万分开转',//张武宗
    '微信：wendyfe12478 备注订单号码，5万以上分开转',//吴文丰
    '微信：zy940814  人一直在',//杨青山
    '支付宝：18080520660  杨青山  到账立马放行',
    '一定要备注订单号  中国银行 6216603200001253387  谢天明 ',
    '工商银行   6222020302075907824   杨青山 火速放行',
    '张武宗  6222023202055255953 工商银行  火速放行'
];

$time=time();
try {
    $conn->query('BEGIN');
    $conn->query('set names utf8');
//========================================  真实用户数据定期返款
$result = $conn->query('SELECT id,num,seller_id from `' . "bitorder" . '` where state=1 and  '. $time.'-start_time>1800   and buy_sell=1 and start_time>0');
while($result && $row = $result->fetch_assoc()) {
    mysqli_query($conn,'update user set banlance=banlance+'.$row['num'].' where id='.$row['seller_id']);
    mysqli_query($conn,'update bitorder set state=-1 where id='.$row['id']);
}
//==========================================
    //-------------------------------------------------------------------------------------
    $result = $conn->query('SELECT num,seller_id from `' . "bitorder" . '` where (state=0 or (state=1 and  '. $time.'-start_time>1800 )) and istest=1 and buy_sell=1 and start_time>0');
    mysqli_query($conn,'DELETE FROM bitorder WHERE (state=0 or (state=1 and  '. $time.'-start_time>1800 )) and istest=1 and buy_sell=1 and start_time>0');
    while($result && $row = $result->fetch_assoc()) {
        mysqli_query($conn,'update user set banlance=banlance+'.$row['num'].' where id='.$row['seller_id']);
    }
    //--------------------------------------------------------------------------------------
    mysqli_query($conn,'DELETE FROM bitorder WHERE (state=0 or (state=1 and  '. $time.'-start_time>1800 )) and istest=1 and buy_sell=1 and start_time=0');
    $buyorder=[];
    unset($temp);
    $maxprice=0;
    for($i=0;$i<20;$i++){//低
        $temp['buy_sell']=1;
        $temp['buyer_id']=475543325;
        $temp['price']=rand((int)($price+1000),(int)($price+1200));
        $temp['num']=rand(1,10)/100;
        $temp['state']=0;
        $temp['owner']=475543325;
        $temp['des']='     ';
        $temp['istest']=1;  
        $buyorder[]=$temp;  
        if($temp['price']>$maxprice){
            $maxprice=$temp['price'];
        } 
    }
    foreach ($buyorder as $key => $value) {
        $time=date("Y-m-d H:i:s",(time()+rand(0,3600)-3600));
        mysqli_query($conn,'
                INSERT INTO `' . "bitorder" . '`
                (`buy_sell`, `price`,`buyer_id`, `num`,`state`,`owner`,`des`,`istest`,`create_time`)
                VALUES
                ('.$value['buy_sell'].', '.$value['price'].', '.$value['buyer_id'].', '.$value['num'].',0, '.$value['owner'].',"'.$value['des'].'",'.$value['istest'].',"'.$time.'")
            ');
    }

    $time=time();
    mysqli_query($conn,'DELETE FROM bitorder WHERE (state=0 or (state=1 and  '. $time.'-start_time>1800 )) and istest=1 and buy_sell=0'); 
    $buyorder=[];
    unset($temp);
    for($i=0;$i<20;$i++){//高
        $temp['buy_sell']=0;
        $temp['seller_id']=475543325;
        $temp['price']=rand((int)($maxprice),(int)($maxprice+300));
        $temp['num']=rand(1,10)/100;
        $temp['state']=0;
        $temp['owner']=475543325;
        $count=count($shoukuanmark)-1;
        $temp['des']=$shoukuanmark[rand(0,$count)];
        $temp['istest']=1;   
        $buyorder[]=$temp; 
    }
    foreach ($buyorder as $key => $value) {
        $time=date("Y-m-d H:i:s",(time()+rand(0,3600)-3600));
        mysqli_query($conn,'
                INSERT INTO `' . "bitorder" . '`
                (`buy_sell`, `price`,`seller_id`, `num`,`state`,`owner`,`des`,`istest`,`create_time`)
                VALUES
                ('.$value['buy_sell'].', '.$value['price'].', '.$value['seller_id'].', '.$value['num'].',0, '.$value['owner'].',"'.$value['des'].'",'.$value['istest'].',"'.$time.'")
            ');
    }
    //--------------------------------------------------------------------------------------
    $conn->query('COMMIT');
    
} catch (Exception $e) {
    $conn->query('ROLLBACK');
}
$conn->close();




