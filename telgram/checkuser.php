<?php
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
    $result = $conn->query('SELECT id from user where id='.$id);
    while($result && $row = $result->fetch_assoc()) {
       echo 1;die;
    }
    
} catch (Exception $e) {
}
$conn->close();




