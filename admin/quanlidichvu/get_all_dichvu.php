<?php
include "../config.php";
header("Content-Type: application/json");

$q = $conn->query("SELECT ma_dich_vu, ten_dich_vu, don_gia FROM dich_vu ORDER BY ten_dich_vu ASC");

$data = [];
while ($r = $q->fetch_assoc()) $data[] = $r;

echo json_encode(["status" => true, "data" => $data]);
