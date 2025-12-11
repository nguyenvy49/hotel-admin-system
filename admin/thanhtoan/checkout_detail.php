<?php
include "../config.php";
header("Content-Type: application/json");

$ma_phong = $_GET['id'] ?? '0';

$q = $conn->query("
    SELECT dp.*, lp.gia_phong, p.so_phong
    FROM dat_phong dp 
    JOIN phong p ON dp.ma_phong = p.ma_phong
    JOIN loai_phong lp ON p.ma_loai_phong = lp.ma_loai_phong
    WHERE dp.ma_phong=$ma_phong AND dp.trang_thai='Đang ở'
");

if ($q->num_rows == 0) {
    echo json_encode(["status" => false, "msg" => "Phòng không có khách!"]);
    exit;
}

$r = $q->fetch_assoc();

/* TÍNH SỐ ĐÊM */
$sn = (strtotime(date("Y-m-d")) - strtotime($r['ngay_nhan'])) / 86400;
$sn = max(1, ceil($sn));

/* LOAD DỊCH VỤ */
$dv_html = "";
$tong_dv = 0;

$dv = $conn->query("
    SELECT ps.*, dv.ten_dich_vu
    FROM phieu_su_dung_dich_vu ps
    JOIN dich_vu dv ON ps.ma_dich_vu = dv.ma_dich_vu
    WHERE ps.ma_dat_phong = ".$r['ma_dat_phong']."
");

while ($d = $dv->fetch_assoc()) {

    $tt = $d['don_gia'] * $d['so_luong'];
    $tong_dv += $tt;

    $dv_html .= "
        <div class='flex justify-between py-1'>
            <span>{$d['ten_dich_vu']} x {$d['so_luong']}</span>
            <span>".number_format($tt)." đ</span>
        </div>
    ";
}

$tien_phong = $sn * $r['gia_phong'];
$tong = $tong_dv + $tien_phong;

/* HTML TRẢ VỀ */
$html = "
    <div class='space-y-3 text-lg'>
        <p><b>Phòng:</b> {$r['so_phong']}</p>
        <p><b>Số đêm:</b> {$sn} đêm</p>
        <p><b>Tiền phòng:</b> ".number_format($tien_phong)." đ</p>

        <h3 class='font-bold mt-4 mb-2'>Dịch vụ:</h3>
        $dv_html

        <hr class='my-3'>
        <p class='text-xl font-bold'>Tổng: ".number_format($tong)." đ</p>
    </div>
";

echo json_encode(["status" => true, "html" => $html]);
