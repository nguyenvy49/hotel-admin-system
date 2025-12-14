<?php
include "../config.php";

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);
error_reporting(0);

// ================== LẤY DỮ LIỆU ==================
$ten = trim($_POST['ten_loai_phong'] ?? '');
$so  = intval($_POST['so_nguoi_toi_da'] ?? 0);
$gia = floatval($_POST['gia_phong'] ?? 0);

// ================== VALIDATE ==================
if ($ten === '' || $so <= 0 || $gia <= 0) {
    echo json_encode(['status' => false, 'msg' => 'Dữ liệu không hợp lệ']);
    exit;
}

// ================== CHECK TRÙNG TÊN ==================
$check = $conn->prepare(
    "SELECT ma_loai_phong FROM loai_phong WHERE ten_loai_phong = ? LIMIT 1"
);
$check->bind_param("s", $ten);
$check->execute();
$exists = $check->get_result();

if ($exists->num_rows > 0) {
    echo json_encode([
        "status" => false,
        "msg" => "⚠ Loại phòng này đã tồn tại!"
    ]);
    exit;
}

// ================== KIỂM TRA ẢNH ==================
if (!isset($_FILES['hinh_anh']) || $_FILES['hinh_anh']['error'] !== 0) {
    echo json_encode(['status' => false, 'msg' => 'Vui lòng chọn hình ảnh']);
    exit;
}

// ================== UPLOAD ẢNH ==================
$uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/hotel-admin-system/assets/img/";
$hinh_anh = time() . '_' . $_FILES['hinh_anh']['name'];

if (!move_uploaded_file($_FILES['hinh_anh']['tmp_name'], $uploadDir . $hinh_anh)) {
    echo json_encode([
        'status' => false,
        'msg' => 'Upload ảnh thất bại'
    ]);
    exit;
}

// ================== INSERT ==================
$stmt = $conn->prepare(
    "INSERT INTO loai_phong (ten_loai_phong, so_nguoi_toi_da, gia_phong, hinh_anh)
     VALUES (?, ?, ?, ?)"
);
$stmt->bind_param("sids", $ten, $so, $gia, $hinh_anh);
$stmt->execute();

// ================== KẾT QUẢ ==================
echo json_encode([
    'status' => $stmt->affected_rows > 0,
    'msg'    => $stmt->affected_rows > 0
        ? 'Thêm loại phòng thành công'
        : 'Thêm thất bại'
]);
exit;
