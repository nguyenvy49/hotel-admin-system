<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hotel Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <style>
    body {
      background: #f1f5f9;
      font-family: 'Segoe UI', sans-serif;
      display: flex;
    }

    .sidebar {
      width: 250px;
      height: 100vh;
      background: linear-gradient(180deg, #1e293b, #334155);
      color: #fff;
      position: fixed;
      top: 0;
      left: 0;
      padding-top: 25px;
    }

    .sidebar h3 {
      text-align: center;
      font-weight: 700;
      margin-bottom: 30px;
    }

    .sidebar ul {
      list-style: none;
      padding: 0;
    }

    .sidebar ul li {
      padding: 12px 25px;
      transition: 0.3s;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .sidebar ul li:hover, .sidebar ul li.active {
      background: #475569;
    }

    .content {
      margin-left: 250px;
      padding: 30px;
      width: 100%;
      min-height: 100vh;
      background: #f8fafc;
      transition: 0.3s;
    }

    iframe {
      width: 100%;
      height: 90vh;
      border: none;
      border-radius: 10px;
      background: white;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
  </style>
</head>

<body>
  <div class="sidebar">
    <h3>🏨 Hotel Admin</h3>
    <ul>
      <li onclick="loadPage('ql_nhanvien.php')"><i class='bx bx-user'></i> Nhân viên</li>
      <li onclick="loadPage('ql_khachhang.php')"><i class='bx bx-group'></i> Khách hàng</li>
      <li onclick="loadPage('ql_phong.php')"><i class='bx bx-bed'></i> Phòng</li>
      <li onclick="loadPage('ql_datphong.php')"><i class='bx bx-calendar'></i> Đặt phòng</li>
      <li onclick="loadPage('ql_dichvu.php')"><i class='bx bx-gift'></i> Dịch vụ</li>
      <li onclick="loadPage('ql_thanhtoan.php')"><i class='bx bx-credit-card'></i> Thanh toán</li>
      <li onclick="loadPage('ql_phanhoi.php')"><i class='bx bx-message-dots'></i> Phản hồi</li>
      <li onclick="loadPage('ql_baocao.php')"><i class='bx bx-bar-chart'></i> Báo cáo</li>
      <li onclick="loadPage('ql_cauhinh.php')"><i class='bx bx-cog'></i> Cấu hình</li>
    </ul>
  </div>

  <div class="content">
    <iframe id="mainFrame" src="dashboard_home.php"></iframe>
  </div>

  <script>
    function loadPage(page) {
      document.getElementById('mainFrame').src = page;
      // Đổi màu active
      document.querySelectorAll('.sidebar ul li').forEach(li => li.classList.remove('active'));
      event.currentTarget.classList.add('active');
    }
  </script>
</body>
</html>
