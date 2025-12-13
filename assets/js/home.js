/* =====================================================
    CHỈ KHỞI TẠO KHI PAGE = HOME
===================================================== */
document.addEventListener("pageLoaded", (e) => {
    if (e.detail.page !== "home") return;

    console.log("HOME loaded — binding events…");

    initRoomSelection();
    initBookingButton();
    initModalForm();
});

/* =====================================================
    BIẾN LƯU PHÒNG ĐÃ CHỌN
===================================================== */
let selectedRooms = [];

/* =====================================================
    1) CLICK CHỌN / BỎ CHỌN PHÒNG
===================================================== */
function initRoomSelection() {
    document.querySelectorAll(".room-card").forEach(card => {
        card.onclick = () => {
            const id = card.dataset.id;
            const empty = card.dataset.empty == "1";
            if (!empty) return;

            const badge = card.querySelector(".select-badge");

            if (selectedRooms.includes(id)) {
                selectedRooms = selectedRooms.filter(x => x !== id);
                badge.classList.add("hidden");
                card.classList.remove("border-indigo-500", "bg-indigo-50");
            } else {
                selectedRooms.push(id);
                badge.classList.remove("hidden");
                card.classList.add("border-indigo-500", "bg-indigo-50");
            }

            updateBookingButton();
        };
    });
}

/* =====================================================
    2) BUTTON "ĐẶT X PHÒNG"
===================================================== */
function initBookingButton() {
    const btn = document.getElementById("btnBooking");
    if (!btn) return;

    btn.onclick = () => {
        document.getElementById("modalMultiBook").classList.remove("hidden");

        document.getElementById("selectedRoomsList").innerHTML =
            selectedRooms.map(id => `<li>Phòng ${id}</li>`).join("");

        document.getElementById("selectedRoomsInput").value =
            JSON.stringify(selectedRooms);
    };
}

function updateBookingButton() {
    const btn = document.getElementById("btnBooking");
    const count = document.getElementById("countSelected");

    if (!btn || !count) return;

    if (selectedRooms.length > 0) {
        btn.classList.remove("hidden");
        count.innerText = selectedRooms.length;
    } else {
        btn.classList.add("hidden");
    }
}

/* =====================================================
    3) ĐÓNG MODAL
===================================================== */
window.closeMultiBook = function () {
    document.getElementById("modalMultiBook").classList.add("hidden");
};

/* =====================================================
    4) SUBMIT ĐẶT NHIỀU PHÒNG
===================================================== */
function initModalForm() {
    const form = document.getElementById("formMultiBook");
    if (!form) return;

    form.onsubmit = async function (e) {
        e.preventDefault();

        const fd = new FormData(form);

        const res = await fetch("quanlidatphong/book_room_direct.php", {
            method: "POST",
            body: fd
        });

        const json = await res.json();
        if (json.status) {
            showToast("Đặt phòng thành công!", "success");
            closeMultiBook();
            loadPage("page=home");
        } else {
            document.getElementById("multiMsg").innerText = json.msg;
        }
    };
}
