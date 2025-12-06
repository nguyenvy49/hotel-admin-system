<?php
// dashboard_home.php
include 'config.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quản lý khách sạn - Trang Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f9f8f6; /* màu be trắng nhẹ */
      font-family: 'Poppins', sans-serif;
      transition: background-color 0.3s ease;
    }

    .sidebar {
      background-color: #fffdf9;
      box-shadow: 2px 0 8px rgba(0,0,0,0.05);
      transition: all 0.3s ease;
    }

    .sidebar-item {
      transition: background 0.2s ease, transform 0.2s ease;
      display: block;
      width: 100%;
      text-align: left;
      padding: 12px 16px;
      border-radius: 10px;
      color: #444;
      font-weight: 500;
    }

    .sidebar-item:hover {
      background-color: #f0ebe4;
      transform: translateX(5px);
      color: #000;
    }

    .fade-enter {
      opacity: 0;
      transform: translateY(10px);
    }

    .fade-enter-active {
      opacity: 1;
      transform: translateY(0);
      transition: all 0.4s ease;
    }

    iframe {
      border: none;
    }
  </style>
</head>
<body>

  <!-- Layout chính -->
  <div class="flex h-screen">

    <!-- Sidebar -->
    <aside class="sidebar w-64 p-5">
      <h2 class="text-2xl font-bold text-gray-700 mb-6 text-center">🏨 Admin Panel</h2>
      <nav class="space-y-3">

        <!-- 🌟 Các nút điều hướng -->
       <button onclick="window.location.href='dashboard_home.php'" class="sidebar-item">🏠 Trang chủ</button>
<button onclick="window.location.href='pages/customers.php'" class="sidebar-item">👤 Khách hàng</button>
<button onclick="window.location.href='pages/nhanVien.php'" class="sidebar-item">💼 Nhân viên</button>
<button onclick="window.location.href='pages/phong.php'" class="sidebar-item">🛏️ Phòng</button>
<button onclick="window.location.href='pages/datPhong.php'" class="sidebar-item">📅 Đặt phòng</button>
<button onclick="window.location.href='pages/dichVu.php'" class="sidebar-item">🧴 Dịch vụ</button>
<button onclick="window.location.href='pages/hoaDon.php'" class="sidebar-item">🧾 Hóa đơn</button>
<button onclick="window.location.href='pages/xuLi.php'" class="sidebar-item">🛠️ Xử lý</button>

      </nav>
    </aside>

    <!-- Nội dung chính -->
    <main class="flex-1 p-8 overflow-y-auto">
      <div class="fade-enter-active">
        <h1 class="text-3xl font-bold text-gray-700 mb-4">Chào mừng đến với trang quản lý khách sạn 🏨</h1>
        <p class="text-gray-600 mb-8">Chọn danh mục bên trái để bắt đầu quản lý hệ thống.</p>

        <!-- Dashboard tổng quan -->
        <div class="grid grid-cols-3 gap-6">
          <div class="bg-white shadow-md p-6 rounded-2xl hover:shadow-lg transition">
            <h2 class="text-lg font-semibold text-gray-800 mb-2">Tổng khách hàng</h2>
            <p class="text-2xl font-bold text-blue-600">
              <?php
                $result = $conn->query("SELECT COUNT(*) AS total FROM khach_hang");
                echo $result->fetch_assoc()['total'];
              ?>
            </p>
          </div>

          <div class="bg-white shadow-md p-6 rounded-2xl hover:shadow-lg transition">
            <h2 class="text-lg font-semibold text-gray-800 mb-2">Phòng đang đặt</h2>
            <p class="text-2xl font-bold text-yellow-600">
              <?php
                $result = $conn->query("SELECT COUNT(*) AS total FROM dat_phong WHERE trang_thai = 'Đã đặt'");
                echo $result->fetch_assoc()['total'];
              ?>
            </p>
          </div>

          <div class="bg-white shadow-md p-6 rounded-2xl hover:shadow-lg transition">
            <h2 class="text-lg font-semibold text-gray-800 mb-2">Tổng doanh thu</h2>
            <p class="text-2xl font-bold text-green-600">
              <?php
                $result = $conn->query("SELECT SUM(tong_tien) AS total FROM hoa_don WHERE trang_thai = 'Đã thanh toán'");
                echo number_format($result->fetch_assoc()['total'] ?? 0, 0, ',', '.') . " VNĐ";
              ?>
            </p>
          </div>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
