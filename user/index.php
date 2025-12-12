<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prestige Manor - Khách sạn Quy Nhơn</title>

    <link rel="stylesheet" href="../assets/css/trangchu.css">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&family=Playfair+Display:wght@400;700;900&family=Roboto:wght@400;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>

<?php 
    session_start();
    $isLogged = isset($_SESSION['khach_hang_id']);
    
?>

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

<!-- =============== SIDEBAR =============== -->
<div id="mySidebar" class="sidebar">
    <a href="index.php">HOME</a>
    <a href="gioithieuphong.php">ACCOMMODATION</a>
    <a href="dat_phong.php">BOOKING</a>
    <a href="gioithieudichvu.php">SERVICES</a>
    <a href="hoa_don_cua_toi.php">MY BILL</a>
    <a href="lienhe.php">CONTACT US</a>

    <?php if(!$isLogged) : ?>
        <a href="login.php">LOGIN</a>
        <a href="dangki.php">SIGN UP</a>
    <?php else: ?>
        <a href="logout.php">LOG OUT</a>
    <?php endif; ?>
</div>

<!-- =============== BOOKING BAR =============== -->
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

<!-- HERO -->
<main class="content">
    <div class="hero-image">
        <img src="../assets/img/trangchu.jpg" alt="Prestige Manor Hotel">
    </div>
</main>

<!-- ABOUT SECTION -->
<section class="about-section">
    <div class="about-container">

        <div class="about-image-content">
            <img src="../assets/img/quynhon.jpg" class="about-img">
        </div>

        <div class="about-text-content">
            <h2 class="section-title-script">Prestige Manor Quy Nhơn</h2>
            <p class="section-subtitle">An Oasis of Tranquility in Nature</p>

            <p class="section-body">
                Nestled in a peaceful coastal area surrounded by tropical scenery, Prestige Manor redefines luxury 
                with elegant design, world-class service, and a serene atmosphere. More than a stay — it is an escape 
                where you can relax, indulge, and create unforgettable memories.
            </p>

            <a href="gioithieuphong.php" class="btn-select-room">Explore Our Rooms</a>
        </div>
    </div>
</section>

<!-- ROOM GALLERY -->
<section class="room-service-section">
    <div class="room-service-header">
        <h2 class="section-title-script">Rooms & Services</h2>

        <div class="service-icons">
            <div class="icon-item">
                <i class="fas fa-bed"></i>
                <p>Rooms</p>
            </div>
            <div class="icon-item">
                <i class="fas fa-concierge-bell"></i>
                <p>Services</p>
            </div>
        </div>
    </div>

    <div class="room-gallery-container">
        <div class="room-gallery-item wide-left">
            <img src="../assets/img/single.jpg">
            <a href="gioithieuphong.php#single" class="btn-room-select btn-room-select-1">Single Room</a>
        </div>

        <div class="room-gallery-item wide-right">
            <img src="../assets/img/double.jpg">
            <a href="gioithieuphong.php#double" class="btn-room-select btn-room-select-2">Twin Room</a>
        </div>

        <div class="room-gallery-item wide-bottom">
            <img src="../assets/img/vip.jpg">
            <a href="gioithieuphong.php#vip" class="btn-room-select btn-room-select-3">VIP Room</a>
        </div>
    </div>
</section>

<!-- BOOK DIRECT CTA -->
<section class="book-direct-section">
    <div class="book-direct-container">
        <div class="direct-image">
            <img src="../assets/img/booktc.jpg">
        </div>

        <div class="direct-content">
            <h3 class="direct-title-script">Prestige Manor</h3>
            <p class="direct-motto">Book now and experience the beauty of Quy Nhon.</p>

            <div class="direct-action">
                <div class="decorative-line"></div>
                <a href="<?= $isLogged ? 'dat_phong.php' : 'login.php' ?>" class="btn-book-direct-lg">BOOK DIRECT</a>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
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

<!-- FLATPICKR -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    // ===== LẤY NGÀY HÔM NAY CHUẨN =====
    let today = new Date();
    today = new Date(today.getFullYear(), today.getMonth(), today.getDate()); // tránh lỗi timezone


    // ==============================
    //  FLATPICKR CHO CHECK-IN/OUT
    // ==============================
    const checkinPicker = flatpickr("#checkin", { 
        minDate: today,
        dateFormat: "Y-m-d",
        allowInput: true,
        onChange: function(selectedDates) {
            if (selectedDates.length > 0) {
                let ci = selectedDates[0];

                // set lại minDate của checkout >= checkin
                checkoutPicker.set("minDate", ci);

                // nếu checkout < checkin => reset checkout
                let coValue = document.getElementById("checkout").value;
                if (coValue && new Date(coValue) <= ci) {
                    document.getElementById("checkout").value = "";
                }
            }
        }
    });

    const checkoutPicker = flatpickr("#checkout", { 
        minDate: today,
        dateFormat: "Y-m-d",
        allowInput: true
    });


    // =============================
    //  HÀM KIỂM TRA NGÀY HỢP LỆ
    // =============================
    function isValidDate(dateStr) {
        const regex = /^\d{4}-\d{2}-\d{2}$/;
        if (!regex.test(dateStr)) return false;

        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return false;

        // tránh lỗi timezone bằng cách so sánh YYYY-MM-DD
        return dateStr === d.toISOString().slice(0, 10);
    }


    // =============================
    //     HANDLE FIND ROOM
    // =============================
    window.handleFindRoom = function () {

        let ci = document.getElementById("checkin");
        let co = document.getElementById("checkout");

        let checkin = ci.value.trim();
        let checkout = co.value.trim();

        <?php if(!$isLogged): ?>
            window.location.href = "login.php";
            return;
        <?php endif; ?>

        // === 1. RỖNG ===
        if (!checkin || !checkout) {
            alert("Vui lòng chọn ngày check-in và check-out!");
            return;
        }

        // === 2. KHÔNG ĐÚNG ĐỊNH DẠNG ===
        if (!isValidDate(checkin)) {
            ci.style.border = "2px solid red";
            alert("Ngày check-in không hợp lệ!");
            return;
        }
        ci.style.border = "";

        if (!isValidDate(checkout)) {
            co.style.border = "2px solid red";
            alert("Ngày check-out không hợp lệ!");
            return;
        }
        co.style.border = "";

        // Convert to Date (không dính timezone)
        let d1 = new Date(`${checkin}T00:00:00`);
        let d2 = new Date(`${checkout}T00:00:00`);

        // === 3. CHECK-IN < HÔM NAY ===
        if (d1 < today) {
            ci.style.border = "2px solid red";
            alert("Ngày check-in không được nhỏ hơn hôm nay!");
            return;
        }

        // === 4. CHECK-OUT < HÔM NAY ===
        if (d2 < today) {
            co.style.border = "2px solid red";
            alert("Ngày check-out không được nhỏ hơn hôm nay!");
            return;
        }

        // === 5. CHECKOUT > CHECKIN ===
        if (d2 <= d1) {
            co.style.border = "2px solid red";
            alert("Ngày check-out phải lớn hơn ngày check-in!");
            return;
        }

        // === OK => CHUYỂN TRANG ===
        window.location.href =
            `dat_phong.php?checkin=${encodeURIComponent(checkin)}&checkout=${encodeURIComponent(checkout)}`;
    };



    // =============================
    //     SIDEBAR MENU
    // =============================
    window.toggleMenu = function () {
        document.getElementById("mySidebar").classList.toggle("active");
        document.querySelector(".menu-toggle").classList.toggle("active");
    };

});
</script>



<script src="../assets/js/guest.js"></script>


</body>
</html>

