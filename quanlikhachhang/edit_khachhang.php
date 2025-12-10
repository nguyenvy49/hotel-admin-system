<?php
include '../config.php';

// Kiểm tra tham số GET
if (!isset($_GET['ma_khach_hang'])) {
    die("❌ Không tìm thấy ID khách hàng.");
}

$ma = (int)$_GET['ma_khach_hang'];

// Lấy dữ liệu khách hàng
$sql = "SELECT * FROM khach_hang WHERE ma_khach_hang = $ma";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    die("❌ Khách hàng không tồn tại.");
}

// Xử lý cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ho     = $_POST['ho'];
    $ten    = $_POST['ten'];
    $ngay_sinh  = $_POST['ngay_sinh'];
    $sdt        = $_POST['sdt'];
    $email      = $_POST['email'];

    // Kiểm tra trùng email / sdt khi sửa
    $check = $conn->prepare("SELECT ma_khach_hang FROM khach_hang WHERE (email=? OR sdt=?) AND ma_khach_hang != ?");
    $check->bind_param("ssi", $email, $sdt, $ma);
    $check->execute();
    $exists = $check->get_result();

    if ($exists->num_rows > 0) {
        echo "<p style='color:red; padding:10px;'>⚠ Email hoặc số điện thoại đã tồn tại.</p>";
    } else {
        $update_sql = "
            UPDATE khach_hang SET
                ho='$ho',
                ten='$ten',
                ngay_sinh='$ngay_sinh',
                sdt='$sdt',
                email='$email'
            WHERE ma_khach_hang=$ma
        ";

        if (mysqli_query($conn, $update_sql)) {
            header("Location: ../dashboard_home.php?page=customers&msg=updated");
            exit;
        } else {
            echo "Lỗi SQL: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Sửa khách hàng</title>
<script src="https://cdn.tailwindcss.com"></script>
<style> body { font-family: 'Inter', sans-serif; } </style>
</head>

<body class="bg-gray-100 min-h-screen">

<div class="max-w-4xl mx-auto mt-14">

    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800 tracking-tight">
            ✏️ Cập nhật thông tin khách hàng
        </h1>

        <a href="../dashboard_home.php?page=customers"
           class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg text-sm font-semibold shadow-sm">
            ⬅ Quay lại danh sách
        </a>
    </div>

    <!-- Card Form -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-10">

        <form method="POST" class="space-y-7">

            <!-- Họ -->
            <div>
                <label class="block mb-2 font-medium text-gray-700">Họ</label>
                <input type="text" name="ho" value="<?= $row['ho'] ?>"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 
                           focus:ring-blue-500 focus:border-blue-500 transition">
            </div>

            <!-- Tên -->
            <div>
                <label class="block mb-2 font-medium text-gray-700">Tên</label>
                <input type="text" name="ten" value="<?= $row['ten'] ?>"
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
                    <label class="block mb-2 font-medium text-gray-700">Số điện thoại</label>
                    <input type="text" name="sdt" value="<?= $row['sdt'] ?>"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 
                               focus:ring-blue-500 focus:border-blue-500 transition">
                </div>
            </div>

            <!-- Email -->
            <div>
                <label class="block mb-2 font-medium text-gray-700">Email</label>
                <input type="email" name="email" value="<?= $row['email'] ?>"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 
                           focus:ring-blue-500 focus:border-blue-500 transition">
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-4 pt-6 border-t">

                <a href="../dashboard_home.php?page=customers"
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
