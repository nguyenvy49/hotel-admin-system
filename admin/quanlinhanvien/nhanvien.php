<div class="p-8">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
                👥 Quản lý nhân viên
            </h2>
            <p class="text-gray-500 mt-1">Danh sách nhân viên của khách sạn Prestige Manor</p>
        </div>

        <button onclick="openAddStaffModal()"
        class="px-5 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl shadow-lg hover:shadow-xl transition font-medium">
    + Thêm nhân viên
</button>

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

                        <a href="javascript:void(0)"
                    onclick="openEditStaffModal(<?= $r['ma_nhan_vien'] ?>)"
                    class="text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1">
                    ✏️ Sửa
                    </a>


                    <a href="javascript:void(0)"
                    onclick="openDeleteStaffModal(<?= $r['ma_nhan_vien'] ?>)"
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

<!-- ADD STAFF MODAL -->
<div id="addStaffModal"
     class="fixed inset-0 bg-black/40 backdrop-blur-sm flex justify-center items-center 
            z-50 hidden">

    <div class="modal-box p-7 w-[460px]">

        <form id="addStaffForm">

            <h2 class="text-2xl font-bold mb-5 text-gray-800 flex items-center gap-2">
                ➕ Thêm nhân viên
            </h2>

            <div class="space-y-4">

                <div>
                    <label class="font-medium">Họ tên</label>
                    <input name="ho_ten" class="input" required>
                </div>

                <div>
                    <label class="font-medium">Giới tính</label>
                    <select name="gioi_tinh" class="input">
                        <option value="Nam">Nam</option>
                        <option value="Nữ">Nữ</option>
                    </select>
                </div>

                <div>
                    <label class="font-medium">Ngày sinh</label>
                    <input type="date" name="ngay_sinh" class="input">
                </div>

                <div>
                    <label class="font-medium">SĐT</label>
                    <input name="sdt" class="input">
                </div>

                <div>
                    <label class="font-medium">Email</label>
                    <input type="email" name="email" class="input">
                </div>

                <div>
                    <label class="font-medium">Chức vụ</label>
                    <select name="ma_chuc_vu" class="input">
                        <?php
                        $roles = $conn->query("SELECT * FROM chuc_vu");
                        while ($cv = $roles->fetch_assoc()):
                        ?>
                            <option value="<?= $cv['ma_chuc_vu'] ?>"><?= $cv['ten_chuc_vu'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

            </div>

            <div class="flex justify-end gap-3 mt-7">
                <button type="button" onclick="closeAddStaffModal()" class="btn-gray">Hủy</button>
                <button type="submit" class="btn-blue">Thêm</button>
            </div>

            <p id="addStaffMsg" class="text-red-600 mt-3"></p>

        </form>

    </div>
</div>


<!-- EDIT STAFF MODAL -->
<div id="editStaffModal"
     class="fixed inset-0 bg-black/40 backdrop-blur-sm flex justify-center items-center 
            z-50 hidden">

    <div class="modal-box p-7 w-[460px]">

        <form id="editStaffForm">

            <h2 class="text-2xl font-bold mb-5 text-gray-800 flex items-center gap-2">
                ✏️ Sửa nhân viên
            </h2>

            <input type="hidden" id="edit_id" name="ma_nhan_vien">

            <div class="space-y-4">

                <div>
                    <label class="font-medium">Họ tên</label>
                    <input id="edit_ho_ten" name="ho_ten" class="input" required>
                </div>

                <div>
                    <label class="font-medium">Giới tính</label>
                    <select id="edit_gioi_tinh" name="gioi_tinh" class="input">
                        <option>Nam</option>
                        <option>Nữ</option>
                        <option>Khác</option>
                    </select>
                </div>

                <div>
                    <label class="font-medium">Ngày sinh</label>
                    <input id="edit_ngay_sinh" type="date" name="ngay_sinh" class="input">
                </div>

                <div>
                    <label class="font-medium">SĐT</label>
                    <input id="edit_sdt" name="sdt" class="input">
                </div>

                <div>
                    <label class="font-medium">Email</label>
                    <input id="edit_email" type="email" name="email" class="input">
                </div>

                <div>
                    <label class="font-medium">Chức vụ</label>
                    <select id="edit_chuc_vu" name="ma_chuc_vu" class="input">
                        <?php
                        $roles = $conn->query("SELECT * FROM chuc_vu");
                        while ($cv = $roles->fetch_assoc()):
                        ?>
                            <option value="<?= $cv['ma_chuc_vu'] ?>"><?= $cv['ten_chuc_vu'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

            </div>

            <div class="flex justify-end gap-3 mt-7">
                <button type="button" onclick="closeEditStaffModal()" class="btn-gray">Hủy</button>
                <button type="submit" class="btn-green">Lưu</button>
            </div>

            <p id="editStaffMsg" class="text-red-600 mt-3"></p>

        </form>

    </div>
</div>


<!-- DELETE STAFF MODAL -->
<div id="deleteStaffModal"
     class="fixed inset-0 bg-black/40 backdrop-blur-sm flex justify-center items-center 
            z-50 hidden">

    <div class="modal-box p-7 w-[380px] text-center">

        <h3 class="text-2xl font-bold text-red-600 mb-4">
            ⚠ Xác nhận xoá
        </h3>

        <p class="text-gray-700 mb-6 leading-relaxed">
            Bạn chắc chắn muốn xóa nhân viên này?<br>
            <span class="font-medium text-gray-900">Hành động này không thể hoàn tác.</span>
        </p>

        <input type="hidden" id="delete_staff_id">

        <div class="flex justify-center gap-3">
            <button onclick="closeDeleteStaffModal()" class="btn-gray">Hủy</button>
            <button onclick="submitDeleteStaff()" class="btn-red">Xóa</button>
        </div>

        <p id="deleteStaffMsg" class="text-red-600 mt-3"></p>

    </div>
</div>



<style>
/* Modal animation */
@keyframes modalShow {
    from {
        opacity: 0;
        transform: translateY(20px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* Modal container */
.modal-box {
    animation: modalShow .25s ease-out;
    border-radius: 20px;
    background: white;
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
}

/* Input đẹp */
.input {
    width: 100%;
    padding: 10px 12px;
    border-radius: 10px;
    border: 1px solid #d1d5db;
    transition: 0.2s;
    background: #fafafa;
}

.input:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.25);
    background: white;
}

/* Nút đẹp */
.btn-blue {
    background: linear-gradient(to right, #4f46e5, #6366f1);
    color: white;
    padding: 10px 18px;
    border-radius: 10px;
    font-weight: 600;
    transition: .2s;
}
.btn-blue:hover { opacity: .85; }

.btn-green {
    background: linear-gradient(to right, #059669, #10b981);
    color: white;
    padding: 10px 18px;
    border-radius: 10px;
    font-weight: 600;
    transition: .2s;
}
.btn-green:hover { opacity: .85; }

.btn-red {
    background: linear-gradient(to right, #dc2626, #ef4444);
    color: white;
    padding: 10px 18px;
    border-radius: 10px;
    font-weight: 600;
    transition: .2s;
}
.btn-red:hover { opacity: .85; }

.btn-gray {
    background: #e5e7eb;
    padding: 10px 18px;
    border-radius: 10px;
    font-weight: 500;
    transition: .2s;
}
.btn-gray:hover {
    background: #d1d5db;
}
</style>

