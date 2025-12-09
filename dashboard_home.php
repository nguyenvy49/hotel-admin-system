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
if($page=='customers'){
  //lay danh sach khach hang
  $q = $conn ->query("
  SELECT nv.ma_khach_hang,nv.ho,nv.ten,nv.ngay_sinh,nv.sdt,nv.ngay_dang_ky,nv.email
  FROM khach_hang nv
  ");
  ?>
  <div class="p-6">
    <div class ="flex justify-between items-center mb-4">
      <a href="quanlikhachhang/add_khachhang.php"
     class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
     +Thêm khách hàng
</a>
  </div>
  <div class="bg-white rounded-lg shadow overflow-auto table-beauty">
        <table class="min-w-full">
          <thead>
<tr>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mã</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Họ</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tên</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày sinh</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Số điện thoại</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email </th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày đăng kí </th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hành động </th>

</tr>
</thead>
<tbody>
  <?php while($r=$q->fetch_assoc()):?>
    <tr>
                      <td class="px-6 py-4"><?= htmlspecialchars($r['ma_khach_hang']) ?></td>
                <td class="px-6 py-4"><?= htmlspecialchars($r['ho']) ?></td>
                 <td class="px-6 py-4"><?= htmlspecialchars($r['ten']) ?></td>
                 <td class="px-6 py-4"><?= htmlspecialchars($r['ngay_sinh']) ?></td>
               <td class="px-6 py-4"><?= htmlspecialchars($r['sdt']) ?></td>
               <td class="px-6 py-4"><?= htmlspecialchars($r['email']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($r['ngay_dang_ky']) ?></td>

                <td class="px-6 py-4 flex gap-3">
                  <a href="quanlikhachhang/delete_khachhang.php?ma_khach_hang=<?= $r['ma_khach_hang'] ?>"
                     onclick="return confirm('Xóa khách hàng này?');"
                     class="text-red-600 hover:text-red-800 font-semibold">
                    Xóa
                  </a>
                   <a href="quanlikhachhang/edit_khachhang.php?ma_khach_hang=<?= $r['ma_khach_hang'] ?>"
                     onclick="return confirm('Sửa khách hàng này?');"
                     class="text-red-600 hover:text-red-800 font-semibold">
                    Sửa
                  </a>
                </td>
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

    // Lấy danh sách nhân viên + chức vụ
    $q = $conn->query("
        SELECT nv.ma_nhan_vien, nv.ho_ten, cv.ten_chuc_vu
        FROM nhan_vien nv
        LEFT JOIN chuc_vu cv ON nv.ma_chuc_vu = cv.ma_chuc_vu
        ORDER BY nv.ma_nhan_vien DESC
        LIMIT 200
    ");
    ?>

    <div class="p-6">

      <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-semibold">Nhân viên</h2>

        <a href="quanlinhanvien/add_nhanvien.php" 
           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
          + Thêm nhân viên
        </a>
      </div>

      <div class="bg-white rounded-lg shadow overflow-auto table-beauty">
        <table class="min-w-full">
          <thead>
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mã</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Họ & Tên</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Chức vụ</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hành động</th>
            </tr>
          </thead>

          <tbody>
            <?php while ($r = $q->fetch_assoc()) : ?>
              <tr>
                <td class="px-6 py-4"><?= htmlspecialchars($r['ma_nhan_vien']) ?></td>
                <td class="px-6 py-4"><?= htmlspecialchars($r['ho_ten']) ?></td>
                <td class="px-6 py-4"><?= htmlspecialchars($r['ten_chuc_vu'] ?? '—') ?></td>

                <td class="px-6 py-4 flex gap-3">
                  <a href="quanlinhanvien/delete_nhanvien.php?ma_nhan_vien=<?= $r['ma_nhan_vien'] ?>"
                     onclick="return confirm('Xóa nhân viên này?');"
                     class="text-red-600 hover:text-red-800 font-semibold">
                    Xóa
                  </a>
                   <a href="quanlinhanvien/edit_nhanvien.php?ma_nhan_vien=<?= $r['ma_nhan_vien'] ?>"
                     onclick="return confirm('Sửa nhân viên này?');"
                     class="text-red-600 hover:text-red-800 font-semibold">
                    Sửa
                  </a>
                </td>
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

    $sql = "
        SELECT 
            p.ma_phong,
            p.so_phong,
            p.trang_thai,
            lp.ten_loai_phong,
            lp.so_nguoi_toi_da,
            lp.gia_phong
        FROM phong p
        JOIN loai_phong lp ON p.ma_loai_phong = lp.ma_loai_phong
        ORDER BY p.so_phong ASC
    ";

    $q = $conn->query($sql);
?>

<div class="p-6">

    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-semibold text-gray-800">Danh sách phòng</h2>
        <a href="quanliphong/add_phong.php"
            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-xl shadow font-medium transition">
            + Thêm phòng
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-auto table-beauty">
        <table class="min-w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Số phòng</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Loại phòng</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Số người tối đa</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Giá</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Trạng thái</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Hành động</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($r = $q->fetch_assoc()) : ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-semibold">Phòng <?= htmlspecialchars($r['so_phong']) ?></td>

                        <td class="px-6 py-4"><?= htmlspecialchars($r['ten_loai_phong']) ?></td>

                        <td class="px-6 py-4"><?= htmlspecialchars($r['so_nguoi_toi_da']) ?> người</td>

                        <td class="px-6 py-4">
                            <?= number_format($r['gia_phong'], 0, ',', '.') ?> đ
                        </td>

                        <td class="px-6 py-4">
                            <?php
                                $colors = [
                                    "Trống" => "bg-green-100 text-green-700",
                                    "Đã đặt" => "bg-yellow-100 text-yellow-700",
                                    "Đang dọn dẹp" => "bg-blue-100 text-blue-700",
                                    "Bảo trì" => "bg-red-100 text-red-700",
                                ];
                            ?>
                            <span class="px-3 py-1 text-sm rounded-xl <?= $colors[$r['trang_thai']] ?>">
                                <?= $r['trang_thai'] ?>
                            </span>
                        </td>

                        <td class="px-6 py-4 flex gap-3">
                            <a href="quanliphong/edit_phong.php?id=<?= $r['ma_phong'] ?>"
                                class="text-blue-600 hover:text-blue-800 font-medium">Sửa</a>

                            <a href="quanliphong/delete_phong.php?id=<?= $r['ma_phong'] ?>"
                                onclick="return confirm('Xóa phòng này?');"
                                class="text-red-600 hover:text-red-800 font-medium">Xóa</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>

        </table>
    </div>

</div>
<?php
    exit;
}

 // ---------- FRAGMENT: DATPHONG ----------
if ($page === 'datphong') {

    $sql = "
    SELECT 
        dp.ma_dat_phong,
        kh.ho,
        kh.ten,
        kh.sdt,
        p.so_phong,
        lp.ten_loai_phong,
        dp.ngay_dat,
        dp.ngay_nhan,
        dp.ngay_tra,
        dp.trang_thai
    FROM dat_phong dp
    JOIN khach_hang kh ON dp.ma_khach_hang = kh.ma_khach_hang
    JOIN phong p ON dp.ma_phong = p.ma_phong
    JOIN loai_phong lp ON p.ma_loai_phong = lp.ma_loai_phong
    ORDER BY dp.ma_dat_phong DESC
    ";

    $q = mysqli_query($conn, $sql);
?>
    <div class="p-6">
        
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold">Danh sách đặt phòng</h2>
            
            <a href="quanlidatphong/add_datphong.php"
               class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
               + Thêm đặt phòng
            </a>
        </div>

        <div class="bg-white rounded-lg shadow overflow-auto table-beauty">
            <table class="min-w-full">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mã đặt</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Khách hàng</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phòng</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loại phòng</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày đặt</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nhận phòng</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trả phòng</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hành động</th>
                    </tr>
                </thead>

                <tbody>
                    <?php 
                    if ($q && mysqli_num_rows($q) > 0):
                        while ($r = $q->fetch_assoc()):
                        $ten_kh = trim($r['ho'] . ' ' . $r['ten']);
                    ?>
                        <tr>
                            <td class="px-6 py-4"><?= $r['ma_dat_phong'] ?></td>

                            <td class="px-6 py-4">
                                <?= htmlspecialchars($ten_kh) ?><br>
                                <span class="text-gray-500 text-sm"><?= htmlspecialchars($r['sdt']) ?></span>
                            </td>

                            <td class="px-6 py-4">Phòng <?= htmlspecialchars($r['so_phong']) ?></td>

                            <td class="px-6 py-4"><?= htmlspecialchars($r['ten_loai_phong']) ?></td>

                            <td class="px-6 py-4"><?= htmlspecialchars($r['ngay_dat']) ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($r['ngay_nhan']) ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($r['ngay_tra']) ?></td>

                            <td class="px-6 py-4">
                                <?= room_badge($r['trang_thai']) ?>
                            </td>

                            <td class="px-6 py-4 flex gap-4">

                                <a href="quanlidatphong/edit_datphong.php?id=<?= $r['ma_dat_phong'] ?>"
                                   class="text-blue-600 hover:underline font-medium">
                                   Sửa
                                </a>

                                <a href="quanlidatphong/delete_datphong.php?id=<?= $r['ma_dat_phong'] ?>"
                                   class="text-red-600 hover:underline font-medium"
                                   onclick="return confirm('Xóa đặt phòng này?')">
                                   Xóa
                                </a>

                            </td>
                        </tr>
                    <?php 
                        endwhile;
                    else: ?>
                        <tr><td colspan="9" class="px-6 py-4 text-center">Không có dữ liệu đặt phòng.</td></tr>
                    <?php endif; ?>
                </tbody>

            </table>
        </div>
    </div>

<?php
    exit;
}


// ---------- FRAGMENT: DICH VU ----------
if ($page === 'dichvu') {

    // Lấy danh sách dịch vụ
    $q = $conn->query("
        SELECT ma_dich_vu, ten_dich_vu, don_gia
        FROM dich_vu
        ORDER BY ma_dich_vu ASC
    ");
?>

<div class="p-6">

    <!-- HEADER + NÚT THÊM -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Quản lý dịch vụ</h2>

        <a href="quanlidichvu/add_dichvu.php"
           class="px-5 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl shadow">
           +Thêm dịch vụ
        </a>
    </div>

    <!-- DANH SÁCH DỊCH VỤ -->
    <div>
        <h3 class="text-xl font-semibold mb-4">Danh sách dịch vụ</h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">

            <?php while ($r = $q->fetch_assoc()) : ?>
                <div class="bg-white p-5 rounded-xl shadow hover:shadow-lg transition relative">

                    <!-- Tên dịch vụ -->
                    <h3 class="font-bold text-lg text-gray-800">
                        <?= htmlspecialchars($r['ten_dich_vu']) ?>
                    </h3>

                    <!-- Giá -->
                    <p class="mt-2 text-gray-700 font-semibold text-lg">
                        <?= number_format($r['don_gia'], 0, ',', '.') ?> đ
                    </p>

                    <!-- Buttons -->
                    <div class="flex gap-3 mt-4">

                        <!-- Sửa -->
                        <a href="quanlidichvu/edit_dichvu.php?id=<?= $r['ma_dich_vu'] ?>"
                           class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm">
                            Sửa
                        </a>

                        <!-- Xóa -->
                        <a onclick="return confirm('Xóa dịch vụ này?')"
                           href="quanlidichvu/delete_dichvu.php?id=<?= $r['ma_dich_vu'] ?>"
                           class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm">
                            Xóa
                        </a>

                    </div>

                </div>
            <?php endwhile; ?>

        </div>
    </div>


    <!-- PHIẾU SỬ DỤNG DỊCH VỤ -->
    <div>
    <h3 class="text-xl font-semibold mt-10 mb-4">
        Phiếu sử dụng dịch vụ
        <a href="quanliphieudichvu/add_sddv.php"
           class="ml-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
           + Thêm mới
        </a>
    </h3>

    <div class="bg-white rounded-xl shadow overflow-auto table-beauty">
        <table class="min-w-full">
            <thead>
                <tr class="bg-gray-100 text-gray-600 font-semibold text-sm">
                    <th class="px-6 py-3 text-left">Mã SDDV</th>
                    <th class="px-6 py-3 text-left">Mã đặt phòng</th>
                    <th class="px-6 py-3 text-left">Mã dịch vụ</th>
                    <th class="px-6 py-3 text-left">Ngày sử dụng</th>
                    <th class="px-6 py-3 text-left">Số lượng</th>
                    <th class="px-6 py-3 text-left">Đơn giá</th>
                    <th class="px-6 py-3 text-center">Hành động</th>
                </tr>
            </thead>

            <tbody class="text-sm">
                <?php
                $q2 = $conn->query("
                    SELECT *
                    FROM phieu_su_dung_dich_vu
                    ORDER BY ma_sddv DESC
                    LIMIT 200
                ");

                if ($q2 && $q2->num_rows > 0):
                    while ($r2 = $q2->fetch_assoc()):
                ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-3"><?= htmlspecialchars($r2['ma_sddv']) ?></td>
                        <td class="px-6 py-3"><?= htmlspecialchars($r2['ma_dat_phong']) ?></td>
                        <td class="px-6 py-3"><?= htmlspecialchars($r2['ma_dich_vu']) ?></td>
                        <td class="px-6 py-3"><?= htmlspecialchars($r2['ngay_su_dung']) ?></td>
                        <td class="px-6 py-3"><?= htmlspecialchars($r2['so_luong']) ?></td>
                        <td class="px-6 py-3"><?= number_format($r2['don_gia'], 0, ',', '.') ?> đ</td>

                        <td class="px-6 py-3 text-center">
                            <a href="quanliphieudichvu/edit_sddv.php?id=<?= $r2['ma_sddv'] ?>"
                               class="px-3 py-1 bg-yellow-500 text-white rounded-md hover:bg-yellow-600">Sửa</a>

                            <a href="quanliphieudichvu/delete_sddv.php?id=<?= $r2['ma_sddv'] ?>"
                               onclick="return confirm('Xóa phiếu này?')"
                               class="px-3 py-1 bg-red-500 text-white rounded-md hover:bg-red-600">Xóa</a>
                        </td>
                    </tr>

                <?php endwhile; else: ?>
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            Không có dữ liệu phiếu sử dụng dịch vụ.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

            </table>
        </div>
    </div>

</div>

<?php
    exit;
}

   // ---------- FRAGMENT: HOADON ----------
if ($page === 'hoadon') {
    $q = $conn->query("
      SELECT ma_hoa_don, ma_dat_phong, tong_tien, trang_thai, ngay_thanh_toan
      FROM hoa_don
      ORDER BY ma_hoa_don DESC
      LIMIT 100
    ");
    ?>
    <div class="p-6">

      <!-- TITLE + ADD BUTTON -->
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-semibold">Hoá đơn</h2>

        <a href="quanlihoadon/add_hoadon.php"
           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
          + Thêm hóa đơn
        </a>
      </div>

      <div class="bg-white rounded-lg shadow overflow-auto table-beauty">
        <table class="min-w-full">
          <thead>
            <tr>
              <th class="px-6 py-3">Mã</th>
              <th class="px-6 py-3">Mã đặt phòng</th>
              <th class="px-6 py-3">Tổng tiền</th>
              <th class="px-6 py-3">Trạng thái</th>
              <th class="px-6 py-3">Ngày TT</th>
              <th class="px-6 py-3">Hành động</th>
            </tr>
          </thead>

          <tbody>
            <?php while ($r = $q->fetch_assoc()) : ?>
              <tr>
                <td class="px-6 py-4"><?= htmlspecialchars($r['ma_hoa_don']) ?></td>
                <td class="px-6 py-4"><?= htmlspecialchars($r['ma_dat_phong']) ?></td>
                <td class="px-6 py-4">
                    <?= number_format($r['tong_tien'], 0, ',', '.') ?> đ
                </td>
                <td class="px-6 py-4"><?= htmlspecialchars($r['trang_thai']) ?></td>
                <td class="px-6 py-4"><?= htmlspecialchars($r['ngay_thanh_toan']) ?></td>

                <td class="px-6 py-4 flex gap-3">
                  <!-- Sửa -->
                  <a href="quanlihoadon/edit_hoadon.php?id=<?= $r['ma_hoa_don'] ?>"
                     class="text-blue-600 hover:underline">
                    Sửa
                  </a>

                  <!-- Xóa -->
                  <a href="quanlihoadon/delete_hoadon.php?id=<?= $r['ma_hoa_don'] ?>"
                     onclick="return confirm('Xác nhận xóa hóa đơn?')"
                     class="text-red-600 hover:underline">
                    Xóa
                  </a>
                </td>
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
        <button data-page="home" class="sidebar-item active">Trang chủ </button>
        <button data-page="customers" class="sidebar-item"> Khách hàng</button>
        <button data-page="nhanvien" class="sidebar-item">Nhân viên</button>
        <button data-page="phong" class="sidebar-item"> Phòng</button>
        <button data-page="datphong" class="sidebar-item"> Đặt phòng</button>
        <button data-page="dichvu" class="sidebar-item"> Dịch vụ</button>
        <button data-page="hoadon" class="sidebar-item"> Hoá đơn</button>
        <button data-page="xuli" class="sidebar-item"> Xử lý</button>
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
