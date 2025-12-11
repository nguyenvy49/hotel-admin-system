<?php
include "../config.php";

$ma = $_GET['ma'] ?? 0;
$ma = (int)$ma;

$q = $conn->prepare("SELECT * FROM khach_hang WHERE ma_khach_hang=? LIMIT 1");
$q->bind_param("i", $ma);
$q->execute();
$res = $q->get_result()->fetch_assoc();

echo json_encode([
    "status" => $res ? true : false,
    "data" => $res
]);
