<?php
session_start();
include 'config.php';

// Check login
$isLogged = isset($_SESSION['khach_hang_id']);

// Nhận ngày tìm phòng
$checkin  = $_GET['checkin']  ?? date('Y-m-d');
$checkout = $_GET['checkout'] ?? date('Y-m-d', strtotime('+1 day'));

// TRUY VẤN TÌM CÁC LOẠI PHÒNG CÓ PHÒNG KHẢ DỤNG
$sql = "
    SELECT 
        lp.ma_loai_phong,
        lp.ten_loai_phong,
        lp.so_nguoi_toi_da,
        lp.gia_phong,
        lp.hinh_anh,

        (
            SELECT COUNT(*)
            FROM phong p
            WHERE p.ma_loai_phong = lp.ma_loai_phong
              AND p.trang_thai = 'Trống' 
              AND p.ma_phong NOT IN (
                    SELECT dp.ma_phong
                    FROM dat_phong dp
                    WHERE 
                        dp.trang_thai <> 'Hủy'
                        AND dp.ma_phong IS NOT NULL
                        AND (
                                dp.ngay_nhan < '$checkout'
                            AND dp.ngay_tra  > '$checkin'
                        )
              )
        ) AS phong_trong

    FROM loai_phong lp
";


$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prestige Manor - Booking</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../assets/css/book.css">
    <link rel="stylesheet" href="../assets/css/trangchu.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* Fix cho popup */
        .confirm-popup {
            position: fixed;
            top:0; left:0;
            width:100%; height:100%;
            display:none;
            justify-content:center;
            align-items:center;
            background:rgba(0,0,0,0.45);
            z-index:3000;
        }
        .popup-box {
            width:420px;
            background:#fff;
            border-radius:14px;
            padding:25px;
            box-shadow:0 6px 20px rgba(0,0,0,0.25);
            animation:fadeIn 0.3s ease-out;
        }
        .popup-btn, .popup-close {
            width:100%;
            margin-top:10px;
            padding:12px;
            border:none;
            border-radius:8px;
            cursor:pointer;
        }
        .popup-btn { background:#A9A48F; color:white; font-weight:bold; }
        .popup-close { background:#ddd; }
        @keyframes fadeIn { from{opacity:0;} to{opacity:1;} }
    </style>

</head>

<body>

<!-- ====================== HEADER ======================= -->
<header class="main-header">
    <div class="header-left">
        <button class="menu-toggle" onclick="toggleMenu()">☰</button>
    </div>

    <div class="logo-container">
        <h1 class="logo-text">Prestige Manor</h1>
    </div>

    <div class="header-right">
        <a href="<?= $isLogged ? 'dat_phong.php' : 'login.php' ?>" class="book-direct">BOOK DIRECT</a>
    </div>
</header>

<!-- ====================== SIDEBAR ======================= -->
<div id="mySidebar" class="sidebar">
    <a href="index.php">HOME</a>
    <a href="gioithieuphong.php">ACCOMMODATION</a>
    <a class="active" href="dat_phong.php">BOOKING</a>
    <a href="gioithieudichvu.php">SERVICES</a>
    <a href="lienhe.php">CONTACT US</a>

    <?php if(!$isLogged): ?>
        <a href="login.php">LOGIN</a>
        <a href="dangki.php">SIGN UP</a>
    <?php else: ?>
        <a href="logout.php">LOG OUT</a>
    <?php endif; ?>
</div>

<!-- ====================== BOOKING BAR ======================= -->
<div class="booking-bar-wrapper">
    <div class="booking-bar">

        <div class="booking-item online-info">
            <p class="label">BOOK ONLINE</p>
            <p class="guarantee">Guaranteed reservation</p>
        </div>

        <div class="booking-item date-input">
            <p class="label">CHECK-IN</p>
            <div class="input-field">
                <input type="text" id="checkin" placeholder="Select date">
                <i class="fas fa-calendar-alt"></i>
            </div>
        </div>

        <div class="booking-item date-input">
            <p class="label">CHECK-OUT</p>
            <div class="input-field">
                <input type="text" id="checkout" placeholder="Select date">
                <i class="fas fa-calendar-alt"></i>
            </div>
        </div>

        

        <!-- Nút tìm phòng theo logic hệ thống -->
        <button class="find-room-btn" onclick="handleFindRoom()">FIND ROOM</button>
    </div>
</div>

<!-- ====================== DANH SÁCH PHÒNG ======================= -->

<?php while ($room = $result->fetch_assoc()): ?>
<section class="card">
    <div class="left">
        <img src="../assets/img/<?= $room['hinh_anh'] ?>" 
             alt="<?= $room['ten_loai_phong'] ?>"
             onerror="this.src='../assets/img/default.jpg'">
    </div>

    <div class="right">
        <h2><?= $room['ten_loai_phong'] ?></h2>

        <p>👥 Up to <?= $room['so_nguoi_toi_da'] ?> Guests</p>
        <p><strong>Available: <?= $room['phong_trong'] ?> rooms</strong></p>

        <!-- GIÁ PHÒNG: lấy từ DB -->
        <p class="price" data-price="<?= $room['gia_phong'] ?>">
            <?= number_format($room['gia_phong']) ?> VND / night
        </p>

        <!-- Ô chọn số lượng -->
        <div class="qty-box">
            <button class="qty-btn" onclick="changeQty(<?= $room['ma_loai_phong'] ?>, -1)">−</button>

            <input type="number" 
                   id="qty_<?= $room['ma_loai_phong'] ?>" 
                   class="qty-input"
                   value="0" 
                   min="0"
                   max="<?= $room['phong_trong'] ?>">

            <button class="qty-btn" onclick="changeQty(<?= $room['ma_loai_phong'] ?>, 1)">+</button>
        </div>
    </div>
</section>
<?php endwhile; ?>

<!-- ================= BUTTON ĐẶT PHÒNG ================== -->
<div class="submit-area">
    <button class="continue-btn" onclick="confirmBooking()">Đặt phòng</button>
</div>

<!-- ====================== POPUP XÁC NHẬN ======================= -->
<div id="confirmPopup" class="confirm-popup">
    <div class="popup-box">
        <h3 style="font-family:'Playfair Display',serif;">Xác nhận đặt phòng</h3>

        <div id="popupContent"></div>

        <button class="popup-btn" onclick="submitBooking()">Xác nhận</button>
        <button class="popup-close" onclick="closePopup()">Hủy</button>
    </div>
</div>

<!-- ====================== FOOTER ======================= -->
<footer class="footer">
    <div class="footer-left">
        <p class="brand">Prestige Manor</p>
    </div>

    <div class="footer-middle">
        <ul>
          <li><a href="index.php">▶ Home</a></li>
          <li><a href="gioithieuphong.php">▶ Accommodation</a></li>
          <li><a href="dat_phong.php">▶ Book Direct</a></li>
          <li><a href="nhahang.php">▶ PM Restaurant</a></li>
          <li><a href="spa.php">▶ PM Spa</a></li>
        </ul>
    </div>

    <div class="footer-right">
        <h3>CONTACT US</h3>
        <p>▶ Hotline/Zalo: +84 94271</p>
        <p>▶ Location: Quy Nhơn</p>
    </div>
</footer>

<!-- ====================== JAVASCRIPT ======================= -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.js"></script>
<script>
// Reload trang khi chọn lại ngày
function reloadWithDates() {
    let ci = document.getElementById("checkin").value;
    let co = document.getElementById("checkout").value;

    window.location.href = `dat_phong.php?checkin=${ci}&checkout=${co}`;
}

// + / – số phòng
function changeQty(id, step) {
    let input = document.getElementById("qty_" + id);
    let max = parseInt(input.max);
    let value = parseInt(input.value) + step;

    if (value < 0) value = 0;
    if (value > max) value = max;

    input.value = value;
}

// Popup xác nhận đặt phòng
function confirmBooking() {

    let popupHTML = "";
    let grandTotal = 0;
    let selectedRooms = [];

    const rooms = document.querySelectorAll(".qty-input");

    rooms.forEach(input => {
        let qty = parseInt(input.value);
        if (qty > 0) {
            let id = input.id.replace("qty_", "");
            let card = input.closest(".card");

            let roomName = card.querySelector("h2").innerText;
            let price = Number(card.querySelector(".price").dataset.price);

            let total = qty * price;
            grandTotal += total;

            selectedRooms.push({ id, roomName, qty, price });

            popupHTML += `
                <div style="margin-bottom:14px;">
                    <p style="font-weight:600; font-size:16px;">${roomName}</p>
                    <p>Giá: ${price.toLocaleString()} VND / đêm</p>
                    <p>Số phòng: ${qty}</p>
                    <p style="font-weight:600;">Thành tiền: ${total.toLocaleString()} VND</p>
                    <hr>
                </div>
            `;
        }
    });

    if (selectedRooms.length === 0) {
        alert("Bạn chưa chọn phòng nào!");
        return;
    }

    popupHTML += `
        <p style="text-align:right;font-size:18px;font-weight:700;margin-top:10px;">
            Tổng cộng: ${grandTotal.toLocaleString()} VND
        </p>
    `;

    document.getElementById("popupContent").innerHTML = popupHTML;
    document.getElementById("confirmPopup").style.display = "flex";

    // Lưu phòng
    sessionStorage.setItem("selectedRooms", JSON.stringify(selectedRooms));
    sessionStorage.setItem("totalPriceRooms", grandTotal);
}

// Đóng popup
function closePopup() { 
    document.getElementById("confirmPopup").style.display = "none"; 
}

// ✨ CHỖ SỬA QUAN TRỌNG NHẤT ✨
// Sang bước chọn dịch vụ → LƯU checkin/checkout
function submitBooking() {

    sessionStorage.setItem("checkin",  document.getElementById("checkin").value);
    sessionStorage.setItem("checkout", document.getElementById("checkout").value);

    window.location.href = "dichVu.php"; 
}

function toggleMenu() {
    document.getElementById("mySidebar").classList.toggle("active");
}
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    // ===== LẤY NGÀY TRONG URL & GÁN LẠI VÀO INPUT =====
    const url = new URL(window.location.href);
    const checkinValue = url.searchParams.get("checkin") || "";
    const checkoutValue = url.searchParams.get("checkout") || "";

    if (checkinValue) document.getElementById("checkin").value = checkinValue;
    if (checkoutValue) document.getElementById("checkout").value = checkoutValue;

    // ===== LẤY NGÀY HÔM NAY CHUẨN =====
    const today = new Date();
    today.setHours(0,0,0,0);

    // ============================================
    // FLATPICKR CHO CHECK-IN
    // ============================================
    const checkinPicker = flatpickr("#checkin", {
        minDate: today,
        dateFormat: "Y-m-d",
        defaultDate: checkinValue || null,
        allowInput: true,
        onChange: function(selectedDates) {
            if (selectedDates.length > 0) {
                let ci = selectedDates[0];
                checkoutPicker.set("minDate", ci);

                let coDate = document.getElementById("checkout").value;
                if (coDate && new Date(coDate) <= ci) {
                    document.getElementById("checkout").value = "";
                }
            }
        }
    });

    // ============================================
    // FLATPICKR CHO CHECK-OUT
    // ============================================
    const checkoutPicker = flatpickr("#checkout", {
        minDate: checkinValue || today,
        dateFormat: "Y-m-d",
        defaultDate: checkoutValue || null,
        allowInput: true,
    });

    // ============================================
    // HÀM CHECK DATE HỢP LỆ
    // ============================================
    function isValidDate(dateStr) {
        const regex = /^\d{4}-\d{2}-\d{2}$/;
        if (!regex.test(dateStr)) return false;

        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return false;

        return dateStr === d.toISOString().slice(0, 10);
    }

    // ============================================
    // HANDLE FIND ROOM BUTTON
    // ============================================
    window.handleFindRoom = function () {

        let checkin = document.getElementById("checkin").value.trim();
        let checkout = document.getElementById("checkout").value.trim();

        // CHƯA LOGIN → CHUYỂN LOGIN
        <?php if(!$isLogged): ?>
            window.location.href = "login.php";
            return;
        <?php endif; ?>

        // KIỂM TRA RỖNG
        if (!checkin || !checkout) {
            alert("Vui lòng chọn ngày check-in và check-out!");
            return;
        }

        // KIỂM TRA ĐỊNH DẠNG
        if (!isValidDate(checkin)) {
            alert("Ngày check-in không hợp lệ!");
            return;
        }

        if (!isValidDate(checkout)) {
            alert("Ngày check-out không hợp lệ!");
            return;
        }

        const d1 = new Date(checkin);
        const d2 = new Date(checkout);

        // KIỂM TRA NGÀY
        if (d1 < today) {
            alert("Ngày check-in phải ≥ hôm nay!");
            return;
        }

        if (d2 <= d1) {
            alert("Ngày check-out phải lớn hơn check-in!");
            return;
        }

        // OK → RELOAD TRANG VỚI PARAM MỚI
        window.location.href =
            `dat_phong.php?checkin=${encodeURIComponent(checkin)}&checkout=${encodeURIComponent(checkout)}`;
    };
});
</script>

<script src="../assets/js/guest.js"></script>
</body>
</html>
