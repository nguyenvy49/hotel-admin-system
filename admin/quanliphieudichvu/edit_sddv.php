<?php
include '../config.php';

$id = (int)$_GET['id'];

// Lấy dữ liệu cũ
$old_q = mysqli_query($conn, "SELECT * FROM phieu_su_dung_dich_vu WHERE ma_sddv = $id");
$old = mysqli_fetch_assoc($old_q);

if (!$old) die("Phiếu không tồn tại!");

// Nếu submit form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $ma_dp = $_POST['ma_dat_phong'];
    $ma_dv = $_POST['ma_dich_vu'];
    $ngay = $_POST['ngay_su_dung'];
    $sl = $_POST['so_luong'];
    $dg = $_POST['don_gia'];

    // UPDATE giống style file edit_datphong.php
    $sql = "
        UPDATE phieu_su_dung_dich_vu SET 
            ma_dat_phong = '$ma_dp',
            ma_dich_vu = '$ma_dv',
            ngay_su_dung = '$ngay',
            so_luong = '$sl',
            don_gia = '$dg'
        WHERE ma_sddv = $id
    ";

    if (mysqli_query($conn, $sql)) {
        header("Location: ../dashboard_home.php?page=dichvu");
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
    <title>Sửa phiếu sử dụng dịch vụ</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

    <div class="max-w-3xl mx-auto mt-10">

        <!-- Title -->
        <h2 class="text-3xl font-bold mb-6 text-gray-800">
            Sửa phiếu sử dụng dịch vụ
        </h2>

        <div class="bg-white shadow-lg rounded-xl p-8">

            <form method="POST" class="space-y-6">

                <!-- Mã đặt phòng -->
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Mã đặt phòng</label>
                    <input type="number" name="ma_dat_phong" value="<?= $old['ma_dat_phong'] ?>" required
                           class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Mã dịch vụ -->
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Mã dịch vụ</label>
                    <input type="number" name="ma_dich_vu" value="<?= $old['ma_dich_vu'] ?>" required
                           class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Ngày sử dụng -->
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Ngày sử dụng</label>
                    <input type="date" name="ngay_su_dung" value="<?= $old['ngay_su_dung'] ?>" required
                           class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Số lượng -->
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Số lượng</label>
                    <input type="number" name="so_luong" value="<?= $old['so_luong'] ?>" required
                           class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Đơn giá -->
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Đơn giá</label>
                    <input type="number" name="don_gia" value=" <?= number_format($r['don_gia'], 0, ',', '.') ?> đ" required
                           class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Buttons -->
                <div class="flex items-center gap-4 pt-4">

                    <button type="submit" name="submit"
                        class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow hover:bg-blue-700 transition">
                        Cập nhật
                    </button>

                    <a href="../dashboard_home.php?page=dichvu"
                       class="text-gray-600 hover:text-gray-800 transition font-medium">
                        ← Quay lại
                    </a>
                </div>

            </form>

        </div>
    </div>

</body>
</html>
