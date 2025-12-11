<?php
include "../config.php";

$ma = $_POST["ma_khach_hang"] ?? 0;
$ma = (int)$ma;

if ($ma <= 0) {
    echo json_encode(["status" => false, "msg" => "ID không hợp lệ"]);
    exit;
}

// XÓA
$stmt = $conn->prepare("DELETE FROM khach_hang WHERE ma_khach_hang=?");
$stmt->bind_param("i", $ma);

if ($stmt->execute()) {
    echo json_encode(["status" => true]);
} else {
    echo json_encode(["status" => false, "msg" => "Không thể xóa khách hàng"]);
}
