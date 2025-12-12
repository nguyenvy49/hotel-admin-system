<?php
include "../config.php";
header("Content-Type: application/json");

$id = intval($_GET['id'] ?? 0);

$q = $conn->query("SELECT * FROM dat_phong WHERE ma_dat_phong=$id");

if ($q->num_rows == 0) {
    echo json_encode(["status"=>false, "msg"=>"Không tìm thấy booking"]);
    exit;
}

$conn->query("
    UPDATE dat_phong 
    SET trang_thai='Chờ nhận phòng'
    WHERE ma_dat_phong=$id
");

echo json_encode(["status"=>true]);
