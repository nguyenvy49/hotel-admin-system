<?php
include '../config.php';

if (isset($_GET['ma_nhan_vien'])) {
    $id = intval($_GET['ma_nhan_vien']);

    $sql = "DELETE FROM nhan_vien WHERE ma_nhan_vien = $id";

    if ($conn->query($sql) === TRUE) {
        header("Location: ../dashboard_home.php?page=nhanvien&msg=deleted");
        exit;
    } else {
        echo "Lỗi: " . $conn->error;
    }
}
?>
