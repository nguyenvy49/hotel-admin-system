<?php
include '../config.php';

if (isset($_POST['submit'])) {

    $ho_nv = $_POST['ho'];
    $ten_nv = $_POST['ten'];
    $ngay_sinh = $_POST['ngay_sinh'];
    $so_dien_thoai = $_POST['sdt'];
    $email = $_POST['email'];

    // Kiểm tra email hoặc số điện thoại đã tồn tại chưa
    $check = $conn->prepare("SELECT * FROM khach_hang WHERE email=? OR sdt=?");
    $check->bind_param("ss", $email, $so_dien_thoai);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "<p style='color:red;'>⚠ Email hoặc số điện thoại này đã tồn tại. Vui lòng nhập thông tin khác.</p>";
    } else {
        // Hash mật khẩu mặc định
        $mat_khau_hash = password_hash('12345678*', PASSWORD_DEFAULT);

        // Thêm khách hàng mới
        $stmt = $conn->prepare("INSERT INTO khach_hang (ho, ten, sdt, ngay_sinh, email, mat_khau) 
                                VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $ho_nv, $ten_nv, $so_dien_thoai, $ngay_sinh, $email, $mat_khau_hash);

        if ($stmt->execute()) {
            // Thêm thành công -> chuyển về fragment customers
            header("Location: ../dashboard_home.php?page=customers&msg=added");
            exit;
        } else {
            echo "<p style='color:red;'>Lỗi: " . $stmt->error . "</p>";
        }
        $stmt->close();
    }
    $check->close();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm khách hàng</title>
    <style>
        body { font-family: Arial; margin: 30px; }
        form { width: 400px; padding: 20px; border: 1px solid #ccc; border-radius: 10px; }
        label { display: block; margin-top: 10px; }
        input, select { width: 100%; padding: 6px; margin-top: 5px; }
        button { margin-top: 15px; padding: 10px 15px; background: #2b7cff; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #1e5ec9; }
        a { display:inline-block; padding:10px 15px; background:#ccc; color:#000; border-radius:5px; margin-left:10px; text-decoration:none;}
        p { margin-top:10px; }
    </style>
</head>
<body>
    <h2>🧾 Thêm khách hàng mới</h2>
    <form method="POST">
        <label>Họ</label>
        <input type="text" name="ho" required>

        <label>Tên</label>
        <input type="text" name="ten" required>

        <label>Ngày sinh:</label>
        <input type="date" name="ngay_sinh">

        <label>Số điện thoại:</label>
        <input type="text" name="sdt" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <button type="submit" name="submit">Thêm khách hàng</button>
        <a href="../dashboard_home.php?page=customers">Quay lại</a>
    </form>
</body>
</html>
