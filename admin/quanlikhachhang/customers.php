<div class="p-6">

    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-semibold">Quản lý khách hàng</h2>
        <button onclick="openAddCustomerModal()"
        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
    + Thêm khách hàng
</button>

    </div>

    <div class="bg-white rounded-lg shadow table-beauty overflow-auto">
        <table class="min-w-full">
            <thead>
                <tr>
                    <th class="px-6 py-3">Mã</th>
                    <th class="px-6 py-3">Họ</th>
                    <th class="px-6 py-3">Tên</th>
                    <th class="px-6 py-3">Ngày sinh</th>
                    <th class="px-6 py-3">SĐT</th>
                    <th class="px-6 py-3">Email</th>
                    <th class="px-6 py-3">Ngày đăng ký</th>
                    <th class="px-6 py-3">Hành động</th>
                </tr>
            </thead>

            <tbody>
            <?php
            $q = $conn->query("SELECT * FROM khach_hang ORDER BY ma_khach_hang DESC");
            while ($r = $q->fetch_assoc()):
            ?>
                <tr>
                    <td class="px-6 py-3"><?= $r['ma_khach_hang'] ?></td>
                    <td class="px-6 py-3"><?= $r['ho'] ?></td>
                    <td class="px-6 py-3"><?= $r['ten'] ?></td>
                    <td class="px-6 py-3"><?= $r['ngay_sinh'] ?></td>
                    <td class="px-6 py-3"><?= $r['sdt'] ?></td>
                    <td class="px-6 py-3"><?= $r['email'] ?></td>
                    <td class="px-6 py-3"><?= $r['ngay_dang_ky'] ?></td>

                    <td class="px-6 py-3 flex gap-3">
                        <a href="javascript:void(0)"
                        onclick="openEditCustomerModal(<?= $r['ma_khach_hang'] ?>)"
                        class="text-blue-600 hover:underline">
                        Sửa
                        </a>

                        <a href="javascript:void(0)"
                        onclick="openDeleteCustomerModal(<?= $r['ma_khach_hang'] ?>)"
                        class="text-red-600 hover:underline">
                        Xóa
                        </a>

                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>

<!-- BACKDROP -->
<div id="addCustomerModal" 
     class="fixed inset-0 bg-black/40 backdrop-blur-sm flex justify-center items-center 
            z-50 hidden">

    <!-- MODAL CONTENT -->
    <div class="bg-white p-6 w-[420px] rounded-2xl shadow-xl animate-fadeIn">

        <form id="addCustomerForm">

            <div class="space-y-3">

                <div>
                    <label class="font-medium">Họ:</label>
                    <input name="ho" class="input" required>
                </div>

                <div>
                    <label class="font-medium">Tên:</label>
                    <input name="ten" class="input" required>
                </div>

                <div>
                    <label class="font-medium">Ngày sinh:</label>
                    <input type="date" name="ngay_sinh" class="input">
                </div>

                <div>
                    <label class="font-medium">Số điện thoại:</label>
                    <input name="sdt" class="input" required>
                </div>

                <div>
                    <label class="font-medium">Email:</label>
                    <input type="email" name="email" class="input" required>
                </div>

            </div>

            <!-- ACTION BUTTONS -->
            <div class="flex justify-end gap-3 mt-6">

                <button type="button"
                        onclick="closeAddCustomerModal()"
                        class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                    Hủy
                </button>

            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Thêm
                    </button>

            </div>

            <p id="addCustomerMsg" class="text-red-600 mt-3"></p>

        </form>
    </div>
</div>

<!-- EDIT CUSTOMER MODAL -->
<div id="editCustomerModal" 
     class="fixed inset-0 bg-black/40 backdrop-blur-sm flex justify-center items-center 
            z-50 hidden">

    <div class="bg-white p-6 w-[420px] rounded-2xl shadow-xl animate-fadeIn">

        <form id="editCustomerForm">

            <input type="hidden" name="ma_khach_hang" id="edit_ma">

            <div class="space-y-3">

                <div>
                    <label class="font-medium">Họ:</label>
                    <input id="edit_ho" name="ho" class="input" required>
                </div>

                <div>
                    <label class="font-medium">Tên:</label>
                    <input id="edit_ten" name="ten" class="input" required>
                </div>

                <div>
                    <label class="font-medium">Ngày sinh:</label>
                    <input id="edit_ngay_sinh" type="date" name="ngay_sinh" class="input">
                </div>

                <div>
                    <label class="font-medium">Số điện thoại:</label>
                    <input id="edit_sdt" name="sdt" class="input" required>
                </div>

                <div>
                    <label class="font-medium">Email:</label>
                    <input id="edit_email" type="email" name="email" class="input" required>
                </div>

            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button"
                        onclick="closeEditCustomerModal()"
                        class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                    Hủy
                </button>

                <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Lưu
                </button>
            </div>

            <p id="editCustomerMsg" class="text-red-600 mt-3"></p>

        </form>

    </div>
</div>

<!-- DELETE CUSTOMER MODAL -->
<div id="deleteCustomerModal"
     class="fixed inset-0 bg-black/40 backdrop-blur-sm flex justify-center items-center 
            z-50 hidden">

    <div class="bg-white p-6 w-[380px] rounded-2xl shadow-xl animate-fadeIn">

        <h3 class="text-xl font-semibold text-red-600 mb-4">
            ⚠ Xác nhận xóa khách hàng
        </h3>

        <p class="text-gray-700 mb-6">
            Bạn có chắc chắn muốn xóa khách hàng này không?<br>
            Hành động này không thể hoàn tác.
        </p>

        <input type="hidden" id="delete_ma">

        <div class="flex justify-end gap-3">

            <button onclick="closeDeleteCustomerModal()"
                    class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                Hủy
            </button>

            <button onclick="submitDeleteCustomer()"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                Xóa
            </button>

        </div>

        <p id="deleteCustomerMsg" class="text-red-600 mt-3"></p>

    </div>
</div>



<style>
.input {
    width: 100%;
    border: 1px solid #d1d5db;
    padding: 8px 10px;
    border-radius: 8px;
}
@keyframes fadeIn {
    from { opacity:0; transform:translateY(10px); }
    to { opacity:1; transform:translateY(0); }
}
</style>


