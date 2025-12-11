/* ============================================================
   QUẢN LÝ DỊCH VỤ – FULL CRUD AJAX + MODAL
   ============================================================ */

/* LOAD ENUM LOẠI DỊCH VỤ */
async function loadServiceTypes() {
    const res = await fetch("quanlidichvu/get_loaidhv.php");
    const json = await res.json();

    if (!json.status) return;

    let html = "";
    json.data.forEach(v => {
        html += `<option value="${v}">${v}</option>`;
    });

    document.getElementById("add_loai_dich_vu").innerHTML = html;
    document.getElementById("edit_loai_dich_vu").innerHTML = html;
}

/* OPEN / CLOSE MODAL */
window.openAddServiceModal = () => {
    loadServiceTypes();
    document.getElementById("addServiceModal").classList.remove("hidden");
};

window.closeAddServiceModal = () => {
    document.getElementById("addServiceModal").classList.add("hidden");
    document.getElementById("addServiceForm").reset();
};

window.openEditServiceModal = async (id) => {
    await loadServiceTypes();

    const res = await fetch("quanlidichvu/get_dichvu.php?id=" + id);
    const json = await res.json();
    if (!json.status) return showToast("Không tìm thấy dịch vụ!", "error");

    const d = json.data;

    document.getElementById("edit_id").value = d.ma_dich_vu;
    document.getElementById("edit_ten_dich_vu").value = d.ten_dich_vu;
    document.getElementById("edit_don_gia").value = d.don_gia;
    document.getElementById("edit_loai_dich_vu").value = d.loai_dich_vu;

    document.getElementById("editServiceModal").classList.remove("hidden");
};

window.closeEditServiceModal = () => {
    document.getElementById("editServiceModal").classList.add("hidden");
    document.getElementById("editServiceForm").reset();
};

window.openDeleteServiceModal = (id) => {
    document.getElementById("delete_service_id").value = id;
    document.getElementById("deleteServiceModal").classList.remove("hidden");
};

window.closeDeleteServiceModal = () => {
    document.getElementById("deleteServiceModal").classList.add("hidden");
};

/* ADD SERVICE */
window.submitAddService = async function () {
    const form = document.getElementById("addServiceForm");
    const formData = new FormData(form);

    const res = await fetch("quanlidichvu/add_dichvu.php", {
        method: "POST",
        body: formData
    });

    const json = await res.json();
    if (json.status) {
        closeAddServiceModal();
        showToast("Thêm dịch vụ thành công!", "success");
        setTimeout(() => loadPage("dichvu"), 500);
    } else {
        showToast(json.msg, "error");
    }
};

/* EDIT SERVICE */
window.submitEditService = async function () {
    const form = document.getElementById("editServiceForm");
    const formData = new FormData(form);

    const res = await fetch("quanlidichvu/edit_dichvu.php", {
        method: "POST",
        body: formData
    });

    const json = await res.json();
    if (json.status) {
        closeEditServiceModal();
        showToast("Cập nhật dịch vụ thành công!", "success");
        setTimeout(() => loadPage("dichvu"), 500);
    } else {
        showToast(json.msg, "error");
    }
};

/* DELETE SERVICE */
window.submitDeleteService = async function () {
    let id = document.getElementById("delete_service_id").value;

    const fd = new FormData();
    fd.append("id", id);

    const res = await fetch("quanlidichvu/delete_dichvu.php", {
        method: "POST",
        body: fd
    });

    const json = await res.json();
    if (json.status) {
        closeDeleteServiceModal();
        showToast("Xóa dịch vụ thành công!", "success");
        setTimeout(() => loadPage("dichvu"), 500);
    } else {
        showToast(json.msg, "error");
    }
};

/* AUTO ATTACH EVENTS */
function attachServiceFormEvents() {
    let addForm = document.getElementById("addServiceForm");
    if (addForm) addForm.onsubmit = e => { e.preventDefault(); submitAddService(); };

    let editForm = document.getElementById("editServiceForm");
    if (editForm) editForm.onsubmit = e => { e.preventDefault(); submitEditService(); };
}

/* HOOK LOADPAGE */
const oldLoadPage_service = window.loadPage;
window.loadPage = async function (page) {
    await oldLoadPage_service(page);
    attachServiceFormEvents();
};
