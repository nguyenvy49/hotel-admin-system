<?php
// quanlihoadon/add_hoadon.php
include '../config.php';
mysqli_set_charset($conn, 'utf8mb4');

$errors = [];

// Load danh sách đặt phòng và nhân viên để hiển thị select
$dp = $conn->query("SELECT ma_dat_phong FROM dat_phong ORDER BY ma_dat_phong DESC");
$nv = $conn->query("SELECT ma_nhan_vien, ho_ten FROM nhan_vien ORDER BY ma_nhan_vien DESC");

// Các phương thức hợp lệ (phải khớp chính xác ENUM trong DB)
$valid_methods = ['Tiền mặt', 'Chuyển khoản', 'Thẻ ngân hàng'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu và trim
    $ma_dp = isset($_POST['ma_dat_phong']) ? trim($_POST['ma_dat_phong']) : '';
    $ma_nhan_vien = isset($_POST['ma_nhan_vien']) ? trim($_POST['ma_nhan_vien']) : '';
    $ngay_lap = isset($_POST['ngay_lap']) ? trim($_POST['ngay_lap']) : '';
    $ngay_thanh_toan = isset($_POST['ngay_thanh_toan']) ? trim($_POST['ngay_thanh_toan']) : '';
    $tong_tien = isset($_POST['tong_tien']) ? trim($_POST['tong_tien']) : '';
    $phuong_thuc = isset($_POST['phuong_thuc']) ? trim($_POST['phuong_thuc']) : '';

    // Basic validation
    if ($ma_dp === '') $errors[] = "Vui lòng chọn Mã đặt phòng.";
    if ($ma_nhan_vien === '') $errors[] = "Vui lòng chọn Nhân viên.";
    if ($ngay_lap === '') $errors[] = "Vui lòng chọn Ngày lập.";
    if ($tong_tien === '' || !is_numeric($tong_tien)) $errors[] = "Tổng tiền không hợp lệ.";
    if (!in_array($phuong_thuc, $valid_methods, true)) $errors[] = "Phương thức thanh toán không hợp lệ.";

    // Kiểm tra tồn tại foreign keys (nếu chưa có lỗi)
    if (empty($errors)) {
        // kiểm tra dat_phong tồn tại
        $stmt = $conn->prepare("SELECT 1 FROM dat_phong WHERE ma_dat_phong = ? LIMIT 1");
        $stmt->bind_param("i", $ma_dp);
        $stmt->execute();
        $res = $stmt->get_result();
        if (!$res->fetch_assoc()) $errors[] = "Mã đặt phòng không hợp lệ (không tồn tại).";
        $stmt->close();

        // kiểm tra nhan_vien tồn tại
        $stmt = $conn->prepare("SELECT 1 FROM nhan_vien WHERE ma_nhan_vien = ? LIMIT 1");
        $stmt->bind_param("i", $ma_nhan_vien);
        $stmt->execute();
        $res = $stmt->get_result();
        if (!$res->fetch_assoc()) $errors[] = "Mã nhân viên không hợp lệ (không tồn tại).";
        $stmt->close();
    }

    // Nếu ok thì insert
    if (empty($errors)) {
        $stmt = $conn->prepare("
            INSERT INTO hoa_don
            (ma_dat_phong, ma_nhan_vien, ngay_lap, ngay_thanh_toan, tong_tien, phuong_thuc)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        if (!$stmt) {
            $errors[] = "Prepare thất bại: " . $conn->error;
        } else {
            // bind types: i i s s s s (tong_tien dùng string để an toàn)
            $stmt->bind_param("iissss", $ma_dp, $ma_nhan_vien, $ngay_lap, $ngay_thanh_toan, $tong_tien, $phuong_thuc);
            if ($stmt->execute()) {
                header("Location: ../dashboard_home.php?page=hoadon");
                exit;
            } else {
                // Nếu vẫn lỗi FK, báo rõ (giúp debug)
                $errors[] = "Lỗi khi thực thi SQL: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="utf-8">
<title>Thêm Hóa Đơn</title>
<style>
    body{font-family:Arial, sans-serif;background:#f1f5f9;margin:0}
    .container{width:520px;margin:40px auto;background:#fff;padding:26px;border-radius:10px;box-shadow:0 6px 18px rgba(0,0,0,.08)}
    h2{text-align:center;color:#0f172a;margin-bottom:18px}
    label{font-weight:600;color:#334155;margin-top:10px;display:block}
    input,select{width:100%;padding:10px;margin-top:6px;border:1px solid #cbd5e1;border-radius:6px;font-size:15px}
    input:focus,select:focus{border-color:#3b82f6;outline:none}
    button{width:100%;padding:12px;background:#16a34a;border:none;color:#fff;border-radius:6px;margin-top:18px;cursor:pointer}
    button:hover{background:#15803d}
    .back{text-align:center;margin-top:10px}
    .back a{color:#475569;text-decoration:none;font-size:14px}
    .errors{background:#fff5f5;border:1px solid #f5c2c2;color:#7a1a1a;padding:10px;border-radius:6px;margin-bottom:12px}
</style>
</head>
<body>
<div class="container">
    <h2>Thêm Hóa Đơn</h2>

    <?php if (!empty($errors)): ?>
        <div class="errors"><strong>Có lỗi:</strong><ul>
            <?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
        </ul></div>
    <?php endif; ?>

    <form method="post">
        <label>Mã đặt phòng</label>
        <select name="ma_dat_phong" required>
            <option value="">-- Chọn mã đặt phòng --</option>
            <?php if ($dp) { mysqli_data_seek($dp,0); while($r = $dp->fetch_assoc()): ?>
                <option value="<?= $r['ma_dat_phong'] ?>" <?= (isset($ma_dp) && $ma_dp == $r['ma_dat_phong']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($r['ma_dat_phong']) ?>
                </option>
            <?php endwhile; } ?>
        </select>

        <label>Nhân viên lập</label>
        <select name="ma_nhan_vien" required>
            <option value="">-- Chọn nhân viên --</option>
            <?php if ($nv) { mysqli_data_seek($nv,0); while($n = $nv->fetch_assoc()): ?>
                <option value="<?= $n['ma_nhan_vien'] ?>" <?= (isset($ma_nhan_vien) && $ma_nhan_vien == $n['ma_nhan_vien']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($n['ho_ten']) ?> (ID: <?= $n['ma_nhan_vien'] ?>)
                </option>
            <?php endwhile; } ?>
        </select>

        <label>Ngày lập</label>
        <input type="date" name="ngay_lap" value="<?= htmlspecialchars($ngay_lap ?? '') ?>" required>

        <label>Ngày thanh toán</label>
        <input type="date" name="ngay_thanh_toan" value="<?= htmlspecialchars($ngay_thanh_toan ?? '') ?>">

        <label>Tổng tiền</label>
        <input type="number" step="0.01" name="tong_tien" value="<?= htmlspecialchars($tong_tien ?? '') ?>" required>

        <label>Phương thức thanh toán</label>
        <select name="phuong_thuc" required>
            <option value="">-- Chọn phương thức --</option>
            <?php foreach($valid_methods as $m): ?>
                <option value="<?= $m ?>" <?= (isset($phuong_thuc) && $phuong_thuc === $m) ? 'selected' : '' ?>><?= htmlspecialchars($m) ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Thêm Hóa Đơn</button>
    </form>

    <div class="back"><a href="../dashboard_home.php?page=hoadon">← Quay lại danh sách</a></div>
</div>
</body>
</html>
