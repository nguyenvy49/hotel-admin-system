<?php
include '../config.php';

// Lấy ID dịch vụ
$id = $_GET['id'];
$q = $conn->query("SELECT * FROM dich_vu WHERE ma_dich_vu = $id");
$dv = $q->fetch_assoc();

if (!$dv) die("Không tìm thấy dịch vụ");

// Nếu nhấn lưu
if (isset($_POST['submit'])) {
    $ten = $_POST['ten_dich_vu'];

    // Làm sạch giá nhập vào
    $gia_raw = $_POST['don_gia'];
    $gia = str_replace(['.', ','], '', $gia_raw); // xóa dấu . và dấu ,
    $gia = intval($gia); // đưa về số nguyên

    $sql = "UPDATE dich_vu 
            SET ten_dich_vu = '$ten', don_gia = '$gia'
            WHERE ma_dich_vu = $id";

    if ($conn->query($sql) === TRUE) {
        header("Location: ../dashboard_home.php?page=dichvu&msg=updated");
        exit;
    } else {
        echo "Lỗi SQL: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Sửa dịch vụ</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>

<style>
    body {
        font-family: "Inter", sans-serif;
        background: linear-gradient(135deg, #e8efff 0%, #f7faff 100%);
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.65);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }

    .input-focus:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .3);
    }

    .btn-main {
        background: linear-gradient(135deg, #2563eb, #1e40af);
    }

    .btn-main:hover {
        background: linear-gradient(135deg, #1d4ed8, #1e3a8a);
    }
</style>

</head>

<body class="min-h-screen flex items-center justify-center p-6">

<div class="glass-card max-w-2xl w-full p-10 rounded-3xl">

    <!-- Header -->
    <div class="flex items-center gap-4 mb-10">
        <div class="w-14 h-14 bg-yellow-100 text-yellow-700 flex items-center justify-center rounded-2xl text-3xl shadow-inner">
            ✏
        </div>
        <h1 class="text-3xl font-bold text-gray-800">Sửa dịch vụ</h1>
    </div>

    <form method="POST" class="space-y-7">

        <!-- Tên dịch vụ -->
        <div>
            <label class="font-semibold text-gray-700 text-lg">Tên dịch vụ</label>
            <input type="text" name="ten_dich_vu"
                   value="<?= $dv['ten_dich_vu'] ?>"
                   required
                   class="w-full p-4 border rounded-2xl mt-2 bg-white/70 shadow-sm input-focus transition">
        </div>

        <!-- Đơn giá -->
        <div>
            <label class="font-semibold text-gray-700 text-lg">Đơn giá (VNĐ)</label>
            <input type="number" name="don_gia"
                   value="<?= $dv['don_gia'] ?>"
                   required
                   class="w-full p-4 border rounded-2xl mt-2 bg-white/70 shadow-sm input-focus transition">
        </div>

        <!-- Buttons -->
        <div class="flex items-center gap-4 pt-4">

            <button type="submit" name="submit"
                class="btn-main px-7 py-3 text-white rounded-2xl font-semibold shadow-lg transition active:scale-95">
                💾 Lưu thay đổi
            </button>

            <a href="../dashboard_home.php?page=dichvu"
                class="px-7 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-2xl shadow transition active:scale-95">
                ⬅ Quay lại
            </a>

        </div>

    </form>

</div>

</body>
</html>
