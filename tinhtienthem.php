<?php
include 'config.php'; // include file config để lấy $conn

$ma_dat_phong = 1;
$gia_them_1h = 450000;

// 1. Lấy giờ dự kiến trả phòng và tổng tiền
$sql = "SELECT dp.ngay_tra, hd.tong_tien 
        FROM dat_phong dp
        JOIN hoa_don hd ON dp.ma_dat_phong = hd.ma_dat_phong
        WHERE dp.ma_dat_phong = ?";
$stmt = $conn->prepare($sql); // <== dùng $conn chứ không phải $mysqli
$stmt->bind_param("i", $ma_dat_phong);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

$ngay_tra = new DateTime($data['ngay_tra']);
$actual_checkout = new DateTime(); // giờ thực tế khách trả phòng

// 2. Tính số giờ ở thêm
$interval = $ngay_tra->diff($actual_checkout);
$gio_o_them = $interval->h + ($interval->days * 24);
if ($interval->i > 0) $gio_o_them += 1;

if ($gio_o_them > 0) {
    $tien_them = $gio_o_them * $gia_them_1h;

    // 3. Cập nhật hóa đơn
    $sql_update_hd = "UPDATE hoa_don 
                      SET tien_them = ?, tong_tien = tong_tien + ? 
                      WHERE ma_dat_phong = ?";
    $stmt_update = $conn->prepare($sql_update_hd); // <== dùng $conn
    $stmt_update->bind_param("dii", $tien_them, $tien_them, $ma_dat_phong);
    $stmt_update->execute();

    // 4. Cập nhật trạng thái và giờ trả thực tế
    $sql_update_dp = "UPDATE dat_phong 
                      SET trang_thai = 'Đã trả', ngay_tra_thuc = ? 
                      WHERE ma_dat_phong = ?";
    $ngay_tra_thuc = $actual_checkout->format('Y-m-d H:i:s');
    $stmt_update_dp = $conn->prepare($sql_update_dp); // <== dùng $conn
    $stmt_update_dp->bind_param("si", $ngay_tra_thuc, $ma_dat_phong);
    $stmt_update_dp->execute();

    echo "Khách ở thêm $gio_o_them giờ, tính tiền thêm: " . number_format($tien_them) . " VNĐ.";
} else {
    echo "Khách trả phòng đúng giờ, không tính phí thêm.";
}

$conn->close();
?>
