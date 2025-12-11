/* ============================================================
    HOME.JS — BẢN ỔN ĐỊNH HOÀN CHỈNH
============================================================ */

/* ==== OVERRIDE loadPage CHỐNG GHI ĐÈ ==== */
if (!window.__HOME_PATCHED__) {
    window.__HOME_PATCHED__ = true;
    window.__originalLoadPage = window.loadPage;

    // Lắng nghe khi pageLoaded được bắn từ index.php
    document.addEventListener("pageLoaded", (e) => {

        if (e.detail.page !== "home") return;

        console.log("HOME LOADED — binding events...");

        // Gán sự kiện form đặt phòng & thêm DV
        attachBookRoomEvent();
        attachAddServiceRoomEvent();

        // Gán sự kiện cho nút lọc (có sẵn trong DOM của home)
        const btn = document.getElementById("btnFilter");
        if (btn) {
            btn.onclick = () => {
                let ci = document.getElementById("filter_checkin").value;
                let co = document.getElementById("filter_checkout").value;

                loadPage(`page=home&checkin=${ci}&checkout=${co}`);
            };
        }
    });
}

/* ============================================================
    1) ĐẶT PHÒNG
============================================================ */

window.openBookRoom = function (maPhong) {
    document.getElementById("book_room_id").value = maPhong;
    document.getElementById("modalBookRoom").classList.remove("hidden");
};

window.closeBookRoom = function () {
    document.getElementById("modalBookRoom").classList.add("hidden");
    document.getElementById("bookRoomMsg").innerText = "";
};

function attachBookRoomEvent() {
    const form = document.getElementById("formBookRoom");
    if (!form) return;

    form.onsubmit = async function (e) {
        e.preventDefault();

        const fd = new FormData(form);

        const res = await fetch("quanlidatphong/book_room_direct.php", {
            method: "POST",
            body: fd
        });

        let json = null;
        try {
            json = await res.json();
        } catch (e) {
            document.getElementById("bookRoomMsg").innerText = "PHP lỗi, xem F12!";
            return;
        }

        if (json.status) {
            showToast("Đặt phòng thành công!", "success");
            closeBookRoom();
            loadPage("page=home");
        } else {
            document.getElementById("bookRoomMsg").innerText = json.msg;
        }
    };
}

/* ============================================================
    2) LẤY LIST DỊCH VỤ
============================================================ */

window.openAddServiceForRoom = async function (maPhong) {

    document.getElementById("service_room_id").value = maPhong;

    const res = await fetch("quanlidichvu/get_all_dichvu.php");
    const json = await res.json();

    if (!json.status) {
        showToast("Không tải được dịch vụ!", "error");
        return;
    }

    window.cachedServices = json.data;

    const firstSelect = document.querySelector("#serviceList .service-select");
    if (firstSelect) loadServiceToSelect(firstSelect);

    document.getElementById("modalAddServiceRoom").classList.remove("hidden");
};

window.closeAddServiceRoom = function () {
    document.getElementById("modalAddServiceRoom").classList.add("hidden");
};

/* Tạo dòng DV */
window.addServiceRow = function () {
    let list = document.getElementById("serviceList");

    let div = document.createElement("div");
    div.className = "service-row flex items-center gap-3 mt-2";

    div.innerHTML = `
        <select name="ma_dich_vu[]" class="input service-select"></select>
        <input type="number" name="so_luong[]" class="input w-20" value="1" min="1">
        <button type="button" class="btn-red px-3 remove-row">✖</button>
    `;

    list.appendChild(div);
    loadServiceToSelect(div.querySelector("select"));
    div.querySelector(".remove-row").onclick = () => div.remove();
};

window.loadServiceToSelect = function (selectEl) {
    selectEl.innerHTML = "";
    window.cachedServices.forEach(s => {
        selectEl.innerHTML += `
            <option value="${s.ma_dich_vu}">
                ${s.ten_dich_vu} – ${Number(s.don_gia).toLocaleString()}đ
            </option>`;
    });
};

/* Submit DV */
function attachAddServiceRoomEvent() {
    const form = document.getElementById("formAddServiceRoom");
    if (!form) return;

    form.onsubmit = async function (e) {
        e.preventDefault();

        const fd = new FormData(form);
        const res = await fetch("quanliphieudichvu/add_service_to_room.php", {
            method: "POST",
            body: fd
        });

        const json = await res.json();

        if (json.status) {
            showToast("Thêm dịch vụ thành công!", "success");
            closeAddServiceRoom();
            loadPage("page=home");
        } else {
            document.getElementById("serviceRoomMsg").innerText = json.msg;
        }
    };
}

/* ============================================================
    3) CHECKOUT
============================================================ */

window.openCheckout = async function (maPhong) {

    const res = await fetch("thanhtoan/checkout_detail.php?id=" + maPhong);
    const json = await res.json();

    if (!json.status) {
        showToast(json.msg, "error");
        return;
    }

    document.getElementById("checkoutContent").innerHTML = json.html;
    window.currentRoomID = maPhong;

    document.getElementById("modalCheckout").classList.remove("hidden");
};

window.closeCheckout = function () {
    document.getElementById("modalCheckout").classList.add("hidden");
};

window.confirmCheckout = async function () {

    const res = await fetch("thanhtoan/confirm_checkout.php?id=" + window.currentRoomID);
    const json = await res.json();

    if (json.status) {
        showToast("Trả phòng thành công!", "success");
        closeCheckout();
        loadPage("page=home");
    } else {
        showToast(json.msg, "error");
    }
};
