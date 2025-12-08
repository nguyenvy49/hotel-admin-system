<?php
include '../config.php'; // đường dẫn đến file cấu hình database

if (!isset($_GET['id'])) {
    die("<h2 style='color:red; text-align:center;'>⚠️ Thiếu mã khách hàng để sửa!</h2>");
}

$id = $_GET['id'];

// Nếu nhấn nút cập nhật
if (isset($_POST['submit'])) {
    $ho = $_POST['ho'];
    $ten = $_POST['ten'];
    $sdt = $_POST['sdt'];
    $ngay_sinh = $_POST['ngay_sinh'];
    $email = $_POST['email'];

    $sql = "UPDATE khach_hang 
            SET ho='$ho', ten='$ten', sdt='$sdt', ngay_sinh='$ngay_sinh', email='$email'
            WHERE ma_khach_hang='$id'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
                alert('✅ Cập nhật thông tin khách hàng thành công!');
                window.location.href='customers.php';
              </script>";
        exit();
    } else {
        echo "<p style='color:red; text-align:center;'>❌ Lỗi: " . $conn->error . "</p>";
    }
}

// Lấy dữ liệu khách hàng hiện tại
$sql = "SELECT * FROM khach_hang WHERE ma_khach_hang='$id'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if (!$row) {
    die("<h2 style='color:red; text-align:center;'>Không tìm thấy khách hàng!</h2>");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Chỉnh sửa khách hàng</title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<style>
    body {
        background: linear-gradient(135deg, #f9fafb, #eef2ff);
        font-family: 'Poppins', sans-serif;
    }
    .form-container {
        max-width: 600px;
        margin: 50px auto;
        background: white;
        padding: 30px 40px;
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        transition: 0.3s;
    }
    .form-container:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    .form-title {
        text-align: center;
        color: #333;
        font-size: 26px;
        font-weight: 600;
        margin-bottom: 25px;
    }
    label {
        font-weight: 500;
        color: #444;
    }
    input {
        margin-top: 6px;
    }
</style>
</head>
<body>

<div class="form-container">
    <h2 class="form-title">✏️ Chỉnh sửa thông tin khách hàng</h2>
    <form method="post" class="space-y-5">

        <div>
            <label>Mã khách hàng</label>
            <input type="text" value="<?php echo $row['ma_khach_hang']; ?>" disabled
                   class="w-full bg-gray-100 border border-gray-300 rounded-lg px-3 py-2 cursor-not-allowed">
        </div>

        <div>
            <label>Họ</label>
            <input type="text" name="ho" value="<?php echo htmlspecialchars($row['ho']); ?>" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400">
        </div>

        <div>
            <label>Tên</label>
            <input type="text" name="ten" value="<?php echo htmlspecialchars($row['ten']); ?>" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400">
        </div>

        <div>
            <label>Số điện thoại</label>
            <input type="text" name="sdt" value="<?php echo htmlspecialchars($row['sdt']); ?>"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400">
        </div>

        <div>
            <label>Ngày sinh</label>
            <input type="date" name="ngay_sinh" value="<?php echo htmlspecialchars($row['ngay_sinh']); ?>"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400">
        </div>

        <div>
            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400">
        </div>

        <div class="flex justify-between items-center mt-6">
            <a href="customers.php"
               class="text-gray-600 hover:text-gray-800 transition">← Quay lại danh sách</a>
            <button type="submit" name="submit"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-5 py-2 rounded-lg transition">💾 Cập nhật</button>
        </div>

    </form>
</div>

</body>
</html>
