<?php
header("Access-Control-Allow-Origin: *");
ini_set('date.timezone','Asia/Shanghai');
$servername = "localhost";
$username = "jack";
$password = "350166483Qp!";
$dbname = "bitcoin";

$conn = new mysqli($servername, $username, $password, $dbname);
// 检测连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}
try {
    $conn->query('BEGIN');
    $conn->query('set names utf8');
    $id=!empty($_GET['id'])?$_GET['id']:0;
    $result_['id']=$id;
    $result = $conn->query('SELECT id from user where id='.$id);
    while($result && $row = $result->fetch_assoc()) {
       $result_['check']=1;
       echo json_encode($result_);die;
    }
    $result_['check']=0;
    echo json_encode($result_);
    
} catch (Exception $e) {
}
$conn->close();