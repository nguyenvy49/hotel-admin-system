<?php
include "../config.php";
header("Content-Type: application/json");

$rooms = json_decode($_POST["rooms"] ?? "[]", true);
$ten_khach = trim($_POST["ten_khach"] ?? "");
$sdt = trim($_POST["sdt"] ?? "");
$ngay_nhan = $_POST["ngay_nhan"] ?? "";
$ngay_tra = $_POST["ngay_tra"] ?? "";

if (!$rooms) {
    echo json_encode(["status" => false, "msg" => "Chưa chọn phòng"]);
    exit;
}

if ($ten_khach == "" || $sdt == "" || !$ngay_nhan || !$ngay_tra) {
    echo json_encode(["status" => false, "msg" => "Thiếu thông tin"]);
    exit;
}

/* ------------------ TÁCH TÊN ------------------ */
$arr = explode(" ", $ten_khach);
$ten = array_pop($arr);
$ho  = implode(" ", $arr);

/* ------------------ KIỂM TRA / TẠO KHÁCH ------------------ */
$stmt = $conn->prepare("SELECT ma_khach_hang FROM khach_hang WHERE sdt=? LIMIT 1");
$stmt->bind_param("s", $sdt);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    $ma_khach = $res->fetch_assoc()["ma_khach_hang"];
} else {
    $email = $sdt."_".time()."@hotel.local";
    $pass = password_hash("123456", PASSWORD_DEFAULT);

    $stmt = $conn->prepare("
        INSERT INTO khach_hang (ho, ten, sdt, email, mat_khau)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("sssss", $ho, $ten, $sdt, $email, $pass);
    $stmt->execute();
    $ma_khach = $stmt->insert_id;
}

/* ------------------ TẠO 1 ĐƠN ĐẶT PHÒNG ------------------ */
$stmt = $conn->prepare("
    INSERT INTO dat_phong (ma_khach_hang, ngay_dat, ngay_nhan, ngay_tra, so_nguoi, trang_thai)
    VALUES (?, CURDATE(), ?, ?, 1, 'Đang ở')
");
$stmt->bind_param("iss", $ma_khach, $ngay_nhan, $ngay_tra);
$stmt->execute();
$ma_dat_phong = $stmt->insert_id;

/* ------------------ INSERT NHIỀU PHÒNG VÀO CTDP ------------------ */
foreach ($rooms as $roomID) {

    // lấy giá phòng
    $g = $conn->query("
        SELECT lp.gia_phong 
        FROM phong p JOIN loai_phong lp ON p.ma_loai_phong = lp.ma_loai_phong
        WHERE p.ma_phong = $roomID
    ")->fetch_assoc();

    $gia = $g["gia_phong"] ?? 0;

    $stmt = $conn->prepare("
        INSERT INTO chi_tiet_dat_phong (ma_dat_phong, ma_phong, gia_phong, so_dem)
        VALUES (?, ?, ?, 0)
    ");
    $stmt->bind_param("iid", $ma_dat_phong, $roomID, $gia);
    $stmt->execute();

    // cập nhật trạng thái phòng
    $conn->query("UPDATE phong SET trang_thai='Đang ở' WHERE ma_phong=$roomID");
}

/* ------------------ TẠO HÓA ĐƠN MẶC ĐỊNH ------------------ */
$stmt = $conn->prepare("
    INSERT INTO hoa_don (ma_dat_phong, ngay_lap, tong_tien, trang_thai)
    VALUES (?, CURDATE(), 0, 'Chưa thanh toán')
");
$stmt->bind_param("i", $ma_dat_phong);
$stmt->execute();

/* ------------------ TRẢ KẾT QUẢ ------------------ */
echo json_encode([
    "status" => true,
    "msg" => "Đặt phòng thành công",
    "ma_dat_phong" => $ma_dat_phong
]);
exit;
