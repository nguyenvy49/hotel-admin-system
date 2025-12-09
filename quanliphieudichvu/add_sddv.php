<?php
include '../config.php';
mysqli_set_charset($conn, 'utf8mb4');

$errors = [];

$dp = $conn->query("SELECT ma_dat_phong FROM dat_phong ORDER BY ma_dat_phong DESC");
$dv = $conn->query("SELECT ma_dich_vu, ten_dich_vu FROM dich_vu ORDER BY ma_dich_vu DESC");

if (isset($_POST['submit'])) {

    $ma_dp = isset($_POST['ma_dat_phong']) ? trim($_POST['ma_dat_phong']) : '';
    $ma_dv = isset($_POST['ma_dich_vu']) ? trim($_POST['ma_dich_vu']) : '';
    $ngay = isset($_POST['ngay_su_dung']) ? trim($_POST['ngay_su_dung']) : null;
    $sl = isset($_POST['so_luong']) ? trim($_POST['so_luong']) : 0;
    $dg = isset($_POST['don_gia']) ? trim($_POST['don_gia']) : 0;

    if ($ma_dp === '') $errors[] = "Chưa chọn mã đặt phòng.";
    if ($ma_dv === '') $errors[] = "Chưa chọn dịch vụ.";
    if ($ngay === '') $errors[] = "Chưa chọn ngày sử dụng.";
    if (!is_numeric($sl) || $sl <= 0) $errors[] = "Số lượng không hợp lệ.";
    if (!is_numeric($dg) || $dg <= 0) $errors[] = "Đơn giá không hợp lệ.";

    if (empty($errors)) {
        $stmt = $conn->prepare("
            INSERT INTO phieu_su_dung_dich_vu 
            (ma_dat_phong, ma_dich_vu, ngay_su_dung, so_luong, don_gia)
            VALUES (?, ?, ?, ?, ?)
        ");

        if (!$stmt) die("Lỗi prepare: " . $conn->error);

        $stmt->bind_param("iisii", $ma_dp, $ma_dv, $ngay, $sl, $dg);

        if ($stmt->execute()) {
            header("Location: ../dashboard_home.php?page=dichvu&msg=added");
            exit;
        } else {
            $errors[] = "Lỗi khi thêm phiếu: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thêm Phiếu Sử Dụng Dịch Vụ</title>
<style>
    body { font-family: Arial, sans-serif; background:#f1f5f9; margin:0; padding:0; }
    .container { max-width:500px; margin:50px auto; background:white; padding:30px; border-radius:12px; box-shadow:0 6px 20px rgba(0,0,0,0.1); }
    h2 { text-align:center; color:#0f172a; margin-bottom:20px; }
    label { font-weight:bold; margin-top:10px; display:block; color:#334155; }
    input, select { width:100%; padding:10px; margin-top:5px; border:1px solid #cbd5e1; border-radius:6px; font-size:15px; }
    input:focus, select:focus { border-color:#3b82f6; outline:none; }
    button { width:100%; padding:12px; background:#16a34a; color:white; border:none; font-size:16px; margin-top:20px; border-radius:6px; cursor:pointer; transition:0.2s; }
    button:hover { background:#15803d; }
    .back { text-align:center; margin-top:12px; }
    .back a { color:#475569; text-decoration:none; font-size:14px; }
    .error { color:red; font-weight:bold; margin-bottom:10px; }
</style>
</head>
<body>

<div class="container">
    <h2>Thêm Phiếu Sử Dụng Dịch Vụ</h2>

    <?php if (!empty($errors)) : ?>
        <?php foreach ($errors as $err) : ?>
            <p class="error"><?= htmlspecialchars($err) ?></p>
        <?php endforeach; ?>
    <?php endif; ?>

    <form method="POST">
        <label>Mã đặt phòng</label>
        <select name="ma_dat_phong" required>
            <option value="">-- Chọn đặt phòng --</option>
            <?php while ($r = mysqli_fetch_assoc($dp)) : ?>
                <option value="<?= $r['ma_dat_phong'] ?>" <?= isset($ma_dp) && $ma_dp==$r['ma_dat_phong']?'selected':'' ?>>
                    Mã <?= $r['ma_dat_phong'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Dịch vụ</label>
        <select name="ma_dich_vu" required>
            <option value="">-- Chọn dịch vụ --</option>
            <?php while ($r = mysqli_fetch_assoc($dv)) : ?>
                <option value="<?= $r['ma_dich_vu'] ?>" <?= isset($ma_dv) && $ma_dv==$r['ma_dich_vu']?'selected':'' ?>>
                    <?= htmlspecialchars($r['ten_dich_vu']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Ngày sử dụng</label>
        <input type="date" name="ngay_su_dung" value="<?= isset($ngay)?$ngay:'' ?>" required>

        <label>Số lượng</label>
        <input type="number" name="so_luong" value="<?= isset($sl)?$sl:1 ?>" min="1" required>

        <label>Đơn giá (VNĐ)</label>
        <input type="number" name="don_gia" value="<?= isset($dg)?$dg:0 ?>" min="0" required>

        <button type="submit" name="submit">Thêm Phiếu</button>
    </form>

    <div class="back">
        <a href="../dashboard_home.php?page=dichvu">← Quay lại danh sách</a>
    </div>
</div>

</body>
</html>
