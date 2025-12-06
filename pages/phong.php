<?php
include '../config.php';

// Lấy danh sách loại phòng
$loai_phong_sql = "SELECT * FROM loai_phong";
$loai_phong_result = mysqli_query($conn, $loai_phong_sql);

// Nếu có loại phòng được chọn
$filter = isset($_GET['ma_loai_phong']) ? $_GET['ma_loai_phong'] : 'all';

// Truy vấn phòng theo loại (hoặc tất cả)
if ($filter === 'all') {
    $sql = "SELECT p.*, lp.ten_loai_phong, lp.gia_phong 
            FROM phong p
            JOIN loai_phong lp ON p.ma_loai_phong = lp.ma_loai_phong
            ORDER BY lp.ten_loai_phong, p.so_phong";
} else {
    $sql = "SELECT p.*, lp.ten_loai_phong, lp.gia_phong 
            FROM phong p
            JOIN loai_phong lp ON p.ma_loai_phong = lp.ma_loai_phong
            WHERE p.ma_loai_phong = $filter
            ORDER BY p.so_phong";
}

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Quản lý phòng</title>
<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background-color: #f4f7fc;
        margin: 0;
        padding: 0;
    }
    .container {
        max-width: 1000px;
        margin: 40px auto;
        background: #fff;
        padding: 30px 40px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
    h2 {
        text-align: center;
        color: #2563eb;
        margin-bottom: 25px;
    }
    .filter {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    select {
        padding: 8px 12px;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        font-size: 14px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }
    th, td {
        border-bottom: 1px solid #e2e8f0;
        padding: 10px;
        text-align: center;
    }
    th {
        background-color: #eff6ff;
        color: #1e40af;
    }
    tr:hover {
        background-color: #f1f5f9;
    }
    .status {
        font-weight: bold;
        padding: 5px 10px;
        border-radius: 8px;
        display: inline-block;
    }
    .Trống { background-color: #d1fae5; color: #065f46; }
    .Đã\ đặt { background-color: #fee2e2; color: #991b1b; }
    .Đang\ dọn\ dẹp { background-color: #fef9c3; color: #92400e; }
    .Bảo\ trì { background-color: #e0e7ff; color: #3730a3; }
    .back {
        text-decoration: none;
        color: #2563eb;
        font-weight: 600;
    }
</style>
</head>
<body>
<div class="container">
    <h2>🏨 Quản lý phòng khách sạn</h2>

    <div class="filter">
        <form method="GET">
            <label for="ma_loai_phong"><b>Lọc theo loại phòng:</b></label>
            <select name="ma_loai_phong" id="ma_loai_phong" onchange="this.form.submit()">
                <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>Tất cả</option>
                <?php while($lp = mysqli_fetch_assoc($loai_phong_result)): ?>
                    <option value="<?= $lp['ma_loai_phong'] ?>" <?= $filter == $lp['ma_loai_phong'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($lp['ten_loai_phong']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </form>
        <a href="../dashboard_home.php" class="back">← Về trang chính</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Mã phòng</th>
                <th>Số phòng</th>
                <th>Loại phòng</th>
                <th>Giá (VNĐ)</th>
                <th>Trạng thái</th>
            </tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $row['ma_phong'] ?></td>
                    <td><?= htmlspecialchars($row['so_phong']) ?></td>
                    <td><?= htmlspecialchars($row['ten_loai_phong']) ?></td>
                    <td><?= number_format($row['gia_phong'], 0, ',', '.') ?></td>
                    <td><span class="status <?= $row['trang_thai'] ?>"><?= $row['trang_thai'] ?></span></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5">Không có phòng nào được tìm thấy.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
