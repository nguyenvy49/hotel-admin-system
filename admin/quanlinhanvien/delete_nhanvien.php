<?php
include "../config.php";

$id = $_POST['id'];

$stmt = $conn->prepare("DELETE FROM nhan_vien WHERE ma_nhan_vien=?");
$stmt->bind_param("i", $id);

echo json_encode(["status" => $stmt->execute()]);
