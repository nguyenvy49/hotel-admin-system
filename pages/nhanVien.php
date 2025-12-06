<?php
include '../config.php';


// Xử lý tìm kiếm
$key = "";
if (isset($_POST['key']) && $_POST['key'] != '') {
    $key = $conn->real_escape_string($_POST['key']);
    $sql = "SELECT * FROM nhan_vien 
            WHERE ho_ten LIKE '%$key%' 
            OR email LIKE '%$key%' 
            OR sdt LIKE '%$key%'";
} else {
    $sql = "SELECT * FROM nhan_vien";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Quản lý nhân viên</title>
<style>
    body {
        font-family: "Segoe UI", sans-serif;
        background-color: #f4f6f8;
        margin: 0;
        padding: 0;
    }
    h2 {
        text-align: center;
        color: #2c3e50;
        margin-top: 30px;
    }
    form {
        text-align: center;
        margin: 20px;
    }
    input[type="text"], input[type="submit"] {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 8px;
        outline: none;
        font-size: 14px;
    }
    input[type="text"] {
        width: 300px;
    }
    input[type="submit"] {
        background-color: #4CAF50;
        color: white;
        cursor: pointer;
        transition: 0.3s;
    }
    input[type="submit"]:hover {
        background-color: #43a047;
    }
    a.add-btn {
        margin-left: 15px;
        color: white;
        background-color: #2196F3;
        padding: 10px 15px;
        border-radius: 8px;
        text-decoration: none;
        transition: 0.3s;
    }
    a.add-btn:hover {
        background-color: #1976d2;
    }
    table {
        border-collapse: collapse;
        width: 90%;
        margin: 25px auto;
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    th, td {
        border-bottom: 1px solid #ddd;
        padding: 12px;
        text-align: center;
        font-size: 14px;
    }
    th {
        background-color: #007BFF;
        color: white;
        font-weight: 600;
    }
    tr:hover {
        background-color: #f1f1f1;
    }
    td a {
        text-decoration: none;
        color: #007BFF;
        margin: 0 5px;
        font-weight: 600;
    }
    td a.delete {
        color: #e74c3c;
    }
    td a:hover {
        text-decoration: underline;
    }
    .no-result {
        text-align: center;
        font-style: italic;
        color: #666;
        padding: 20px;
        background-color: #fff8f8;
    }
</style>
</head>
<body>

<h2>👥 Quản lý nhân viên trong hệ thống</h2>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <input type="text" name="key" value="<?php echo htmlspecialchars($key); ?>" placeholder="🔍 Tìm theo tên, email hoặc số điện thoại...">
    <input type="submit" value="Tìm kiếm">
    <a href="add_nhanvien.php" class="add-btn">➕ Thêm nhân viên</a>
</form>

<table>
    <tr>
        <th>Mã NV</th>
        <th>Họ tên</th>
        <th>Ngày sinh</th>
        <th>Giới tính</th>
        <th>SĐT</th>
        <th>Email</th>
        <th>Mật khẩu</th>
        <th>Mã chức vụ</th>
        <th>Chức năng</th>
    </tr>

<?php
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['ma_nhan_vien']}</td>
                <td>{$row['ho_ten']}</td>
                <td>{$row['ngay_sinh']}</td>
                <td>{$row['gioi_tinh']}</td>
                <td>{$row['sdt']}</td>
                <td>{$row['email']}</td>
                <td>••••••••</td>
                <td>{$row['ma_chuc_vu']}</td>
                <td>
                    <a href='edit_nhanvien.php?id={$row['ma_nhan_vien']}'>Sửa</a> | 
                    <a href='delete_nhanvien.php?id={$row['ma_nhan_vien']}' class='delete'
                       onclick='return confirm(\"⚠️ Bạn có chắc muốn xóa nhân viên này không?\")'>Xóa</a>
                </td>
              </tr>";
    }
} else {
    if (!empty($key)) {
        echo "<tr><td colspan='9' class='no-result'>❌ Không tìm thấy kết quả nào khớp với từ khóa '<b>".htmlspecialchars($key)."</b>'.</td></tr>";
    } else {
        echo "<tr><td colspan='9' class='no-result'>Hiện chưa có nhân viên nào trong hệ thống.</td></tr>";
    }
}
$conn->close();
?>
</table>

</body>
</html>
