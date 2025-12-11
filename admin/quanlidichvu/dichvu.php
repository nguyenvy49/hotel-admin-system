<div class="p-8">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-10">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-2">
                🛎️ Quản lý dịch vụ
            </h1>
            <p class="text-gray-500 mt-1 text-base">Danh sách các dịch vụ khách sạn đang cung cấp</p>
        </div>

        <button onclick="openAddServiceModal()"
            class="px-6 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold 
                   shadow-md hover:shadow-xl hover:opacity-90 transition-all duration-200 active:scale-95">
            + Thêm dịch vụ
        </button>
    </div>

    <!-- SUB HEADER -->
    <h2 class="text-xl font-bold text-gray-800 mb-5">Danh sách dịch vụ</h2>

    <!-- SERVICE GRID -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-7">

        <?php
        $q = $conn->query("SELECT * FROM dich_vu ORDER BY ma_dich_vu ASC");
        while ($r = $q->fetch_assoc()):
        ?>

        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm
                    hover:shadow-xl hover:-translate-y-1 transition-all duration-200">

            <!-- TITLE -->
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h3 class="font-bold text-xl text-gray-900"><?= $r['ten_dich_vu'] ?></h3>
                    <p class="text-gray-500 mt-1 text-sm">Mã DV: <?= $r['ma_dich_vu'] ?></p>
                </div>

                <div class="text-4xl">✨</div>
            </div>

            <!-- PRICE -->
            <p class="mt-4 text-3xl font-bold text-indigo-600 tracking-tight">
                <?= number_format($r['don_gia']) ?> đ
            </p>

            <!-- ACTIONS -->
            <div class="flex gap-4 mt-6">

                <!-- EDIT -->
                <button onclick="openEditServiceModal(<?= $r['ma_dich_vu'] ?>)"
                    class="flex items-center gap-1 px-4 py-2 bg-blue-500 text-white rounded-lg 
                        hover:bg-blue-600 shadow active:scale-95 transition">
                    ✏️ <span>Sửa</span>
                </button>

                <!-- DELETE -->
                <button onclick="openDeleteServiceModal(<?= $r['ma_dich_vu'] ?>)"
                    class="flex items-center gap-1 px-4 py-2 bg-red-500 text-white rounded-lg 
                        hover:bg-red-600 shadow active:scale-95 transition">
                    🗑️ <span>Xóa</span>
                </button>

            </div>

        </div>

        <?php endwhile; ?>

    </div>

</div>


<!-- Add -->
<div id="addServiceModal"
    class="fixed inset-0 bg-black/40 backdrop-blur-sm flex justify-center items-center z-50 hidden">

    <div class="modal-box w-[420px] p-8">

        <form id="addServiceForm">

            <h2 class="text-2xl font-bold mb-6 text-gray-900 flex items-center gap-2">
                ➕ Thêm dịch vụ
            </h2>

            <div class="space-y-5">

                <div>
                    <label class="font-semibold text-gray-700">Tên dịch vụ</label>
                    <input name="ten_dich_vu" class="input mt-1" required>
                </div>

                <div>
                    <label class="font-semibold text-gray-700">Đơn giá (VNĐ)</label>
                    <input type="number" name="don_gia" class="input mt-1" required>
                </div>

                <div>
                    <label class="font-semibold text-gray-700">Loại dịch vụ</label>
                    <select name="loai_dich_vu" id="add_loai_dich_vu" class="input mt-1"></select>
                </div>

            </div>

            <div class="flex justify-end gap-3 mt-8">
                <button type="button" onclick="closeAddServiceModal()" class="btn-gray">Hủy</button>
                <button type="submit" class="btn-blue">Thêm</button>
            </div>

            <p id="addServiceMsg" class="text-red-600 mt-3 text-sm"></p>

        </form>

    </div>

</div>



<!-- Update -->
<div id="editServiceModal"
    class="fixed inset-0 bg-black/40 backdrop-blur-sm flex justify-center items-center z-50 hidden">

    <div class="modal-box w-[420px] p-8">

        <form id="editServiceForm">

            <h2 class="text-2xl font-bold mb-6 text-gray-900 flex items-center gap-2">
                ✏️ Sửa dịch vụ
            </h2>

            <input type="hidden" id="edit_id" name="ma_dich_vu">

            <div class="space-y-5">

                <div>
                    <label class="font-semibold text-gray-700">Tên dịch vụ</label>
                    <input id="edit_ten_dich_vu" name="ten_dich_vu" class="input mt-1" required>
                </div>

                <div>
                    <label class="font-semibold text-gray-700">Đơn giá (VNĐ)</label>
                    <input id="edit_don_gia" type="number" name="don_gia" class="input mt-1" required>
                </div>

                <div>
                    <label class="font-semibold text-gray-700">Loại dịch vụ</label>
                    <select id="edit_loai_dich_vu" name="loai_dich_vu" class="input mt-1"></select>
                </div>

            </div>

            <div class="flex justify-end gap-3 mt-8">
                <button type="button" onclick="closeEditServiceModal()" class="btn-gray">Hủy</button>
                <button type="submit" class="btn-green">Lưu</button>
            </div>

            <p id="editServiceMsg" class="text-red-600 mt-3 text-sm"></p>

        </form>

    </div>

</div>



<!-- Delete -->
<div id="deleteServiceModal"
    class="fixed inset-0 bg-black/40 backdrop-blur-sm flex justify-center items-center z-50 hidden">

    <div class="modal-box w-[360px] p-8 text-center">

        <h3 class="text-2xl font-bold text-red-600 mb-3">⚠ Xóa dịch vụ</h3>

        <p class="text-gray-700 leading-relaxed">
            Bạn có chắc chắn muốn xoá dịch vụ này?<br>
            <span class="font-semibold">Hành động này không thể hoàn tác.</span>
        </p>

        <input type="hidden" id="delete_service_id">

        <div class="flex justify-center gap-4 mt-6">
            <button onclick="closeDeleteServiceModal()" class="btn-gray">Hủy</button>
            <button onclick="submitDeleteService()" class="btn-red">Xóa</button>
        </div>

        <p id="deleteServiceMsg" class="text-red-600 mt-3 text-sm"></p>

    </div>

</div>



<style>
.input {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid #d1d5db;
    background: #f9fafb;
    border-radius: 12px;
    font-size: 15px;
    transition: .2s;
}
.input:focus {
    background: #fff;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,.25);
}

.modal-box {
    background: white;
    border-radius: 22px;
    box-shadow: 0 10px 35px rgba(0,0,0,0.18);
    animation: fadeIn .25s ease-out;
}

.btn-gray, .btn-blue, .btn-green, .btn-red {
    padding: 9px 20px;
    border-radius: 10px;
    font-weight: 600;
    transition: .15s;
}

.btn-gray { background:#e5e7eb; }
.btn-gray:hover { background:#d1d5db; }

.btn-blue { background:#2563eb; color:white; }
.btn-blue:hover { background:#1d4ed8; }

.btn-green { background:#10b981; color:white; }
.btn-green:hover { background:#059669; }

.btn-red { background:#ef4444; color:white; }
.btn-red:hover { background:#dc2626; }

@keyframes fadeIn {
    from { opacity:0; transform:translateY(10px); }
    to { opacity:1; transform:translateY(0); }
}
</style>

