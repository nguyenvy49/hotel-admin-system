<?php
include '../config.php';

// Lấy khách hàng
$kh = mysqli_query($conn, "SELECT * FROM khach_hang ORDER BY ma_khach_hang DESC");

// Lấy phòng TRỐNG  (đúng ENUM phải là 'Trống')
$phong = mysqli_query($conn, "
    SELECT p.ma_phong, p.so_phong, lp.ten_loai_phong
    FROM phong p
    JOIN loai_phong lp ON p.ma_loai_phong = lp.ma_loai_phong
    WHERE p.trang_thai = 'Trống'
");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $ma_khach_hang = $_POST['ma_khach_hang'];
    $ma_phong = $_POST['ma_phong'];
    $ngay_nhan = $_POST['ngay_nhan'];
    $ngay_tra = $_POST['ngay_tra'];
    $ngay_dat = date("Y-m-d");

    // Mặc định khi khách đặt phòng → trạng thái "Đã đặt"
    $sql = "
    INSERT INTO dat_phong (ma_khach_hang, ma_phong, ngay_dat, ngay_nhan, ngay_tra, trang_thai)
    VALUES ('$ma_khach_hang', '$ma_phong', '$ngay_dat', '$ngay_nhan', '$ngay_tra', 'Đã đặt')
    ";

    if (mysqli_query($conn, $sql)) {

        // Cập nhật phòng thành "Đã đặt" (đúng ENUM)
        mysqli_query($conn, "
            UPDATE phong 
            SET trang_thai = 'Đã đặt'
            WHERE ma_phong = '$ma_phong'
        ");

        header("Location: ../dashboard_home.php?page=datphong");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thêm đặt phòng</title>
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
        <span class="text-blue-600"></span> Thêm đặt phòng mới
    </h2>

    <form method="POST" class="space-y-7">

        <div>
            <label class="label-x">Khách hàng</label>
            <select name="ma_khach_hang" class="input-x" required>
                <option value="">— Chọn khách hàng —</option>
                <?php while($r = mysqli_fetch_assoc($kh)): ?>
                    <option value="<?= $r['ma_khach_hang'] ?>">
                        <?= $r['ho']." ".$r['ten']." — ".$r['sdt'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div>
            <label class="label-x">Phòng còn trống</label>
            <select name="ma_phong" class="input-x" required>
                <option value="">— Chọn phòng —</option>
                <?php while($p = mysqli_fetch_assoc($phong)): ?>
                    <option value="<?= $p['ma_phong'] ?>">
                        Phòng <?= $p['so_phong'] ?> — <?= $p['ten_loai_phong'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="grid grid-cols-2 gap-7">
            <div>
                <label class="label-x">Ngày nhận phòng</label>
                <input type="date" name="ngay_nhan" class="input-x" required>
            </div>

            <div>
                <label class="label-x">Ngày trả phòng</label>
                <input type="date" name="ngay_tra" class="input-x" required>
            </div>
        </div><br>

        <div class="flex justify-between pt-3">
            <a href="../dashboard_home.php?page=datphong" class="btn-secondary">← Quay lại</a>
            <button class="btn-primary">Thêm đặt phòng</button>
        </div>

    </form>
</div>

</body>
</html>
