<?php
include '../config.php';

$id = $_GET['id'];
$sql = "SELECT * FROM nhan_vien WHERE ma_nhan_vien = $id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ho_ten = $_POST['ho_ten'];
    $ngay_sinh = $_POST['ngay_sinh'];
    $gioi_tinh = $_POST['gioi_tinh'];
    $sdt = $_POST['sdt'];
    $email = $_POST['email'];
    $ma_chuc_vu = $_POST['ma_chuc_vu'];

    $update_sql = "UPDATE nhan_vien 
                   SET ho_ten='$ho_ten', ngay_sinh='$ngay_sinh', gioi_tinh='$gioi_tinh', 
                       sdt='$sdt', email='$email', ma_chuc_vu='$ma_chuc_vu'
                   WHERE ma_nhan_vien=$id";

    if (mysqli_query($conn, $update_sql)) {
        header("Location: nhanVien.php?msg=updated");
        exit;
    } else {
        echo "Lỗi: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Sửa thông tin nhân viên</title>
<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background-color: #f3f6fa;
        margin: 0;
        padding: 0;
    }
    .container {
        max-width: 700px;
        background: white;
        margin: 60px auto;
        padding: 40px 50px;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }
    h2 {
        text-align: center;
        color: #2d5a88;
        margin-bottom: 30px;
    }
    form {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }
    label {
        font-weight: 600;
        color: #333;
    }
    input[type="text"],
    input[type="email"],
    input[type="date"],
    select {
        padding: 10px 14px;
        border: 1px solid #d0d7de;
        border-radius: 8px;
        font-size: 15px;
        transition: all 0.2s;
    }
    input:focus, select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
        outline: none;
    }
    .btn-container {
        display: flex;
        justify-content: space-between;
        margin-top: 25px;
    }
    button {
        background-color: #3b82f6;
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 15px;
        transition: 0.2s;
    }
    button:hover {
        background-color: #2563eb;
        transform: translateY(-1px);
    }
    a {
        text-decoration: none;
        color: #555;
        padding: 10px 25px;
        border: 1px solid #d0d7de;
        border-radius: 8px;
        transition: 0.2s;
    }
    a:hover {
        background-color: #f2f4f7;
    }
</style>
</head>
<body>
<div class="container">
    <h2><b>Cập nhật thông tin nhân viên</b></h2>
    <form method="POST">
        <label>Họ tên:</label>
        <input type="text" name="ho_ten" value="<?= htmlspecialchars($row['ho_ten']) ?>" required>

        <label>Ngày sinh:</label>
        <input type="date" name="ngay_sinh" value="<?= $row['ngay_sinh'] ?>" required>

        <label>Giới tính:</label>
        <select name="gioi_tinh">
            <option value="Nam" <?= $row['gioi_tinh']=='Nam'?'selected':'' ?>>Nam</option>
            <option value="Nữ" <?= $row['gioi_tinh']=='Nữ'?'selected':'' ?>>Nữ</option>
            <option value="Khác" <?= $row['gioi_tinh']=='Khác'?'selected':'' ?>>Khác</option>
        </select>

        <label>Số điện thoại:</label>
        <input type="text" name="sdt" value="<?= htmlspecialchars($row['sdt']) ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($row['email']) ?>" required>

        <label>Chức vụ:</label>
        <select name="ma_chuc_vu">
            <?php
            $roles = mysqli_query($conn, "SELECT ma_chuc_vu, ten_chuc_vu FROM chuc_vu");
            while ($r = mysqli_fetch_assoc($roles)) {
                $selected = ($r['ma_chuc_vu'] == $row['ma_chuc_vu']) ? 'selected' : '';
                echo "<option value='{$r['ma_chuc_vu']}' $selected>{$r['ten_chuc_vu']}</option>";
            }
            ?>
        </select>

        <div class="btn-container">
            <button type="submit"> Lưu thay đổi</button>
            <a href="nhanVien.php">Hủy</a>
        </div>
    </form>
</div>
</body>
</html>
