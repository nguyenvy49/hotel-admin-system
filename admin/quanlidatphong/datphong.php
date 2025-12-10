<div class="p-8">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
                📌 Quản lý đặt phòng
            </h1>
            <p class="text-gray-500 mt-1">Theo dõi toàn bộ lịch đặt phòng của khách sạn</p>
        </div>

        <a href="quanlidatphong/add_datphong.php"
           class="px-5 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl shadow-lg 
                  text-white font-medium hover:opacity-90 transition hover:shadow-xl">
           + Thêm đặt phòng
        </a>
    </div>

    <!-- TABLE WRAPPER -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">

        <table class="min-w-full table-auto">
            
            <!-- HEAD -->
            <thead>
                <tr class="bg-gray-50 border-b">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Mã đặt</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Khách hàng</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Phòng</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Loại</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Ngày đặt</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Nhận</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Trả</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Trạng thái</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Hành động</th>
                </tr>
            </thead>

            <tbody class="text-gray-800">

                <?php
                $q = $conn->query("
                    SELECT dp.*, kh.ho, kh.ten, kh.sdt,
                           p.so_phong, lp.ten_loai_phong
                    FROM dat_phong dp
                    JOIN khach_hang kh ON dp.ma_khach_hang = kh.ma_khach_hang
                    JOIN phong p ON dp.ma_phong = p.ma_phong
                    JOIN loai_phong lp ON p.ma_loai_phong = lp.ma_loai_phong
                    ORDER BY dp.ma_dat_phong DESC
                ");

                $statusColor = [
                    "Đang ở" => "bg-green-100 text-green-700",
                    "Đã đặt" => "bg-yellow-100 text-yellow-700",
                    "Hủy"    => "bg-red-100 text-red-700"
                ];

                while ($r = $q->fetch_assoc()):
                ?>

                <tr class="border-b hover:bg-blue-50 transition">

                    <!-- MÃ ĐẶT -->
                    <td class="px-6 py-4 font-semibold text-gray-900">
                        #<?= $r['ma_dat_phong'] ?>
                    </td>

                    <!-- KHÁCH -->
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-medium"><?= $r['ho'] . " " . $r['ten'] ?></span>
                            <span class="text-gray-500 text-sm">📞 <?= $r['sdt'] ?></span>
                        </div>
                    </td>

                    <!-- PHÒNG -->
                    <td class="px-6 py-4 font-medium">
                        Phòng <?= $r['so_phong'] ?>
                    </td>

                    <!-- LOẠI -->
                    <td class="px-6 py-4 text-gray-700">
                        <?= $r['ten_loai_phong'] ?>
                    </td>

                    <!-- NGÀY -->
                    <td class="px-6 py-4 text-gray-700"><?= $r['ngay_dat'] ?></td>
                    <td class="px-6 py-4 text-gray-700"><?= $r['ngay_nhan'] ?></td>
                    <td class="px-6 py-4 text-gray-700"><?= $r['ngay_tra'] ?></td>

                    <!-- TRẠNG THÁI -->
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-xl text-sm font-medium
                            <?= $statusColor[$r['trang_thai']] ?? 'bg-gray-100 text-gray-600' ?>">
                            <?= $r['trang_thai'] ?>
                        </span>
                    </td>

                    <!-- ACTION -->
                    <td class="px-6 py-4 flex justify-center gap-4 text-sm">

                        <a href="quanlidatphong/edit_datphong.php?id=<?= $r['ma_dat_phong'] ?>"
                           class="text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1">
                            ✏️ Sửa
                        </a>

                        <a href="quanlidatphong/delete_datphong.php?id=<?= $r['ma_dat_phong'] ?>"
                           onclick="return confirm('Bạn chắc chắn muốn xoá đơn đặt phòng?')"
                           class="text-red-600 hover:text-red-800 font-medium flex items-center gap-1">
                            🗑️ Xóa
                        </a>

                    </td>
                </tr>

                <?php endwhile; ?>

            </tbody>

        </table>

    </div>

</div>
