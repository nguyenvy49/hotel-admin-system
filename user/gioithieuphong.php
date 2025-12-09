<?php
session_start();
include 'config.php';

$isLogged = isset($_SESSION['khach_hang_id']);

// Ngày mặc định
$checkin  = $_GET['checkin']  ?? date('Y-m-d');
$checkout = $_GET['checkout'] ?? date('Y-m-d', strtotime('+1 day'));

// Lấy loại phòng từ SQL
$rooms = $conn->query("SELECT * FROM loai_phong ORDER BY ma_loai_phong ASC");

/* HARD-CODE PHÒNG → ẢNH */
$roomImages = [
    1 => "vip.jpg",      // VIP
    2 => "single.jpg",   // Single Room
    3 => "double.jpg"    // Double / Twin Room
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prestige Manor - Accommodation</title>

    <link rel="stylesheet" href="../assets/css/gioithieuphong.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&family=Cormorant+Garamond&family=Dancing+Script&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>

<!-- ================= HEADER ================= -->
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

<!-- ================= SIDEBAR ================= -->
<div id="mySidebar" class="sidebar">
    <a href="index.php">HOME</a>
    <a href="gioithieuphong.php" class="active">ACCOMMODATION</a>
    <a href="dat_phong.php">BOOKING</a>
    <a href="gioithieudichvu.php">SERVICES</a>
    <a href="lienhe.php">CONTACT US</a>

    <?php if ($isLogged): ?>
        <a href="logout.php">LOG OUT</a>
    <?php else: ?>
        <a href="login.php">LOGIN</a>
        <a href="dangki.php">SIGN UP</a>
    <?php endif; ?>
</div>

<!-- ================= BOOKING BAR ================= -->
<div class="booking-bar-wrapper">
    <div class="booking-bar">

        <div class="booking-item online-info">
            <p class="label">BOOK ONLINE</p>
            <p class="guarantee">Guaranteed accommodation</p>
        </div>

        <div class="booking-item date-input">
            <p class="label">CHECK-IN</p>
            <div class="input-field">
                <input type="text" id="checkin" value="<?= $checkin ?>">
                <i class="fas fa-calendar-alt"></i>
            </div>
        </div>

        <div class="booking-item date-input">
            <p class="label">CHECK-OUT</p>
            <div class="input-field">
                <input type="text" id="checkout" value="<?= $checkout ?>">
                <i class="fas fa-calendar-alt"></i>
            </div>
        </div>

        <button class="find-room-btn" onclick="findRoom()">FIND ROOM</button>

    </div>
</div>

<!-- ================= MAIN IMAGE ================= -->
<section class="main-image-section">
    <img src="../assets/img/accomotion.jpg" alt="Accommodation" class="main-image">
</section>

<!-- ================= ROOM LIST FROM SQL + HARD-CODE IMAGE ================= -->
<section class="room-options-section">
    <div class="section-title">
        <h2 class="signature-room-title">Our Rooms & Suites</h2>
        <p class="section-description-script">
            Experience comfort, elegance and oceanfront luxury at Prestige Manor Quy Nhon.
        </p>
    </div>

    <div class="room-gallery">

        <?php while ($row = $rooms->fetch_assoc()): ?>

            <?php  
                // Lấy ảnh theo mã loại phòng
                $img = $roomImages[$row['ma_loai_phong']] ?? "default.jpg";
            ?>

            <div class="room-detail-row">

                <!-- ẢNH PHÒNG -->
                <div class="room-image-wrap">
                    <img src="../assets/img/<?= $img ?>" 
                         alt="<?= $row['ten_loai_phong'] ?>" 
                         class="room-image">
                </div>

                <!-- THÔNG TIN PHÒNG -->
                <div class="room-info">
                    <h3 class="room-name"><?= $row['ten_loai_phong'] ?></h3>

                    <div class="room-specs">
                        <p>👤 Up to <?= $row['so_nguoi_toi_da'] ?> guests</p>
                        <p>💲 <?= number_format($row['gia_phong'], 0, ',', '.') ?> VND/night</p>
                    </div>

                    <button class="check-price-btn" 
                        onclick="checkPrice(<?= $row['ma_loai_phong'] ?>)">
                        CHECK PRICE
                    </button>
                </div>

            </div>

        <?php endwhile; ?>

    </div>
</section>

<!-- ================= FOOTER ================= -->
<footer class="footer">
    <div class="footer-left"><p class="brand">Prestige Manor</p></div>

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

<!-- ================= SCRIPT ================= -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>

function toggleMenu() {
    document.getElementById("mySidebar").classList.toggle("active");
}

function findRoom() {
    let ci = document.getElementById("checkin").value;
    let co = document.getElementById("checkout").value;

    <?php if (!$isLogged): ?>
        window.location.href = "login.php";
        return;
    <?php endif; ?>

    if (!ci || !co) { alert("Please select dates"); return; }
    if (co <= ci) { alert("Check-out must be later than check-in"); return; }

    window.location.href = `dat_phong.php?checkin=${ci}&checkout=${co}`;
}

function checkPrice(roomType) {
    let ci = document.getElementById("checkin").value;
    let co = document.getElementById("checkout").value;

    <?php if (!$isLogged): ?>
        window.location.href = "login.php";
        return;
    <?php endif; ?>

    window.location.href = `dat_phong.php?type=${roomType}&checkin=${ci}&checkout=${co}`;
}
</script>

</body>
</html>
