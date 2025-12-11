<?php
include "../config.php";

$q = $conn->query("SHOW COLUMNS FROM phong LIKE 'trang_thai'");
$row = $q->fetch_assoc();

$type = $row['Type'];

preg_match("/^enum\((.*)\)$/", $type, $matches);
$enumValues = [];

foreach (explode(",", $matches[1]) as $value) {
    $enumValues[] = trim($value, " '");
}

echo json_encode([
    "status" => true,
    "data"   => $enumValues
]);
