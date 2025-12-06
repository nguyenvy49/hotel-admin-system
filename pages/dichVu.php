<?php
// dichVu.php (phiên bản có kiểm tra schema để tránh lỗi Unknown column)
include '../config.php'; // chỉnh đường dẫn nếu cần

// ---- (1) Hàm kiểm tra tồn tại cột trong bảng
function column_exists($conn, $table, $column) {
    $db = $conn->real_escape_string($conn->query("SELECT DATABASE()")->fetch_row()[0]);
    $table = $conn->real_escape_string($table);
    $column = $conn->real_escape_string($column);
    $q = "SELECT COUNT(*) as cnt FROM INFORMATION_SCHEMA.COLUMNS
          WHERE TABLE_SCHEMA = '$db' AND TABLE_NAME = '$table' AND COLUMN_NAME = '$column'";
    $r = $conn->query($q);
    if (!$r) return false;
    return intval($r->fetch_assoc()['cnt']) > 0;
}

// ---- (2) Tìm cột tên phòng phù hợp
// ưu tiên: phong.ten_phong -> phong.so_phong -> dat_phong.ten_phong -> dat_phong.so_phong -> fallback to dp.ma_dat_phong
$room_field = null;
$room_source = null; // 'p' or 'dp' or fallback

if (column_exists($conn, 'phong', 'ten_phong')) {
    $room_field = "p.ten_phong";
    $room_source = 'p';
} elseif (column_exists($conn, 'phong', 'so_phong')) {
    $room_field = "p.so_phong";
    $room_source = 'p';
} elseif (column_exists($conn, 'dat_phong', 'ten_phong')) {
    $room_field = "dp.ten_phong";
    $room_source = 'dp';
} elseif (column_exists($conn, 'dat_phong', 'so_phong')) {
    $room_field = "dp.so_phong";
    $room_source = 'dp';
} else {
    // fallback: show ma_dat_phong
    $room_field = "dp.ma_dat_phong";
    $room_source = 'dp';
}

// ---- (3) Xây dựng truy vấn JOIN dựa trên khả năng tồn tại các bảng/khóa
// Kiểm tra bảng dat_phong tồn tại chưa
$has_dat_phong = $conn->query("SHOW TABLES LIKE 'dat_phong'")->num_rows > 0;
$has_phong = $conn->query("SHOW TABLES LIKE 'phong'")->num_rows > 0;
$has_dich_vu = $conn->query("SHOW TABLES LIKE 'dich_vu'")->num_rows > 0;

if (!$has_dich_vu) {
    die("Bảng 'dich_vu' không tồn tại. Vui lòng tạo bảng trước khi chạy trang này.");
}

if (!$has_dat_phong) {
    // Nếu không có dat_phong thì chỉ hiển thị phiếu nếu bảng tồn tại
    $sql = "
        SELECT psd.ma_sddv, psd.ma_dat_phong, dv.ten_dich_vu, psd.ngay_su_dung,
               psd.so_luong, psd.don_gia, (psd.so_luong * psd.don_gia) AS thanh_tien
        FROM phieu_su_dung_dich_vu psd
        JOIN dich_vu dv ON psd.ma_dich_vu = dv.ma_dich_vu
        ORDER BY psd.ma_sddv DESC
    ";
} else {
    // dat_phong tồn tại
    // nếu có bảng phong và dat_phong có cột ma_phong thì join phong để lấy tên phòng
    $dp_has_ma_phong = column_exists($conn, 'dat_phong', 'ma_phong');

    if ($has_phong && $dp_has_ma_phong && $room_source === 'p') {
        // join phong để lấy tên phòng từ bảng phong
        $sql = "
            SELECT psd.ma_sddv, dp.ma_dat_phong, $room_field AS ten_phong,
                   dv.ten_dich_vu, psd.ngay_su_dung, psd.so_luong, psd.don_gia,
                   (psd.so_luong * psd.don_gia) AS thanh_tien
            FROM phieu_su_dung_dich_vu psd
            JOIN dich_vu dv ON psd.ma_dich_vu = dv.ma_dich_vu
            JOIN dat_phong dp ON psd.ma_dat_phong = dp.ma_dat_phong
            JOIN phong p ON dp.ma_phong = p.ma_phong
            ORDER BY psd.ma_sddv DESC
        ";
    } else {
        // lấy tên phòng/identifier từ dat_phong trực tiếp (dp.ten_phong hoặc dp.so_phong hoặc dp.ma_dat_phong)
        $sql = "
            SELECT psd.ma_sddv, dp.ma_dat_phong, $room_field AS ten_phong,
                   dv.ten_dich_vu, psd.ngay_su_dung, psd.so_luong, psd.don_gia,
                   (psd.so_luong * psd.don_gia) AS thanh_tien
            FROM phieu_su_dung_dich_vu psd
            JOIN dich_vu dv ON psd.ma_dich_vu = dv.ma_dich_vu
            JOIN dat_phong dp ON psd.ma_dat_phong = dp.ma_dat_phong
            ORDER BY psd.ma_sddv DESC
        ";
    }
}

// thực hiện truy vấn
$result = $conn->query($sql);
if ($result === false) {
    die("Lỗi SQL: " . $conn->error . "\n\nSQL dùng: " . $sql);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="utf-8">
<title>Quản lý dịch vụ nâng cao</title>
<style>
body { font-family: "Segoe UI", sans-serif; background:#f7f8fb; margin:20px; }
.container { max-width:1100px; margin:0 auto; background:#fff; padding:20px; border-radius:10px; box-shadow:0 6px 18px rgba(0,0,0,0.06); }
h2 { text-align:center; color:#333; }
table { width:100%; border-collapse:collapse; margin-top:15px; }
th, td { padding:10px; border:1px solid #e6e9ef; text-align:center; }
th { background:#007bff; color:#fff; }
tr:nth-child(even){ background:#f9fbff; }
.btn { padding:8px 12px; border-radius:6px; text-decoration:none; color:#fff; }
.btn-add{ background:#28a745; }
.btn-edit{ background:#ffc107; color:#333; }
.btn-del{ background:#dc3545; }
</style>
</head>
<body>
<div class="container">
    <h2>🧾 Phiếu sử dụng dịch vụ — Hiển thị phòng & dịch vụ</h2>

    <table>
        <thead>
            <tr>
                <th>Mã phiếu</th>
                <th>Phòng (hiển thị)</th>
                <th>Tên dịch vụ</th>
                <th>Ngày sử dụng</th>
                <th>Số lượng</th>
                <th>Đơn giá</th>
                <th>Tổng tiền</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows == 0): ?>
            <tr><td colspan="7">Chưa có phiếu sử dụng dịch vụ.</td></tr>
        <?php else: ?>
            <?php while($r = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $r['ma_sddv']; ?></td>
                    <td><?php echo htmlspecialchars($r['ten_phong']); ?></td>
                    <td><?php echo htmlspecialchars($r['ten_dich_vu']); ?></td>
                    <td><?php echo !empty($r['ngay_su_dung']) ? date('d/m/Y', strtotime($r['ngay_su_dung'])) : ''; ?></td>
                    <td><?php echo $r['so_luong']; ?></td>
                    <td><?php echo number_format($r['don_gia'],0,',','.'); ?></td>
                    <td><?php echo number_format($r['thanh_tien'],0,',','.'); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
