<div class="p-8">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
                👥 Quản lý nhân viên
            </h2>
            <p class="text-gray-500 mt-1">Danh sách nhân viên của khách sạn Prestige Manor</p>
        </div>

        <a href="quanlinhanvien/add_nhanvien.php"
           class="px-5 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl shadow-lg hover:shadow-xl hover:opacity-90 transition font-medium">
            + Thêm nhân viên
        </a>
    </div>

    <!-- TABLE WRAPPER -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">

        <table class="min-w-full table-beauty">
            <thead>
                <tr class="bg-gray-50 border-b">
                    <th class="px-6 py-4 text-left text-gray-600 font-semibold uppercase text-xs">Mã NV</th>
                    <th class="px-6 py-4 text-left text-gray-600 font-semibold uppercase text-xs">Họ tên</th>
                    <th class="px-6 py-4 text-left text-gray-600 font-semibold uppercase text-xs">Chức vụ</th>
                    <th class="px-6 py-4 text-center text-gray-600 font-semibold uppercase text-xs">Hành động</th>
                </tr>
            </thead>

            <tbody class="text-gray-700">

            <?php
            $q = $conn->query("
                SELECT nv.ma_nhan_vien, nv.ho_ten, cv.ten_chuc_vu
                FROM nhan_vien nv
                LEFT JOIN chuc_vu cv ON nv.ma_chuc_vu = cv.ma_chuc_vu
                ORDER BY nv.ma_nhan_vien DESC
            ");

            while ($r = $q->fetch_assoc()):
            ?>

                <tr class="border-b hover:bg-blue-50 transition">
                    <td class="px-6 py-4 font-semibold text-gray-900"><?= $r['ma_nhan_vien'] ?></td>
                    <td class="px-6 py-4"><?= $r['ho_ten'] ?></td>

                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-sm 
                              <?= $r['ten_chuc_vu'] ? 'bg-blue-100 text-blue-800' : 'bg-gray-200 text-gray-600' ?>">
                            <?= $r['ten_chuc_vu'] ?? 'Chưa phân công' ?>
                        </span>
                    </td>

                    <td class="px-6 py-4 flex items-center justify-center gap-4">

                        <a href="quanlinhanvien/edit_nhanvien.php?id=<?= $r['ma_nhan_vien'] ?>"
                           class="text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1">
                            ✏️ Sửa
                        </a>

                        <a href="quanlinhanvien/delete_nhanvien.php?id=<?= $r['ma_nhan_vien'] ?>"
                           onclick="return confirm('Bạn có chắc muốn xoá nhân viên này?')"
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
