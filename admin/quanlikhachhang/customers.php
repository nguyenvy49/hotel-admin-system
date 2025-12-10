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
                        <a href="quanlikhachhang/edit_khachhang.php?ma=<?= $r['ma_khach_hang'] ?>"
                           class="text-blue-600 hover:underline">Sửa</a>
                        <a href="quanlikhachhang/delete_khachhang.php?ma=<?= $r['ma_khach_hang'] ?>"
                           onclick="return confirm('Xóa khách hàng này?')"
                           class="text-red-600 hover:underline">Xóa</a>
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
<script>
// GLOBAL MODAL FUNCTIONS (cho mọi fragment)

window.openAddCustomerModal = function () {
    document.getElementById("addCustomerModal")?.classList.remove("hidden");
};

window.closeAddCustomerModal = function () {
    document.getElementById("addCustomerModal")?.classList.add("hidden");
    document.getElementById("addCustomerForm")?.reset();
    let msg = document.getElementById("addCustomerMsg");
    if (msg) msg.innerText = "";
};

// Submit AJAX thêm khách hàng
window.submitAddCustomer = async function () {
    let form = document.getElementById("addCustomerForm");
    if (!form) return;

    let formData = new FormData(form);

    const res = await fetch("quanlikhachhang/add_khachhang.php", {
        method: "POST",
        body: formData
    });

    const json = await res.json();

   if (json.status === true) {

    closeAddCustomerModal();
    showToast("Thêm khách hàng thành công!", "success");

    setTimeout(() => {
        loadPage("customers");
    }, 600); // đợi animation modal đóng rồi load
} 
else {
    document.getElementById("addCustomerMsg").innerText = json.msg;
    showToast(json.msg, "error");
}

};

// Attach submit handler mỗi khi fragment load xong
async function attachFormEvents() {
    let form = document.getElementById("addCustomerForm");
    if (form) {
        form.onsubmit = (e) => {
            e.preventDefault();
            submitAddCustomer();
        };
    }
}

// Hook vào loadPage
const oldLoadPage = loadPage;
loadPage = async function(page) {
    await oldLoadPage(page);
    attachFormEvents();
};
</script>


