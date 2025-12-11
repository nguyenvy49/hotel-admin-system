
/* ============================
   -------- ADD MODAL ---------
   ============================ */

window.openAddStaffModal = function () {
    document.getElementById("addStaffModal")?.classList.remove("hidden");
};

window.closeAddStaffModal = function () {
    document.getElementById("addStaffModal")?.classList.add("hidden");
    document.getElementById("addStaffForm")?.reset();
    let msg = document.getElementById("addStaffMsg");
    if (msg) msg.innerText = "";
};

window.submitAddStaff = async function () {
    let form = document.getElementById("addStaffForm");
    if (!form) return;

    let formData = new FormData(form);

    const res = await fetch("quanlinhanvien/add_nhanvien.php", {
        method: "POST",
        body: formData
    });

    const json = await res.json();

    if (json.status === true) {
        closeAddStaffModal();
        showToast("Thêm nhân viên thành công!", "success");
        setTimeout(() => loadPage("nhanvien"), 600);
    } else {
        document.getElementById("addStaffMsg").innerText = json.msg ?? "Lỗi!";
        showToast(json.msg ?? "Lỗi!", "error");
    }
};



/* ============================
   -------- EDIT MODAL --------
   ============================ */

window.openEditStaffModal = async function (id) {
    const res = await fetch("quanlinhanvien/get_nhanvien.php?id=" + id);
    const json = await res.json();

    if (!json.status) {
        showToast("Không tìm thấy nhân viên!", "error");
        return;
    }

    let d = json.data;

    document.getElementById("edit_id").value = d.ma_nhan_vien;
    document.getElementById("edit_ho_ten").value = d.ho_ten;
    document.getElementById("edit_gioi_tinh").value = d.gioi_tinh;
    document.getElementById("edit_ngay_sinh").value = d.ngay_sinh;
    document.getElementById("edit_sdt").value = d.sdt;
    document.getElementById("edit_email").value = d.email;
    document.getElementById("edit_chuc_vu").value = d.ma_chuc_vu;

    document.getElementById("editStaffModal")?.classList.remove("hidden");
};

window.closeEditStaffModal = function () {
    document.getElementById("editStaffModal")?.classList.add("hidden");
    document.getElementById("editStaffForm")?.reset();
    let msg = document.getElementById("editStaffMsg");
    if (msg) msg.innerText = "";
};

window.submitEditStaff = async function () {
    let form = document.getElementById("editStaffForm");
    if (!form) return;

    let formData = new FormData(form);

    const res = await fetch("quanlinhanvien/edit_nhanvien.php", {
        method: "POST",
        body: formData
    });

    const json = await res.json();

    if (json.status === true) {
        closeEditStaffModal();
        showToast("Cập nhật nhân viên thành công!", "success");
        setTimeout(() => loadPage("nhanvien"), 600);
    } else {
        document.getElementById("editStaffMsg").innerText = json.msg ?? "Lỗi!";
        showToast(json.msg ?? "Lỗi!", "error");
    }
};



/* ============================
   -------- DELETE MODAL ------
   ============================ */

window.openDeleteStaffModal = function (id) {
    document.getElementById("delete_staff_id").value = id;
    document.getElementById("deleteStaffModal")?.classList.remove("hidden");
};

window.closeDeleteStaffModal = function () {
    document.getElementById("deleteStaffModal")?.classList.add("hidden");
    let msg = document.getElementById("deleteStaffMsg");
    if (msg) msg.innerText = "";
};

window.submitDeleteStaff = async function () {
    let id = document.getElementById("delete_staff_id").value;

    let formData = new FormData();
    formData.append("id", id);

    const res = await fetch("quanlinhanvien/delete_nhanvien.php", {
        method: "POST",
        body: formData
    });

    const json = await res.json();

    if (json.status === true) {
        closeDeleteStaffModal();
        showToast("Xóa nhân viên thành công!", "success");
        setTimeout(() => loadPage("nhanvien"), 600);
    } else {
        document.getElementById("deleteStaffMsg").innerText = json.msg ?? "Lỗi!";
        showToast(json.msg ?? "Lỗi!", "error");
    }
};



/* ============================================================
   ATTACH FORM EVENTS mỗi khi fragment load xong
   ============================================================ */

function attachStaffFormEvents() {

    // ADD
    let addForm = document.getElementById("addStaffForm");
    if (addForm) {
        addForm.onsubmit = (e) => {
            e.preventDefault();
            submitAddStaff();
        };
    }

    // EDIT
    let editForm = document.getElementById("editStaffForm");
    if (editForm) {
        editForm.onsubmit = (e) => {
            e.preventDefault();
            submitEditStaff();
        };
    }
}

/* Hook vào loadPage() để gắn event sau mỗi lần fragment load */
const originalLoadPage_staff = window.loadPage;

window.loadPage = async function (page) {
    await originalLoadPage_staff(page);
    attachStaffFormEvents();
};
