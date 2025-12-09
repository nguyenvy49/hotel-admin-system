<?php
include '../config.php';

// Kiểm tra tham số GET
if (!isset($_GET['ma_nhan_vien'])) {
    die("❌ Không tìm thấy ID nhân viên.");
}

$ma = (int)$_GET['ma_nhan_vien'];

// Lấy dữ liệu nhân viên
$sql = "SELECT * FROM nhan_vien WHERE ma_nhan_vien = $ma";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    die("❌ Nhân viên không tồn tại.");
}

// Xử lý cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ho_ten     = $_POST['ho_ten'];
    $ngay_sinh  = $_POST['ngay_sinh'];
    $gioi_tinh  = $_POST['gioi_tinh'];
    $sdt        = $_POST['sdt'];
    $email      = $_POST['email'];
    $dia_chi    = $_POST['dia_chi'];
    $ma_chuc_vu = $_POST['ma_chuc_vu'];

    $update_sql = "
        UPDATE nhan_vien SET
            ho_ten='$ho_ten',
            ngay_sinh='$ngay_sinh',
            gioi_tinh='$gioi_tinh',
            sdt='$sdt',
            email='$email',
            dia_chi='$dia_chi',
            ma_chuc_vu='$ma_chuc_vu'
        WHERE ma_nhan_vien=$ma
    ";

    if (mysqli_query($conn, $update_sql)) {
        header("Location: ../dashboard_home.php?page=nhanvien&msg=updated");
        exit;
    } else {
        echo "Lỗi SQL: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Sửa nhân viên</title>
<script src="https://cdn.tailwindcss.com"></script>

<style>
    body {
        font-family: 'Inter', sans-serif;
    }
</style>

</head>
<body class="bg-gray-100 min-h-screen">

<div class="max-w-4xl mx-auto mt-14">

    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800 tracking-tight">
            ✏️ Cập nhật thông tin nhân viên
        </h1>

        <a href="../dashboard_home.php?page=nhanvien"
           class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg text-sm font-semibold shadow-sm">
            ⬅ Quay lại danh sách
        </a>
    </div>

    <!-- Card Form -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-10">

        <form method="POST" class="space-y-7">

            <!-- Họ tên -->
            <div>
                <label class="block mb-2 font-medium text-gray-700">Họ & Tên</label>
                <input type="text" name="ho_ten" value="<?= $row['ho_ten'] ?>"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 
                           focus:ring-blue-500 focus:border-blue-500 transition">
            </div>

            <!-- Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>
                    <label class="block mb-2 font-medium text-gray-700">Ngày sinh</label>
                    <input type="date" name="ngay_sinh" value="<?= $row['ngay_sinh'] ?>"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 
                               focus:ring-blue-500 focus:border-blue-500 transition">
                </div>

                <div>
                    <label class="block mb-2 font-medium text-gray-700">Giới tính</label>
                    <select name="gioi_tinh"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 
                               focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="Nam"  <?= $row['gioi_tinh']=='Nam'?'selected':'' ?>>Nam</option>
                        <option value="Nữ"   <?= $row['gioi_tinh']=='Nữ'?'selected':'' ?>>Nữ</option>
                        <option value="Khác" <?= $row['gioi_tinh']=='Khác'?'selected':'' ?>>Khác</option>
                    </select>
                </div>

            </div>

            <!-- SĐT -->
            <div>
                <label class="block mb-2 font-medium text-gray-700">Số điện thoại</label>
                <input type="text" name="sdt" value="<?= $row['sdt'] ?>"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 
                           focus:ring-blue-500 focus:border-blue-500 transition">
            </div>

            <!-- Email -->
            <div>
                <label class="block mb-2 font-medium text-gray-700">Email</label>
                <input type="email" name="email" value="<?= $row['email'] ?>"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 
                           focus:ring-blue-500 focus:border-blue-500 transition">
            </div>

            <!-- Địa chỉ -->
            <div>
                <label class="block mb-2 font-medium text-gray-700">Địa chỉ</label>
                <input type="text" name="dia_chi" value="<?= $row['dia_chi'] ?>"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 
                           focus:ring-blue-500 focus:border-blue-500 transition">
            </div>

            <!-- Chức vụ -->
            <div>
                <label class="block mb-2 font-medium text-gray-700">Chức vụ</label>
                <select name="ma_chuc_vu"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 
                           focus:ring-blue-500 focus:border-blue-500 transition">

                    <?php
                    $roles = mysqli_query($conn, "SELECT ma_chuc_vu, ten_chuc_vu FROM chuc_vu");
                    while ($r = mysqli_fetch_assoc($roles)) {
                        $selected = ($r['ma_chuc_vu'] == $row['ma_chuc_vu']) ? 'selected' : '';
                        echo "<option value='{$r['ma_chuc_vu']}' $selected>{$r['ten_chuc_vu']}</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-4 pt-6 border-t">

                <a href="../dashboard_home.php?page=nhanvien"
                    class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-xl font-semibold shadow">
                    Hủy
                </a>

                <button type="submit"
                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold shadow">
                    💾 Lưu thay đổi
                </button>

            </div>

        </form>

    </div>
</div>

</body>
</html>
