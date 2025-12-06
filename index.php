<?php
include 'config.php';

// Thống kê nhanh (giả lập dữ liệu)
$tong_khachhang = 1245;
$tong_phong = 85;
$phong_trong = 28;
$doanhthu_thang = "325,000,000";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Trang chủ quản trị khách sạn</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      background-color: #f1f5f9;
      font-family: 'Segoe UI', sans-serif;
    }
    .dashboard-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 30px;
    }
    .dashboard-header h2 {
      font-weight: 700;
      color: #1e293b;
    }
    .stats-card {
      background: white;
      border-radius: 16px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      padding: 25px;
      display: flex;
      align-items: center;
      gap: 15px;
      transition: 0.3s;
    }
    .stats-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 4px 14px rgba(0,0,0,0.12);
    }
    .stats-card i {
      font-size: 40px;
      color: #2563eb;
    }
    .stats-card h5 {
      margin: 0;
      font-weight: 700;
    }
    .chart-container {
      background: white;
      border-radius: 16px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      padding: 25px;
      margin-top: 30px;
    }
    .recent-table {
      background: white;
      border-radius: 16px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      padding: 20px;
      margin-top: 30px;
    }
  </style>
</head>

<body>
  <div class="container mt-4">
    <div class="dashboard-header">
      <h2>Xin chào, Quản trị viên 🌟</h2>
      <p class="text-muted">Cập nhật lần cuối: <?php echo date("H:i d/m/Y"); ?></p>
    </div>

    <!-- Thống kê nhanh -->
    <div class="row g-4">
      <div class="col-md-3">
        <div class="stats-card">
          <i class='bx bx-group text-primary'></i>
          <div>
            <h5><?php echo $tong_khachhang; ?></h5>
            <p class="text-muted mb-0">Khách hàng</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stats-card">
          <i class='bx bx-bed text-success'></i>
          <div>
            <h5><?php echo $tong_phong; ?></h5>
            <p class="text-muted mb-0">Tổng số phòng</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stats-card">
          <i class='bx bx-door-open text-warning'></i>
          <div>
            <h5><?php echo $phong_trong; ?></h5>
            <p class="text-muted mb-0">Phòng trống</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stats-card">
          <i class='bx bx-dollar-circle text-danger'></i>
          <div>
            <h5><?php echo $doanhthu_thang; ?>₫</h5>
            <p class="text-muted mb-0">Doanh thu tháng này</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Biểu đồ -->
    <div class="chart-container">
      <h5 class="fw-bold mb-3">Thống kê doanh thu 6 tháng gần nhất</h5>
      <canvas id="revenueChart" height="100"></canvas>
    </div>

    <!-- Bảng đơn đặt phòng gần đây -->
    <div class="recent-table">
      <h5 class="fw-bold mb-3">Đơn đặt phòng gần đây</h5>
      <table class="table table-hover align-middle text-center">
        <thead class="table-primary">
          <tr>
            <th>Mã đơn</th>
            <th>Tên khách</th>
            <th>Phòng</th>
            <th>Ngày nhận</th>
            <th>Ngày trả</th>
            <th>Trạng thái</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>DP1023</td>
            <td>Nguyễn Minh Anh</td>
            <td>VIP - 302</td>
            <td>15/10/2025</td>
            <td>18/10/2025</td>
            <td><span class="badge bg-success">Hoàn thành</span></td>
          </tr>
          <tr>
            <td>DP1024</td>
            <td>Trần Quốc Việt</td>
            <td>Đôi - 205</td>
            <td>19/10/2025</td>
            <td>21/10/2025</td>
            <td><span class="badge bg-warning text-dark">Đang ở</span></td>
          </tr>
          <tr>
            <td>DP1025</td>
            <td>Phạm Hoài Thương</td>
            <td>Đơn - 108</td>
            <td>22/10/2025</td>
            <td>24/10/2025</td>
            <td><span class="badge bg-info text-dark">Đã xác nhận</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <script>
    // Biểu đồ doanh thu
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: ['Th4', 'Th5', 'Th6', 'Th7', 'Th8', 'Th9'],
        datasets: [{
          label: 'Doanh thu (VNĐ)',
          data: [220, 250, 280, 310, 325, 360],
          borderColor: '#2563eb',
          backgroundColor: 'rgba(37,99,235,0.2)',
          tension: 0.3,
          fill: true,
          borderWidth: 3
        }]
      },
      options: {
        plugins: {
          legend: { display: false }
        },
        scales: {
          y: { beginAtZero: true }
        }
      }
    });
  </script>
</body>
</html>
