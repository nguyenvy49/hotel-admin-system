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
<title>Sửa nhân viên</title>
<link rel="stylesheet" href="../style.css">
</head>
<body>
<h2>✏️ Sửa thông tin nhân viên</h2>
<form method="POST">
    <label>Họ tên:</label>
    <input type="text" name="ho_ten" value="<?= $row['ho_ten'] ?>" required>

    <label>Ngày sinh:</label>
    <input type="date" name="ngay_sinh" value="<?= $row['ngay_sinh'] ?>" required>

    <label>Giới tính:</label>
    <select name="gioi_tinh">
        <option value="Nam" <?= $row['gioi_tinh']=='Nam'?'selected':'' ?>>Nam</option>
        <option value="Nữ" <?= $row['gioi_tinh']=='Nữ'?'selected':'' ?>>Nữ</option>
        <option value="Khác" <?= $row['gioi_tinh']=='Khác'?'selected':'' ?>>Khác</option>
    </select>

    <label>Số điện thoại:</label>
    <input type="text" name="sdt" value="<?= $row['sdt'] ?>" required>

    <label>Email:</label>
    <input type="email" name="email" value="<?= $row['email'] ?>" required>

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

    <button type="submit">Cập nhật</button>
    <a href="nhanVien.php">Hủy</a>
</form>
</body>
</html>
