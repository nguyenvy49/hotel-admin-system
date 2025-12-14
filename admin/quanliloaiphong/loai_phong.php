
<div class="p-8">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
                 Quản lý loại phòng
            </h1>
            <p class="text-gray-500 mt-1">Danh sách toàn bộ loại phòng trong hệ thống khách sạn Prestige Manor</p>
        </div>

        <button onclick="openAddLoaiPhongModal()"
                class="px-5 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl shadow-lg 
                    text-white font-medium hover:opacity-90 transition hover:shadow-xl">
            + Thêm loại phòng
        </button>
    </div>

    <!-- TABLE CONTAINER -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">

        <table class="min-w-full table-auto">
            <thead>
                <tr class="bg-gray-50 border-b">
                    <th class="px-6 py-4 text-left text-gray-600 uppercase text-xs font-semibold">Ảnh</th>
                    <th class="px-6 py-4 text-left text-gray-600 uppercase text-xs font-semibold">Tên loại</th>
                    <th class="px-6 py-4 text-left text-gray-600 uppercase text-xs font-semibold">Số người</th>
                    <th class="px-6 py-4 text-left text-gray-600 uppercase text-xs font-semibold">Giá</th>
                    <th class="px-6 py-4 text-center text-gray-600 uppercase text-xs font-semibold">Hành động</th>
                </tr>
            </thead>

            <tbody class="text-gray-800">

            <?php
            $q = $conn->query("SELECT * FROM loai_phong ORDER BY ma_loai_phong DESC");
            while ($r = $q->fetch_assoc()):
            ?>
                <tr class="border-b hover:bg-blue-50 transition">
                    <td class="px-6 py-4">
                    <img
    src="/hotel-admin-system/assets/img/<?= htmlspecialchars($r['hinh_anh']) ?>"
    alt="<?= htmlspecialchars($r['ten_loai_phong']) ?>"
    style="width:100px; height:auto; border-radius:8px;"
    onerror="this.onerror=null; this.src='/hotel-admin-system/assets/img/default.jpg';">

             </td>

                    <td class="px-6 py-4 font-semibold"><?= $r['ten_loai_phong'] ?></td>
                    <td class="px-6 py-4"><?= $r['so_nguoi_toi_da'] ?> người</td>
                    <td class="px-6 py-4 text-blue-700 font-semibold"><?= number_format($r['gia_phong'],0,',','.') ?> đ</td>
                    <td class="px-6 py-4 text-center flex justify-center gap-4">

                        <!-- BUTTON SỬA -->
                        <a href="javascript:void(0)"
                           onclick="openEditLoaiPhongModal(<?= htmlspecialchars(json_encode($r)) ?>)"
                           class="text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1">
                            Sửa
                        </a>

                        <!-- BUTTON XÓA -->
                        <a href="javascript:void(0)"
                           onclick="openDeleteLoaiPhongModal(<?= $r['ma_loai_phong'] ?>)"
                           class="text-red-600 hover:text-red-800 font-medium flex items-center gap-1">
                            Xóa
                        </a>

                    </td>
                </tr>
            <?php endwhile; ?>

            </tbody>
        </table>

    </div>
</div>

<!-- ADD LOAI PHONG MODAL -->
<div id="addLoaiPhongModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex justify-center items-center z-50 hidden">
    <div class="modal-box p-8 w-[480px]">
        <h2 class="text-2xl font-bold mb-6 bg-gradient-to-r from-blue-600 to-indigo-500 text-white 
                   px-5 py-3 rounded-xl shadow flex items-center gap-2">
            + Thêm loại phòng
        </h2>
        <form id="addLoaiPhongForm" class="space-y-4" enctype="multipart/form-data">
            <div>
                <label class="font-medium text-gray-700">Tên loại phòng</label>
                <input name="ten_loai_phong" class="input" required>
            </div>
            <div>
                <label class="font-medium text-gray-700">Số người tối đa</label>
                <input name="so_nguoi_toi_da" type="number" class="input" required>
            </div>
            <div>
                <label class="font-medium text-gray-700">Giá phòng</label>
                <input name="gia_phong" type="number" class="input" required>
            </div>
            <div>
                <label class="font-medium text-gray-700">Hình ảnh</label>
                <input type="file" name="hinh_anh" class="input" required>
            </div>

            <div class="flex justify-end gap-3 pt-3">
                <button type="button" onclick="closeAddLoaiPhongModal()" class="btn-gray">Hủy</button>
                <button type="submit" class="btn-blue">Thêm loại phòng</button>
            </div>

            <p id="addLoaiPhongMsg" class="text-red-600 pt-2"></p>
        </form>
    </div>
</div>

<!-- EDIT LOAI PHONG MODAL -->
<div id="editLoaiPhongModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex justify-center items-center z-50 hidden">
    <div class="modal-box p-8 w-[480px]">
        <h2 class="text-2xl font-bold mb-6 bg-gradient-to-r from-emerald-600 to-green-500 text-white 
                   px-5 py-3 rounded-xl shadow flex items-center gap-2">
             Chỉnh sửa loại phòng
        </h2>
        <form id="editLoaiPhongForm" class="space-y-4" enctype="multipart/form-data">
            <input type="hidden" name="ma_loai_phong" id="edit_id">
            <div>
                <label class="font-medium">Tên loại phòng</label>
                <input name="ten_loai_phong" id="edit_ten" class="input" required>
            </div>
            <div>
                <label class="font-medium">Số người tối đa</label>
                <input name="so_nguoi_toi_da" id="edit_so" type="number" class="input" required>
            </div>
            <div>
                <label class="font-medium">Giá phòng</label>
                <input name="gia_phong" id="edit_gia" type="number" class="input" required>
            </div>
           <div>
                <label class="font-medium">Hình ảnh hiện tại</label><br>
                <img id="preview_hinh_anh"
                    src=""
                    style="width:120px; border-radius:6px; margin-bottom:10px">
                    <input type="hidden" name="old_image" id="edit_old_image">


                <label class="font-medium">Hình ảnh mới (nếu muốn thay)</label>
                <input type="file" name="hinh_anh" id="edit_hinh_anh" class="input">
            </div>



            <div class="flex justify-end gap-3 pt-3">
                <button type="button" onclick="closeEditLoaiPhongModal()" class="btn-gray">Hủy</button>
                <button type="submit" class="btn-green">Lưu thay đổi</button>
            </div>

            <p id="editLoaiPhongMsg" class="text-red-600 pt-2"></p>
        </form>
    </div>
</div>

<!-- DELETE LOAI PHONG MODAL -->
<div id="deleteLoaiPhongModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex justify-center items-center z-50 hidden">
    <div class="modal-box p-8 w-[380px]">
        <h3 class="text-2xl font-bold text-center text-red-600 mb-4">⚠ Xóa loại phòng</h3>
        <p class="text-gray-700 text-center mb-6 leading-relaxed">
            Bạn có chắc chắn muốn xóa loại phòng này?<br>
            <span class="font-semibold">Hành động này không thể hoàn tác.</span>
        </p>
        <input type="hidden" id="delete_loai_phong_id">
        <div class="flex justify-center gap-3">
            <button onclick="closeDeleteLoaiPhongModal()" class="btn-gray">Hủy</button>
            <button onclick="submitDeleteLoaiPhong()" class="btn-red">Xóa</button>
        </div>
        <p id="deleteLoaiPhongMsg" class="text-red-600 text-center pt-2"></p>
    </div>
</div>

<style>
.modal-box { background: #fff; border-radius:22px; box-shadow:0 10px 40px rgba(0,0,0,0.18); animation: modalFadeIn .28s ease; }
@keyframes modalFadeIn { from {opacity:0; transform:scale(0.95) translateY(10px);} to{opacity:1; transform:scale(1) translateY(0);} }
.input{width:100%;padding:10px 14px;border-radius:12px;border:1px solid #d1d5db;background:#f9fafb;transition:.2s;}
.input:focus{border-color:#6366f1;background:#fff;box-shadow:0 0 0 3px rgba(99,102,241,0.15);}
.btn-gray{padding:9px 18px;border-radius:10px;background:#e5e7eb;transition:.2s;}
.btn-gray:hover{background:#d1d5db;}
.btn-blue{padding:10px 20px;border-radius:10px;background:linear-gradient(to right,#4f46e5,#6366f1);color:white;font-weight:600;transition:.2s;}
.btn-blue:hover{opacity:.9;}
.btn-green{padding:10px 20px;border-radius:10px;background:linear-gradient(to right,#10b981,#34d399);color:white;font-weight:600;}
.btn-red{padding:10px 20px;border-radius:10px;background:linear-gradient(to right,#ef4444,#dc2626);color:white;font-weight:600;}
.btn-red:hover{opacity:.9;}
</style>

