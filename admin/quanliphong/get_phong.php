<?php
include "../config.php";

$id = $_GET['id'];

$sql = "
SELECT p.*, lp.ten_loai_phong, lp.so_nguoi_toi_da, lp.gia_phong
FROM phong p
JOIN loai_phong lp ON p.ma_loai_phong = lp.ma_loai_phong
WHERE ma_phong = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

echo json_encode([
    "status" => $data ? true : false,
    "data"   => $data
]);
