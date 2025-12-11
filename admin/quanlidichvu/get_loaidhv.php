<?php
include "../config.php";

$q = $conn->query("SHOW COLUMNS FROM dich_vu LIKE 'loai_dich_vu'");
$row = $q->fetch_assoc();

$enumRaw = $row['Type']; // enum('Spa','Giặt ủi','Ăn uống',...)

preg_match("/^enum\((.*)\)$/", $enumRaw, $matches);

$values = [];
foreach (explode(",", $matches[1]) as $v) {
    $values[] = trim($v, " '");
}

echo json_encode([
    "status" => true,
    "data" => $values
]);
