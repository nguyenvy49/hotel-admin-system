<?php
include "../config.php";

// chỉ nhận POST
if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    echo json_encode(["status" => false, "msg" => "Invalid request"]);
    exit;
}

$ten  = $_POST['ten_dich_vu'] ?? "";
$gia  = intval($_POST['don_gia'] ?? 0);
$loai = $_POST['loai_dich_vu'] ?? "";

if ($ten === "" || $gia <= 0 || $loai === "") {
    echo json_encode(["status" => false, "msg" => "Dữ liệu không hợp lệ"]);
    exit;
}

$stmt = $conn->prepare("
    INSERT INTO dich_vu (ten_dich_vu, don_gia, loai_dich_vu)
    VALUES (?, ?, ?)
");

$stmt->bind_param("sis", $ten, $gia, $loai);
$ok = $stmt->execute();

echo json_encode([
    "status" => $ok,
    "msg" => $ok ? "Thêm thành công" : "Lỗi khi thêm"
]);
