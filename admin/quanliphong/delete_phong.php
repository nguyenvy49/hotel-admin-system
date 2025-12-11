<?php
include "../config.php";

$id = $_POST["id"];

$stmt = $conn->prepare("DELETE FROM phong WHERE ma_phong=?");
$stmt->bind_param("i", $id);

echo json_encode(["status" => $stmt->execute()]);
