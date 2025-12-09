<?php
include '../config.php';

if (isset($_POST['submit'])) {
    $ten = $_POST['ten_dich_vu'];
    $gia = $_POST['don_gia'];

    $sql = "INSERT INTO dich_vu (ten_dich_vu, don_gia)
            VALUES ('$ten', '$gia')";

    if ($conn->query($sql) === TRUE) {
        header("Location: ../dashboard_home.php?page=dichvu&msg=added");
        exit;
    } else {
        echo "Lỗi: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thêm dịch vụ</title>

<!-- FONT + TAILWIND -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>

<style>
    body {
        font-family: "Inter", sans-serif;
        background: linear-gradient(135deg, #dfe8ff 0%, #f7faff 100%);
    }

    /* Glass card */
    .glass-card {
        background: rgba(255, 255, 255, 0.65);
        backdrop-filter: blur(18px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08);
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

<div class="glass-card w-full max-w-2xl p-10 rounded-3xl">

    <!-- Header -->
    <div class="flex items-center gap-4 mb-8">
        <div class="w-14 h-14 bg-blue-100 text-blue-700 flex items-center justify-center rounded-2xl text-3xl shadow-inner">
            ➕
        </div>
        <h1 class="text-3xl font-bold text-gray-800 tracking-tight">
            Thêm dịch vụ mới
        </h1>
    </div>

    <p class="text-gray-600 mb-10 leading-relaxed text-lg">
        Nhập thông tin chi tiết để bổ sung dịch vụ vào hệ thống quản lý khách sạn.
    </p>

    <!-- Form -->
    <form method="POST" class="space-y-7">

        <!-- Tên dịch vụ -->
        <div>
            <label class="font-semibold text-gray-700 text-lg">Tên dịch vụ</label>
            <input type="text" name="ten_dich_vu" required
                class="w-full p-4 border rounded-2xl mt-2 input-focus transition bg-white/70 shadow-sm">
        </div>

        <!-- Đơn giá -->
        <div>
            <label class="font-semibold text-gray-700 text-lg">Đơn giá (VNĐ)</label>
            <input type="number" name="don_gia" required
                class="w-full p-4 border rounded-2xl mt-2 input-focus transition bg-white/70 shadow-sm">
        </div>

        <!-- Buttons -->
        <div class="flex items-center gap-4 pt-4">

            <button type="submit" name="submit"
                class="btn-main px-7 py-3 text-white rounded-2xl font-semibold shadow-lg transition active:scale-95">
                💾 Lưu dịch vụ
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
