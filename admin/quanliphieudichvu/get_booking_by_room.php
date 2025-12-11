<?php
include "../config.php";

$ma_phong = $_GET['ma_phong'];

$q = $conn->prepare("
    SELECT ma_dat_phong 
    FROM dat_phong 
    WHERE ma_phong = ? AND trang_thai = 'Đang ở'
    LIMIT 1
");
$q->bind_param("i", $ma_phong);
$q->execute();
$res = $q->get_result()->fetch_assoc();

if(!$res){
    echo json_encode([
        "status" => false,
        "msg" => "Phòng chưa có khách ở!"
    ]);
    exit;
}

echo json_encode([
    "status" => true,
    "ma_dat_phong" => $res['ma_dat_phong']
]);
