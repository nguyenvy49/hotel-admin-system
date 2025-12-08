<?php
include '../config.php';

// Tìm kiếm
$key = "";
if (isset($_POST['key']) && $_POST['key'] != '') {
    $key = $conn->real_escape_string($_POST['key']);
    $sql = "SELECT * FROM khach_hang 
            WHERE ho LIKE '%$key%' 
            OR ten LIKE '%$key%' 
            OR email LIKE '%$key%' 
            OR sdt LIKE '%$key%'";
} else {
    $sql = "SELECT * FROM khach_hang";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Danh sách khách hàng</title>
<style>
    body {
        font-family: "Segoe UI", sans-serif;
        background: #f4f6f8;
        margin: 0;
        padding: 0;
    }
    h2 {
        text-align: center;
        color: #2c3e50;
        margin-top: 30px;
        font-weight: 600;
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
        color: #e74c3c;
        font-weight: 600;
        padding: 5px 8px;
        border-radius: 5px;
        transition: 0.3s;
    }
    td a:hover {
        background-color: #e74c3c;
        color: white;
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

<h2>Danh sách khách hàng đã đăng ký</h2>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <input type="text" name="key" value="<?php echo htmlspecialchars($key); ?>" placeholder=" Tìm theo tên, email hoặc số điện thoại...">
    <input type="submit" value="Tìm kiếm">
</form>

<table>
    <tr>
        <th>Mã KH</th>
        <th>Họ</th>
        <th>Tên</th>
        <th>Số điện thoại</th>
        <th>Ngày sinh</th>
        <th>Email</th>
        <th>Chức năng</th>
    </tr>

<?php
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['ma_khach_hang']}</td>
                <td>{$row['ho']}</td>
                <td>{$row['ten']}</td>
                <td>{$row['sdt']}</td>
                <td>{$row['ngay_sinh']}</td>
                <td>{$row['email']}</td>
                <td>
                    <a href='delete_khachhang.php?id={$row['ma_khach_hang']}'
                       onclick='return confirm(\"⚠️ Bạn có chắc muốn xóa khách hàng này không?\")'>
                        Xóa
                    </a>
                </td>
              </tr>";
    }
} else {
    if (!empty($key)) {
        echo "<tr><td colspan='7' class='no-result'>❌ Không tìm thấy kết quả nào khớp với từ khóa '<b>".htmlspecialchars($key)."</b>'.</td></tr>";
    } else {
        echo "<tr><td colspan='7' class='no-result'>Hiện chưa có khách hàng nào trong hệ thống.</td></tr>";
    }
}
$conn->close();
?>
</table>

</body>
</html>
