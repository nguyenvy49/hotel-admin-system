/* ============================================================
   QUẢN LÝ PHÒNG – CRUD FULL AJAX + MODAL
   Version: 1.1 (fix theo API mới)
   ============================================================ */


/* ============================
   -------- ADD ROOM ---------
   ============================ */

window.openAddRoomModal = function () {
    document.getElementById("addRoomModal")?.classList.remove("hidden");
};

window.closeAddRoomModal = function () {
    document.getElementById("addRoomModal")?.classList.add("hidden");
    document.getElementById("addRoomForm")?.reset();
    document.getElementById("addRoomMsg").innerText = "";
};

window.submitAddRoom = async function () {
    let form = document.getElementById("addRoomForm");
    if (!form) return;

    let formData = new FormData(form);

    const res = await fetch("quanliphong/add_phong.php", {
        method: "POST",
        body: formData
    });

    const json = await res.json();

    if (json.status === true) {
        closeAddRoomModal();
        showToast("Thêm phòng thành công!", "success");

        setTimeout(() => loadPage("phong"), 600);
    } else {
        document.getElementById("addRoomMsg").innerText = json.msg ?? "Lỗi!";
        showToast(json.msg ?? "Lỗi!", "error");
    }
};



/* ============================
   -------- EDIT ROOM --------
   ============================ */

window.openEditRoomModal = async function (id) {

    const res = await fetch("quanliphong/get_phong.php?id=" + id);
    const json = await res.json();

    if (!json.status) {
        showToast("Không tìm thấy phòng!", "error");
        return;
    }

    let d = json.data;

    // Gán dữ liệu vào form sửa
    document.getElementById("edit_id").value = d.ma_phong;
    document.getElementById("edit_so_phong").value = d.so_phong;
    document.getElementById("edit_ma_loai_phong").value = d.ma_loai_phong;
    document.getElementById("edit_trang_thai").value = d.trang_thai;

    document.getElementById("editRoomModal")?.classList.remove("hidden");
};

window.closeEditRoomModal = function () {
    document.getElementById("editRoomModal")?.classList.add("hidden");
    document.getElementById("editRoomForm")?.reset();
    document.getElementById("editRoomMsg").innerText = "";
};

window.submitEditRoom = async function () {

    let form = document.getElementById("editRoomForm");
    if (!form) return;

    let formData = new FormData(form);

    const res = await fetch("quanliphong/edit_phong.php", {
        method: "POST",
        body: formData
    });

    const json = await res.json();

    if (json.status === true) {
        closeEditRoomModal();
        showToast("Cập nhật phòng thành công!", "success");

        setTimeout(() => loadPage("phong"), 600);
    } else {
        document.getElementById("editRoomMsg").innerText = json.msg ?? "Lỗi!";
        showToast(json.msg ?? "Lỗi!", "error");
    }
};



/* ============================
   -------- DELETE ROOM -------
   ============================ */

window.openDeleteRoomModal = function (id) {
    document.getElementById("delete_room_id").value = id;
    document.getElementById("deleteRoomModal")?.classList.remove("hidden");
};

window.closeDeleteRoomModal = function () {
    document.getElementById("deleteRoomModal")?.classList.add("hidden");
    document.getElementById("deleteRoomMsg").innerText = "";
};

window.submitDeleteRoom = async function () {

    let id = document.getElementById("delete_room_id").value;

    let formData = new FormData();
    formData.append("id", id);

    const res = await fetch("quanliphong/delete_phong.php", {
        method: "POST",
        body: formData
    });

    const json = await res.json();

    if (json.status === true) {
        closeDeleteRoomModal();
        showToast("Xóa phòng thành công!", "success");

        setTimeout(() => loadPage("phong"), 600);
    } else {
        document.getElementById("deleteRoomMsg").innerText = json.msg ?? "Lỗi!";
        showToast(json.msg ?? "Lỗi!", "error");
    }
};

async function loadRoomStatusOptions() {
    const res = await fetch("quanliphong/get_trangthai.php");
    const json = await res.json();

    if (!json.status) return;

    let opts = json.data;

    let addSelect = document.getElementById("add_trang_thai");
    let editSelect = document.getElementById("edit_trang_thai");

    if (addSelect) {
        addSelect.innerHTML = "";
        opts.forEach(v => {
            addSelect.innerHTML += `<option value="${v}">${v}</option>`;
        });
    }

    if (editSelect) {
        editSelect.innerHTML = "";
        opts.forEach(v => {
            editSelect.innerHTML += `<option value="${v}">${v}</option>`;
        });
    }
}





/* ============================================================
   ATTACH FORM EVENTS AFTER FRAGMENT LOAD
   ============================================================ */

function attachRoomFormEvents() {

    // Add room
    let addForm = document.getElementById("addRoomForm");
    if (addForm) {
        addForm.onsubmit = (e) => {
            e.preventDefault();
            submitAddRoom();
        };
    }

    // Edit room
    let editForm = document.getElementById("editRoomForm");
    if (editForm) {
        editForm.onsubmit = (e) => {
            e.preventDefault();
            submitEditRoom();
        };
    }
}


/* ============================================================
   HOOK LOADPAGE TO ATTACH EVENTS
   ============================================================ */

const originalLoadPage_room = window.loadPage;

window.loadPage = async function (page) {
    await originalLoadPage_room(page);
    attachRoomFormEvents();
    loadRoomStatusOptions();
};

