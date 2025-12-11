<?php
include "../config.php";

$id          = $_POST['ma_phong'];
$so_phong    = $_POST['so_phong'];
$ma_loai     = $_POST['ma_loai_phong'];
$trang_thai  = $_POST['trang_thai'];

$stmt = $conn->prepare("
    UPDATE phong SET 
        so_phong = ?, 
        ma_loai_phong = ?, 
        trang_thai = ?
    WHERE ma_phong = ?
");

$stmt->bind_param("sisi", $so_phong, $ma_loai, $trang_thai, $id);

echo json_encode(["status" => $stmt->execute()]);
