<?php
include "../config.php";
header("Content-Type: application/json");

$ma_hd = intval($_GET["id"] ?? 0);
if (!$ma_hd) {
    echo json_encode(["status" => false, "msg" => "Thiếu mã hóa đơn"]); exit;
}

$hd = $conn->query("
    SELECT hd.*, dp.ma_dat_phong, kh.ho, kh.ten, kh.sdt
    FROM hoa_don hd
    JOIN dat_phong dp ON dp.ma_dat_phong = hd.ma_dat_phong
    JOIN khach_hang kh ON kh.ma_khach_hang = dp.ma_khach_hang
    WHERE hd.ma_hoa_don = $ma_hd
")->fetch_assoc();

if (!$hd) {
    echo json_encode(["status"=>false,"msg"=>"Không tìm thấy hóa đơn"]);
    exit;
}

$ct = $conn->query("SELECT * FROM chi_tiet_hoa_don WHERE ma_hoa_don = $ma_hd");

/* ========= HTML HIỂN THỊ TRONG MODAL ========= */
$body = "
<h2 class='text-2xl font-bold mb-3'>Hóa đơn #{$ma_hd}</h2>

<p><b>Khách hàng:</b> {$hd['ho']} {$hd['ten']}</p>
<p><b>SĐT:</b> {$hd['sdt']}</p>
<p><b>Mã đặt phòng:</b> {$hd['ma_dat_phong']}</p>
<p><b>Ngày lập:</b> {$hd['ngay_lap']}</p>
<p><b>Trạng thái:</b> {$hd['trang_thai']}</p>

<table class='w-full mt-4 border-collapse'>
<tr class='bg-gray-200'>
    <th class='p-2 text-left'>Nội dung</th>
    <th class='p-2 text-left'>SL</th>
    <th class='p-2 text-left'>Đơn giá</th>
    <th class='p-2 text-left'>Thành tiền</th>
</tr>";

/* build table rows */
$print_rows = "";
$ct->data_seek(0);

while ($r = $ct->fetch_assoc()) {
    $body .= "
    <tr>
        <td class='p-2'>{$r['noi_dung']}</td>
        <td class='p-2'>{$r['so_luong']}</td>
        <td class='p-2'>".number_format($r['don_gia'])." đ</td>
        <td class='p-2'>".number_format($r['thanh_tien'])." đ</td>
    </tr>";

    // for printing window
    $print_rows .= "
    <tr>
        <td>{$r['noi_dung']}</td>
        <td>{$r['so_luong']}</td>
        <td>".number_format($r['don_gia'])." đ</td>
        <td>".number_format($r['thanh_tien'])." đ</td>
    </tr>";
}

$body .= "</table><p class='text-xl font-bold mt-4'>Tổng: " . number_format($hd['tong_tien']) . " đ</p>";

/* ========= HTML DÙNG ĐỂ IN ========= */
$print_html = "
<h2>Prestige Manor – Hóa đơn thanh toán</h2>
<p><b>Hóa đơn:</b> #{$ma_hd}</p>
<p><b>Khách hàng:</b> {$hd['ho']} {$hd['ten']}</p>
<p><b>SĐT:</b> {$hd['sdt']}</p>
<p><b>Mã đặt phòng:</b> {$hd['ma_dat_phong']}</p>
<p><b>Ngày lập:</b> {$hd['ngay_lap']}</p>

<table border='1' cellspacing='0' cellpadding='6' width='100%'>
<tr>
    <th>Nội dung</th>
    <th>SL</th>
    <th>Đơn giá</th>
    <th>Thành tiền</th>
</tr>
$print_rows
</table>

<h3>Tổng tiền: " . number_format($hd['tong_tien']) . " đ</h3>
";

echo json_encode([
    "status" => true,
    "html"   => $body,
    "print"  => $print_html
]);
exit;
?>
