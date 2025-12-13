<?php
include "../config.php";
header("Content-Type: application/json");
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/*
    EXPECTED INPUT:
    ma_dat_phong   : int
    ma_dich_vu[]   : array
    so_luong[]     : array
*/

$ma_dp = intval($_POST["ma_dat_phong"] ?? 0);
$arrDV = $_POST["ma_dich_vu"] ?? [];
$arrSL = $_POST["so_luong"] ?? [];

if (!$ma_dp) {
    echo json_encode(["status" => false, "msg" => "Thiếu mã đặt phòng"]);
    exit;
}

if (count($arrDV) == 0 || count($arrDV) != count($arrSL)) {
    echo json_encode(["status" => false, "msg" => "Dữ liệu dịch vụ không hợp lệ"]);
    exit;
}

/* ----------------------------------------------------
   CHUẨN BỊ STATEMENTS
---------------------------------------------------- */

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
    (ma_dat_phong, ma_dich_vu, so_luong, don_gia, ngay_su_dung)
    VALUES (?, ?, ?, ?, CURDATE())
");

$stmtGia = $conn->prepare("SELECT don_gia FROM dich_vu WHERE ma_dich_vu = ?");

/* ----------------------------------------------------
   LẶP QUA TỪNG DỊCH VỤ ĐƯỢC GỬI LÊN
---------------------------------------------------- */

for ($i = 0; $i < count($arrDV); $i++) {

    $dv = intval($arrDV[$i]);       // ID dịch vụ
    $sl = max(1, intval($arrSL[$i])); // số lượng (>=1)

    /* 1. LẤY ĐƠN GIÁ */
    $stmtGia->bind_param("i", $dv);
    $stmtGia->execute();
    $rowGia = $stmtGia->get_result()->fetch_assoc();

    if (!$rowGia) continue;
    $don_gia = floatval($rowGia["don_gia"]);

    /* 2. KIỂM TRA DỊCH VỤ ĐÃ TỒN TẠI CHƯA */
    $stmtCheck->bind_param("ii", $ma_dp, $dv);
    $stmtCheck->execute();
    $resCheck = $stmtCheck->get_result();

    /* 3A. NẾU ĐÃ CÓ → CỘNG DỒN */
    if ($resCheck->num_rows > 0) {
        $row = $resCheck->fetch_assoc();
        $ma_sddv = $row["ma_sddv"];

        $stmtUpdate->bind_param("ii", $sl, $ma_sddv);
        $stmtUpdate->execute();
    }

    /* 3B. NẾU CHƯA CÓ → INSERT MỚI */
    else {
        $stmtInsert->bind_param("iiid", $ma_dp, $dv, $sl, $don_gia);
        $stmtInsert->execute();
    }
}

/* ----------------------------------------------------
   RESPONSE
---------------------------------------------------- */

echo json_encode([
    "status" => true,
    "msg" => "Thêm dịch vụ thành công!"
]);

exit;
?>
