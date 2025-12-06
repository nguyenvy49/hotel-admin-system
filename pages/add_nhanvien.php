<?php
include '../config.php';

if (isset($_POST['submit'])) {
    // Lấy dữ liệu từ form
    $ho_ten = $_POST['ten_nv'];
    $gioi_tinh = $_POST['gioi_tinh'];
    $ngay_sinh = $_POST['ngay_sinh'];
    $sdt = $_POST['so_dien_thoai'];
    $email = $_POST['email'];
    $ma_chuc_vu = $_POST['ma_chuc_vu'];

    // Câu truy vấn thêm nhân viên (bỏ mã NV vì AUTO_INCREMENT)
    $sql = "INSERT INTO nhan_vien (ho_ten, gioi_tinh, ngay_sinh, sdt, email, ma_chuc_vu)
            VALUES ('$ho_ten', '$gioi_tinh', '$ngay_sinh', '$sdt', '$email', '$ma_chuc_vu')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Thêm nhân viên thành công!'); window.location='nhanvien.php';</script>";
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

        <label>Tên nhân viên:</label>
        <input type="text" name="ten_nv" required>

        <label>Giới tính:</label>
        <select name="gioi_tinh">
            <option value="Nam">Nam</option>
            <option value="Nữ">Nữ</option>
            <option value="Khác">Khác</option>
        </select>

        <label>Ngày sinh:</label>
        <input type="date" name="ngay_sinh">

        <label>Số điện thoại:</label>
        <input type="text" name="so_dien_thoai">

        <label>Email:</label>
        <input type="email" name="email">

        <label>Chức vụ:</label>
        <select name="ma_chuc_vu" required>
            <option value="">-- Chọn chức vụ --</option>
            <?php
            $result = $conn->query("SELECT ma_chuc_vu, ten_chuc_vu FROM chuc_vu");
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['ma_chuc_vu']}'>{$row['ten_chuc_vu']}</option>";
                }
            }
            ?>
        </select>

        <button type="submit" name="submit">Thêm nhân viên</button>
    </form>
</body>
</html>
