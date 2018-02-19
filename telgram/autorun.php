<?php
ini_set('date.timezone','Asia/Shanghai');
$servername = "localhost";
$username = "jack";
$password = "350166483Qp!";
$dbname = "bitcoin";

// 创建连接
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


//----------------------------------机器人------------------------------


$tempbuy=file_get_contents("https://api-otc.huobi.pro/v1/otc/trade/list/public?coinId=1&tradeType=1&currentPage=1&payWay=&country=&merchant=1&online=1&range=0");
$temp=json_decode($tempbuy,true);
$price=$temp['data'][0]['price'];

$conn = new mysqli($servername, $username, $password, $dbname);
// 检测连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
} 
$shoukuanmark=[
    '微信： QP350166483 ',
    '支付宝：350166483@qq.com  谯鹏',
    '支付宝：18080520660  杨青山',
    '支付宝：1107969784@qq.com  谢天明',
    '支付宝：15072466127  张武宗',
    '支付宝：18190117297  吴文丰',
    '支付宝：350166483@qq.com  谯鹏',
    '微信：id_tianming',//谢天明
    '微信：zy940814',//杨青山
    '微信：zwz_07',//张武宗
    '微信：wendyfe12478',//吴文丰
];
$time=time();
try {
    $conn->query('BEGIN');
    $conn->query('set names utf8');
    //--------------------------------------------------------------------------------------

    mysqli_query($conn,'DELETE FROM bitorder WHERE (state=0 or (state=1 and  '. $time.'-start_time>1800 )) and istest=1 and buy_sell=1'); 
    $buyorder=[];
    unset($temp);
    for($i=0;$i<10;$i++){
        $temp['buy_sell']=1;
        $temp['buyer_id']=528254045;
        $temp['price']=rand((int)($price-500),(int)($price+500));
        $temp['num']=rand(1,10)/100;
        $temp['state']=0;
        $temp['owner']=528254045;
        $temp['des']='     ';
        $temp['istest']=1;  
        $buyorder[]=$temp;   
    }
    foreach ($buyorder as $key => $value) {
        mysqli_query($conn,'
                INSERT INTO `' . "bitorder" . '`
                (`buy_sell`, `price`,`buyer_id`, `num`,`state`,`owner`,`des`,`istest`,`create_time`)
                VALUES
                ('.$value['buy_sell'].', '.$value['price'].', '.$value['buyer_id'].', '.$value['num'].',0, '.$value['owner'].',"'.$value['des'].'",'.$value['istest'].',"'.date("Y-m-d H:i:s",time()).'")
            ');
    }

    $time=time();
    mysqli_query($conn,'DELETE FROM bitorder WHERE (state=0 or (state=1 and  '. $time.'-start_time>1800 )) and istest=1 and buy_sell=0'); 
    $buyorder=[];
    unset($temp);
    for($i=0;$i<10;$i++){
        $temp['buy_sell']=0;
        $temp['seller_id']=528254045;
        $temp['price']=rand((int)($price-500),(int)($price+500));
        $temp['num']=rand(1,10)/100;
        $temp['state']=0;
        $temp['owner']=528254045;
        $temp['des']=$shoukuanmark[rand(0,10)];
        $temp['istest']=1;   
        $buyorder[]=$temp; 
    }
    foreach ($buyorder as $key => $value) {
        mysqli_query($conn,'
                INSERT INTO `' . "bitorder" . '`
                (`buy_sell`, `price`,`seller_id`, `num`,`state`,`owner`,`des`,`istest`,`create_time`)
                VALUES
                ('.$value['buy_sell'].', '.$value['price'].', '.$value['seller_id'].', '.$value['num'].',0, '.$value['owner'].',"'.$value['des'].'",'.$value['istest'].',"'.date("Y-m-d H:i:s",time()).'")
            ');
    }
    //--------------------------------------------------------------------------------------
    $conn->query('COMMIT');
    
} catch (Exception $e) {
    $conn->query('ROLLBACK');
}
$conn->close();




