/* ============================================================
   QUẢN LÝ LOẠI PHÒNG – CRUD FULL AJAX + MODAL
   Version: 1.0
   ============================================================ */

/* ============================
   -------- ADD LOAI PHONG ----
   ============================ */

window.openAddLoaiPhongModal = function () {
    document.getElementById("addLoaiPhongModal")?.classList.remove("hidden");
};

window.closeAddLoaiPhongModal = function () {
    document.getElementById("addLoaiPhongModal")?.classList.add("hidden");
    document.getElementById("addLoaiPhongForm")?.reset();
    document.getElementById("addLoaiPhongMsg").innerText = "";
};

window.submitAddLoaiPhong = async function () {
    let form = document.getElementById("addLoaiPhongForm");
    if (!form) return;

    let formData = new FormData(form);

    const res = await fetch("quanliloaiphong/add_loai_phong.php", {
        method: "POST",
        body: formData
    });

    const json = await res.json();

    if (json.status === true) {
        closeAddLoaiPhongModal();
        showToast("Thêm loại phòng thành công!", "success");
        setTimeout(() => loadPage("loaiphong"), 600);
    } else {
        document.getElementById("addLoaiPhongMsg").innerText = json.msg ?? "Lỗi!";
        showToast(json.msg ?? "Lỗi!", "error");
    }
};



/* ============================
   -------- EDIT LOAI PHONG ---
   ============================ */

window.openEditLoaiPhongModal = function (data) {

    // Data từ PHP đã là object → không cần parse nữa
    document.getElementById("edit_id").value = data.ma_loai_phong;
    document.getElementById("edit_ten").value = data.ten_loai_phong;
    document.getElementById("edit_so").value = data.so_nguoi_toi_da;
    document.getElementById("edit_gia").value = data.gia_phong;
    document.getElementById("edit_old_image").value = data.hinh_anh;

    // Preview ảnh
    const preview = document.getElementById("preview_hinh_anh");
    preview.src = data.hinh_anh
        ? "../assets/img/" + data.hinh_anh
        : "../assets/img/default.jpg";

    // Xử lý khi chọn ảnh mới
    const fileInput = document.getElementById("edit_hinh_anh");
    fileInput.value = ""; // reset input
    fileInput.onchange = function (e) {
        const file = e.target.files[0];
        if (file) {
            preview.src = URL.createObjectURL(file);
        }
    };

    // Hiện modal
    document.getElementById("editLoaiPhongModal").classList.remove("hidden");
};




window.closeEditLoaiPhongModal = function () {
    document.getElementById("editLoaiPhongModal")?.classList.add("hidden");
    document.getElementById("editLoaiPhongForm")?.reset();
    document.getElementById("editLoaiPhongMsg").innerText = "";
};

window.submitEditLoaiPhong = async function () {
    let form = document.getElementById("editLoaiPhongForm");
    if (!form) return;

    let formData = new FormData(form);

    const res = await fetch("quanliloaiphong/edit_loai_phong.php", {
        method: "POST",
        body: formData
    });

    const json = await res.json();

    if (json.status === true) {
        closeEditLoaiPhongModal();
        showToast("Cập nhật loại phòng thành công!", "success");
        setTimeout(() => loadPage("loaiphong"), 600);
    } else {
        document.getElementById("editLoaiPhongMsg").innerText = json.msg ?? "Lỗi!";
        showToast(json.msg ?? "Lỗi!", "error");
    }
};



/* ============================
   -------- DELETE LOAI PHONG -
   ============================ */

window.openDeleteLoaiPhongModal = function (id) {
    document.getElementById("delete_loai_phong_id").value = id;
    document.getElementById("deleteLoaiPhongModal")?.classList.remove("hidden");
};

window.closeDeleteLoaiPhongModal = function () {
    document.getElementById("deleteLoaiPhongModal")?.classList.add("hidden");
    document.getElementById("deleteLoaiPhongMsg").innerText = "";
};

window.submitDeleteLoaiPhong = async function () {
    let id = document.getElementById("delete_loai_phong_id").value;

    let formData = new FormData();
    formData.append("id", id);

    const res = await fetch("quanliloaiphong/delete_loai_phong.php", {
        method: "POST",
        body: formData
    });

    const json = await res.json();

    if (json.status === true) {
        closeDeleteLoaiPhongModal();
        showToast("Xóa loại phòng thành công!", "success");
        setTimeout(() => loadPage("loaiphong"), 600);
    } else {
        document.getElementById("deleteLoaiPhongMsg").innerText = json.msg ?? "Không thể xóa!";
        showToast(json.msg ?? "Lỗi!", "error");
    }
};



/* ============================================================
   ATTACH FORM EVENTS AFTER FRAGMENT LOAD
   ============================================================ */

function attachLoaiPhongFormEvents() {

    // ADD
    let addForm = document.getElementById("addLoaiPhongForm");
    if (addForm) {
        addForm.onsubmit = (e) => {
            e.preventDefault();
            submitAddLoaiPhong();
        };
    }

    // EDIT
    let editForm = document.getElementById("editLoaiPhongForm");
    if (editForm) {
        editForm.onsubmit = (e) => {
            e.preventDefault();
            submitEditLoaiPhong();
        };
    }
}



/* ============================================================
   HOOK LOADPAGE
   ============================================================ */

if (typeof window.loadPage === "function") {

    const originalLoadPage_loai_phong = window.loadPage;

    window.loadPage = async function (page) {
        await originalLoadPage_loai_phong(page);
        attachLoaiPhongFormEvents();
    };

}
