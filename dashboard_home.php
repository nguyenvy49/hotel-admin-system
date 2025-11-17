<?php
include 'config.php';

// Tổng số khách hàng
$sql_customers = "SELECT COUNT(*) AS total_customers FROM customers";
$result_customers = $conn->query($sql_customers);
$total_customers = ($result_customers->num_rows > 0) ? $result_customers->fetch_assoc()['total_customers'] : 0;

// Tổng số phòng
$sql_rooms = "SELECT COUNT(*) AS total_rooms FROM rooms";
$result_rooms = $conn->query($sql_rooms);
$total_rooms = ($result_rooms->num_rows > 0) ? $result_rooms->fetch_assoc()['total_rooms'] : 0;

// Phòng trống
$sql_available_rooms = "SELECT COUNT(*) AS available_rooms FROM rooms WHERE status = 'Trong'";
$result_available_rooms = $conn->query($sql_available_rooms);
$available_rooms = ($result_available_rooms->num_rows > 0) ? $result_available_rooms->fetch_assoc()['available_rooms'] : 0;

// Doanh thu tháng này
$sql_revenue = "SELECT SUM(total_price) AS monthly_revenue 
                FROM bookings 
                WHERE status = 'XacNhan' 
                AND MONTH(check_in) = MONTH(CURRENT_DATE()) 
                AND YEAR(check_in) = YEAR(CURRENT_DATE())";
$result_revenue = $conn->query($sql_revenue);
$monthly_revenue = ($result_revenue->num_rows > 0) ? $result_revenue->fetch_assoc()['monthly_revenue'] : 0;
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
<?php
include 'config.php';

// Câu truy vấn đúng với cấu trúc bảng của em
$sql = "SELECT 
            b.id AS booking_id,
            c.full_name AS customer_name,
            r.room_number AS room_name,
            b.check_in,
            b.check_out,
            b.status,
            b.total_price,
            b.created_at
        FROM bookings b
        JOIN customers c ON b.customer_id = c.id
        JOIN rooms r ON b.room_id = r.id
        ORDER BY b.created_at DESC
        LIMIT 5";

$result = $conn->query($sql);
?>
<div class="card p-4 mt-4">
  <h5 class="mb-3"><i class='bx bx-calendar-check'></i> Đơn đặt phòng gần đây</h5>
  <div class="table-responsive">
    <table class="table table-striped align-middle">
      <thead>
        <tr>
          <th>Mã đơn</th>
          <th>Khách hàng</th>
          <th>Phòng</th>
          <th>Ngày nhận</th>
          <th>Ngày trả</th>
          <th>Trạng thái</th>
          <th>Tổng tiền</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0): ?>
          <?php while($row = $result->fetch_assoc()): ?>
            <tr>
              <td>#<?= $row['booking_id'] ?></td>
              <td><?= htmlspecialchars($row['customer_name']) ?></td>
              <td><?= htmlspecialchars($row['room_name']) ?></td>
              <td><?= $row['check_in'] ?></td>
              <td><?= $row['check_out'] ?></td>
              <td>
                <?php if ($row['status'] == 'XacNhan'): ?>
                  <span class="badge bg-success">Xác nhận</span>
                <?php elseif ($row['status'] == 'Huy'): ?>
                  <span class="badge bg-danger">Hủy</span>
                <?php else: ?>
                  <span class="badge bg-warning text-dark">Đang xử lý</span>
                <?php endif; ?>
              </td>
              <td><?= number_format($row['total_price'], 0, ',', '.') ?>₫</td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="7" class="text-center text-muted">Chưa có đơn đặt phòng nào</td></tr>
        <?php endif; ?>
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
