<?php
include '../config.php';

// Kiểm tra tham số GET
if (!isset($_GET['ma_khach_hang'])) {
    die("❌ Không tìm thấy ID khách hàng.");
}

$ma = (int)$_GET['ma_khach_hang'];

// Kiểm tra khách hàng có tồn tại không
$check = mysqli_query($conn, "SELECT * FROM khach_hang WHERE ma_khach_hang = $ma");
if (mysqli_num_rows($check) == 0) {
    die("❌ Khách hàng không tồn tại.");
}

// Thực hiện xóa
$sql = "DELETE FROM khach_hang WHERE ma_khach_hang = $ma";

if (mysqli_query($conn, $sql)) {
    // Quay lại danh sách khách hàng kèm thông báo
    header("Location: ../dashboard_home.php?page=customers&msg=deleted");
    exit;
} else {
    echo "❌ Lỗi SQL: " . mysqli_error($conn);
}
?>
