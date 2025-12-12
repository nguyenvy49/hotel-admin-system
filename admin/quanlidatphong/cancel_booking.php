<?php
include "../config.php";
header("Content-Type: application/json");

$id = intval($_GET['id'] ?? 0);

$conn->query("UPDATE dat_phong SET trang_thai='Hủy' WHERE ma_dat_phong=$id");

echo json_encode(["status"=>true]);
