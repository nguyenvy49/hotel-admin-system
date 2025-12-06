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
    <title>Prestige Manor - Dịch vụ</title>
    <link rel="stylesheet" href="trangchu.css">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&family=Playfair+Display:wght@400;700;900&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
      <a href="dangky.html" class="book-direct">BOOK DIRECT</a>
    </div>
  </header>

  <!-- Sidebar + Toggle menu -->
<button class="menu-toggle" onclick="toggleMenu()">☰</button>

<div id="mySidebar" class="sidebar">
    <a href="trangchu2.php">HOME</a>
    <a href="gioithieuphong.php">ACCOMMODATION</a>
    <a href="dat_phong.php">BOOKING</a>
    <a href="gioithieudichvu.php">SERVICES</a>
    <a href="lienhe.php">CONTACT US</a>
    <a href="login.php">LOGIN</a>
    <a href="dangki.php">SIGN IN</a>
  </div>

<!-- Nội dung chính giữ nguyên -->
<div class="main-content">
  <!-- ... tất cả nội dung cũ của trangchu.php ... -->
</div>

<!-- CSS -->
<style>
  .menu-toggle {
    position: fixed;
    top: 10px;
    left: 10px;
    font-size: 24px;
    cursor: pointer;
    z-index: 1000;
    background: transparent;
    border: none;
    color: #000;
  }

  .sidebar {
    position: fixed;
    top: 0;
    left: -250px; /* ẩn mặc định */
    width: 220px;
    height: 100%;
    background: #A9A48F;
    color: #fff;
    display: flex;
    flex-direction: column;
    padding-top: 60px;
    transition: left 0.35s ease;
    z-index: 999;
  }

  .sidebar a {
    padding: 12px 20px;
    text-decoration: none;
    color: #fff;
    display: block;
  }

  .sidebar a:hover {
    background-color: #888;
  }

  .sidebar.active {
    left: 0; /* mở sidebar */
  }

  .main-content {
    margin-left: 0; /* sidebar ẩn ban đầu */
    transition: margin-left 0.35s ease;
  }

  .sidebar.active ~ .main-content {
    margin-left: 220px; /* trượt nội dung khi sidebar mở */
  }
</style>

<!-- JS -->
<script>
  function toggleMenu() {
    var sb = document.getElementById("mySidebar");
    sb.classList.toggle("active");
  }
</script>
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
<!-- ==========CONTENT================-->
    <main class="services-content">
        <div class="services-hero-image">
            <img src="dvu.jpg" alt="Prestige Manor Services Bar">
            <div class="services-overlay">
            </div>
        </div>
    </main>

    <section class="service-intro-section">
        <div class="intro-container">
            <div class="intro-title-box">
                <h3 class="intro-title-script">Prestige Manor</h3>
            </div>
            <p class="intro-body">
                Prestige Manor Quy Nhon is a 5-star boutique hotel by the beautiful Quy Nhon coastline, where modern elegance meets the charm of Central Vietnam.
            </p>
            <p class="intro-body">
                We offer personalized services from airport transfers to local tours, with **24/7 concierge support**. Guests can enjoy high-speed Wi-Fi, daily housekeeping, flexible check-in/out, and tailored romantic or celebration packages.
            </p>
            <p class="intro-body">
                Beyond comfort, we connect you with local culture – from herbal welcome drinks to authentic fishing village and market experiences. Whether for business, leisure, or a quick getaway, Prestige Manor promises warmth, sophistication, and unforgettable memories by the sea.
            </p>
        </div>
    </section>

    <section class="service-gallery-section">
    <div class="gallery-container">
        <div class="service-item">
            <div class="service-image-wrapper">
                <img src="spaa.jpg" alt="Spa Service">
            </div>
            <a href="spa.php" class="service-btn">SPA</a>
        </div>

        <div class="service-item">
            <div class="service-image-wrapper">
                <img src="nhahang.jpg" alt="Restaurant Service">
            </div>
            <a href="nhahang.php" class="service-btn">RESTAURANT</a>
        </div>

        <div class="service-item">
            <div class="service-image-wrapper">
                <img src="oto.jpg" alt="Transportation Service">
            </div>
            <a href="car.html" class="service-btn">TRANSPORTATION</a>
        </div>
        
        <div class="service-item">
            <div class="service-image-wrapper">
                <img src="bar.jpg" alt="Bar Service">
            </div>
            <a href="bar.php" class="service-btn">BAR</a>
        </div>
    </div>
    
    <div class="gallery-decoration">
        <div class="decoration-circle circle-gallery-bottom"></div>
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
  <script src="guest.js"></script>
</body>

</html>