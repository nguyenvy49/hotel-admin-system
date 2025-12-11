<?php
include "../config.php";
header("Content-Type: application/json");
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/* =======================================================
    INPUT
======================================================= */
$ma_phong = $_POST['ma_phong'] ?? '';
$ds_dv    = $_POST['ma_dich_vu'] ?? [];
$ds_sl    = $_POST['so_luong'] ?? [];

if (!$ma_phong || empty($ds_dv) || empty($ds_sl)) {
    echo json_encode(["status" => false, "msg" => "Dữ liệu không hợp lệ"]);
    exit;
}

if (count($ds_dv) !== count($ds_sl)) {
    echo json_encode(["status" => false, "msg" => "Danh sách dịch vụ không hợp lệ"]);
    exit;
}

/* =======================================================
   1) Lấy mã đặt phòng đang ở
======================================================= */
$stmt = $conn->prepare("
    SELECT ma_dat_phong 
    FROM dat_phong 
    WHERE ma_phong = ? AND trang_thai = 'Đang ở'
");
$stmt->bind_param("i", $ma_phong);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows == 0) {
    echo json_encode(["status" => false, "msg" => "Phòng hiện không có khách ở"]);
    exit;
}

$ma_dp = $res->fetch_assoc()['ma_dat_phong'];

/* =======================================================
   2) Chuẩn bị câu SQL dùng chung
======================================================= */

$stmtCheck = $conn->prepare("
    SELECT ma_sddv, so_luong 
    FROM phieu_su_dung_dich_vu 
    WHERE ma_dat_phong = ? AND ma_dich_vu = ?
");

$stmtUpdate = $conn->prepare("
    UPDATE phieu_su_dung_dich_vu
    SET so_luong = so_luong + ?
    WHERE ma_sddv = ?
");

$stmtInsert = $conn->prepare("
    INSERT INTO phieu_su_dung_dich_vu 
    (ma_dat_phong, ma_dich_vu, ngay_su_dung, so_luong, don_gia)
    VALUES (?, ?, CURDATE(), ?, ?)
");

$stmtGia = $conn->prepare("SELECT don_gia FROM dich_vu WHERE ma_dich_vu = ?");

/* =======================================================
   3) Lặp từng dịch vụ
======================================================= */

for ($i = 0; $i < count($ds_dv); $i++) {

    $dv = (int)$ds_dv[$i];
    $sl = max(1, (int)$ds_sl[$i]);   // không bao giờ <1

    /* 3.1 Lấy đơn giá dịch vụ */
    $stmtGia->bind_param("i", $dv);
    $stmtGia->execute();
    $rowGia = $stmtGia->get_result()->fetch_assoc();
    if (!$rowGia) continue;
    $don_gia = (float)$rowGia['don_gia'];

    /* 3.2 Kiểm tra dịch vụ đã tồn tại chưa */
    $stmtCheck->bind_param("ii", $ma_dp, $dv);
    $stmtCheck->execute();
    $resCheck = $stmtCheck->get_result();

    if ($resCheck->num_rows > 0) {
        // Đã có → cộng dồn
        $sddv = $resCheck->fetch_assoc()['ma_sddv'];

        $stmtUpdate->bind_param("ii", $sl, $sddv);
        $stmtUpdate->execute();
    } else {
        // Chưa có → thêm mới
        $stmtInsert->bind_param("iiid", $ma_dp, $dv, $sl, $don_gia);
        $stmtInsert->execute();
    }
}

/* =======================================================
   DONE
======================================================= */

echo json_encode(["status" => true, "msg" => "Thêm dịch vụ thành công (cộng dồn)!"]);
exit;
