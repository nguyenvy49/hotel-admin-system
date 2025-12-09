<?php
include '../config.php';

if (!isset($_GET['id'])) {
    die("Thiếu ID");
}

$id = $_GET['id'];

$sql = "DELETE FROM dich_vu WHERE ma_dich_vu = $id";

if ($conn->query($sql) === TRUE) {
    header("Location: ../dashboard_home.php?page=dichvu&msg=deleted");
    exit;
} else {
    echo "Lỗi khi xóa: " . $conn->error;
}
?>
