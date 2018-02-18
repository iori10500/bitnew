<?php
$servername = "localhost";
$username = "jack";
$password = "350166483Qp!";
$dbname = "bitcoin";

// 创建连接
$con = new mysqli($servername, $username, $password, $dbname);
// 检测连接
if ($con->connect_error) {
    die("连接失败: " . $con->connect_error);
} 
$time=time();
try {
    mysqli_query($con,'BEGIN');
    mysqli_query($con,'set names utf8');
    $result = mysqli_query($con,'SELECT id,num from `' . "bitorder" . '` where state=2 and '.$time.'-start_time>=1800  limit 5');

    while($row = $result->fetch_assoc())
    {
        $result = mysqli_query($con,'update bitorder set state=3 where id='.$row['id'].' and state=2');
        $result = mysqli_query($con,'update user set banlance=banlance+'.$row['num'].' where id='.$row['id']);
        $result2 = mysqli_query($con,'SELECT parentId,id,first_name from `' . "user" . '` where id in ('.$row['buyer_id'].",".$row['seller_id'].')');
        while($row2 = $result2->fetch_assoc())
        {
            mysqli_query('
                            INSERT INTO `' . "bitorder" . '`
                            (`buy_sell`, `buyer_id`, `price`, `num`,`state`,`owner`,`des`)
                            VALUES
                            (2, '.$row2['parentId'].', 0, 0.00001,3, 0,'.$row2['first_name'].')
                        ');
            mysqli_query('update user set banlance=banlance+0.00001 where id='.$row2['parentId']

        }
        
    }
    mysqli_query($con,'COMMIT');
    
} catch (Exception $e) {
    mysqli_query($con,'ROLLBACK');
}
$conn->close();