<?php
include '../config.php';

if (!isset($_GET['id'])) {
    die("Thiếu ID hóa đơn!");
}

$id = intval($_GET['id']);

// Lấy thông tin hóa đơn
$q = mysqli_query($conn, "SELECT * FROM hoa_don WHERE ma_hoa_don = $id");
$data = mysqli_fetch_assoc($q);

if (!$data) {
    die("Hoá đơn không tồn tại!");
}

// Danh sách mã đặt phòng
$dp = mysqli_query($conn, "SELECT ma_dat_phong FROM dat_phong ORDER BY ma_dat_phong DESC");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ma_dp = $_POST['ma_dat_phong'];
    $tong_tien = $_POST['tong_tien'];
    $trang_thai = $_POST['trang_thai'];
    $ngay_tt = $_POST['ngay_thanh_toan'];

    mysqli_query($conn, "
        UPDATE hoa_don SET 
            ma_dat_phong='$ma_dp',
            tong_tien='$tong_tien',
            trang_thai='$trang_thai',
            ngay_thanh_toan='$ngay_tt'
        WHERE ma_hoa_don=$id
    ");

    header("Location: ../dashboard_home.php?page=hoadon");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Sửa hoá đơn</title>
<link rel="stylesheet" href="../assets/tailwind.css">
<style>
    .card { background:white; padding:35px; border-radius:18px; box-shadow:0 8px 25px rgba(0,0,0,0.08); }
    .label-x { font-weight:600; color:#374151; margin-bottom:6px; display:block; }
    .input-x { width:100%; padding:12px 14px; border:1px solid #d1d5db; border-radius:10px; }
    .btn-primary { background:#2563eb; color:white; padding:12px 22px; border-radius:10px; }
    .btn-secondary { background:#e5e7eb; padding:12px 22px; border-radius:10px; }
</style>
</head>

<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen p-10">

<div class="max-w-3xl mx-auto card">

    <h2 class="text-4xl font-bold text-gray-800 mb-8 flex items-center gap-3">
        <span class="text-blue-500">🧾</span> Cập nhật hóa đơn
    </h2>

    <form method="POST" class="space-y-7">

        <!-- Mã đặt phòng -->
        <div>
            <label class="label-x">Mã đặt phòng</label>
            <select name="ma_dat_phong" class="input-x">
                <?php while ($r = mysqli_fetch_assoc($dp)) : ?>
                    <option value="<?= $r['ma_dat_phong'] ?>"
                        <?= $data['ma_dat_phong'] == $r['ma_dat_phong'] ? 'selected' : '' ?>>
                        Mã đặt phòng <?= $r['ma_dat_phong'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <!-- Tổng tiền -->
        <div>
            <label class="label-x">Tổng tiền (VNĐ)</label>
            <input type="number" name="tong_tien" value="<?= $data['tong_tien'] ?>" class="input-x">
        </div>

        <!-- Trạng thái -->
        <div>
            <label class="label-x">Trạng thái</label>
            <select name="trang_thai" class="input-x">
                <option value="Đã thanh toán" <?= $data['trang_thai']=='Đã thanh toán'?'selected':'' ?>>Đã thanh toán</option>
                <option value="Chưa thanh toán" <?= $data['trang_thai']=='Chưa thanh toán'?'selected':'' ?>>Chưa thanh toán</option>
                <option value="Đang xử lý" <?= $data['trang_thai']=='Đang xử lý'?'selected':'' ?>>Đang xử lý</option>
            </select>
        </div>

        <!-- Ngày thanh toán -->
        <div>
            <label class="label-x">Ngày thanh toán</label>
            <input type="date" name="ngay_thanh_toan" value="<?= $data['ngay_thanh_toan'] ?>" class="input-x">
        </div>

        <div class="flex justify-between pt-3">
            <a href="../dashboard_home.php?page=hoadon" class="btn-secondary">← Quay lại</a>
            <button class="btn-primary">Cập nhật</button>
        </div>

    </form>
</div>

</body>
</html>
