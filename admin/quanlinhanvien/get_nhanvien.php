<?php
include "../config.php";

$id = $_GET['id'] ?? 0;

$q = $conn->prepare("SELECT * FROM nhan_vien WHERE ma_nhan_vien=?");
$q->bind_param("i", $id);
$q->execute();

$data = $q->get_result()->fetch_assoc();

echo json_encode([
    "status" => $data ? true : false,
    "data" => $data
]);
