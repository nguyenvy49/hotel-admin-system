<div class="p-8">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">
        
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
                🏨 Quản lý phòng
            </h1>
            <p class="text-gray-500 mt-1">Danh sách toàn bộ phòng trong hệ thống khách sạn Prestige Manor</p>
        </div>

        <button onclick="openAddRoomModal()"
                class="px-5 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl shadow-lg 
                    text-white font-medium hover:opacity-90 transition hover:shadow-xl">
            + Thêm phòng
        </button>


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
                        <a href="javascript:void(0)"
                        onclick="openEditRoomModal(<?= $r['ma_phong'] ?>)"
                        class="text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1">
                        ✏️ Sửa
                        </a>


                        <!-- BUTTON XÓA -->
                        <a href="javascript:void(0)"
                        onclick="openDeleteRoomModal(<?= $r['ma_phong'] ?>)"
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

<!-- ADD ROOM MODAL -->
<div id="addRoomModal"
     class="fixed inset-0 bg-black/50 backdrop-blur-sm flex justify-center items-center z-50 hidden">

    <div class="modal-box p-8 w-[480px]">

        <h2 class="text-2xl font-bold mb-6 bg-gradient-to-r from-blue-600 to-indigo-500 text-white 
                   px-5 py-3 rounded-xl shadow flex items-center gap-2">
            ➕ Thêm phòng mới
        </h2>

        <form id="addRoomForm" class="space-y-4">

            <div>
                <label class="font-medium text-gray-700">Số phòng</label>
                <input name="so_phong" class="input" required>
            </div>

            <div>
                <label class="font-medium text-gray-700">Loại phòng</label>
                <select name="ma_loai_phong" class="input">
                    <?php
                    $rs = $conn->query("SELECT * FROM loai_phong ORDER BY ten_loai_phong");
                    while ($lp = $rs->fetch_assoc()):
                    ?>
                        <option value="<?= $lp['ma_loai_phong'] ?>">
                            <?= $lp['ten_loai_phong'] ?> 
                            (<?= $lp['so_nguoi_toi_da'] ?> người – <?= number_format($lp['gia_phong']) ?>đ)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div>
                <label class="font-medium text-gray-700">Trạng thái</label>
                <select name="trang_thai" id="add_trang_thai" class="input"></select>

            </div>

            <div class="flex justify-end gap-3 pt-3">
                <button type="button" onclick="closeAddRoomModal()" class="btn-gray">Hủy</button>
                <button type="submit" class="btn-blue">Thêm phòng</button>
            </div>

            <p id="addRoomMsg" class="text-red-600 pt-2"></p>

        </form>
    </div>

</div>



<!-- EDIT ROOM MODAL -->
<div id="editRoomModal"
     class="fixed inset-0 bg-black/50 backdrop-blur-sm flex justify-center items-center z-50 hidden">

    <div class="modal-box p-8 w-[480px]">

        <h2 class="text-2xl font-bold mb-6 bg-gradient-to-r from-emerald-600 to-green-500 text-white 
                   px-5 py-3 rounded-xl shadow flex items-center gap-2">
            ✏️ Chỉnh sửa phòng
        </h2>

        <form id="editRoomForm" class="space-y-4">

            <input type="hidden" name="ma_phong" id="edit_id">

            <div>
                <label class="font-medium">Số phòng</label>
                <input id="edit_so_phong" name="so_phong" class="input" required>
            </div>

            <div>
                <label class="font-medium">Loại phòng</label>
                <select id="edit_ma_loai_phong" name="ma_loai_phong" class="input">
                    <?php
                    $rs = $conn->query("SELECT * FROM loai_phong");
                    while ($lp = $rs->fetch_assoc()):
                    ?>
                        <option value="<?= $lp['ma_loai_phong'] ?>">
                            <?= $lp['ten_loai_phong'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div>
                <label class="font-medium">Trạng thái</label>
                <select id="edit_trang_thai" name="trang_thai" class="input"></select>
            </div>

            <div class="flex justify-end gap-3 pt-3">
                <button type="button" onclick="closeEditRoomModal()" class="btn-gray">Hủy</button>
                <button type="submit" class="btn-green">Lưu thay đổi</button>
            </div>

            <p id="editRoomMsg" class="text-red-600 pt-2"></p>

        </form>

    </div>

</div>



<!-- DELETE ROOM MODAL -->
<div id="deleteRoomModal"
     class="fixed inset-0 bg-black/50 backdrop-blur-sm flex justify-center items-center z-50 hidden">

    <div class="modal-box p-8 w-[380px]">

        <h3 class="text-2xl font-bold text-center text-red-600 mb-4">
            ⚠ Xóa phòng
        </h3>

        <p class="text-gray-700 text-center mb-6 leading-relaxed">
            Bạn có chắc chắn muốn xóa phòng này?<br>
            <span class="font-semibold">Hành động này không thể hoàn tác.</span>
        </p>

        <input type="hidden" id="delete_room_id">

        <div class="flex justify-center gap-3">
            <button onclick="closeDeleteRoomModal()" class="btn-gray">Hủy</button>
            <button onclick="submitDeleteRoom()" class="btn-red">Xóa</button>
        </div>

        <p id="deleteRoomMsg" class="text-red-600 text-center pt-2"></p>

    </div>

</div>


<style>
.modal-box {
    background: #ffffff;
    border-radius: 22px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.18);
    animation: modalFadeIn .28s ease;
}

@keyframes modalFadeIn {
    from { opacity: 0; transform: scale(0.95) translateY(10px); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
}

.input {
    width: 100%;
    padding: 10px 14px;
    border-radius: 12px;
    border: 1px solid #d1d5db;
    background: #f9fafb;
    transition: .2s;
}
.input:focus {
    border-color: #6366f1;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
}

.btn-gray {
    padding: 9px 18px;
    border-radius: 10px;
    background: #e5e7eb;
    transition: .2s;
}
.btn-gray:hover { background:#d1d5db; }

.btn-blue {
    padding: 10px 20px;
    border-radius: 10px;
    background: linear-gradient(to right, #4f46e5, #6366f1);
    color:white;
    font-weight:600;
    transition:.2s;
}
.btn-blue:hover { opacity: .9; }

.btn-green {
    padding: 10px 20px;
    border-radius: 10px;
    background: linear-gradient(to right, #10b981, #34d399);
    color:white;
    font-weight:600;
}
.btn-red {
    padding: 10px 20px;
    border-radius: 10px;
    background: linear-gradient(to right, #ef4444, #dc2626);
    color:white;
    font-weight:600;
}
.btn-red:hover { opacity:.9; }
</style>
