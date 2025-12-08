<?php
include '../config.php'; // Đảm bảo đường dẫn đúng với cấu trúc thư mục của em

if (!isset($_GET['id'])) {
    die("<div style='
        font-family: Segoe UI;
        color: #ff4444;
        text-align:center;
        margin-top:50px;
        font-size:18px;'>
        ❌ Thiếu mã khách hàng để xóa!
    </div>");
}

$id = intval($_GET['id']);

// Truy vấn kiểm tra khách hàng có tồn tại không
$check = $conn->query("SELECT * FROM khach_hang WHERE ma_khach_hang = $id");
if ($check->num_rows == 0) {
    die("<div style='
        font-family: Segoe UI;
        color: #ff4444;
        text-align:center;
        margin-top:50px;
        font-size:18px;'>
        ⚠️ Không tìm thấy khách hàng có mã #$id!
    </div>");
}

// Nếu tồn tại thì tiến hành xóa
$sql = "DELETE FROM khach_hang WHERE ma_khach_hang = $id";

if ($conn->query($sql) === TRUE) {
    echo "
    <html>
    <head>
    <meta charset='UTF-8'>
    <title>Xóa thành công</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8fafc;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .box {
            text-align: center;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 380px;
        }
        .success-icon {
            font-size: 60px;
            color: #4CAF50;
        }
        h2 {
            color: #333;
            margin-top: 15px;
        }
        p {
            color: #666;
            font-size: 15px;
            margin-bottom: 25px;
        }
        a {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            transition: 0.3s;
        }
        a:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        setTimeout(function(){
            window.location.href = 'customers.php';
        }, 1500);
    </script>
    </head>
    <body>
        <div class='box'>
            <div class='success-icon'>✅</div>
            <h2>Xóa thành công!</h2>
            <p>Khách hàng có mã #$id đã được xóa khỏi hệ thống.</p>
            <a href='customers.php'>⬅ Quay lại danh sách</a>
        </div>
    </body>
    </html>
    ";
} else {
    echo "
    <div style='
        font-family: Segoe UI;
        color: #ff4444;
        text-align:center;
        margin-top:50px;
        font-size:18px;'>
        ❌ Lỗi khi xóa khách hàng: " . $conn->error . "
    </div>";
}

$conn->close();
?>
