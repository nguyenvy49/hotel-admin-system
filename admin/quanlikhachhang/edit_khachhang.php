<?php
include "../config.php";

$ma = $_POST["ma_khach_hang"];

$ho = $_POST["ho"];
$ten = $_POST["ten"];
$ngay_sinh = $_POST["ngay_sinh"];
$sdt = $_POST["sdt"];
$email = $_POST["email"];

// Kiểm tra email/sdt trùng
$check = $conn->prepare("SELECT ma_khach_hang FROM khach_hang WHERE (email=? OR sdt=?) AND ma_khach_hang != ?");
$check->bind_param("ssi", $email, $sdt, $ma);
$check->execute();
$rs = $check->get_result();

if ($rs->num_rows > 0) {
    echo json_encode(["status" => false, "msg" => "⚠ Email hoặc số điện thoại đã tồn tại"]);
    exit;
}

$update = $conn->prepare("
    UPDATE khach_hang SET ho=?, ten=?, ngay_sinh=?, sdt=?, email=?
    WHERE ma_khach_hang=?
");
$update->bind_param("sssssi", $ho, $ten, $ngay_sinh, $sdt, $email, $ma);

echo json_encode(["status" => $update->execute(), "msg" => ""]);
