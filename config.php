<?php
$servername = "localhost";
$username = "root"; // mặc định XAMPP
$password = "1234567@ngoc_";     // mặc định XAMPP để trống
$dbname = "quanly_khachsan";

// Kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
$conn->set_charset('utf8');
?>
