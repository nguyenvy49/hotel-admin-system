<?php
session_start();
include 'config.php';

if (!isset($_SESSION["khach_hang_id"])) {
    header("Location: login.php");
    exit;
}

$kh_id = $_SESSION["khach_hang_id"];

/* ============================================================
   NẾU KHÔNG TRUYỀN MÃ → HIỂN THỊ DANH SÁCH TẤT CẢ HÓA ĐƠN
============================================================ */
if (!isset($_GET['ma'])) {

    $sql_all = "
        SELECT hd.*, dp.ngay_nhan, dp.ngay_tra
        FROM hoa_don hd
        JOIN dat_phong dp ON dp.ma_dat_phong = hd.ma_dat_phong
        WHERE dp.ma_khach_hang = $kh_id
          AND hd.trang_thai = 'Đã thanh toán'
        ORDER BY hd.ma_hoa_don DESC
    ";

    $rs = $conn->query($sql_all);
    ?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>My Bills | Prestige Manor</title>

<link rel="stylesheet" href="../assets/css/trangchu.css">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
<style>
body { background:#f6f2eb; font-family:Roboto; }
.container {
    max-width: 900px; margin:140px auto; background:white;
    padding:35px; border-radius:14px; box-shadow:0 4px 20px rgba(0,0,0,0.12);
}
.title {
    text-align:center; font-family:'Playfair Display'; font-size:36px; color:#8b6e4b;
}
.table {
    width:100%; border-collapse:collapse; margin-top:20px;
}
.table th {
    background:#eee2d0; padding:12px; text-align:left;
}
.table td {
    padding:12px; border-bottom:1px solid #ddd;
}
.btn-view {
    padding:8px 14px; background:#8b6e4b; color:white;
    border-radius:6px; font-size:14px; text-decoration:none;
}
.no-data { text-align:center; font-size:18px; padding:30px; color:#666; }
</style>
</head>

<body>

<header class="main-header">
    <div class="header-left">
      <button class="menu-toggle" onclick="toggleMenu()">☰</button>
    </div>

    <div class="logo-container">
      <h1 class="logo-text">Prestige Manor</h1>
    </div>

    <div class="header-right">
      <a href="<?= $isLogged ? 'dat_phong.php' : 'login.php' ?>" class="book-direct">BOOK DIRECT</a>
    </div>
</header>

<!-- =============== SIDEBAR =============== -->
<div id="mySidebar" class="sidebar">
    <a href="index.php">HOME</a>
    <a href="gioithieuphong.php">ACCOMMODATION</a>
    <a href="dat_phong.php">BOOKING</a>
    <a href="gioithieudichvu.php">SERVICES</a>
    <a href="hoa_don_cua_toi.php">MY BILL</a>
    <a href="lienhe.php">CONTACT US</a>

    <?php if(!$isLogged) : ?>
        <a href="login.php">LOGIN</a>
        <a href="dangki.php">SIGN UP</a>
    <?php else: ?>
        <a href="logout.php">LOG OUT</a>
    <?php endif; ?>
</div>

<!-- =============== BOOKING BAR =============== -->
<div class="booking-bar-wrapper">
    <div class="booking-bar">

        <div class="booking-item online-info">
            <p class="label">BOOK ONLINE</p>
            <p class="guarantee">Guaranteed reservation</p>
        </div>

        <div class="booking-item date-input">
            <p class="label">CHECK-IN</p>
            <div class="input-field">
                <input type="text" id="checkin" placeholder="Select date">
                <i class="fas fa-calendar-alt"></i>
            </div>
        </div>

        <div class="booking-item date-input">
            <p class="label">CHECK-OUT</p>
            <div class="input-field">
                <input type="text" id="checkout" placeholder="Select date">
                <i class="fas fa-calendar-alt"></i>
            </div>
        </div>

        

        <!-- Nút tìm phòng theo logic hệ thống -->
        <button class="find-room-btn" onclick="handleFindRoom()">FIND ROOM</button>
    </div>
</div>

<div class="container">

    <h2 class="title">Your Paid Invoices</h2>

    <table class="table">
        <tr>
            <th>Mã HĐ</th>
            <th>Ngày ở</th>
            <th>Tổng tiền</th>
            <th>Hành động</th>
        </tr>

        <?php if ($rs->num_rows == 0): ?>
            <tr><td colspan="4" class="no-data">Không có hóa đơn đã thanh toán.</td></tr>
        <?php else: 
            while ($row = $rs->fetch_assoc()): ?>
            <tr>
                <td>#<?= $row["ma_hoa_don"] ?></td>
                <td><?= $row["ngay_nhan"] ?> → <?= $row["ngay_tra"] ?></td>
                <td style="color:#8b6e4b; font-weight:bold;">
                    <?= number_format($row["tong_tien"]) ?>đ
                </td>
                <td>
                    <a class="btn-view" href="hoa_don.php?ma=<?= $row["ma_hoa_don"] ?>">
                        Xem chi tiết
                    </a>
                </td>
            </tr>
        <?php endwhile; endif; ?>

    </table>

</div>

<footer class="footer">
    <div class="footer-left">
      <p class="brand">Prestige Manor</p>
    </div>

    <div class="footer-middle">
        <ul>
          <li><a href="index.php">▶ Home</a></li>
          <li><a href="gioithieuphong.php">▶ Accommodation</a></li>
          <li><a href="dat_phong.php">▶ Book Direct</a></li>
          <li><a href="nhahang.php">▶ PM Restaurant</a></li>
          <li><a href="spa.php">▶ PM Spa</a></li>
        </ul>
    </div>

    <div class="footer-right">
        <h3>CONTACT US</h3>
        <p>▶ Hotline/Zalo: +84 94271</p>
        <p>▶ Location: Quy Nhơn</p>
    </div>
</footer>
</body>
</html>

<?php
exit;
}

/* ============================================================
   NGƯỢC LẠI: CÓ MÃ HÓA ĐƠN → HIỂN THỊ CHI TIẾT HÓA ĐƠN
============================================================ */
$ma_hoa_don = intval($_GET['ma']);

$sql_bill = "
SELECT 
    hd.ma_hoa_don,
    hd.ma_dat_phong,
    hd.ngay_lap,
    hd.tong_tien,
    hd.trang_thai,
    hd.phuong_thuc,
    kh.ho, kh.ten, kh.email, kh.sdt,
    dp.ngay_nhan, dp.ngay_tra, dp.so_nguoi
FROM hoa_don hd
JOIN dat_phong dp ON hd.ma_dat_phong = dp.ma_dat_phong
LEFT JOIN khach_hang kh ON dp.ma_khach_hang = kh.ma_khach_hang
WHERE hd.ma_hoa_don = $ma_hoa_don
  AND dp.ma_khach_hang = $kh_id
";

$bill = $conn->query($sql_bill)->fetch_assoc();
if (!$bill) die("Hóa đơn không tồn tại hoặc không thuộc về bạn!");

$ma_dat_phong = $bill['ma_dat_phong'];

/* Lấy chi tiết phòng */
$sql_rooms = "
SELECT ct.*, p.so_phong, lp.ten_loai_phong
FROM chi_tiet_dat_phong ct
JOIN phong p ON ct.ma_phong = p.ma_phong
JOIN loai_phong lp ON p.ma_loai_phong = lp.ma_loai_phong
WHERE ct.ma_dat_phong = $ma_dat_phong
";

$rs_rooms = $conn->query($sql_rooms);

/* Lấy dịch vụ */
$sql_dv = "
SELECT sddv.*, dv.ten_dich_vu
FROM phieu_su_dung_dich_vu sddv
JOIN dich_vu dv ON sddv.ma_dich_vu = dv.ma_dich_vu
WHERE sddv.ma_dat_phong = $ma_dat_phong
";

$rs_dv = $conn->query($sql_dv);

$rooms = [];
$services = [];
$roomTotal = 0;
$serviceTotal = 0;

while ($r = $rs_rooms->fetch_assoc()) {
    $r["total"] = $r["gia_phong"] * $r["so_dem"];
    $roomTotal += $r["total"];
    $rooms[] = $r;
}

while ($dv = $rs_dv->fetch_assoc()) {
    $dv["total"] = $dv["so_luong"] * $dv["don_gia"];
    $serviceTotal += $dv["total"];
    $services[] = $dv;
}

$grand = $roomTotal + $serviceTotal;
?>

