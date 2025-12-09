<?php
include '../config.php';

if (isset($_POST['submit'])) {

    $so_phong = $_POST['so_phong'];
    $ma_loai_phong = $_POST['ma_loai_phong'];
    $trang_thai = $_POST['trang_thai'];

    $sql = "INSERT INTO phong (so_phong, ma_loai_phong, trang_thai)
            VALUES ('$so_phong', '$ma_loai_phong', '$trang_thai')";

    if ($conn->query($sql) === TRUE) {
        header("Location:../ dashboard_home.php?page=phong&msg=added");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thêm phòng</title>
<style>
    body { background:#f7f9fc; font-family: Arial; padding:30px; }
    .card { width:480px; margin:auto; background:#fff; padding:25px; border-radius:16px; box-shadow:0 4px 16px rgba(0,0,0,0.08); }
    label { font-weight:bold; margin-top:12px; display:block; }
    input, select {
        width:100%; padding:10px; margin-top:6px; border-radius:10px;
        border:1px solid #cfd6e1; background:#f3f6fb;
    }
    button {
        margin-top:20px; width:100%; padding:12px; border:none;
        background:#5b8bf7; color:white; border-radius:12px; cursor:pointer;
        font-size:16px;
    }
    button:hover { background:#416de0; }
    .back {
        display:block; margin-top:15px; text-align:center; color:#555;
        text-decoration:none;
    }
</style>
</head>

<body>
<div class="card">
    <h2 style="text-align:center; margin-bottom:20px;">➕ Thêm phòng mới</h2>

    <form method="POST">
        <label>Số phòng:</label>
        <input type="text" name="so_phong" required>

        <label>Loại phòng:</label>
        <select name="ma_loai_phong" required>
            <?php
                $q = $conn->query("SELECT * FROM loai_phong");
                while ($r = $q->fetch_assoc()) {
                    echo "<option value='{$r['ma_loai_phong']}'>{$r['ten_loai_phong']}</option>";
                }
            ?>
        </select>

        <label>Trạng thái:</label>
        <select name="trang_thai">
            <option value="Trống">Trống</option>
            <option value="Đã đặt">Đã đặt</option>
            <option value="Đang dọn dẹp">Đang dọn dẹp</option>
            <option value="Bảo trì">Bảo trì</option>
        </select>

        <button type="submit" name="submit">Thêm phòng</button>
        <a class="back" href="../dashboard_home.php?page=phong">← Quay lại</a>
    </form>
</div>

</body>
</html>
