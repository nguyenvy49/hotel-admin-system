window.openAddCustomerModal = function () {
    document.getElementById("addCustomerModal")?.classList.remove("hidden");
};

window.closeAddCustomerModal = function () {
    document.getElementById("addCustomerModal")?.classList.add("hidden");
    document.getElementById("addCustomerForm")?.reset();
    let msg = document.getElementById("addCustomerMsg");
    if (msg) msg.innerText = "";
};

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
        }, 600);
    } else {
        document.getElementById("addCustomerMsg").innerText = json.msg;
        showToast(json.msg, "error");
    }
};


// ========================
// EDIT CUSTOMER MODAL
// ========================

window.openEditCustomerModal = async function (id) {
    const res = await fetch("quanlikhachhang/get_khachhang.php?ma=" + id);
    const json = await res.json();

    if (!json.status) {
        showToast("Không tìm thấy khách hàng", "error");
        return;
    }

    let d = json.data;

    document.getElementById("edit_ma").value = d.ma_khach_hang;
    document.getElementById("edit_ho").value = d.ho;
    document.getElementById("edit_ten").value = d.ten;
    document.getElementById("edit_ngay_sinh").value = d.ngay_sinh;
    document.getElementById("edit_sdt").value = d.sdt;
    document.getElementById("edit_email").value = d.email;

    document.getElementById("editCustomerModal")?.classList.remove("hidden");
};

window.closeEditCustomerModal = function () {
    document.getElementById("editCustomerModal")?.classList.add("hidden");
    document.getElementById("editCustomerForm")?.reset();
    let msg = document.getElementById("editCustomerMsg");
    if (msg) msg.innerText = "";
};

window.submitEditCustomer = async function () {
    let form = document.getElementById("editCustomerForm");
    if (!form) return;

    let formData = new FormData(form);

    const res = await fetch("quanlikhachhang/update_khachhang.php", {
        method: "POST",
        body: formData
    });

    const json = await res.json();

    if (json.status === true) {
        closeEditCustomerModal();
        showToast("Cập nhật thành công!", "success");

        setTimeout(() => {
            loadPage("customers");
        }, 600);

    } else {
        document.getElementById("editCustomerMsg").innerText = json.msg;
        showToast(json.msg, "error");
    }
};

// Delete Customer Modal
window.openDeleteCustomerModal = function (id) {
    document.getElementById("delete_ma").value = id;
    document.getElementById("deleteCustomerModal")?.classList.remove("hidden");
};

window.closeDeleteCustomerModal = function () {
    document.getElementById("deleteCustomerModal")?.classList.add("hidden");
    document.getElementById("deleteCustomerMsg").innerText = "";
};
window.submitDeleteCustomer = async function () {
    let id = document.getElementById("delete_ma").value;

    let formData = new FormData();
    formData.append("ma_khach_hang", id);

    const res = await fetch("quanlikhachhang/delete_khachhang.php", {
        method: "POST",
        body: formData
    });

    const json = await res.json();

    if (json.status === true) {
        closeDeleteCustomerModal();
        showToast("Xóa khách hàng thành công!", "success");

        setTimeout(() => {
            loadPage("customers");
        }, 600);
    } else {
        document.getElementById("deleteCustomerMsg").innerText = json.msg;
        showToast(json.msg, "error");
    }
};

// ========================
// ATTACH FORM EVENTS (RUN AFTER FRAGMENT LOADED)
// ========================

function attachFormEvents() {
    // Add customer form handler
    let addForm = document.getElementById("addCustomerForm");
    if (addForm) {
        addForm.onsubmit = (e) => {
            e.preventDefault();
            submitAddCustomer();
        };
    }

    // Edit customer form handler
    let editForm = document.getElementById("editCustomerForm");
    if (editForm) {
        editForm.onsubmit = (e) => {
            e.preventDefault();
            submitEditCustomer();
        };
    }
}

// ========================
// OVERRIDE loadPage TO ATTACH EVENTS AFTER LOAD
// ========================

const originalLoadPage = loadPage;

loadPage = async function (page) {
    await originalLoadPage(page);
    attachFormEvents();
};
