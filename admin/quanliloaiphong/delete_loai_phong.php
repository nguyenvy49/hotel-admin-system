<?php
include "../config.php";

$id = $_POST['id'];

$stmt = $conn->prepare("DELETE FROM loai_phong WHERE ma_loai_phong=?");
$stmt->bind_param("i", $id);

echo json_encode([
    "status" => $stmt->execute()
]);
