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
    <title>Prestige Manor - Restaurant</title>
    <!-- Liên kết Google Fonts cho font chữ: Dancing Script, Playfair Display, và Inter (hoặc một sans-serif mặc định) -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&family=Playfair+Display:wght@400..900&family=Inter:wght@400;600&display=swap">
    <!-- Liên kết Font Awesome cho icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Liên kết file CSS riêng -->
    <link rel="stylesheet" href="../assets/css/nhahang.css">
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
    <a href="index.php">HOME</a>
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

        <!-- 3. BANNER CHÍNH CỦA NHÀ HÀNG -->
        <section class="restaurant-hero-section">
            <div class="hero-image-wrapper">
                <!-- Ảnh Nhà hàng lớn làm nền (Ảnh mẫu) -->
                <img src="../assets/img/nhahang.jpg" alt="Dining area with pool view and natural light">
            </div>
        </section>

        <!-- 4. PHẦN MÔ TẢ CHÍNH (Font Dancing Script) -->
        <section class="main-description-section">
            <div class="description-text">
                <p>Guests enjoy a curated menu of fusion cuisine, served in an atmosphere of quiet refinement and sun-drenched sophistication.</p>
            </div>
        </section>

        <!-- 5. PHẦN GALLERY NỘI DUNG (Ảnh xen kẽ chữ) -->
        <section class="gallery-content-section">
            <div class="content-row row-1">
                <div class="image-wrapper image-left">
                    <!-- Ảnh Tiệc cưới -->
                    <img src="../assets/img/cuoi.jpg" alt="Organize a romantic and cozy wedding">
                </div>
                <div class="text-wrapper text-right">
                    <p>Organize a romantic and cozy wedding</p>
                </div>
            </div>

            <div class="content-row row-2">
                <div class="text-wrapper text-left">
                    <p>Romantic date nights</p>
                </div>
                <div class="image-wrapper image-right">
                    <!-- Ảnh Hẹn hò lãng mạn -->
                    <img src="../assets/img/cauhon.jpg" alt="Romantic date nights">
                </div>
            </div>

            <div class="content-row row-3">
                <div class="image-wrapper image-left">
                    <!-- Ảnh Cooking Class -->
                    <img src="../assets/img/cook.jpg" alt="Cooking class">
                </div>
                <div class="text-wrapper text-right">
                    <p>Cooking class</p>
                </div>
                <!-- Hình tròn trang trí lớn (Bên phải) -->
                <div class="decoration-circle circle-large"></div>
            </div>
        </section>

        <!-- 6. PHẦN THỰC ĐƠN ĐẶC BIỆT (3 MÓN Ăn) -->
        <section class="menu-highlight-section">
            <div class="menu-description">
                <p>A luxurious culinary selection where every dish reflects artistry, precision, and impeccable taste</p>
            </div>
            <div class="dish-gallery">
                <div class="dish-item">
                    <!-- Gan Ngỗng Áp Chảo -->
                    <img src="../assets/img/ngan.jpg" alt="Foie Gras and Fig Jam">
                </div>
                <div class="dish-item">
                    <!-- Thăn Bò Rossini -->
                    <img src="../assets/img/bo.jpg" alt="Beef Tenderloin Rossini">
                </div>
                <div class="dish-item">
                    <!-- Bánh Dung Nham Sô-cô-la -->
                    <img src="../assets/img/scl.jpg" alt="Chocolate Lava Cake and Vanilla Ice Cream">
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
    <script src="guest.js"></script>


</body>
</html>