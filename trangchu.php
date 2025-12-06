<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prestige Manor - Khách sạn</title>
    <link rel="stylesheet" href="trangchu.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&family=Playfair+Display:wght@400;700;900&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

   <header class="main-header">
    <div class="header-left">
      <button class="menu-toggle" aria-label="Menu" onclick="toggleMenu()">☰</button>
    </div>
    <div class="logo-container">
      <h1 class="logo-text">Prestige Manor</h1>
    </div>
    <div class="header-right">
      <a href="login.php" class="book-direct">BOOK DIRECT</a>
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

     
      <a href="login.php">
        <button class="find-room-btn">FIND ROOM</button></a>
    </div>
  </div>

    <main class="content">
        <div class="hero-image">
            <img src="trangchu.jpg" alt="Khu nghỉ dưỡng sang trọng Prestige Manor">
        </div>
    </main>

    <section class="about-section">
        <div class="about-container">
            
            <div class="about-image-content">
                <img src="quynhon.jpg" alt="Infinity Pool View" class="about-img">
            </div>

            <div class="about-text-content">
                <h2 class="section-title-script">Hotel Prestige Manor Quy Nhon</h2>
                <p class="section-subtitle">Prestige Manor - An Oasis of Tranquility in Nature.</p>
                <p class="section-body">
                    Nestled in a peaceful setting with lush tropical views, Prestige Manor redefines luxury with 
                    modern elegance and serene charm. Featuring a stylish infinity pool, sophisticated 
                    rooms, and impeccable service, the hotel is more than just accommodation – it is a haven where you 
                    can recharge, indulge, and create unforgettable memories.
                </p>
                <a href="gioithieuphong.php" class="btn-select-room">Select your room</a>
            </div>

        </div>

        <div class="decoration-elements">
            <a href="gioithieuchung.php" class="btn-discover-more">Discover more</a>
            <div class="decoration-circle circle-large"></div>
        </div>
    </section>

    <section class="room-service-section">
        <div class="room-service-header">
            <h2 class="section-title-script">Prestige Manor Stay & Service</h2>
            <div class="service-icons">
                <div class="icon-item">
                    <i class="fas fa-bed"></i>
                    <p>Suites</p>
                </div>
                <div class="icon-item">
                    <i class="fas fa-hand-holding-heart"></i>
                    <p>Services</p>
                </div>
            </div>
        </div>

        <div class="room-gallery-container">
            <div class="room-gallery-item wide-left">
                <img src="single.jpg" alt="Single Room">
                <a href="#" class="btn-room-select btn-room-select-1">Single room</a>
            </div>

            <div class="room-gallery-item wide-right">
                <img src="double.jpg" alt="Twin Room">
                <a href="#" class="btn-room-select btn-room-select-2">Twin room</a>
            </div>

            <div class="room-gallery-item wide-bottom">
                <img src="vip.jpg" alt="VIP Room">
                <a href="#" class="btn-room-select btn-room-select-3">VIP room</a>
            </div>
        </div>

        <div class="room-decoration-elements">
            <div class="decoration-circle circle-small-left"></div>
            <div class="decoration-circle circle-large-right"></div>
        </div>
    </section>


    <section class="book-direct-section">
        <div class="book-direct-container">
            <div class="direct-image">
                <img src="booktc.jpg" alt="Prestige Manor Leather Book">
            </div>
            
            <div class="direct-content">
                <h3 class="direct-title-script">Prestige Manor</h3>
                <p class="direct-motto">Hurry up and book an appointment to experience the beauty here.</p>
                
                <div class="direct-action">
                    <div class="decorative-line"></div>
                    <a href="dat_phong.php" class="btn-book-direct-lg">BOOK DIRECT</a>
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
  <script src="guest.js"></script>
</body>
</html>