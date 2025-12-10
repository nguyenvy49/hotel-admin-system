<?php
include '../config.php';

// Lấy ID phòng
if (!isset($_GET['id'])) {
    die("Không tìm thấy phòng!");
}

$id = $_GET['id'];

// Lấy dữ liệu phòng + giá phòng
$sql = "
    SELECT p.*, lp.ten_loai_phong, lp.gia_phong
    FROM phong p
    JOIN loai_phong lp ON p.ma_loai_phong = lp.ma_loai_phong
    WHERE p.ma_phong = $id
";

$result = $conn->query($sql);
$phong = $result->fetch_assoc();

if (!$phong) {
    die("Phòng không tồn tại!");
}

// Update
if (isset($_POST['submit'])) {
    $so_phong = $_POST['so_phong'];
    $ma_loai_phong = $_POST['ma_loai_phong'];
    $trang_thai = $_POST['trang_thai'];
    $gia_phong = $_POST['gia_phong']; // thêm lấy giá phòng mới

    // Cập nhật bảng phòng
    $update_phong = "
        UPDATE phong
        SET so_phong='$so_phong',
            ma_loai_phong='$ma_loai_phong',
            trang_thai='$trang_thai'
        WHERE ma_phong=$id
    ";

    // Cập nhật giá phòng trong bảng loại phòng
    $update_gia = "
        UPDATE loai_phong
        SET gia_phong='$gia_phong'
        WHERE ma_loai_phong='$ma_loai_phong'
    ";

    if ($conn->query($update_phong) === TRUE && $conn->query($update_gia) === TRUE) {
        header("Location: ../dashboard_home.php?page=phong&msg=updated");
        exit;
    } else {
        echo "Lỗi cập nhật: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Chỉnh sửa phòng</title>
<style>
    body { background:#f6f8fc; font-family:Arial; padding:30px; }
    .card {
        width:480px; margin:auto; background:white; padding:25px;
        border-radius:16px; box-shadow:0 4px 18px rgba(0,0,0,0.1);
        animation: fadeIn .3s ease;
    }
    @keyframes fadeIn { from {opacity:0; transform:translateY(10px);} to {opacity:1;} }
    label { margin-top:12px; display:block; font-weight:bold; color:#333; }
    input, select {
        width:100%; padding:10px; border-radius:10px; border:1px solid #ccd3e1;
        background:#f2f5fa; margin-top:6px;
    }
    button {
        width:100%; padding:12px; margin-top:20px; border:none;
        border-radius:12px; cursor:pointer; font-size:16px;
        background:#4f7df3; color:white;
    }
    button:hover { background:#3a63d6; }

    a.back {
        margin-top:15px; display:block; text-align:center;
        text-decoration:none; color:#555;
    }
</style>
</head>

<body>

<div class="card">
    <h2 style="text-align:center;">✏ Chỉnh sửa phòng</h2>

    <form method="POST">

        <label>Số phòng:</label>
        <input type="text" name="so_phong" value="<?= $phong['so_phong'] ?>" required>

        <label>Loại phòng:</label>
        <select name="ma_loai_phong">
            <?php
                $lp = $conn->query("SELECT * FROM loai_phong");
                while ($r = $lp->fetch_assoc()) {
                    $sel = ($r['ma_loai_phong'] == $phong['ma_loai_phong']) ? "selected" : "";
                    echo "<option value='{$r['ma_loai_phong']}' $sel>{$r['ten_loai_phong']}</option>";
                }
            ?>
        </select>
        <label>Giá phòng:</label>
<input type="number" step="0.01" name="gia_phong" value="<?= $phong['gia_phong'] ?>" required>
        <label>Trạng thái:</label>
        <select name="trang_thai">
            <option value="Trống" <?= $phong['trang_thai'] == "Trống" ? "selected" : "" ?>>Trống</option>
            <option value="Đã đặt" <?= $phong['trang_thai'] == "Đã đặt" ? "selected" : "" ?>>Đã đặt</option>
            <option value="Đang dọn dẹp" <?= $phong['trang_thai'] == "Đang dọn dẹp" ? "selected" : "" ?>>Đang dọn dẹp</option>
            <option value="Bảo trì" <?= $phong['trang_thai'] == "Bảo trì" ? "selected" : "" ?>>Bảo trì</option>
        </select>

        <button type="submit" name="submit">💾 Lưu thay đổi</button>
        <a class="back" href="../dashboard_home.php?page=phong">← Quay lại danh sách</a>
    </form>
</div>

</body>
</html>
