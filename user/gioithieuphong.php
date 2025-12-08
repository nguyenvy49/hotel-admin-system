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
    <title>Prestige Manor - Trải nghiệm sang trọng và thiên nhiên</title>
    <link rel="stylesheet" href="../assets/css/gioithieuphong.css">
    
    <!-- Tải các Font sang trọng: Playfair Display, Cormorant Garamond, Dancing Script -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Cormorant+Garamond:ital,wght@0,300;0,400;1,700;1,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Tải Font Awesome cho các icon tinh tế -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
 <!-- ================= HEADER ================= -->
  <header class="main-header">
    <div class="header-left">
      <button class="menu-toggle" aria-label="Menu" onclick="toggleMenu()">☰</button>
    </div>
    <div class="logo-container">
      <h1 class="logo-text">Prestige Manor</h1>
    </div>
    <div class="header-right">
      <a href="dat_phong.php" class="book-direct">BOOK DIRECT</a>
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

    <!-- MAIN IMAGE SECTION - Giữ nguyên -->
    <section class="main-image-section">
        <img src="../assets/img/accomotion.jpg" onerror="this.src='../assets/img/accomotion.jpg';" alt="Outdoor Lounge Area at Prestige Manor" class="main-image">
    </section>
    
    <!-- ROOM OPTIONS SECTION - Giữ nguyên các chỉnh sửa trước -->
    <section class="room-options-section">
        <div class="section-title">
            <h2 class="signature-room-title">Signature Prestige Manor Room</h2>
            <div class="section-description-box">
                <p class="section-description-script">Immerse yourself in the vintage charm of Prestige Manor Quy Nhon, where timeless elegance meets modern comfort. Located in the heart of the seaside city, our hotel offers cozy, refined rooms and an unforgettable stay by the blue ocean.</p>
            </div>
        </div>

        <div class="room-gallery">
            <div class="room-detail-row">
                <div class="room-image-wrap left-image">
                    <img src="../assets/img/single.jpg" onerror="this.src='../assets/img/single.jpg';" alt="Single Room" class="room-image">
                </div>
                <div class="room-info right-info">
                    <h3 class="room-name">Single Room</h3>
                    <div class="room-specs">
                        <p>👤 up to 2 guests</p>
                        <p>↔️ 20m2</p>
                    </div>
                    <button class="check-price-btn">CHECK PRICE</button>
                </div>
            </div>

            <div class="room-detail-row reverse">
                <div class="room-info left-info">
                    <h3 class="room-name">Twin Room</h3>
                    <div class="room-specs">
                        <p>👤 up to 4 guests</p>
                        <p>↔️ 25m2</p>
                    </div>
                    <button class="check-price-btn">CHECK PRICE</button>
                </div>
                <div class="room-image-wrap right-image">
                    <img src="../assets/img/double.jpg" onerror="this.src='../assets/img/double.jpg';" alt="Twin Room" class="room-image">
                </div>
            </div>

            <div class="room-detail-row">
                <div class="room-image-wrap left-image">
                    <img src="../assets/img/vip.jpg" onerror="this.src='../assets/img/vip.jpg';" alt="VIP Room" class="room-image">
                </div>
                <div class="room-info right-info">
                    <h3 class="room-name">VIP Room</h3>
                    <div class="room-specs">
                        <p>👤 up to 4 guests</p>
                        <p>↔️ 25m2</p>
                    </div>
                    <button class="check-price-btn">CHECK PRICE</button>
                </div>
            </div>
        </div>
    </section>

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