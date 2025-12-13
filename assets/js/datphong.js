/* ============================================================
    CHỐNG GÁN SỰ KIỆN 2 LẦN
============================================================ */
if (!window.__DATPHONG_INIT__) {
    window.__DATPHONG_INIT__ = true;

    document.addEventListener("pageLoaded", (e) => {
        if (e.detail.page !== "datphong") return;

        console.log("📌 DATPHONG PAGE LOADED");
        bindFilters();
    });
}

/* ============================================================
    1) FILTER
============================================================ */
function bindFilters() {
    let kw = document.getElementById("searchInput");
    if (!kw) return;

    document.getElementById("searchInput").oninput  = applyFilter;
    document.getElementById("filterStatus").onchange = applyFilter;
    document.getElementById("filterFrom").onchange   = applyFilter;
    document.getElementById("filterTo").onchange     = applyFilter;
}

function applyFilter() {
    let kw = document.getElementById("searchInput").value;
    let st = document.getElementById("filterStatus").value;
    let f  = document.getElementById("filterFrom").value;
    let t  = document.getElementById("filterTo").value;

    loadPage(`page=datphong&keyword=${kw}&status=${st}&from=${f}&to=${t}`);
}

/* ============================================================
    2) XEM CHI TIẾT BOOKING
============================================================ */
window.viewDetail = async function(id) {
    let res = await fetch(`quanlidatphong/view_datphong.php?id=${id}`);
    let js = await res.json();

    if (!js.status) return alert(js.msg);

    showModal(js.html);
};

/* ============================================================
    3) XÁC NHẬN BOOKING (Đã đặt → Chờ nhận phòng)
============================================================ */
window.confirmBooking = async function(id) {
    if (!confirm("Xác nhận đơn đặt phòng?")) return;

    let res = await fetch(`quanlidatphong/confirm_booking.php?id=${id}`);
    let js = await res.json();

    if (js.status) {
        showToast("✔ Đã chuyển sang trạng thái Chờ nhận phòng", "success");
        loadPage("page=datphong");
    } else alert(js.msg);
};

/* ============================================================
    4) HỦY BOOKING
============================================================ */
window.cancelBooking = async function(id) {
    if (!confirm("Bạn có chắc muốn hủy booking này?")) return;

    let res = await fetch(`quanlidatphong/cancel_booking.php?id=${id}`);
    let js = await res.json();

    if (js.status) {
        showToast("❌ Đã hủy booking", "success");
        loadPage("page=datphong");
    } else alert(js.msg);
};

/* ============================================================
    5) CHECK-IN (Chờ nhận phòng → Đang ở)
============================================================ */
window.checkin = async function(id) {
    if (!confirm("Xác nhận khách đã nhận phòng?")) return;

    let res = await fetch(`quanlidatphong/checkin.php?id=${id}`);
    let js = await res.json();

    if (js.status) {
        showToast("🚪 Check-in thành công!", "success");
        loadPage("page=datphong");
    } else alert(js.msg);
};

/* ============================================================
    6) MODAL THÊM DỊCH VỤ
============================================================ */
window.openAddService = function(ma_dp) {
    document.getElementById("ma_dat_phong").value = ma_dp;

    let modal = document.getElementById("modalAddService");
    modal.classList.remove("hidden");
    modal.classList.add("flex");
};

window.closeServiceModal = function() {
    let modal = document.getElementById("modalAddService");
    modal.classList.add("hidden");
    modal.classList.remove("flex");
};

window.addServiceRow = function () {
    let list = document.getElementById("serviceList");
    let original = document.querySelector(".service-row");

    let clone = original.cloneNode(true);
    clone.querySelector("input").value = 1;

    list.appendChild(clone);
};

window.submitAddService = async function () {
    let form = document.getElementById("serviceForm");
    let fd = new FormData(form);

    let res = await fetch("quanliphieudichvu/add_service_to_room.php", {
        method: "POST",
        body: fd
    });

    let js = await res.json();

    if (js.status) {
        showToast("➕ Thêm dịch vụ thành công!", "success");
        closeServiceModal();
        loadPage("page=datphong");
    } else {
        alert(js.msg);
    }
};

/* ============================================================
    7) CHECKOUT (TRẢ PHÒNG → BƯỚC 1: XEM TRƯỚC)
============================================================ */
window.checkoutRoom = async function (id) {

    let res = await fetch(`thanhtoan/checkout_preview.php?id=${id}`);
    let js = await res.json();

    if (!js.status) return alert(js.msg);

    showModalCheckout(js.data);
};

/* ============================================================
    8) CHECKOUT (BƯỚC 2: XÁC NHẬN KHÁCH ĐÃ THANH TOÁN)
============================================================ */
window.confirmFinalCheckout = async function(id) {

    if (!confirm("Xác nhận khách đã thanh toán đầy đủ?")) return;

    let res = await fetch(`thanhtoan/confirm_checkout.php?id=${id}`);
    let js = await res.json();

    if (js.status) {
        showToast("💳 Checkout thành công!", "success");
        closeModal();
        loadPage("page=datphong");
    } else {
        alert(js.msg);
    }
};

/* ============================================================
    9) MODAL HIỂN THỊ TIỀN CHECKOUT
============================================================ */
function showModalCheckout(data) {

    closeModal();

    // BẢO VỆ GIÁ TRỊ NULL
    let tien_phong   = Number(data.tien_phong) || 0;
    let tien_dv      = Number(data.tien_dv) || 0;
    let phu_phi      = Number(data.phu_phi) || 0;
    let tien_coc     = Number(data.tien_coc) || 0;
    let phai_tra     = Number(data.phai_tra) || 0;
    let noi_pp       = data.noi_dung_phu_phi || "Không có phụ phí";

    let html = `
        <h2 class="text-2xl font-bold mb-4">💳 Thanh toán trả phòng</h2>

        <div class="space-y-3 text-gray-800">

            <div class="p-3 bg-gray-100 rounded-lg">
                <b>Tiền phòng:</b> ${tien_phong.toLocaleString()} đ
            </div>

            <div class="p-3 bg-gray-100 rounded-lg">
                <b>Tiền dịch vụ:</b> ${tien_dv.toLocaleString()} đ
            </div>

            <div class="p-3 bg-gray-100 rounded-lg">
                <b>Phụ phí checkout:</b> ${phu_phi.toLocaleString()} đ
                <div class="text-sm text-gray-500">${noi_pp}</div>
            </div>

            <div class="p-3 bg-yellow-100 rounded-lg">
                <b>Tiền cọc (VNPay):</b> -${tien_coc.toLocaleString()} đ
            </div>

            <div class="p-3 bg-green-100 rounded-lg text-xl font-bold">
                Khách cần thanh toán: ${phai_tra.toLocaleString()} đ
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <button onclick="closeModal()" 
                    class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">
                Hủy
            </button>

            <button onclick="confirmFinalCheckout(${data.ma_dat_phong})"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Xác nhận đã thanh toán
            </button>
        </div>
    `;

    showModal(html);
}

/* ============================================================
    10) MODAL CHUNG
============================================================ */
function showModal(html) {
    closeModal();

    let modal = document.createElement("div");
    modal.id = "modal";
    modal.className =
        "fixed inset-0 bg-black/40 flex items-center justify-center z-[9999]";

    modal.innerHTML = `
        <div class="bg-white p-8 rounded-3xl w-[600px] max-h-[80vh] overflow-auto shadow-xl animate-fadeIn">
            ${html}
        </div>
    `;

    document.body.appendChild(modal);
}

function closeModal() {
    let m = document.getElementById("modal");
    if (m) m.remove();
}
