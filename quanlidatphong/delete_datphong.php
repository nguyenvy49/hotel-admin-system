<?php
include '../config.php';

$id = $_GET['id'];

$dp = mysqli_query($conn, "
    SELECT dp.ma_dat_phong, kh.ho, kh.ten, p.so_phong
    FROM dat_phong dp
    JOIN khach_hang kh ON dp.ma_khach_hang=kh.ma_khach_hang
    JOIN phong p ON dp.ma_phong=p.ma_phong
    WHERE ma_dat_phong=$id
");

$data = mysqli_fetch_assoc($dp);

if (isset($_POST['confirm'])) {

    // lấy mã phòng
    $get = mysqli_query($conn, "SELECT ma_phong FROM dat_phong WHERE ma_dat_phong=$id");
    $info = mysqli_fetch_assoc($get);

    // xóa
    mysqli_query($conn, "DELETE FROM dat_phong WHERE ma_dat_phong=$id");
    mysqli_query($conn, "UPDATE phong SET trang_thai='trong' WHERE ma_phong=".$info['ma_phong']);

    header("Location: ../dashboard_home.php?page=datphong");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Xóa đặt phòng</title>
<link rel="stylesheet" href="../assets/tailwind.css">
<style>
    .card { background:white; padding:35px; border-radius:18px; box-shadow:0 8px 25px rgba(0,0,0,0.08); }
    .btn-danger { background:#dc2626; color:white; padding:12px 22px; border-radius:10px; }
    .btn-secondary { background:#e5e7eb; padding:12px 22px; border-radius:10px; }
</style>
</head>

<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen p-10">

<div class="max-w-xl mx-auto card text-center">

    <h2 class="text-3xl font-bold text-red-600 mb-5">⚠ Xác nhận xoá</h2>

    <p class="text-lg text-gray-700 mb-6">
        Bạn có chắc muốn xoá đặt phòng  
        <span class="font-semibold text-gray-900">#<?= $data['ma_dat_phong'] ?></span><br>
        của khách <b><?= $data['ho']." ".$data['ten'] ?></b>
        (Phòng <?= $data['so_phong'] ?>)?
    </p>

    <form method="POST" class="flex justify-center gap-5">
        <a href="../dashboard_home.php?page=datphong" class="btn-secondary">Không xoá</a>
        <button name="confirm" class="btn-danger">Xoá ngay</button>
    </form>

</div>

</body>
</html>
