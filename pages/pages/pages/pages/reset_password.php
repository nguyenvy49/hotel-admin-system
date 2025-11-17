<?php
include('../includes/db_connect.php');
$id = intval($_POST['id']);
$newPass = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
$hash = password_hash($newPass, PASSWORD_BCRYPT);
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
$stmt->bind_param("si", $hash, $id);
$stmt->execute();
if($stmt->affected_rows >= 0) {
  // TRONG DEMO: trả về mật khẩu mới (thực tế không hiển thị mà gửi mail)
  echo "Mật khẩu mới: <strong>$newPass</strong>";
} else {
  echo "Có lỗi khi reset mật khẩu.";
}
$stmt->close();
?>
