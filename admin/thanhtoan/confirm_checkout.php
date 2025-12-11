<?php
include "../config.php";
header("Content-Type: application/json");

$ma_phong = $_GET['id'] ?? 0;

$q = $conn->query("
    SELECT ma_dat_phong 
    FROM dat_phong 
    WHERE ma_phong=$ma_phong AND trang_thai='Đang ở'
");

if ($q->num_rows == 0) {
    echo json_encode(["status" => false, "msg" => "Không tìm thấy khách!"]);
    exit;
}

$ma_dp = $q->fetch_assoc()['ma_dat_phong'];

/* 1) Update trạng thái đặt phòng */
$conn->query("
    UPDATE dat_phong 
    SET trang_thai='Đã trả', ngay_tra_thuc=NOW()
    WHERE ma_dat_phong=$ma_dp
");

/* 2) Update phòng */
$conn->query("UPDATE phong SET trang_thai='Trống' WHERE ma_phong=$ma_phong");

echo json_encode(["status" => true]);
exit;
