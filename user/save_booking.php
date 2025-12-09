<?php
ob_start();
header("Content-Type: application/json; charset=UTF-8");
session_start();
include "config.php";

function endJSON($data) {
    ob_clean();
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

if (!isset($_SESSION["khach_hang_id"])) {
    endJSON(["success" => false, "message" => "Bạn chưa đăng nhập"]);
}

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) endJSON(["success" => false, "message" => "Dữ liệu không hợp lệ"]);

$checkin  = $data["checkin"] ?? null;
$checkout = $data["checkout"] ?? null;
$rooms    = $data["rooms"] ?? [];
$services = $data["services"] ?? [];

if (!$checkin || !$checkout) {
    endJSON(["success" => false, "message" => "Ngày nhận/trả phòng không hợp lệ"]);
}

$ma_khach = intval($_SESSION["khach_hang_id"]);
$conn->set_charset("utf8mb4");

// BẮT ĐẦU GIAO DỊCH
$conn->begin_transaction();

try {

    /*====================================================
      1️⃣ TÍNH SỐ KHÁCH
    ====================================================*/
    $total_guest = 0;
    foreach ($rooms as $r) $total_guest += ($r["qty"] * 2);

    /*====================================================
      2️⃣ TẠO BOOKING CHÍNH
    ====================================================*/
    $stmt = $conn->prepare("
        INSERT INTO dat_phong 
        (ma_khach_hang, ngay_dat, ngay_nhan, ngay_tra, so_nguoi, trang_thai)
        VALUES (?, NOW(), ?, ?, ?, 'Đã đặt')
    ");
    $stmt->bind_param("isss", $ma_khach, $checkin, $checkout, $total_guest);
    $stmt->execute();

    $ma_dat_phong = $stmt->insert_id;

    /*====================================================
      3️⃣ TÍNH SỐ ĐÊM
    ====================================================*/
    $in  = new DateTime($checkin);
    $out = new DateTime($checkout);
    $so_dem = $in->diff($out)->days;
    if ($so_dem <= 0) $so_dem = 1;

    /*====================================================
      4️⃣ GHI CHI TIẾT ĐẶT PHÒNG
    ====================================================*/
    $total_room_money = 0;

    foreach ($rooms as $r) {

        $roomType = intval($r["id"]);
        $qty      = intval($r["qty"]);

        // Lấy giá phòng chuẩn
        $priceRow = $conn->query("
            SELECT gia_phong FROM loai_phong WHERE ma_loai_phong = $roomType
        ")->fetch_assoc();

        $price = floatval($priceRow["gia_phong"]);

        for ($i = 0; $i < $qty; $i++) {

            // Lấy 1 phòng trống
            $ph = $conn->query("
                SELECT ma_phong 
                FROM phong 
                WHERE ma_loai_phong = $roomType
                  AND trang_thai='Trống'
                LIMIT 1
            ")->fetch_assoc();

            if (!$ph) {
                throw new Exception("Không đủ phòng loại {$r['roomName']}");
            }

            $ma_phong = $ph["ma_phong"];

            // Ghi chi tiết đặt phòng
            $conn->query("
                INSERT INTO chi_tiet_dat_phong (ma_dat_phong, ma_phong, gia_phong, so_dem)
                VALUES ($ma_dat_phong, $ma_phong, $price, $so_dem)
            ");

            // Đánh dấu phòng đã đặt
            $conn->query("UPDATE phong SET trang_thai='Đã đặt' WHERE ma_phong=$ma_phong");

            $total_room_money += ($price * $so_dem);
        }
    }

    /*====================================================
      5️⃣ GHI DỊCH VỤ
    ====================================================*/
    $total_service_money = 0;

    foreach ($services as $s) {
        $dvId = intval($s["id"]);
        $qty  = intval($s["qty"]);

        $dvRow = $conn->query("
            SELECT don_gia FROM dich_vu WHERE ma_dich_vu = $dvId
        ")->fetch_assoc();

        $price = floatval($dvRow["don_gia"]);
        $tt = $qty * $price;

        $conn->query("
            INSERT INTO phieu_su_dung_dich_vu
            (ma_dat_phong, ma_dich_vu, ngay_su_dung, so_luong, don_gia)
            VALUES ($ma_dat_phong, $dvId, NOW(), $qty, $price)
        ");

        $total_service_money += $tt;
    }

    /*====================================================
      6️⃣ TẠO HÓA ĐƠN
    ====================================================*/
    $tong_tien = $total_room_money + $total_service_money;

    $conn->query("
        INSERT INTO hoa_don 
        (ma_dat_phong, ngay_lap, tong_tien, trang_thai, phuong_thuc)
        VALUES ($ma_dat_phong, NOW(), $tong_tien, 'Chưa thanh toán', 'Tiền mặt')
    ");

    $ma_hoa_don = $conn->insert_id;

    /*====================================================
      7️⃣ GHI CHI TIẾT HÓA ĐƠN
    ====================================================*/

    // TIỀN PHÒNG
    $conn->query("
        INSERT INTO chi_tiet_hoa_don 
        (ma_hoa_don, noi_dung, so_luong, don_gia, thanh_tien)
        VALUES ($ma_hoa_don, 'Tiền phòng ($so_dem đêm)', 1, $total_room_money, $total_room_money)
    ");

    // DỊCH VỤ
    foreach ($services as $s) {
        $dvId = intval($s["id"]);
        $qty  = intval($s["qty"]);

        $dvRow = $conn->query("SELECT don_gia FROM dich_vu WHERE ma_dich_vu = $dvId")->fetch_assoc();
        $price = $dvRow["don_gia"];
        $tt = $qty * $price;

        $conn->query("
            INSERT INTO chi_tiet_hoa_don 
            (ma_hoa_don, noi_dung, so_luong, don_gia, thanh_tien)
            VALUES ($ma_hoa_don, 'Dịch vụ: {$s['name']}', $qty, $price, $tt)
        ");
    }

    // NẾU TỚI ĐÂY MÀ KHÔNG LỖI → COMMIT
    $conn->commit();

endJSON([
    "success" => true,
    "message" => "Đặt phòng thành công!",
    "ma_hoa_don" => $ma_hoa_don,
    "ma_dat_phong" => $ma_dat_phong,
    "tong_tien" => $tong_tien
]);


} catch (Exception $e) {

    // LỖI → ROLLBACK
    $conn->rollback();

    endJSON([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
