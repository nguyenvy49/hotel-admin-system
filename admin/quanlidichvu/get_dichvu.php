<?php
include "../config.php";

if (!isset($_GET['id'])) {
    echo json_encode(["status" => false, "msg" => "Thiếu ID"]);
    exit;
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM dich_vu WHERE ma_dich_vu = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

echo json_encode([
    "status" => $data ? true : false,
    "data" => $data
]);
