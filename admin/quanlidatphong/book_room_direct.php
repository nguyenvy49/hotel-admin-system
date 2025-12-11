<?php
include "../config.php";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
header("Content-Type: application/json");

$ma_phong  = $_POST['ma_phong'] ?? '';
$ten_khach = trim($_POST['ten_khach'] ?? '');
$sdt       = trim($_POST['sdt'] ?? '');
$ngay_nhan = $_POST['ngay_nhan'] ?? '';
$ngay_tra  = $_POST['ngay_tra'] ?? '';

if ($ten_khach == "" || $sdt == "" || !$ngay_nhan || !$ngay_tra) {
    echo json_encode(["status" => false, "msg" => "Vui lòng nhập đầy đủ thông tin"]);
    exit;
}

/* TÁCH HỌ TÊN */
$arr = explode(" ", $ten_khach);
$ten = array_pop($arr);
$ho  = implode(" ", $arr);

/* 1) CHECK KHÁCH HÀNG */
$stmt = $conn->prepare("SELECT ma_khach_hang FROM khach_hang WHERE sdt=?");
$stmt->bind_param("s", $sdt);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {

    $ma_khach = $res->fetch_assoc()['ma_khach_hang'];

} else {

    // email never duplicate
    $emailFake = $sdt . "_" . time() . "@noemail.local";
    $pass = password_hash("12345678", PASSWORD_DEFAULT);

    $stmt = $conn->prepare("
        INSERT INTO khach_hang (ho, ten, sdt, email, mat_khau)
        VALUES (?, ?, ?, ?, ?)
    ");

    try {
        $stmt->bind_param("sssss", $ho, $ten, $sdt, $emailFake, $pass);
        $stmt->execute();
    } catch (Exception $e) {
        echo json_encode(["status" => false, "msg" => "Lỗi KH: " . $e->getMessage()]);
        exit;
    }

    $ma_khach = $stmt->insert_id;
}

/* 2) TẠO ĐẶT PHÒNG */
$ngay_dat = date("Y-m-d");
$so_nguoi = 1;

$stmt = $conn->prepare("
    INSERT INTO dat_phong (ma_khach_hang, ma_phong, ngay_dat, ngay_nhan, ngay_tra, so_nguoi, trang_thai)
    VALUES (?, ?, ?, ?, ?, ?, 'Đang ở')
");

try {
    $stmt->bind_param("iisssi", $ma_khach, $ma_phong, $ngay_dat, $ngay_nhan, $ngay_tra, $so_nguoi);
    $stmt->execute();
} catch (Exception $e) {
    echo json_encode(["status" => false, "msg" => "Lỗi đặt phòng: " . $e->getMessage()]);
    exit;
}

$conn->query("UPDATE phong SET trang_thai='Đang ở' WHERE ma_phong=$ma_phong");

echo json_encode(["status" => true, "msg" => "Đặt phòng thành công"]);
exit;
