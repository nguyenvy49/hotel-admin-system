<?php
include "../config.php";
header("Content-Type: application/json");

$ma_dp = intval($_GET['id'] ?? 0);
if (!$ma_dp) {
    echo json_encode(["status" => false, "msg" => "Thiếu mã đặt phòng"]);
    exit;
}

/* LẤY THÔNG TIN BOOKING */
$info = $conn->query("
    SELECT dp.*, kh.ho, kh.ten, kh.sdt
    FROM dat_phong dp
    JOIN khach_hang kh ON dp.ma_khach_hang = kh.ma_khach_hang
    WHERE dp.ma_dat_phong = $ma_dp
");

if ($info->num_rows == 0) {
    echo json_encode(["status" => false, "msg" => "Không tìm thấy đặt phòng"]);
    exit;
}

$r = $info->fetch_assoc();

/* LẤY DANH SÁCH PHÒNG */
$phongs = $conn->query("
    SELECT p.so_phong, lp.ten_loai_phong, lp.gia_phong
    FROM chi_tiet_dat_phong ct
    JOIN phong p ON ct.ma_phong = p.ma_phong
    JOIN loai_phong lp ON p.ma_loai_phong = lp.ma_loai_phong
    WHERE ct.ma_dat_phong = $ma_dp
");

$ph_html = "";
$tien_phong = 0;

$sn = ceil((strtotime($r['ngay_tra']) - strtotime($r['ngay_nhan'])) / 86400);

while ($p = $phongs->fetch_assoc()) {
    $tien = $sn * $p['gia_phong'];
    $tien_phong += $tien;

    $ph_html .= "
        <tr>
            <td>Phòng {$p['so_phong']}</td>
            <td>{$p['ten_loai_phong']}</td>
            <td>{$sn}</td>
            <td>".number_format($p['gia_phong'])."</td>
            <td>".number_format($tien)."</td>
        </tr>
    ";
}

/* DỊCH VỤ */
$dv = $conn->query("
    SELECT dv.ten_dich_vu, dv.don_gia, sd.so_luong
    FROM phieu_su_dung_dich_vu sd
    JOIN dich_vu dv ON sd.ma_dich_vu = dv.ma_dich_vu
    WHERE sd.ma_dat_phong = $ma_dp
");

$dv_html = "";
$tong_dv = 0;

while ($d = $dv->fetch_assoc()) {
    $t = $d['so_luong'] * $d['don_gia'];
    $tong_dv += $t;

    $dv_html .= "
        <tr>
            <td>{$d['ten_dich_vu']}</td>
            <td>".number_format($d['don_gia'])."</td>
            <td>{$d['so_luong']}</td>
            <td>".number_format($t)."</td>
        </tr>
    ";
}

$html = "
<h2 class='text-2xl font-bold mb-4'>Chi tiết đặt phòng #$ma_dp</h2>

<p><b>Khách:</b> {$r['ho']} {$r['ten']} — {$r['sdt']}</p>
<p><b>Ngày nhận:</b> {$r['ngay_nhan']}</p>
<p><b>Ngày trả:</b> {$r['ngay_tra']}</p>

<h3 class='text-xl font-bold mt-6 mb-2'>Danh sách phòng</h3>
<table class='w-full border'>
<tr class='bg-gray-100 font-semibold'>
<td>Phòng</td><td>Loại</td><td>Số đêm</td><td>Giá</td><td>Tổng</td>
</tr>
$ph_html
</table>

<h3 class='text-xl font-bold mt-6 mb-2'>Dịch vụ sử dụng</h3>
<table class='w-full border'>
<tr class='bg-gray-100 font-semibold'><td>Tên DV</td><td>Giá</td><td>SL</td><td>Tổng</td></tr>
$dv_html
</table>

<h3 class='mt-6 text-xl font-bold text-indigo-700'>
Tổng tiền: ".number_format($tien_phong + $tong_dv)." đ
</h3>
";

echo json_encode(["status" => true, "html" => $html]);
?>
