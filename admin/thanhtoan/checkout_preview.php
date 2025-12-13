<?php
include "../config.php";
header("Content-Type: application/json");

$ma_dat_phong = intval($_GET["id"] ?? 0);
if (!$ma_dat_phong) {
    echo json_encode(["status"=>false,"msg"=>"Thiếu mã booking"]);
    exit;
}

/* ==========================================================
    1) LẤY BOOKING + TIỀN CỌC (LẤY ĐÚNG HÓA ĐƠN CỌC)
========================================================== */
$q = $conn->query("
    SELECT dp.*, 
           COALESCE(hd.tien_coc,0) AS tien_coc
    FROM dat_phong dp
    LEFT JOIN hoa_don hd 
           ON hd.ma_dat_phong = dp.ma_dat_phong
          AND hd.trang_thai = 'Đã đặt cọc'
    WHERE dp.ma_dat_phong = $ma_dat_phong
");

if ($q->num_rows == 0) {
    echo json_encode(["status"=>false,"msg"=>"Không tìm thấy booking"]);
    exit;
}

$bk = $q->fetch_assoc();

if ($bk["trang_thai"] !== "Đang ở") {
    echo json_encode(["status"=>false,"msg"=>"Chỉ checkout khi khách đang ở"]);
    exit;
}

$ngay_nhan = $bk["ngay_nhan"];
$ngay_tra  = $bk["ngay_tra"];
$tien_coc  = floatval($bk["tien_coc"]);

/* ==========================================================
    2) TÍNH SỐ ĐÊM (CHECK-IN → NGÀY TRẢ DỰ KIẾN)
========================================================== */
$so_dem = (new DateTime($ngay_nhan))->diff(new DateTime($ngay_tra))->days;
if ($so_dem <= 0) $so_dem = 1;

/* ==========================================================
    3) TÍNH TIỀN PHÒNG CHI TIẾT THEO TỪNG PHÒNG
========================================================== */
$sql_rooms = $conn->query("
    SELECT lp.gia_phong
    FROM chi_tiet_dat_phong c
    JOIN phong p ON p.ma_phong = c.ma_phong
    JOIN loai_phong lp ON lp.ma_loai_phong = p.ma_loai_phong
    WHERE c.ma_dat_phong = $ma_dat_phong
");

$tien_phong = 0;

while ($r = $sql_rooms->fetch_assoc()) {
    $tien_phong += $r["gia_phong"] * $so_dem;
}

/* ==========================================================
    4) TÍNH TIỀN DỊCH VỤ
========================================================== */
$sql_dv = $conn->query("
    SELECT SUM(so_luong * don_gia) AS tong_dv
    FROM phieu_su_dung_dich_vu
    WHERE ma_dat_phong = $ma_dat_phong
");

$tien_dv = floatval($sql_dv->fetch_assoc()["tong_dv"] ?? 0);

/* ==========================================================
    5) TÍNH PHỤ PHÍ CHECKOUT MUỘN
========================================================== */

$now = new DateTime();
$gio = intval($now->format("H"));
$phu_phi = 0;
$noi_pp = "";

/*
    Quy chuẩn:
    - Trước 12h → miễn phí
    - 12h–18h → +25% tiền phòng
    - Sau 18h → +1 đêm
*/

if ($gio >= 12 && $gio < 18) {
    $phu_phi = $tien_phong * 0.25;
    $noi_pp = "Checkout muộn (12h–18h): +25% tiền phòng";
} 
else if ($gio >= 18) {
    $gia_tb = $tien_phong / max(1, $so_dem);
    $phu_phi = $gia_tb;
    $noi_pp = "Checkout sau 18h → tính thêm 1 đêm";
}

/* ==========================================================
    6) TỔNG TIỀN & TIỀN PHẢI TRẢ SAU KHI TRỪ CỌC
========================================================== */
$tong = $tien_phong + $tien_dv + $phu_phi;

$phai_tra = max(0, $tong - $tien_coc);

/* ==========================================================
    7) TRẢ JSON → JAVASCRIPT HIỂN THỊ THANH TOÁN
========================================================== */
echo json_encode([
    "status" => true,
    "data" => [
        "ma_dat_phong"      => $ma_dat_phong,
        "tien_phong"        => $tien_phong,
        "tien_dv"           => $tien_dv,
        "phu_phi"           => $phu_phi,
        "noi_dung_phu_phi"  => $noi_pp,
        "tien_coc"          => $tien_coc,
        "phai_tra"          => $phai_tra
    ]
]);
exit;
