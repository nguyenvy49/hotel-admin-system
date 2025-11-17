<?php
include('../includes/db_connect.php');
$id = intval($_POST['id']);
$current = $_POST['status'] ?? 'Active';
$new = ($current == 'Active') ? 'Locked' : 'Active';
$stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
$stmt->bind_param("si", $new, $id);
$stmt->execute();
if($stmt->affected_rows >= 0) echo "Tài khoản đã được $new.";
else echo "Có lỗi, thử lại.";
$stmt->close();
?>
