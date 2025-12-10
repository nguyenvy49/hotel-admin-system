<?php
include '../config.php';

$id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM phieu_su_dung_dich_vu WHERE ma_sddv = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: ../dashboard_home.php?page=dichvu&msg=deleted");
    exit;
} else {
    echo "Lỗi khi xoá: " . $conn->error;
}
