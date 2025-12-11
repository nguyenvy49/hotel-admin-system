<?php
include "../config.php";

$so_phong    = $_POST['so_phong'];
$ma_loai     = $_POST['ma_loai_phong'];   // lấy từ select
$trang_thai  = $_POST['trang_thai'];

// Kiểm tra số phòng trùng
$check = $conn->prepare("SELECT ma_phong FROM phong WHERE so_phong=? LIMIT 1");
$check->bind_param("s", $so_phong);
$check->execute();
$exists = $check->get_result();

if ($exists->num_rows > 0) {
    echo json_encode([
        "status" => false,
        "msg" => "⚠ Số phòng này đã tồn tại!"
    ]);
    exit;
}

$stmt = $conn->prepare("
    INSERT INTO phong (so_phong, ma_loai_phong, trang_thai)
    VALUES (?, ?, ?)
");
$stmt->bind_param("sis", $so_phong, $ma_loai, $trang_thai);

echo json_encode([
    "status" => $stmt->execute(),
    "msg" => $stmt->execute() ? "" : "Không thể thêm phòng"
]);
