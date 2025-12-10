<div class="p-8">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">
        
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
                🏨 Quản lý phòng
            </h1>
            <p class="text-gray-500 mt-1">Danh sách toàn bộ phòng trong hệ thống khách sạn Prestige Manor</p>
        </div>

        <a href="quanliphong/add_phong.php"
           class="px-5 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl shadow-lg 
                  text-white font-medium hover:opacity-90 transition hover:shadow-xl">
            + Thêm phòng
        </a>

    </div>

    <!-- TABLE CONTAINER -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">

        <table class="min-w-full table-auto">
            <thead>
                <tr class="bg-gray-50 border-b">
                    <th class="px-6 py-4 text-left text-gray-600 uppercase text-xs font-semibold">Phòng</th>
                    <th class="px-6 py-4 text-left text-gray-600 uppercase text-xs font-semibold">Loại</th>
                    <th class="px-6 py-4 text-left text-gray-600 uppercase text-xs font-semibold">Tối đa</th>
                    <th class="px-6 py-4 text-left text-gray-600 uppercase text-xs font-semibold">Giá</th>
                    <th class="px-6 py-4 text-left text-gray-600 uppercase text-xs font-semibold">Trạng thái</th>
                    <th class="px-6 py-4 text-center text-gray-600 uppercase text-xs font-semibold">Hành động</th>
                </tr>
            </thead>

            <tbody class="text-gray-800">

            <?php
            $q = $conn->query("
                SELECT p.ma_phong, p.so_phong, p.trang_thai,
                       lp.ten_loai_phong, lp.so_nguoi_toi_da, lp.gia_phong
                FROM phong p
                JOIN loai_phong lp ON p.ma_loai_phong = lp.ma_loai_phong
                ORDER BY p.so_phong ASC
            ");

            while ($r = $q->fetch_assoc()):
            ?>

                <tr class="border-b hover:bg-blue-50 transition">
                    <td class="px-6 py-4 font-semibold text-gray-900">Phòng <?= $r['so_phong'] ?></td>
                    <td class="px-6 py-4"><?= $r['ten_loai_phong'] ?></td>
                    <td class="px-6 py-4"><?= $r['so_nguoi_toi_da'] ?> người</td>

                    <td class="px-6 py-4 text-blue-700 font-semibold">
                        <?= number_format($r['gia_phong'], 0, ',', '.') ?> đ
                    </td>

                    <td class="px-6 py-4">
                        <?= room_badge($r['trang_thai']) ?>
                    </td>

                    <td class="px-6 py-4 text-center flex justify-center gap-4">

                        <!-- BUTTON SỬA -->
                        <a href="quanliphong/edit_phong.php?id=<?= $r['ma_phong'] ?>"
                           class="text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1">
                            ✏️ Sửa
                        </a>

                        <!-- BUTTON XÓA -->
                        <a href="quanliphong/delete_phong.php?id=<?= $r['ma_phong'] ?>"
                           onclick="return confirm('Xác nhận xoá phòng này?')"
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
