<?php
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
        $result = $conn->query('update bitorder set state=3 where id='.$row['id'].' and state=2');
        $result = $conn->query('update user set banlance=banlance+'.$row['num'].' where id='.$row['buyer_id']);
        $result2 = $conn->query('SELECT parentId,id,first_name from `' . "user" . '` where id in ('.$row['buyer_id'].",".$row['seller_id'].')');
        while($row2 = $result2->fetch_assoc())
        {
            if($row2['parentId'] && ($row2['parentId'] != $row2['id'])){
                $conn->query('
                            INSERT INTO `' . "bitorder" . '`
                            (`buy_sell`, `buyer_id`, `price`, `num`,`state`,`owner`,`des`)
                            VALUES
                            (2, '.$row2['parentId'].', 0, 0.00001,3, 0,"'.$row2['first_name'].'")
                        ');
                $conn->query('update user set banlance=banlance+0.00001 where id='.$row2['parentId']);
            }
        }
    }
    $conn->query('COMMIT');
    
} catch (Exception $e) {
    $conn->query('ROLLBACK');
}
$conn->close();