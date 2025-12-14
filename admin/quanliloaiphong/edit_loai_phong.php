<?php
include "../config.php";

$ma_loai_phong = $_POST["ma_loai_phong"];
$ten_loai_phong = $_POST["ten_loai_phong"];
$so_nguoi_toi_da = $_POST["so_nguoi_toi_da"];
$gia_phong = $_POST["gia_phong"];
$old_image = $_POST["old_image"] ?? "";   // tránh lỗi undefined
$new_image = $_FILES["hinh_anh"]["name"] ?? "";

// Đường dẫn upload
$uploadDir = __DIR__ . "/../../assets/img/";

// Nếu có file mới → upload thay thế
if (!empty($new_image)) {

    $ext = pathinfo($new_image, PATHINFO_EXTENSION);
    $new_name = time() . "_" . rand(1000, 9999) . "." . $ext;

    $uploadPath = $uploadDir . $new_name;

    if (move_uploaded_file($_FILES["hinh_anh"]["tmp_name"], $uploadPath)) {
        // xóa ảnh cũ nếu tồn tại
        if (!empty($old_image) && file_exists($uploadDir . $old_image)) {
            unlink($uploadDir . $old_image);
        }
        $hinh_anh = $new_name;
    } else {
        echo json_encode(["status" => false, "msg" => "Upload ảnh thất bại!"]);
        exit;
    }

} else {
    // Không upload → giữ ảnh cũ
    $hinh_anh = $old_image;
}

// Cập nhật DB
$stmt = $conn->prepare("
    UPDATE loai_phong 
    SET ten_loai_phong = ?, 
        so_nguoi_toi_da = ?, 
        gia_phong = ?, 
        hinh_anh = ?
    WHERE ma_loai_phong = ?
");

$stmt->bind_param("sidsi",
    $ten_loai_phong,
    $so_nguoi_toi_da,
    $gia_phong,
    $hinh_anh,
    $ma_loai_phong
);

echo json_encode(["status" => $stmt->execute()]);
