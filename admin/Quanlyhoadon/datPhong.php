<?php
include '../config.php';

// Truy vấn danh sách đặt phòng
$sql = "
SELECT 
    dp.ma_dat_phong,
    CONCAT(kh.ho, ' ', kh.ten) AS ho_ten,
    p.so_phong,
    dp.ngay_dat,
    dp.ngay_nhan,
    dp.ngay_tra,
    dp.so_nguoi,
    dp.trang_thai
FROM dat_phong dp
JOIN khach_hang kh ON dp.ma_khach_hang = kh.ma_khach_hang
JOIN phong p ON dp.ma_phong = p.ma_phong
ORDER BY dp.ma_dat_phong DESC
";


$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Đặt Phòng</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #fdfbf7;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            margin: 40px auto;
            background: #fffaf5;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 20px 40px;
        }
        h2 {
            text-align: center;
            color: #654321;
            margin-bottom: 25px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table thead {
            background: #f0e3d2;
            color: #4b3621;
        }
        table th, table td {
            padding: 12px;
            border: 1px solid #e5d2b8;
            text-align: center;
        }
        tr:nth-child(even) {
            background: #fff8f0;
        }
        tr:hover {
            background: #f6e9d8;
            transition: 0.3s;
        }
        .btn-add {
            display: inline-block;
            padding: 10px 16px;
            background-color: #d7b899;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 15px;
            transition: 0.3s;
        }
        .btn-add:hover {
            background-color: #b58f67;
        }
        .status {
            padding: 6px 10px;
            border-radius: 6px;
            font-weight: bold;
        }
        .Đã\ đặt { background-color: #ffefc0; color: #8a6d00; }
        .Đang\ ở { background-color: #d8ffd8; color: #006400; }
        .Đã\ trả { background-color: #d4e6ff; color: #004aad; }
        .Hủy { background-color: #ffd6d6; color: #9b0000; }
    </style>
</head>
<body>
    <div class="container">
        <h2> Danh Sách Đặt Phòng</h2>
        <a href="themDatPhong.php" class="btn-add">Thêm đặt phòng</a>

        <table>
            <thead>
                <tr>
                    <th>Mã đặt</th>
                    <th>Tên khách hàng</th>
                    <th>Số phòng</th>
                    <th>Nhân viên phụ trách</th>
                    <th>Ngày đặt</th>
                    <th>Ngày nhận</th>
                    <th>Ngày trả</th>
                    <th>Số người</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                            <td>{$row['ma_dat_phong']}</td>
                            <td>{$row['ten_khach']}</td>
                            <td>{$row['so_phong']}</td>
                            <td>" . ($row['ten_nv'] ?: '<i>Chưa phân công</i>') . "</td>
                            <td>{$row['ngay_dat']}</td>
                            <td>{$row['ngay_nhan']}</td>
                            <td>{$row['ngay_tra']}</td>
                            <td>{$row['so_nguoi']}</td>
                            <td><span class='status {$row['trang_thai']}'>{$row['trang_thai']}</span></td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='9'>Không có dữ liệu đặt phòng nào.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>


