<?php
include '../config.php';

if (!isset($_GET['id'])) {
    die("Thiếu ID hóa đơn!");
}

$id = intval($_GET['id']);

// Kiểm tra hóa đơn có tồn tại không
$check = $conn->query("SELECT * FROM hoa_don WHERE ma_hoa_don = $id");
if ($check->num_rows == 0) {
    die("Hóa đơn không tồn tại!");
}

// Nếu người dùng xác nhận xoá
if (isset($_POST['confirm'])) {

    // Xoá bằng prepared statement để an toàn hơn
    $stmt = $conn->prepare("DELETE FROM hoa_don WHERE ma_hoa_don = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: ../dashboard_home.php?page=hoadon&msg=deleted");
        exit;
    } else {
        echo "<h2 style='color:red;text-align:center;margin-top:50px'>
                ❌ Không thể xóa hóa đơn do ràng buộc dữ liệu!<br>
                (Có thể hóa đơn đang được liên kết với bảng khác)
              </h2>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Xoá hóa đơn</title>

<style>
    body {
        background: #f1f5f9;
        font-family: Arial, sans-serif;
        padding: 40px;
    }

    .box {
        max-width: 420px;
        background: white;
        margin: auto;
        padding: 30px;
        border-radius: 14px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        text-align: center;
    }

    .btn {
        padding: 12px 22px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 15px;
        margin: 10px;
    }

    .yes {
        background: #dc2626;
        color: white;
    }
    .yes:hover {
        background: #b91c1c;
    }

    .no {
        background: #e5e7eb;
        color: #111;
    }
    .no:hover {
        background: #d1d5db;
    }

</style>
</head>
<body>

<div class="box">
    <h2 style="color:#dc2626">⚠ Xác nhận xoá hóa đơn</h2>
    <p style="margin-top:10px;color:#334155;font-size:16px;">
        Em có chắc chắn muốn xoá hóa đơn <b>#<?= $id ?></b> không?<br>
        Hành động này không thể hoàn tác.
    </p>

    <form method="POST">
        <button name="confirm" class="btn yes">Xoá ngay</button>
        <a href="../dashboard_home.php?page=hoadon">
            <button type="button" class="btn no">Hủy</button>
        </a>
    </form>
</div>

</body>
</html>
