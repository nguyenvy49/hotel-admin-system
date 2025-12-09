<?php
include '../config.php';

if (!isset($_GET['id'])) {
    die("Không tìm thấy ID!");
}

$id = $_GET['id'];

$sql = "DELETE FROM phong WHERE ma_phong = $id";

if ($conn->query($sql) === TRUE) {
    header("Location: ../dashboard_home.php?page=phong&msg=deleted");
    exit;
} else {
    echo "Lỗi xóa phòng: " . $conn->error;
}
?>
