<?php

include '../config.php';

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Hóa Đơn</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #fdfcf9;
            margin: 0;
            padding: 0;
        }
        h2 {
            text-align: center;
            margin: 20px 0;
            color: #5c4b3b;
        }
        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background: #fffaf2;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }
        th {
            background-color: #f3e9dc;
            color: #4a3f35;
        }
        tr:hover {
            background-color: #fff4e6;
        }
        .status {
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: bold;
        }
        .paid {
            background-color: #d4edda;
            color: #155724;
        }
        .unpaid {
            background-color: #f8d7da;
            color: #721c24;
        }
        .method {
            font-style: italic;
            color: #7c6a5b;
        }
    </style>
</head>
<body>

<h2>📑 Danh sách Hóa Đơn</h2>

<table>
    <thead>
        <tr>
            <th>Mã hóa đơn</th>
            <th>Khách hàng</th>
            <th>Phòng</th>
            <th>Nhân viên lập</th>
            <th>Ngày lập</th>
            <th>Tổng tiền (VNĐ)</th>
            <th>Trạng thái</th>
            <th>Phương thức</th>
            <th>Ngày thanh toán</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $query = "
            SELECT 
                hd.ma_hoa_don,
                CONCAT(kh.ho, ' ', kh.ten) AS ten_khach_hang,
                p.so_phong,
                nv.ho_ten AS nhan_vien,
                hd.ngay_lap,
                hd.tong_tien,
                hd.trang_thai,
                hd.phuong_thuc,
                hd.ngay_thanh_toan
            FROM hoa_don hd
            JOIN dat_phong dp ON hd.ma_dat_phong = dp.ma_dat_phong
            JOIN khach_hang kh ON dp.ma_khach_hang = kh.ma_khach_hang
            JOIN phong p ON dp.ma_phong = p.ma_phong
            JOIN nhan_vien nv ON hd.ma_nhan_vien = nv.ma_nhan_vien
            ORDER BY hd.ma_hoa_don DESC
        ";

        $result = mysqli_query($conn, $query);

        if (!$result) {
            echo "<tr><td colspan='9'>Lỗi truy vấn: " . mysqli_error($mysqli) . "</td></tr>";
        } elseif (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $statusClass = ($row['trang_thai'] === 'Đã thanh toán') ? 'paid' : 'unpaid';
                
                // Format tổng tiền và ngày tháng
                $tongTien = number_format($row['tong_tien'], 0, ',', '.');
                $ngayLap = date('d/m/Y', strtotime($row['ngay_lap']));
                $ngayThanhToan = $row['ngay_thanh_toan'] ? date('d/m/Y', strtotime($row['ngay_thanh_toan'])) : '-';
                
                echo "<tr>
                    <td>{$row['ma_hoa_don']}</td>
                    <td>{$row['ten_khach_hang']}</td>
                    <td>{$row['so_phong']}</td>
                    <td>{$row['nhan_vien']}</td>
                    <td>{$ngayLap}</td>
                    <td>{$tongTien}</td>
                    <td><span class='status {$statusClass}'>{$row['trang_thai']}</span></td>
                    <td class='method'>{$row['phuong_thuc']}</td>
                    <td>{$ngayThanhToan}</td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='9'>Chưa có hóa đơn nào.</td></tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>
