<?php
include "../config.php";
header("Content-Type: application/json");

$id = intval($_GET['id'] ?? 0);

/* Cập nhật booking */
$conn->query("
    UPDATE dat_phong 
    SET trang_thai='Đang ở'
    WHERE ma_dat_phong=$id
");

/* Set trạng thái phòng về Đang ở */
$conn->query("
    UPDATE phong 
    SET trang_thai='Đang ở'
    WHERE ma_phong IN (
        SELECT ma_phong FROM chi_tiet_dat_phong WHERE ma_dat_phong=$id
    )
");

echo json_encode(["status"=>true]);
