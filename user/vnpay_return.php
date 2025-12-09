<?php
session_start();
include "config.php";

$invoice = intval($_GET['vnp_TxnRef'] ?? 0);
$respCode = $_GET['vnp_ResponseCode'] ?? "";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Prestige Manor – Thanh toán</title>

<style>
    body {
        margin: 0;
        background: #f5f5f0;
        font-family: "Playfair Display", serif;
    }

    /* ===== HEADER BANNER ===== */
    .banner {
        width: 100%;
        height: 190px;
        background: url('../assets/img/banner.jpg') center/cover no-repeat;
        display: flex;
        justify-content: center;
        align-items: center;
        color: white;
        font-size: 36px;
        font-weight: bold;
        text-shadow: 0 3px 10px rgba(0,0,0,0.4);
        position: relative;
    }

    .banner-overlay {
        position: absolute;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.35);
    }

    .banner h1 {
        position: relative;
        z-index: 10;
    }

    /* ===== MAIN CONTAINER ===== */
    .container {
        max-width: 900px;
        background: white;
        margin: -40px auto 50px;
        padding: 40px;
        border-radius: 18px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
        from {opacity: 0; transform: translateY(20px);}
        to   {opacity: 1; transform: translateY(0);}
    }

    .title-success {
        text-align: center;
        font-size: 28px;
        color: #4CAF50;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .subtitle {
        text-align: center;
        font-size: 18px;
        color: #666;
        margin-bottom: 35px;
    }

    /* ===== SECTIONS ===== */
    .section {
        margin-top: 25px;
        padding: 25px;
        background: #fafafa;
        border-radius: 12px;
        border-left: 5px solid #A9A48F;
    }

    .section h3 {
        margin: 0;
        font-size: 22px;
        margin-bottom: 10px;
        color: #2b2b2b;
    }

    .grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
        margin-top: 15px;
    }

    .item {
        background: white;
        padding: 18px;
        border-radius: 12px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        font-size: 16px;
        line-height: 1.6;
    }

    .item b {
        color: #444;
        font-size: 17px;
    }

    /* BUTTON */
    .btn {
        display: block;
        width: 260px;
        margin: 35px auto 0;
        padding: 14px;
        text-align: center;
        background: #A9A48F;
        color: white;
        font-size: 18px;
        border-radius: 10px;
        text-decoration: none;
        transition: 0.25s;
        font-weight: bold;
    }

    .btn:hover {
        background: #8c8772;
        transform: translateY(-2px);
    }

    /* FAIL MESSAGE */
    .fail-title {
        text-align: center;
        color: #d9534f;
        font-size: 28px;
        margin-bottom: 10px;
    }

    .fail-msg {
        text-align: center;
        color: #8a1f1f;
        font-size: 18px;
    }
</style>

</head>

<body>

<!-- ===== HEADER BANNER ===== -->
<div class="banner">
    <div class="banner-overlay"></div>
    <h1>Prestige Manor</h1>
</div>

<div class="container">

<?php
/* ==========================================================
   THANH TOÁN THÀNH CÔNG
========================================================== */
if ($respCode === "00") {

    $conn->query("
        UPDATE hoa_don
        SET trang_thai='Đã đặt cọc', phuong_thuc='VNPay'
        WHERE ma_hoa_don = $invoice
    ");

    echo "<div class='title-success'>🎉 ĐẶT CỌC THÀNH CÔNG</div>";
    echo "<div class='subtitle'>Cảm ơn bạn! Prestige Manor đã nhận khoản đặt cọc cho đơn đặt phòng.</div>";

    // Lấy booking
    $booking = $conn->query("SELECT ma_dat_phong FROM hoa_don WHERE ma_hoa_don=$invoice")->fetch_assoc();
    $ma_dat_phong = $booking["ma_dat_phong"];

    /* ===================== PHÒNG ====================== */
    echo "<div class='section'><h3>Phòng bạn đã đặt</h3><div class='grid'>";

    $rooms = $conn->query("
        SELECT p.so_phong, lp.ten_loai_phong, ctdp.gia_phong, ctdp.so_dem
        FROM chi_tiet_dat_phong ctdp
        JOIN phong p ON ctdp.ma_phong = p.ma_phong
        JOIN loai_phong lp ON p.ma_loai_phong = lp.ma_loai_phong
        WHERE ctdp.ma_dat_phong = $ma_dat_phong
    ");

    while ($r = $rooms->fetch_assoc()) {
        echo "
            <div class='item'>
                <b>{$r['ten_loai_phong']} – Phòng {$r['so_phong']}</b><br>
                Giá/đêm: " . number_format($r['gia_phong']) . "₫ <br>
                Số đêm: {$r['so_dem']}
            </div>
        ";
    }
    echo "</div></div>";

    /* ===================== DỊCH VỤ ====================== */
    echo "<div class='section'><h3>Dịch vụ đã chọn</h3><div class='grid'>";

    $services = $conn->query("
        SELECT dv.ten_dich_vu, dv.don_gia, sddv.so_luong
        FROM phieu_su_dung_dich_vu sddv
        JOIN dich_vu dv ON sddv.ma_dich_vu = dv.ma_dich_vu
        WHERE sddv.ma_dat_phong = $ma_dat_phong
    ");

    if ($services->num_rows > 0) {
        while ($s = $services->fetch_assoc()) {
            echo "
                <div class='item'>
                    <b>{$s['ten_dich_vu']}</b><br>
                    Số lượng: {$s['so_luong']}<br>
                    Giá: " . number_format($s['don_gia']) . "₫
                </div>
            ";
        }
    } else {
        echo "<div class='item'>Không sử dụng dịch vụ nào.</div>";
    }

    echo "</div></div>";

    echo "<a href='index.php' class='btn'>Quay về trang chủ</a>";

} else {
    /* ==========================================================
       THANH TOÁN THẤT BẠI
    =========================================================== */
    echo "<div class='fail-title'>THANH TOÁN THẤT BẠI</div>";
    echo "<p class='fail-msg'>Mã lỗi: $respCode</p>";
    echo "<a href='dat_phong.php' class='btn'>Thử lại</a>";
}
?>

</div>

</body>
</html>
