<?php
include '../config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM nhan_vien WHERE ma_nhan_vien = $id";

    if (mysqli_query($conn, $sql)) {
        header("Location: nhanVien.php?msg=deleted");
        exit;
    } else {
        echo "Lỗi: " . mysqli_error($conn);
    }
}
?>
