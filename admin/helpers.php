<?php

function room_badge($status) {
    $colors = [
        "Trống" => "bg-green-100 text-green-700",
        "Đã đặt" => "bg-yellow-100 text-yellow-700",
        "Bảo trì" => "bg-red-100 text-red-700",
        "Đang dọn dẹp" => "bg-gray-100 text-gray-700"
    ];
    $c = $colors[$status] ?? "bg-gray-200 text-gray-600";
    
    return "<span class='px-3 py-1 rounded-full text-sm $c'>$status</span>";
}

function get_stats($conn) {
    $out = [];

    $q = $conn->query("SELECT COUNT(*) AS total FROM khach_hang");
    $r = $q ? $q->fetch_assoc() : null;
    $out['kh'] = $r ? intval($r['total']) : 0;

    $out['phong_trang_thai'] = ['Trống'=>0,'Đã đặt'=>0,'Đang dọn dẹp'=>0,'Bảo trì'=>0];

    $pt = $conn->query("SELECT trang_thai, COUNT(*) AS cnt FROM phong GROUP BY trang_thai");
    if ($pt) while ($row = $pt->fetch_assoc()) {
        $out['phong_trang_thai'][$row['trang_thai']] = intval($row['cnt']);
    }

    $out['phong_trong'] = $out['phong_trang_thai']['Trống'];
    $out['phong_dang_dat'] = $out['phong_trang_thai']['Đã đặt'];
    $out['phong_bao_tri'] = $out['phong_trang_thai']['Bảo trì'];

    $q = $conn->query("
        SELECT SUM(tong_tien) AS total FROM hoa_don
        WHERE trang_thai='Đã thanh toán'
        AND MONTH(ngay_thanh_toan)=MONTH(CURDATE())
        AND YEAR(ngay_thanh_toan)=YEAR(CURDATE())
    ");
    $r = $q ? $q->fetch_assoc() : null;
    $out['dt_thang'] = $r && $r['total'] ? floatval($r['total']) : 0;

    $q = $conn->query("SELECT SUM(so_luong) AS total FROM phieu_su_dung_dich_vu");
    $r = $q ? $q->fetch_assoc() : null;
    $out['dv'] = $r && $r['total'] ? intval($r['total']) : 0;

    $rs = $conn->query("
        SELECT DATE_FORMAT(ngay_thanh_toan,'%m') AS thang, SUM(tong_tien) AS total
        FROM hoa_don
        WHERE trang_thai='Đã thanh toán'
        AND ngay_thanh_toan >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        GROUP BY thang
        ORDER BY thang ASC
    ");

    $out['doanh_thu'] = [];
    if ($rs) while ($row = $rs->fetch_assoc()) {
        $out['doanh_thu'][] = [
            "thang" => $row['thang'],
            "total" => floatval($row['total'])
        ];
    }

    return $out;
}
