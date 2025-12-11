<?php
include "../config.php";

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    echo json_encode(["status" => false, "msg" => "Invalid request"]);
    exit;
}

$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(["status" => false, "msg" => "Thiếu ID"]);
    exit;
}

$stmt = $conn->prepare("DELETE FROM dich_vu WHERE ma_dich_vu=?");
$stmt->bind_param("i", $id);
$ok = $stmt->execute();

echo json_encode([
    "status" => $ok,
    "msg" => $ok ? "Xóa thành công" : "Lỗi khi xóa"
]);
