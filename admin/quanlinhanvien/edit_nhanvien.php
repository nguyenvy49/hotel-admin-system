<?php
include "../config.php";

$id = $_POST['ma_nhan_vien'];

$stmt = $conn->prepare("
UPDATE nhan_vien SET 
    ho_ten=?, gioi_tinh=?, ngay_sinh=?, sdt=?, email=?, ma_chuc_vu=? 
WHERE ma_nhan_vien=?
");

$stmt->bind_param("sssssii",
    $_POST['ho_ten'], $_POST['gioi_tinh'], $_POST['ngay_sinh'],
    $_POST['sdt'], $_POST['email'], 
    $_POST['ma_chuc_vu'], $id
);

echo json_encode(["status" => $stmt->execute()]);
