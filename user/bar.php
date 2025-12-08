<?php
session_start(); // bắt buộc để đọc session
include 'config.php';
// Lấy GET
$checkin = $_POST['checkin'] ?? date('Y-m-d');
$checkout = $_POST['checkout'] ?? date('Y-m-d', strtotime('+1 day'));
$guests = isset($_POST['guests']) ? (int)$_POST['guests'] : 1;
$total = isset($_POST['totalPrice']) ? floatval($_POST['totalPrice']) : 0;

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prestige Manor - Bar</title>
    <!-- Liên kết Google Fonts cho font chữ chính: Playfair Display và Dancing Script -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Dancing+Script:wght@400..700&display=swap">
    <!-- Liên kết Font Awesome cho icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Liên kết file CSS -->
    <link rel="stylesheet" href="../assets/css/bar.css">
</head>

<body>
    <!-- 1. HEADER CHÍNH -->
    <header class="main-header">
        <div class="header-left">
            <button class="menu-toggle" aria-label="Menu" onclick="toggleMenu()">☰</button>
        </div>
        <div class="logo-container">
            <h1 class="logo-text">Prestige Manor</h1>
        </div>
        <div class="header-right">
            <a href="dangky.html" class="book-direct">BOOK DIRECT</a>
        </div>
    </header>

    <!-- ================= SIDEBAR ================= -->
    <div id="mySidebar" class="sidebar">
    <a href="trangchu2.php">HOME</a>
    <a href="gioithieuphong.php">ACCOMMODATION</a>
    <a href="dat_phong.php">BOOKING</a>
    <a href="gioithieudichvu.php">SERVICES</a>
    <a href="lienhe.php">CONTACT US</a>
    <a href="login.php">LOGIN</a>
    <a href="dangki.php">SIGN IN</a>
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
          <input type="text" id="checkin" placeholder="Select date" value="<?= htmlspecialchars($checkin) ?>">
          <i class="fas fa-calendar-alt"></i>
        </div>
      </div>

      <div class="booking-item date-input">
        <p class="label">CHECK-OUT</p>
        <div class="input-field">
          <input type="text" id="checkout" placeholder="Select date" value="<?= htmlspecialchars($checkout) ?>">
          <i class="fas fa-calendar-alt"></i>
        </div>
      </div>
            <a href="dat_phong.php">
                <button class="find-room-btn">FIND ROOM</button></a>
        </div>
    </div>

    <!-- 3. BANNER CHÍNH CỦA BAR -->
    <section class="bar-hero-section">
        <div class="hero-image-wrapper">
            <!-- Ảnh Bar lớn làm nền -->
            <!-- Sử dụng ảnh BẢ.jpg hoặc BAR2.jpg làm nền -->
            <img src="../assets/img/bar nền.jpg" alt="Bar Counter and Lounge">
        </div>
    </section>

    <!-- 4. PHẦN GIỚI THIỆU CHI TIẾT -->
    <section class="bar-intro-section">
        <div class="title-wrapper">
            <h3 class="section-title">PRESTIGE MANOR BAR</h3>
        </div>

        <!-- Phần mô tả giữa (Dựa trên ảnh BAR2.jpg) -->
        <div class="description-middle">
            <p>Indulge your senses in this sophisticated comfort, where you can unwind and savor the finest wines and
                spirits.</p>
        </div>

        <!-- Phần Gallery 2 cột (Dựa trên ảnh BAR3.jpg) -->
        <div class="bar-gallery-detail">

            <div class="detail-item image-right">
                <div class="image-wrapper">
                    <!-- Ảnh Noble Lounge (BAR3.jpg) -->
                    <img src="../assets/img/phache.jpg" alt="Noble Lounge">
                </div>
                <div class="text-content text-left">
                    <p>The experience culminates in sipping expertly crafted cocktails or rare spirits beneath the soft
                        glow of the crystal chandelier.</p>
                    <!-- Hình tròn trang trí -->
                    <div class="decoration-circle circle-small"></div>
                </div>
            </div>

            <div class="detail-item image-left">
                <div class="text-content text-right">
                    <p>The exterior spaces, characterized by organic textures and lush, curated landscapes, offer an
                        unparalleled sense of tranquility and space</p>
                    <!-- Hình tròn trang trí -->
                    <div class="decoration-circle circle-large"></div>
                </div>
                <div class="image-wrapper">
                    <!-- Ảnh thứ 2 (giả định) -->
                    <img src="../assets/img/trochuyen.jpg" Bar Exterior">
                </div>
            </div>
        </div>

    </section>
    </main>
    <!-- ================= FOOTER ================= -->
    <footer class="footer">
        <div class="footer-left">
            <p class="brand">Prestige Manor</p>
        </div>
        <div class="footer-middle">
             <ul>
        <li><a href="trangchu2.php">▶ Home</a></li>
        <li><a href="gioithieuphong.php">▶ Accommodation</a></li>
        <li><a href="dat_phong.php">▶ Book Direct</a></li>
        <li><a href="nhahang.php">▶ PM Restaurant</a></li>
        <li><a href="spa.php">▶ PM Spa</a></li>
      </ul>
        </div>
        <div class="footer-right">
            <h3>CONTACT US</h3>
            <p>▶ Hotline/Zalo: +84 94271</p>
            <p>▶ Location: Quy Nhon</p>
        </div>
    </footer>

    <!-- ================= SCRIPT ================= -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="../assets/js/guest.js"></script>


</body>

</html>