<?php
include '../config.php';

if (isset($_POST['submit'])) {

    $ten_nv = $_POST['ho_ten'];
    $gioi_tinh = $_POST['gioi_tinh'];
    $ngay_sinh = $_POST['ngay_sinh'];
    $so_dien_thoai = $_POST['sdt'];
    $email = $_POST['email'];
    $dia_chi = $_POST['dia_chi'];
    $ma_chuc_vu = $_POST['ma_chuc_vu'];

    $sql = "INSERT INTO nhan_vien (ho_ten, gioi_tinh, ngay_sinh, sdt, email, dia_chi, ma_chuc_vu)
            VALUES ('$ten_nv', '$gioi_tinh', '$ngay_sinh', '$so_dien_thoai', '$email', '$dia_chi', '$ma_chuc_vu')";

    if ($conn->query($sql) === TRUE) {
        header("Location: ../dashboard_home.php?page=nhanvien&msg=added");
        exit;
    } else {
        echo "Lỗi khi thêm nhân viên: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm nhân viên</title>
    <style>
        body { font-family: Arial; margin: 30px; }
        form { width: 400px; padding: 20px; border: 1px solid #ccc; border-radius: 10px; }
        label { display: block; margin-top: 10px; }
        input, select { width: 100%; padding: 6px; margin-top: 5px; }
        button { margin-top: 15px; padding: 10px 15px; background: #2b7cff; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #1e5ec9; }
    </style>
</head>
<body>
    <h2>🧾 Thêm nhân viên mới</h2>
   <form method="POST">
        <label>Mã nhân viên:</label>
        <input type="text" name="ma_nhan_vien" required>

        <label>Tên nhân viên:</label>
        <input type="text" name="ho_ten" required>

        <label>Giới tính:</label>
        <select name="gioi_tinh">
            <option value="Nam">Nam</option>
            <option value="Nữ">Nữ</option>
        </select>

        <label>Ngày sinh:</label>
        <input type="date" name="ngay_sinh">

        <label>Số điện thoại:</label>
        <input type="text" name="sdt">

        <label>Email:</label>
        <input type="email" name="email">

        <label>Địa chỉ:</label>
        <input type="text" name="dia_chi">

        <label>Chức vụ:</label>
        <select name="ma_chuc_vu" required>
            <option value="">-- Chọn chức vụ --</option>
            <?php
            $result = $conn->query("SELECT ma_chuc_vu, ten_chuc_vu FROM chuc_vu");
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['ma_chuc_vu']}'>{$row['ten_chuc_vu']}</option>";
            }
            ?>
        </select>

        <!-- Nút SUBMIT đúng -->
        <button type="submit" name="submit">Thêm nhân viên</button>

        <!-- Nút quay lại -->
        <a href="../dashboard_home.php?page=nhanvien" 
           style="padding:10px 15px; background:#ccc; color:#000; border-radius:5px; margin-left:10px; text-decoration:none;">
            Quay lại
        </a>
</form>

</body>
</html>
