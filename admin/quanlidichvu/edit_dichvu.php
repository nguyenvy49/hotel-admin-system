<?php
include "../config.php";

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    echo json_encode(["status" => false, "msg" => "Invalid request"]);
    exit;
}

$id   = intval($_POST['ma_dich_vu'] ?? 0);
$ten  = $_POST['ten_dich_vu'] ?? "";
$gia  = intval($_POST['don_gia'] ?? 0);
$loai = $_POST['loai_dich_vu'] ?? "";

if ($id <= 0 || $ten === "" || $gia <= 0 || $loai === "") {
    echo json_encode(["status" => false, "msg" => "Dữ liệu không hợp lệ"]);
    exit;
}

$stmt = $conn->prepare("
    UPDATE dich_vu 
    SET ten_dich_vu=?, don_gia=?, loai_dich_vu=?
    WHERE ma_dich_vu=?
");

$stmt->bind_param("sisi", $ten, $gia, $loai, $id);
$ok = $stmt->execute();

echo json_encode([
    "status" => $ok,
    "msg" => $ok ? "Cập nhật thành công" : "Lỗi khi cập nhật"
]);
