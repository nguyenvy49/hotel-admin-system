<?php
include "../config.php";
header("Content-Type: application/json");

$ma_dat_phong = intval($_GET['id'] ?? 0);
if (!$ma_dat_phong) {
    echo json_encode(["status"=>false,"msg"=>"Thiếu mã đặt phòng"]);
    exit;
}

/* =======================================================
   1) LẤY BOOKING
======================================================= */
$q = $conn->query("
    SELECT ngay_nhan, ngay_tra, trang_thai
    FROM dat_phong
    WHERE ma_dat_phong = $ma_dat_phong
");
if ($q->num_rows == 0) {
    echo json_encode(["status"=>false,"msg"=>"Booking không tồn tại"]);
    exit;
}
$bk = $q->fetch_assoc();

if ($bk['trang_thai'] !== "Đang ở") {
    echo json_encode(["status"=>false,"msg"=>"Chỉ checkout khi khách đang ở!"]);
    exit;
}

$ngay_nhan = $bk['ngay_nhan'];
$ngay_tra  = $bk['ngay_tra'];
$ngay_tra_thuc = date("Y-m-d H:i:s");

/* SỐ ĐÊM CHUẨN */
$so_dem = (new DateTime($ngay_nhan))->diff(new DateTime($ngay_tra))->days;

/* =======================================================
   2) LẤY PHÒNG
======================================================= */
$rooms = $conn->query("
    SELECT p.ma_phong, p.so_phong, lp.gia_phong
    FROM chi_tiet_dat_phong ctdp
    JOIN phong p ON p.ma_phong = ctdp.ma_phong
    JOIN loai_phong lp ON lp.ma_loai_phong = p.ma_loai_phong
    WHERE ctdp.ma_dat_phong = $ma_dat_phong
");

$tien_phong = 0;
$ct_phong = [];

while ($r = $rooms->fetch_assoc()) {

    $thanh_tien = $r["gia_phong"] * $so_dem;
    $tien_phong += $thanh_tien;

    $ct_phong[] = [
        "so_phong" => $r["so_phong"],
        "don_gia"  => $r["gia_phong"],
        "so_dem"   => $so_dem,
        "tt"       => $thanh_tien
    ];

    // trả phòng
    $conn->query("UPDATE phong SET trang_thai='Trống' WHERE ma_phong={$r['ma_phong']}");
}

/* =======================================================
   3) LẤY DỊCH VỤ
======================================================= */
$dv = $conn->query("
    SELECT dv.ten_dich_vu, ps.so_luong, ps.don_gia
    FROM phieu_su_dung_dich_vu ps
    JOIN dich_vu dv ON dv.ma_dich_vu = ps.ma_dich_vu
    WHERE ps.ma_dat_phong = $ma_dat_phong
");

$ds_dv = [];
$tien_dv = 0;

while ($d = $dv->fetch_assoc()) {
    $tt = $d["so_luong"] * $d["don_gia"];
    $tien_dv += $tt;

    $ds_dv[] = [
        "ten" => $d["ten_dich_vu"],
        "sl"  => $d["so_luong"],
        "gia" => $d["don_gia"],
        "tt"  => $tt
    ];
}

/* =======================================================
   4) LẤY HÓA ĐƠN CỌC (NẾU CÓ)
======================================================= */
$hd = $conn->query("
    SELECT ma_hoa_don, tien_coc
    FROM hoa_don
    WHERE ma_dat_phong = $ma_dat_phong
    ORDER BY ma_hoa_don ASC
    LIMIT 1
");

$hasDeposit = $hd->num_rows > 0;
$ma_hd = null;
$tien_coc = 0;

if ($hasDeposit) {
    $h = $hd->fetch_assoc();
    $ma_hd = $h["ma_hoa_don"];
    $tien_coc = floatval($h["tien_coc"]);
}

/* =======================================================
   5) TÍNH PHỤ PHÍ CHECKOUT MUỘN
======================================================= */
$now = new DateTime($ngay_tra_thuc);
$checkout_std = new DateTime($ngay_tra . " 12:00:00");

$phu_phi = 0;
$noi_dung_phu_phi = "";

if ($now > $checkout_std) {
    $gio = intval($now->format("H"));

    if ($gio < 18) {
        $phu_phi = $tien_phong * 0.25;
        $noi_dung_phu_phi = "Phụ phí checkout muộn 25%";
    } else {
        $gia_tb = $tien_phong / max(1, $so_dem);
        $phu_phi = $gia_tb;
        $noi_dung_phu_phi = "Checkout sau 18h → phụ thu 1 đêm";
    }
}

/* =======================================================
   6) TÍNH TỔNG PHẢI TRẢ
======================================================= */
$tong = $tien_phong + $tien_dv + $phu_phi;
$phai_tra = $tong - $tien_coc;

/* =======================================================
   7) NẾU CÓ HÓA ĐƠN CỌC → UPDATE, KHÔNG INSERT MỚI
======================================================= */
if ($hasDeposit) {

    $conn->query("
        UPDATE hoa_don
        SET tong_tien = $tong,
            tien_them = $phu_phi,
            trang_thai = 'Đã thanh toán',
            ngay_thanh_toan = CURDATE()
        WHERE ma_hoa_don = $ma_hd
    ");

} else {
    // nếu không có hóa đơn cọc → tạo mới
    $conn->query("
        INSERT INTO hoa_don (ma_dat_phong, ngay_lap, tong_tien, trang_thai, tien_them)
        VALUES ($ma_dat_phong, CURDATE(), $tong, 'Đã thanh toán', $phu_phi)
    ");
    $ma_hd = $conn->insert_id;
}

/* =======================================================
   8) XÓA CHI TIẾT CŨ & THÊM CHI TIẾT MỚI
======================================================= */
$conn->query("DELETE FROM chi_tiet_hoa_don WHERE ma_hoa_don = $ma_hd");

foreach ($ct_phong as $p) {
    $nd = "Tiền phòng ({$p['so_dem']} đêm - Phòng {$p['so_phong']})";
    $conn->query("
        INSERT INTO chi_tiet_hoa_don (ma_hoa_don, noi_dung, so_luong, don_gia, thanh_tien)
        VALUES ($ma_hd, '$nd', 1, {$p['tt']}, {$p['tt']})
    ");
}

foreach ($ds_dv as $dv) {
    $nd = "Dịch vụ: {$dv['ten']}";
    $conn->query("
        INSERT INTO chi_tiet_hoa_don (ma_hoa_don, noi_dung, so_luong, don_gia, thanh_tien)
        VALUES ($ma_hd, '$nd', {$dv['sl']}, {$dv['gia']}, {$dv['tt']})
    ");
}

if ($phu_phi > 0) {
    $conn->query("
        INSERT INTO chi_tiet_hoa_don (ma_hoa_don, noi_dung, so_luong, don_gia, thanh_tien)
        VALUES ($ma_hd, '$noi_dung_phu_phi', 1, $phu_phi, $phu_phi)
    ");
}

/* =======================================================
   9) CẬP NHẬT BOOKING
======================================================= */
$conn->query("
    UPDATE dat_phong
    SET trang_thai='Đã trả', ngay_tra_thuc='$ngay_tra_thuc'
    WHERE ma_dat_phong = $ma_dat_phong
");

/* =======================================================
   10) TRẢ JSON
======================================================= */
echo json_encode([
    "status" => true,
    "msg" => "Checkout thành công!",
    "ma_hd" => $ma_hd,
    "tong" => $tong,
    "tien_coc" => $tien_coc,
    "phai_tra" => $phai_tra
]);
exit;
?>
