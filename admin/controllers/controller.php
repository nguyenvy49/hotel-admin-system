<?php
include_once __DIR__ . "/../helpers.php"; // để dùng get_stats()

function load_view($page, $conn)
{
    $base = __DIR__ . "/../";

    switch ($page) {

        case "phong":
            include $base . "quanliphong/phong.php";
            break;

        case "customers":
            include $base . "quanlikhachhang/customers.php";
            break;

        case "nhanvien":
            include $base . "quanlinhanvien/nhanvien.php";
            break;

        case "datphong":
            include $base . "quanlidatphong/datphong.php";
            break;

        case "dichvu":
            include $base . "quanlidichvu/dichvu.php";
            break;

        case "hoadon":
            include $base . "quanlihoadon/hoadon.php";
            break;

        case "xuli":
            include $base . "quanlixuli/xuli.php";
            break;

        case "thongke":
             $stats = get_stats($conn);
            include $base . "dashbroad/thongke.php";
            break;

        default:
            $stats = get_stats($conn);
            include $base . "dashbroad/home.php";
            break;
    }
}
