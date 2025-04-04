<?php
$host = 'localhost';
$db = 'foreign_trade';
$user = 'root';
$pass = '';
header('Content-Type: text/html; charset=utf-8');
$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8mb4");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>