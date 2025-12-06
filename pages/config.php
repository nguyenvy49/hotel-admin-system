<?php
$host = "localhost";      // máy cục bộ
$port = 3306;             // port MySQL server
$user = "root";           // user Workbench
$password = "1234567@ngoc_"; // password Workbench
$database = "quanly_khachsan";

$conn = new mysqli($host, $user, $password, $database, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

