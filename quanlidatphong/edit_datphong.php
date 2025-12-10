<?php
include '../config.php';

$id = $_GET['id'];

// lấy thông tin
$dp = mysqli_query($conn, "SELECT * FROM dat_phong WHERE ma_dat_phong = $id");
$data = mysqli_fetch_assoc($dp);

// danh sách KH
$kh = mysqli_query($conn, "SELECT * FROM khach_hang ORDER BY ma_khach_hang DESC");

// danh sách phòng
$phong = mysqli_query($conn, "
    SELECT p.ma_phong, p.so_phong, lp.ten_loai_phong
    FROM phong p
    JOIN loai_phong lp ON p.ma_loai_phong = lp.ma_loai_phong
");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ma_khach_hang = $_POST['ma_khach_hang'];
    $ma_phong = $_POST['ma_phong'];
    $ngay_nhan = $_POST['ngay_nhan'];
    $ngay_tra = $_POST['ngay_tra'];
    $trang_thai = $_POST['trang_thai'];

    mysqli_query($conn, "
        UPDATE dat_phong SET 
            ma_khach_hang='$ma_khach_hang',
            ma_phong='$ma_phong',
            ngay_nhan='$ngay_nhan',
            ngay_tra='$ngay_tra',
            trang_thai='$trang_thai'
        WHERE ma_dat_phong=$id
    ");

    header("Location: ../dashboard_home.php?page=datphong");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Sửa đặt phòng</title>
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
        <span class="text-yellow-500">✏️</span> Cập nhật đặt phòng
    </h2>

    <form method="POST" class="space-y-7">

        <div>
            <label class="label-x">Khách hàng</label>
            <select name="ma_khach_hang" class="input-x">
                <?php while($r = mysqli_fetch_assoc($kh)): ?>
                    <option value="<?= $r['ma_khach_hang'] ?>" 
                        <?= $data['ma_khach_hang']==$r['ma_khach_hang']?'selected':'' ?>>
                        <?= $r['ho']." ".$r['ten']." — ".$r['sdt'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div>
            <label class="label-x">Phòng</label>
            <select name="ma_phong" class="input-x">
                <?php while($p = mysqli_fetch_assoc($phong)): ?>
                    <option value="<?= $p['ma_phong'] ?>" 
                        <?= $data['ma_phong']==$p['ma_phong']?'selected':'' ?>>
                        Phòng <?= $p['so_phong'] ?> — <?= $p['ten_loai_phong'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="grid grid-cols-2 gap-7">
            <div>
                <label class="label-x">Ngày nhận</label>
                <input type="date" name="ngay_nhan" value="<?= $data['ngay_nhan'] ?>" class="input-x">
            </div>

            <div>
                <label class="label-x">Ngày trả</label>
                <input type="date" name="ngay_tra" value="<?= $data['ngay_tra'] ?>" class="input-x">
            </div>
        </div>

        <div>
            <label class="label-x">Trạng thái</label>
            <select name="trang_thai" class="input-x">
    <option value="Đã đặt" <?= $data['trang_thai']=='Đã đặt'?'selected':'' ?>>Đã đặt</option>
    <option value="Đang ở" <?= $data['trang_thai']=='Đang ở'?'selected':'' ?>>Đang ở</option>
    <option value="Đã trả" <?= $data['trang_thai']=='Đã trả'?'selected':'' ?>>Đã trả</option>
    <option value="Hủy" <?= $data['trang_thai']=='Hủy'?'selected':'' ?>>Hủy</option>
     </select>

        </div>

        <div class="flex justify-between pt-3">
            <a href="../dashboard_home.php?page=datphong" class="btn-secondary">← Quay lại</a>
            <button class="btn-primary">Cập nhật</button>
        </div>

    </form>

</div>

</body>
</html>
