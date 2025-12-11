<?php
include "../config.php";

$ho_ten = $_POST['ho_ten'];
$gioi_tinh = $_POST['gioi_tinh'];
$ngay_sinh = $_POST['ngay_sinh'];
$sdt = $_POST['sdt'];
$email = $_POST['email'];
$ma_chuc_vu = $_POST['ma_chuc_vu'];

$stmt = $conn->prepare("
INSERT INTO nhan_vien (ho_ten, gioi_tinh, ngay_sinh, sdt, email,  ma_chuc_vu)
VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->bind_param("sssssi", $ho_ten, $gioi_tinh, $ngay_sinh, $sdt, $email, $ma_chuc_vu);

echo json_encode(["status" => $stmt->execute()]);
