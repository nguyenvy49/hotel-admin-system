<div class="p-6">

    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-semibold">Quản lý hóa đơn</h2>
        <a href="quanlihoadon/add_hoadon.php"
           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            + Thêm hóa đơn
        </a>
    </div>

    <div class="bg-white rounded-lg shadow table-beauty overflow-auto">
        <table class="min-w-full">
            <thead>
                <tr>
                    <th class="px-6 py-3">Mã HĐ</th>
                    <th class="px-6 py-3">Mã đặt phòng</th>
                    <th class="px-6 py-3">Tổng tiền</th>
                    <th class="px-6 py-3">Trạng thái</th>
                    <th class="px-6 py-3">Ngày thanh toán</th>
                    <th class="px-6 py-3">Hành động</th>
                </tr>
            </thead>

            <tbody>
            <?php
            $q = $conn->query("
                SELECT * FROM hoa_don ORDER BY ma_hoa_don DESC
            ");
            while ($r = $q->fetch_assoc()):
            ?>
                <tr>
                    <td class="px-6 py-3"><?= $r['ma_hoa_don'] ?></td>
                    <td class="px-6 py-3"><?= $r['ma_dat_phong'] ?></td>
                    <td class="px-6 py-3"><?= number_format($r['tong_tien']) ?> đ</td>

                    <td class="px-6 py-3">
                        <span class="px-3 py-1 rounded-lg text-sm
                            <?= $r['trang_thai'] === 'Đã thanh toán' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' ?>">
                            <?= $r['trang_thai'] ?>
                        </span>
                    </td>

                    <td class="px-6 py-3"><?= $r['ngay_thanh_toan'] ?></td>

                    <td class="px-6 py-3 flex gap-3">
                        <a href="quanlihoadon/edit_hoadon.php?id=<?= $r['ma_hoa_don'] ?>"
                           class="text-blue-600 hover:underline">Sửa</a>

                        <a href="quanlihoadon/delete_hoadon.php?id=<?= $r['ma_hoa_don'] ?>"
                           onclick="return confirm('Xóa hóa đơn?')"
                           class="text-red-600 hover:underline">Xóa</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>
