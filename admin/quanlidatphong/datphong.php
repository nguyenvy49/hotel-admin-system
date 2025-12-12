<?php

/* INPUT FILTER */
$keyword = $_GET['keyword'] ?? '';
$status  = $_GET['status']  ?? '';
$from    = $_GET['from']    ?? '';
$to      = $_GET['to']      ?? '';

/* BUILD WHERE */
$where = " WHERE 1=1 ";

if ($keyword !== "") {
    $kw = $conn->real_escape_string($keyword);
    $where .= " AND (
        kh.ho LIKE '%$kw%' OR 
        kh.ten LIKE '%$kw%' OR 
        kh.sdt LIKE '%$kw%' OR 
        GROUP_CONCAT(DISTINCT p.so_phong) LIKE '%$kw%'
    )";
}

if ($status !== "")     $where .= " AND dp.trang_thai = '$status' ";
if ($from !== "")       $where .= " AND dp.ngay_nhan >= '$from' ";
if ($to !== "")         $where .= " AND dp.ngay_tra  <= '$to' ";

/* QUERY — GỘP PHÒNG THEO BOOKING */
$q = $conn->query("
    SELECT 
        dp.ma_dat_phong,
        dp.ngay_nhan,
        dp.ngay_tra,
        dp.trang_thai,
        kh.ho, kh.ten, kh.sdt,

        GROUP_CONCAT(DISTINCT p.so_phong ORDER BY p.so_phong SEPARATOR ', ') AS ds_phong,
        COUNT(DISTINCT p.ma_phong) AS so_phong

    FROM dat_phong dp
    JOIN khach_hang kh ON dp.ma_khach_hang = kh.ma_khach_hang
    JOIN chi_tiet_dat_phong ctdp ON ctdp.ma_dat_phong = dp.ma_dat_phong
    JOIN phong p ON ctdp.ma_phong = p.ma_phong
    $where
    GROUP BY dp.ma_dat_phong
    ORDER BY dp.ma_dat_phong DESC
");

$statusColor = [
    "Đã đặt"          => "bg-yellow-100 text-yellow-700",
    "Chờ nhận phòng"  => "bg-purple-100 text-purple-700",
    "Đang ở"          => "bg-blue-100 text-blue-700",
    "Đã trả"          => "bg-green-100 text-green-700",
    "Hủy"             => "bg-red-100 text-red-700"
];
?>

<div class="p-8">

    <h1 class="text-4xl font-extrabold text-gray-900 mb-6">📃 Danh sách đặt phòng</h1>

    <!-- FILTER -->
    <div class="bg-white p-6 rounded-2xl shadow mb-8 grid grid-cols-1 md:grid-cols-5 gap-4">

        <div class="col-span-2">
            <label class="font-semibold">Tìm kiếm</label>
            <input id="searchInput" value="<?= $keyword ?>"
                   class="input mt-1" placeholder="Tên, SĐT, số phòng...">
        </div>

        <div>
            <label class="font-semibold">Trạng thái</label>
            <select id="filterStatus" class="input mt-1">
                <option value="">Tất cả</option>
                <?php foreach ($statusColor as $st => $_): ?>
                    <option value="<?= $st ?>" <?= $status==$st?"selected":"" ?>><?= $st ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="font-semibold">Nhận từ</label>
            <input type="date" id="filterFrom" class="input mt-1" value="<?= $from ?>">
        </div>

        <div>
            <label class="font-semibold">Trả đến</label>
            <input type="date" id="filterTo" class="input mt-1" value="<?= $to ?>">
        </div>
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        <table class="min-w-full table-auto">

            <thead class="bg-gray-50 border-b">
            <tr>
                <th class="th">Mã</th>
                <th class="th">Khách</th>
                <th class="th">Phòng</th>
                <th class="th">Nhận</th>
                <th class="th">Trả</th>
                <th class="th">Trạng thái</th>
                <th class="th text-center">Hành động</th>
            </tr>
            </thead>

            <tbody class="text-gray-800">

            <?php while ($r = $q->fetch_assoc()): ?>

                <tr class="border-b hover:bg-indigo-50 transition">

                    <td class="td font-semibold">#<?= $r['ma_dat_phong'] ?></td>

                    <td class="td">
                        <div class="font-semibold"><?= $r['ho']." ".$r['ten'] ?></div>
                        <div class="text-gray-500 text-sm">📞 <?= $r['sdt'] ?></div>
                    </td>

                    <td class="td font-medium">
                        <?= $r['ds_phong'] ?>
                        <br><span class="text-gray-500 text-sm">(<?= $r['so_phong'] ?> phòng)</span>
                    </td>

                    <td class="td"><?= $r['ngay_nhan'] ?></td>
                    <td class="td"><?= $r['ngay_tra'] ?></td>

                    <td class="td">
                        <span class="badge <?= $statusColor[$r['trang_thai']] ?? '' ?>">
                            <?= $r['trang_thai'] ?>
                        </span>
                    </td>

                    <td class="td text-center flex justify-center gap-3">

                        <!-- XEM -->
                        <button onclick="viewDetail(<?= $r['ma_dat_phong'] ?>)"
                                class="btn-view">🔍</button>

                        <!-- ĐÃ ĐẶT → CHỜ NHẬN -->
                        <?php if ($r['trang_thai'] == 'Đã đặt'): ?>
                            <button onclick="confirmBooking(<?= $r['ma_dat_phong'] ?>)"
                                    class="btn-yellow">✔ Xác nhận</button>
                            <button onclick="cancelBooking(<?= $r['ma_dat_phong'] ?>)"
                                    class="btn-red">✖ Hủy</button>
                        <?php endif; ?>

                        <!-- CHỜ NHẬN → CHECK-IN -->
                        <?php if ($r['trang_thai'] == 'Chờ nhận phòng'): ?>
                            <button onclick="checkin(<?= $r['ma_dat_phong'] ?>)"
                                    class="btn-blue">🚪 Check-in</button>
                        <?php endif; ?>

                        <!-- ĐANG Ở → THÊM DV + CHECKOUT -->
                        <?php if ($r['trang_thai'] == 'Đang ở'): ?>
                            <button onclick="openAddService(<?= $r['ma_dat_phong'] ?>)"
                                    class="btn-purple">➕ Dịch vụ</button>
                            <button onclick="checkoutRoom(<?= $r['ma_dat_phong'] ?>)"
                                    class="btn-green">💳 Checkout</button>
                        <?php endif; ?>

                    </td>

                </tr>

            <?php endwhile; ?>

            </tbody>
        </table>
    </div>
</div>

<!-- =========================================
    MODAL THÊM DỊCH VỤ (CSS + HTML HOÀN CHỈNH)
========================================= -->
<div id="modalAddService"
     class="fixed inset-0 bg-black/40 hidden z-[9999] justify-center items-center">

    <div class="bg-white w-[600px] p-8 rounded-3xl shadow-xl max-h-[85vh] overflow-auto animate-fadeIn">

        <h2 class="text-2xl font-bold mb-4">➕ Thêm dịch vụ</h2>

        <form id="serviceForm">

            <input type="hidden" name="ma_dat_phong" id="ma_dat_phong">

            <div id="serviceList">
                <div class="flex gap-3 mb-3 service-row">

                    <select class="input w-2/3" name="ma_dich_vu[]">
                        <?php
                        $dv = $conn->query("SELECT ma_dich_vu, ten_dich_vu, don_gia FROM dich_vu ORDER BY ten_dich_vu ASC");
                        while ($d = $dv->fetch_assoc()):
                        ?>
                        <option value="<?= $d['ma_dich_vu'] ?>">
                            <?= $d['ten_dich_vu'] ?> — <?= number_format($d['don_gia']) ?>đ
                        </option>
                        <?php endwhile; ?>
                    </select>

                    <input type="number" min="1" value="1" name="so_luong[]" class="input w-1/3">
                </div>
            </div>

            <button type="button" onclick="addServiceRow()"
                    class="px-3 py-2 bg-indigo-600 text-white rounded-lg mb-4">+ Thêm dòng</button>

            <div class="flex justify-end gap-4">
                <button type="button" onclick="closeServiceModal()"
                        class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">Hủy</button>

                <button type="button" onclick="submitAddService()"
                        class="px-4 py-2 bg-green-600 rounded-lg text-white">Lưu</button>
            </div>

        </form>
    </div>
</div>


<style>
.input { width: 100%; padding: 10px 14px; border-radius: 12px; border: 2px solid #e5e7eb; }
.th { padding: 12px 16px; font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; }
.td { padding: 14px 16px; }
.badge { padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; }

.btn-view{background:#6366f1;color:white;padding:6px 10px;border-radius:10px}
.btn-yellow{background:#fbbf24;color:white;padding:6px 12px;border-radius:10px}
.btn-red{background:#ef4444;color:white;padding:6px 12px;border-radius:10px}
.btn-blue{background:#3b82f6;color:white;padding:6px 12px;border-radius:10px}
.btn-purple{background:#7c3aed;color:white;padding:6px 12px;border-radius:10px}
.btn-green{background:#10b981;color:white;padding:6px 12px;border-radius:10px}

@keyframes fadeIn {
    from { opacity: 0; transform: scale(.95); }
    to { opacity: 1; transform: scale(1); }
}
.animate-fadeIn { animation: fadeIn .2s ease-out; }
</style>
