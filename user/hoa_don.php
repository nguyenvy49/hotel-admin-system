<?php
session_start();
include 'config.php';

if (!isset($_GET['ma'])) {
    die("Không tìm thấy mã hóa đơn!");
}

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
";

$bill = $conn->query($sql_bill)->fetch_assoc();
if (!$bill) die("Hóa đơn không tồn tại!");

$ma_dat_phong = $bill['ma_dat_phong'];

$sql_rooms = "
SELECT 
    ct.*, 
    p.so_phong, 
    lp.ten_loai_phong
FROM chi_tiet_dat_phong ct
JOIN phong p ON ct.ma_phong = p.ma_phong
JOIN loai_phong lp ON p.ma_loai_phong = lp.ma_loai_phong
WHERE ct.ma_dat_phong = $ma_dat_phong
";
$rs_rooms = $conn->query($sql_rooms);

$sql_dv = "
SELECT 
    sddv.*, 
    dv.ten_dich_vu
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

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Prestige Manor | Invoice</title>

<link rel="stylesheet" href="../assets/css/trangchu.css">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<style>
body {
    background: #f7f4ef;
    margin: 0;
    font-family: 'Roboto', sans-serif;
}

.invoice-container {
    max-width: 900px;
    margin: 40px auto;
    background: white;
    padding: 45px;
    border-radius: 14px;
    box-shadow: 0 0 20px rgba(0,0,0,0.12);
}

.title-box h1 {
    font-family: 'Playfair Display', serif;
    color: #8b6e4b;
    font-size: 42px;
    text-align: center;
    margin-bottom: 0;
}

.title-box h3 {
    text-align: center;
    margin-top: 5px;
    font-weight: 300;
}

.section-title {
    margin-top: 35px;
    font-size: 22px;
    font-weight: bold;
    border-left: 5px solid #8b6e4b;
    padding-left: 12px;
}

.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 18px;
}

.table th {
    background: #eee2d0;
    padding: 12px;
    font-size: 16px;
}

.table td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
}

.total-box {
    margin-top: 25px;
    text-align: right;
}

.total-box p {
    font-size: 18px;
}

.total-final {
    font-size: 26px;
    font-weight: bold;
    color: #8b6e4b;
}

.print-btn {
    text-align: center;
    margin-top: 40px;
}

.print-btn button {
    background: #8b6e4b;
    padding: 14px 26px;
    border: none;
    color: white;
    border-radius: 10px;
    cursor: pointer;
    font-size: 18px;
}
.invoice-page {
    max-width: 900px;
    margin: 40px auto;
}

.invoice-pdf-wrapper {
    background: white;
    padding: 35px 40px;
    border-radius: 12px;
}

.inv-title {
    font-family: 'Playfair Display', serif;
    color: #8b6e4b;
    font-size: 40px;
    text-align: center;
}

.inv-subtitle {
    text-align: center;
    font-weight: 300;
    margin-top: 4px;
}

.inv-no {
    text-align: center;
    margin-bottom: 25px;
}

.section-title {
    margin-top: 25px;
    font-size: 20px;
    font-weight: bold;
    border-left: 5px solid #8b6e4b;
    padding-left: 10px;
}

.info-box p {
    margin: 3px 0;
}

.summary {
    text-align: right;
    margin-top: 25px;
}

.grand-total {
    font-size: 26px;
    margin-top: 10px;
    font-weight: bold;
    color: #8b6e4b;
}

.action-btns {
    text-align: center;
    margin-top: 25px;
}

.action-btns button {
    padding: 12px 26px;
    background: #8b6e4b;
    border: none;
    color: white;
    font-size: 18px;
    margin: 0 5px;
    border-radius: 8px;
    cursor: pointer;
}

.action-btns .pdf-btn {
    background: #4b6e8b;
}

@media print {
    .print-btn,
    .main-header,
    .sidebar,
    .footer {
        display: none !important;
    }

    body {
        background: white !important;
    }

    .invoice-container {
        box-shadow: none !important;
        margin: 0 !important;
        padding: 20px !important;
    }
}

</style>

</head>

<body>

<!-- HEADER -->
<header class="main-header">
    <div class="header-left">
        <button class="menu-toggle" onclick="toggleMenu()">☰</button>
    </div>
    <div class="logo-container">
        <h1 class="logo-text">Prestige Manor</h1>
    </div>
</header>

<!-- SIDEBAR -->
<div id="mySidebar" class="sidebar">
    <a href="index.php">HOME</a>
    <a href="gioithieuphong.php">ACCOMMODATION</a>
    <a href="dat_phong.php">BOOKING</a>
    <a href="gioithieudichvu.php">SERVICES</a>
    <a href="lienhe.php">CONTACT US</a>
</div>

<!-- ========================== INVOICE WRAPPER ========================== -->
<div class="invoice-page">

    <!-- KHUNG XUẤT PDF (SẠCH, KHÔNG MARGIN, KHÔNG SHADOW) -->
    <div id="invoicePDF" class="invoice-pdf-wrapper">

        <h1 class="inv-title">Hotel Invoice</h1>
        <h3 class="inv-subtitle">Prestige Manor Luxury Receipt</h3>
        <p class="inv-no">Invoice No: <b><?= $bill["ma_hoa_don"] ?></b></p>

        <!-- CUSTOMER INFO -->
        <h2 class="section-title">Customer Information</h2>
        <div class="info-box">
            <p><b>Name:</b> <?= $bill["ho"] . " " . $bill["ten"] ?></p>
            <p><b>Email:</b> <?= $bill["email"] ?></p>
            <p><b>Phone:</b> <?= $bill["sdt"] ?></p>
            <p><b>Check-in:</b> <?= date("d/m/Y", strtotime($bill["ngay_nhan"])) ?></p>
            <p><b>Check-out:</b> <?= date("d/m/Y", strtotime($bill["ngay_tra"])) ?></p>
        </div>

        <!-- ROOM DETAILS -->
        <h2 class="section-title">Room Details</h2>
        <table class="table">
            <tr>
                <th>Room Type</th>
                <th>Room No.</th>
                <th>Nights</th>
                <th>Price/night</th>
                <th>Total</th>
            </tr>
            <?php foreach ($rooms as $r): ?>
            <tr>
                <td><?= $r["ten_loai_phong"] ?></td>
                <td><?= $r["so_phong"] ?></td>
                <td><?= $r["so_dem"] ?></td>
                <td><?= number_format($r["gia_phong"]) ?>đ</td>
                <td><?= number_format($r["total"]) ?>đ</td>
            </tr>
            <?php endforeach; ?>
        </table>

        <!-- SERVICES -->
        <h2 class="section-title">Services Used</h2>
        <table class="table">
            <tr>
                <th>Service</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
            </tr>

            <?php if (empty($services)): ?>
            <tr><td colspan="4" style="text-align:center;">No services used.</td></tr>

            <?php else: foreach ($services as $s): ?>
            <tr>
                <td><?= $s["ten_dich_vu"] ?></td>
                <td><?= $s["so_luong"] ?></td>
                <td><?= number_format($s["don_gia"]) ?>đ</td>
                <td><?= number_format($s["total"]) ?>đ</td>
            </tr>
            <?php endforeach; endif; ?>
        </table>

        <!-- SUMMARY -->
        <div class="summary">
            <p><b>Room Total:</b> <?= number_format($roomTotal) ?>đ</p>
            <p><b>Service Total:</b> <?= number_format($serviceTotal) ?>đ</p>

            <p class="grand-total">
                Grand Total: <?= number_format($grand) ?>đ
            </p>
        </div>

    </div>

    <!-- BUTTONS (KHÔNG XUẤT PDF/PRINT) -->
    <div class="action-btns">
        <button onclick="window.print()">🖨 Print Invoice</button>
        <button onclick="downloadPDF()" class="pdf-btn">⬇ Download PDF</button>
    </div>

</div>

<!-- FOOTER -->
<footer class="footer">
    <div class="footer-left"><p class="brand">Prestige Manor</p></div>
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
        <p>Hotline: +84 94271</p>
        <p>Quy Nhơn</p>
    </div>
</footer>

<script>

function downloadPDF() {
    const element = document.getElementById("invoicePDF");

    let opt = {
        margin: 0,
        filename: "invoice_<?= $bill['ma_hoa_don'] ?>.pdf",
        image: { type: "jpeg", quality: 1 },
        html2canvas: { scale: 3 },
        jsPDF: { unit: "pt", format: "a4", orientation: "portrait" }
    };

    html2pdf().from(element).set(opt).save();
}
</script>

<script>
function toggleMenu() {
    document.getElementById("mySidebar").classList.toggle("active");
}
</script>

</body>
</html>

