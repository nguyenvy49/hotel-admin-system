<?php
// dashboard.php - Full version (AJAX fragments + layout)
// Yêu cầu: config.php phải tạo kết nối $conn (mysqli)
include 'config.php';

// --- Hàm lấy dữ liệu thống kê (dùng chung cho fragment dashboard) ---
function get_stats($conn) {
    $out = [];

    // 1. Tổng khách hàng
    $out['kh'] = (int) ($conn->query("SELECT COUNT(*) AS total FROM khach_hang")->fetch_assoc()['total'] ?? 0);

    // 2. Phòng đã đặt (dựa trên dat_phong.trang_thai)
    $out['phong_dat'] = (int) ($conn->query("
        SELECT COUNT(*) AS total FROM dat_phong 
        WHERE trang_thai IN ('Đã đặt','Đang ở')
    ")->fetch_assoc()['total'] ?? 0);

    // 3. Doanh thu tháng này (hoa_don.tong_tien, trang_thai = 'Đã thanh toán')
    $out['dt_thang'] = (float) ($conn->query("
        SELECT SUM(tong_tien) AS total FROM hoa_don 
        WHERE trang_thai = 'Đã thanh toán'
          AND MONTH(ngay_thanh_toan) = MONTH(CURDATE())
          AND YEAR(ngay_thanh_toan) = YEAR(CURDATE())
    ")->fetch_assoc()['total'] ?? 0);

    // 4. Dịch vụ sử dụng (tổng so_luong trong phieu_su_dung_dich_vu)
    $out['dv'] = (int) ($conn->query("SELECT SUM(so_luong) AS total FROM phieu_su_dung_dich_vu")->fetch_assoc()['total'] ?? 0);

    // 5. Tình trạng phòng (lấy trực tiếp từ bảng phong)
    $res_pt = $conn->query("
        SELECT trang_thai, COUNT(*) AS cnt
        FROM phong
        GROUP BY trang_thai
    ");
    $out['phong_trang_thai'] = ['Trống'=>0,'Đã đặt'=>0,'Đang dọn dẹp'=>0,'Bảo trì'=>0];
    while ($r = $res_pt->fetch_assoc()) {
        $k = $r['trang_thai'];
        $out['phong_trang_thai'][$k] = (int)$r['cnt'];
    }
    $out['phong_trong'] = $out['phong_trang_thai']['Trống'] ?? 0;
    $out['phong_dang_dat'] = $out['phong_trang_thai']['Đã đặt'] ?? 0;
    $out['phong_bao_tri'] = $out['phong_trang_thai']['Bảo trì'] ?? 0;

    // 6. Doanh thu theo tháng 6 tháng gần nhất (sử dụng hoa_don.ngay_thanh_toan)
    $res = $conn->query("
        SELECT DATE_FORMAT(ngay_thanh_toan,'%m') AS thang, SUM(tong_tien) AS total
        FROM hoa_don
        WHERE trang_thai = 'Đã thanh toán'
          AND ngay_thanh_toan >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        GROUP BY thang
        ORDER BY thang ASC
    ");
    $out['doanh_thu'] = [];
    while ($r = $res->fetch_assoc()) $out['doanh_thu'][] = $r;

    return $out;
}

// helper: badge HTML for room status
function room_badge($status) {
    $map = [
        'Trống' => 'inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800',
        'Đã đặt' => 'inline-flex items-center px-3 py-1 rounded-full text-sm bg-yellow-100 text-yellow-800',
        'Bảo trì' => 'inline-flex items-center px-3 py-1 rounded-full text-sm bg-red-100 text-red-800',
        'Đang dọn dẹp' => 'inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-800',
    ];
    $cls = $map[$status] ?? 'inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-50 text-gray-700';
    return "<span class=\"$cls\">".htmlspecialchars($status)."</span>";
}

// --- Xử lý request fragment (AJAX) ---
if (isset($_GET['fragment']) && $_GET['fragment'] === '1') {
    $page = $_GET['page'] ?? 'home';
    $allowed = ['home','customers','nhanvien','phong','datphong','dichvu','hoadon','xuli'];
    if (!in_array($page, $allowed)) {
        http_response_code(400);
        echo "Page không hợp lệ.";
        exit;
    }

    $stats = get_stats($conn);

    // ---------- FRAGMENT: HOME (dashboard) ----------
    if ($page === 'home') {
        ?>
        <div class="p-6">
          <h1 class="text-3xl font-bold text-gray-700 mb-2">Chào mừng đến với trang quản lý khách sạn</h1>
          <p class="text-gray-600 mb-6">Tổng quan thống kê hệ thống:</p>

          <div class="grid grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow">
              <p class="text-gray-600 text-lg">Khách hàng</p>
              <h1 class="text-4xl font-bold text-blue-600"><?= htmlspecialchars($stats['kh']) ?></h1>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow">
              <p class="text-gray-600 text-lg">Phòng đã đặt</p>
              <h1 class="text-4xl font-bold text-yellow-600"><?= htmlspecialchars($stats['phong_dang_dat']) ?></h1>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow">
              <p class="text-gray-600 text-lg">Doanh thu tháng</p>
              <h1 class="text-3xl font-bold text-green-600">
                <?= number_format($stats['dt_thang'], 0, ',', '.') ?> đ
              </h1>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow">
              <p class="text-gray-600 text-lg">Dịch vụ sử dụng</p>
              <h1 class="text-4xl font-bold text-red-600"><?= htmlspecialchars($stats['dv']) ?></h1>
            </div>

          </div>

          <div class="grid grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl shadow">
              <h2 class="text-xl font-semibold mb-4">Tình trạng phòng</h2>
              <div class="space-y-3">
                <div class="flex justify-between items-center p-2">
                  <span>Phòng trống</span>
                  <span class="px-4 py-1 bg-green-100 text-green-700 rounded-lg"><?= htmlspecialchars($stats['phong_trong']) ?></span>
                </div>
                <div class="flex justify-between items-center p-2">
                  <span>Đang đặt</span>
                  <span class="px-4 py-1 bg-yellow-100 text-yellow-700 rounded-lg"><?= htmlspecialchars($stats['phong_dang_dat']) ?></span>
                </div>
                <div class="flex justify-between items-center p-2">
                  <span>Bảo trì</span>
                  <span class="px-4 py-1 bg-red-100 text-red-700 rounded-lg"><?= htmlspecialchars($stats['phong_bao_tri']) ?></span>
                </div>
                <div class="mt-4">
                  <h3 class="text-sm text-gray-500 mb-2">Chi tiết theo trạng thái</h3>
                  <div class="flex flex-wrap gap-2">
                    <?php foreach ($stats['phong_trang_thai'] as $st => $cnt): ?>
                      <div class="flex items-center space-x-2">
                        <?= room_badge($st) ?>
                        <span class="text-sm text-gray-600 ml-1"><?= $cnt ?></span>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow col-span-2">
              <h2 class="text-xl font-semibold mb-4">Doanh thu 6 tháng gần nhất</h2>
              <canvas id="revenueChart" height="140"></canvas>
            </div>
          </div>
        </div>

        <script>
        (function(){
          const labels = [<?php
            $labels = [];
            $data = [];
            foreach ($stats['doanh_thu'] as $d) {
                $labels[] = "'T".addslashes($d['thang'])."'";
                $data[] = (int)$d['total'];
            }
            echo implode(',', $labels);
          ?>];

          const data = [<?= implode(',', $data) ?>];

          function draw() {
            if (typeof Chart === 'undefined') {
              setTimeout(draw, 100);
              return;
            }
            const ctx = document.getElementById('revenueChart').getContext('2d');
            new Chart(ctx, {
              type: 'line',
              data: {
                labels: labels,
                datasets: [{
                  label: 'Doanh thu',
                  data: data,
                  borderWidth: 3,
                  borderColor: '#3b82f6',
                  backgroundColor: 'rgba(59,130,246,0.25)',
                  tension: 0.3,
                  fill: true
                }]
              },
              options: { responsive: true, maintainAspectRatio: false }
            });
          }
          draw();
        })();
        </script>
        <?php
        exit;
    }

    // ---------- FRAGMENT: CUSTOMERS ----------
    if ($page === 'customers') {
        // chú ý: bảng khach_hang có: ma_khach_hang, ho, ten, sdt, email, ...
        $q = $conn->query("SELECT ma_khach_hang, ho, ten, email, sdt FROM khach_hang ORDER BY ma_khach_hang DESC LIMIT 200");
        ?>
        <div class="p-6">
          <h2 class="text-2xl font-semibold mb-4">Danh sách khách hàng</h2>
          <div class="bg-white rounded-lg shadow overflow-auto table-beauty">
            <table class="min-w-full">
              <thead>
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mã</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Họ & Tên</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SĐT</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($r = $q->fetch_assoc()) : 
                  $full = trim($r['ho'].' '.$r['ten']);
                ?>
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($r['ma_khach_hang']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($full) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($r['email']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($r['sdt']) ?></td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
        <?php
        exit;
    }

    // ---------- FRAGMENT: NHANVIEN ----------
    if ($page === 'nhanvien') {
        // bảng nhan_vien: ma_nhan_vien, ho_ten, ma_chuc_vu (tham chiếu chuc_vu)
        $q = $conn->query("
            SELECT nv.ma_nhan_vien, nv.ho_ten, cv.ten_chuc_vu
            FROM nhan_vien nv
            LEFT JOIN chuc_vu cv ON nv.ma_chuc_vu = cv.ma_chuc_vu
            ORDER BY nv.ma_nhan_vien DESC
            LIMIT 200
        ");
        ?>
        <div class="p-6">
          <h2 class="text-2xl font-semibold mb-4">Nhân viên</h2>
          <div class="bg-white rounded-lg shadow overflow-auto table-beauty">
            <table class="min-w-full">
              <thead>
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mã</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Họ & Tên</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Chức vụ</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($r = $q->fetch_assoc()) : ?>
                  <tr>
                    <td class="px-6 py-4"><?= htmlspecialchars($r['ma_nhan_vien']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($r['ho_ten']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($r['ten_chuc_vu'] ?? '—') ?></td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
        <?php
        exit;
    }

    // ---------- FRAGMENT: PHONG ----------
    if ($page === 'phong') {
        // bảng phong: ma_phong, so_phong, ma_loai_phong, trang_thai
        // join loai_phong để hiển thị tên loại
        $q = $conn->query("
          SELECT p.ma_phong, p.so_phong, p.trang_thai, lp.ten_loai_phong
          FROM phong p
          LEFT JOIN loai_phong lp ON p.ma_loai_phong = lp.ma_loai_phong
          ORDER BY p.so_phong + 0 ASC, p.so_phong ASC
        ");
        ?>
        <div class="p-6">
          <h2 class="text-2xl font-semibold mb-4">Quản lý phòng</h2>
          <div class="grid grid-cols-3 gap-4">
            <?php while ($r = $q->fetch_assoc()) : ?>
              <div class="bg-white p-4 rounded-lg shadow">
                <div class="flex justify-between items-center">
                  <div>
                    <h3 class="font-bold">Phòng <?= htmlspecialchars($r['so_phong']) ?> (<?= htmlspecialchars($r['ten_loai_phong'] ?? '—') ?>)</h3>
                    <p class="mt-2 text-sm text-gray-600">Mã: <?= htmlspecialchars($r['ma_phong']) ?></p>
                  </div>
                  <div>
                    <?= room_badge($r['trang_thai']) ?>
                  </div>
                </div>
              </div>
            <?php endwhile; ?>
          </div>
        </div>
        <?php
        exit;
    }

    // ---------- FRAGMENT: DATPHONG ----------
    if ($page === 'datphong') {
        // dat_phong: ma_dat_phong, ma_khach_hang, ma_phong, ngay_dat, trang_thai
        $q = $conn->query("
          SELECT dp.ma_dat_phong, dp.ma_khach_hang, dp.ma_phong, dp.ngay_dat, dp.trang_thai
          FROM dat_phong dp
          ORDER BY dp.ma_dat_phong DESC
          LIMIT 100
        ");
        ?>
        <div class="p-6">
          <h2 class="text-2xl font-semibold mb-4">Đặt phòng</h2>
          <div class="bg-white rounded-lg shadow overflow-auto table-beauty">
            <table class="min-w-full">
              <thead>
                <tr>
                  <th class="px-6 py-3">Mã</th>
                  <th class="px-6 py-3">Mã KH</th>
                  <th class="px-6 py-3">Mã phòng</th>
                  <th class="px-6 py-3">Ngày đặt</th>
                  <th class="px-6 py-3">Trạng thái</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($r = $q->fetch_assoc()) : ?>
                  <tr>
                    <td class="px-6 py-4"><?= htmlspecialchars($r['ma_dat_phong']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($r['ma_khach_hang']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($r['ma_phong']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($r['ngay_dat']) ?></td>
                    <td class="px-6 py-4"><?= room_badge($r['trang_thai']) ?></td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
        <?php
        exit;
    }

    // ---------- FRAGMENT: DICHVU ----------
   if ($page === 'dichvu') {
    // dich_vu: ma_dich_vu, ten_dich_vu, don_gia
    $q = $conn->query("SELECT ma_dich_vu, ten_dich_vu, don_gia FROM dich_vu ORDER BY ma_dich_vu ASC");
    ?>
    <div class="p-6">
      <h2 class="text-2xl font-semibold mb-4">Dịch vụ</h2>
      <div class="grid grid-cols-3 gap-4 mb-8">
        <?php while ($r = $q->fetch_assoc()) : ?>
          <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="font-bold"><?= htmlspecialchars($r['ten_dich_vu']) ?></h3>
            <p class="mt-2"><?= number_format($r['don_gia'],0,',','.') ?> đ</p>
          </div>
        <?php endwhile; ?>
      </div>

      <!-- Bảng phiếu sử dụng dịch vụ -->
      <h2 class="text-2xl font-semibold mb-4 mt-6">Phiếu sử dụng dịch vụ</h2>
      <div class="bg-white rounded-lg shadow overflow-auto table-beauty">
        <table class="min-w-full">
          <thead>
            <tr>
              <th class="px-6 py-3">Mã SDDV</th>
              <th class="px-6 py-3">Mã đặt phòng</th>
              <th class="px-6 py-3">Mã dịch vụ</th>
              <th class="px-6 py-3">Ngày sử dụng</th>
              <th class="px-6 py-3">Số lượng</th>
              <th class="px-6 py-3">Đơn giá</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $q2 = $conn->query("SELECT * FROM phieu_su_dung_dich_vu ORDER BY ma_sddv DESC LIMIT 200");
            while ($r2 = $q2->fetch_assoc()) :
            ?>
              <tr>
                <td class="px-6 py-4"><?= htmlspecialchars($r2['ma_sddv']) ?></td>
                <td class="px-6 py-4"><?= htmlspecialchars($r2['ma_dat_phong']) ?></td>
                <td class="px-6 py-4"><?= htmlspecialchars($r2['ma_dich_vu']) ?></td>
                <td class="px-6 py-4"><?= htmlspecialchars($r2['ngay_su_dung']) ?></td>
                <td class="px-6 py-4"><?= htmlspecialchars($r2['so_luong']) ?></td>
                <td class="px-6 py-4"><?= number_format($r2['don_gia'],0,',','.') ?> đ</td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php
    exit;
}

    // ---------- FRAGMENT: HOADON ----------
    if ($page === 'hoadon') {
        // hoa_don: ma_hoa_don, ma_dat_phong, tong_tien, trang_thai, ngay_thanh_toan
        $q = $conn->query("
          SELECT ma_hoa_don, ma_dat_phong, tong_tien, trang_thai, ngay_thanh_toan
          FROM hoa_don
          ORDER BY ma_hoa_don DESC
          LIMIT 100
        ");
        ?>
        <div class="p-6">
          <h2 class="text-2xl font-semibold mb-4">Hoá đơn</h2>
          <div class="bg-white rounded-lg shadow overflow-auto table-beauty">
            <table class="min-w-full">
              <thead>
                <tr>
                  <th class="px-6 py-3">Mã</th>
                  <th class="px-6 py-3">Mã đặt phòng</th>
                  <th class="px-6 py-3">Tổng tiền</th>
                  <th class="px-6 py-3">Trạng thái</th>
                  <th class="px-6 py-3">Ngày TT</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($r = $q->fetch_assoc()) : ?>
                  <tr>
                    <td class="px-6 py-4"><?= htmlspecialchars($r['ma_hoa_don']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($r['ma_dat_phong']) ?></td>
                    <td class="px-6 py-4"><?= number_format($r['tong_tien'],0,',','.') ?> đ</td>
                    <td class="px-6 py-4"><?= htmlspecialchars($r['trang_thai']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($r['ngay_thanh_toan']) ?></td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
        <?php
        exit;
    }

    // ---------- FRAGMENT: XULI ----------
    if ($page === 'xuli') {
        ?>
        <div class="p-6">
          <h2 class="text-2xl font-semibold mb-4">Xử lý / Bảo trì</h2>
          <p class="text-gray-600 mb-4">Các tác vụ thao tác nhanh: đánh dấu bảo trì, chuyển phòng, ...</p>
          <div class="bg-white p-4 rounded-lg shadow">
            
        </div>
        <?php
        exit;
    }

    // fallback
    echo "Không có nội dung.";
    exit;
}

// Nếu không phải fragment -> render layout chính
$stats_main = get_stats($conn); // dùng cho hiển thị tóm tắt bên sidebar
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin - Dashboard</title>

  <!-- Tailwind CDN -->
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    body { background-color: #f9f8f6; font-family: 'Poppins', sans-serif; }
    .sidebar { background-color: #fffdf9; box-shadow: 2px 0 8px rgba(0,0,0,0.05); transition: all 0.2s ease; }
    .sidebar-item { display:block; padding:12px 16px; border-radius:10px; font-weight:500; color:#444; transition:0.15s; text-align:left; width:100%; }
    .sidebar-item:hover { background-color:#f0ebe4; transform: translateX(4px); color:#000; }
    .sidebar-item.active { background-color:#e6eefc; color:#1e3a8a; font-weight:700; transform:none; }
    .content-area { height: 100vh; overflow-y: auto; -webkit-overflow-scrolling: touch; }

    /* Bảng đẹp hơn – màu nền xen kẽ, border rõ */
    .table-beauty table {
        width: 100%;
        border-collapse: collapse;
        font-size: 15px;
    }

    .table-beauty thead tr {
        background: #f1f5f9;
    }

    .table-beauty th {
        padding: 12px 16px;
        font-weight: 600;
        color: #475569;
        border-bottom: 2px solid #e2e8f0;
        text-transform: uppercase;
        font-size: 13px;
    }

    /* dòng xen kẽ */
    .table-beauty tbody tr:nth-child(odd) {
        background: #ffffff;
    }
    .table-beauty tbody tr:nth-child(even) {
        background: #f8fafc;
    }

    /* hover */
    .table-beauty tbody tr:hover {
        background: #e0f2fe !important;
    }

    /* ô */
    .table-beauty td {
        padding: 12px 16px;
        border-bottom: 1px solid #e2e8f0;
        color: #334155;
        font-size: 15px;
    }
  </style>
</head>
<body>
  <div class="flex h-screen">

    <!-- Sidebar -->
    <aside class="sidebar w-64 p-5">
      <h2 class="text-2xl font-bold text-gray-700 mb-6 text-center">Admin Panel</h2>

      <nav id="nav" class="space-y-3">
        <button data-page="home" class="sidebar-item active">🏠 Trang chủ</button>
        <button data-page="customers" class="sidebar-item">👤 Khách hàng</button>
        <button data-page="nhanvien" class="sidebar-item">💼 Nhân viên</button>
        <button data-page="phong" class="sidebar-item">🛏️ Phòng</button>
        <button data-page="datphong" class="sidebar-item">📅 Đặt phòng</button>
        <button data-page="dichvu" class="sidebar-item">🧴 Dịch vụ</button>
        <button data-page="hoadon" class="sidebar-item">🧾 Hoá đơn</button>
        <button data-page="xuli" class="sidebar-item">🛠️ Xử lý</button>
      </nav>

      <div class="mt-6 text-sm text-gray-600">
        <p>Quick:</p>
        <p class="mt-2">Phòng trống: <strong><?= htmlspecialchars($stats_main['phong_trong']) ?></strong></p>
        <p>Phòng đang đặt: <strong><?= htmlspecialchars($stats_main['phong_dang_dat']) ?></strong></p>
      </div>
    </aside>

    <!-- Main content: vùng load AJAX -->
    <main class="flex-1 content-area" id="mainContent">
      <!-- content sẽ load ở đây -->
      <div class="p-6">
        <div class="text-center text-gray-500 py-20">Đang tải nội dung...</div>
      </div>
    </main>
  </div>

<script>
// Fetch fragment và chèn vào mainContent
async function loadFragment(page, pushHistory = true) {
  const main = document.getElementById('mainContent');
  main.innerHTML = '<div class="p-6"><div class="text-center text-gray-500 py-20">Đang tải nội dung...</div></div>';
  try {
    const res = await fetch('?fragment=1&page=' + encodeURIComponent(page));
    if (!res.ok) {
      main.innerHTML = '<div class="p-6 text-red-600">Lỗi khi tải nội dung: ' + res.status + '</div>';
      return;
    }
    const html = await res.text();
    main.innerHTML = html;
    setActiveMenu(page);
    if (pushHistory) {
      const url = new URL(window.location);
      url.searchParams.set('page', page);
      history.pushState({page: page}, '', url);
    }
  } catch (err) {
    console.error(err);
    main.innerHTML = '<div class="p-6 text-red-600">Lỗi kết nối.</div>';
  }
}

function setActiveMenu(page) {
  document.querySelectorAll('#nav .sidebar-item').forEach(btn => {
    btn.classList.toggle('active', btn.dataset.page === page);
  });
}

document.querySelectorAll('#nav .sidebar-item').forEach(btn => {
  btn.addEventListener('click', () => {
    loadFragment(btn.dataset.page);
  });
});

window.addEventListener('popstate', (ev) => {
  const params = new URLSearchParams(window.location.search);
  const page = params.get('page') || (ev.state && ev.state.page) || 'home';
  loadFragment(page, false);
});

(function initialLoad(){
  const params = new URLSearchParams(window.location.search);
  const start = params.get('page') || 'home';
  loadFragment(start, false);
})();
</script>

</body>
</html>
