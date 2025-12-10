<?php
header("Content-Type: application/json; charset=UTF-8");
include "../config.php";

// Nhận dữ liệu POST
$ho         = trim($_POST["ho"] ?? "");
$ten        = trim($_POST["ten"] ?? "");
$ngay_sinh  = $_POST["ngay_sinh"] ?? null;
$sdt        = trim($_POST["sdt"] ?? "");
$email      = trim($_POST["email"] ?? "");

// ===========================
// VALIDATION CƠ BẢN
// ===========================
if ($ho === "" || $ten === "" || $sdt === "" || $email === "") {
    echo json_encode(["status" => false, "msg" => "Vui lòng nhập đầy đủ thông tin!"]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => false, "msg" => "Email không hợp lệ!"]);
    exit;
}

if (!preg_match("/^[0-9]{9,11}$/", $sdt)) {
    echo json_encode(["status" => false, "msg" => "Số điện thoại không hợp lệ!"]);
    exit;
}

// ===========================
// KIỂM TRA TRÙNG EMAIL / SĐT
// ===========================
$check = $conn->prepare("SELECT ma_khach_hang FROM khach_hang WHERE email=? OR sdt=?");
$check->bind_param("ss", $email, $sdt);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["status" => false, "msg" => "⚠ Email hoặc SĐT đã tồn tại!"]);
    exit;
}

// ===========================
// INSERT KHÁCH HÀNG
// ===========================
$mat_khau_mac_dinh = "12345678*";
$hashed = password_hash($mat_khau_mac_dinh, PASSWORD_DEFAULT);

$stmt = $conn->prepare("
    INSERT INTO khach_hang (ho, ten, sdt, ngay_sinh, email, mat_khau, ngay_dang_ky)
    VALUES (?, ?, ?, ?, ?, ?, NOW())
");

$stmt->bind_param("ssssss", $ho, $ten, $sdt, $ngay_sinh, $email, $hashed);

if ($stmt->execute()) {
    echo json_encode(["status" => true, "msg" => "OK"]);
} else {
    echo json_encode(["status" => false, "msg" => "Lỗi thêm khách hàng!"]);
}
