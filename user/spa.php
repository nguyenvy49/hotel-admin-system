<?php
session_start();
include 'config.php';

// Ngày checkin - checkout
$checkin = $_POST['checkin'] ?? date('Y-m-d');
$checkout = $_POST['checkout'] ?? date('Y-m-d', strtotime('+1 day'));

/* ============================
      LẤY DỊCH VỤ SPA TỪ DB
===============================*/
$services = [];
$query = "SELECT ma_dich_vu, ten_dich_vu, don_gia FROM dich_vu WHERE loai_dich_vu = 'Spa'";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    $services[] = $row;
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Prestige Manor - Spa</title>

  <link rel="stylesheet" href="../assets/css/spa.css">

  <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&family=Playfair+Display:wght@400;700;900&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
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
      <a href="dat_phong.php" class="book-direct">BOOK DIRECT</a>
    </div>
</header>

<!-- ================= SIDEBAR ================= -->
<div id="mySidebar" class="sidebar">
    <a href="index.php">HOME</a>
    <a href="gioithieuphong.php">ACCOMMODATION</a>
    <a href="dat_phong.php">BOOKING</a>
    <a href="gioithieudichvu.php">SERVICES</a>
    <a class="active" href="spa.php">SPA</a>
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
        <input type="text" id="checkin" value="<?= htmlspecialchars($checkin) ?>">
        <i class="fas fa-calendar-alt"></i>
      </div>
    </div>

    <div class="booking-item date-input">
      <p class="label">CHECK-OUT</p>
      <div class="input-field">
        <input type="text" id="checkout" value="<?= htmlspecialchars($checkout) ?>">
        <i class="fas fa-calendar-alt"></i>
      </div>
    </div>

    <a href="dat_phong.php">
      <button class="find-room-btn">FIND ROOM</button>
    </a>

  </div>
</div>

<!--==================== BANNER ==================== -->
<div class="banner-text">
  <h2 class="banner-title">PM Spa</h2>
</div>

<!-- INTRO SECTION -->
<section class="text">
    <p>
      With a luxurious yet intimate, warm atmosphere, PM Spa brings a relaxing and harmonious experience from the very first moment.
      Every detail — scent, music, lighting, technique — is crafted to create a perfect premium spa journey.
    </p>

    <div class="info-line">⏰ Open: 10:00 - 22:00</div>
    <div class="info-line">🚩 Location: 12th Floor</div>
</section>

<!--================== SPA SERVICES ==================-->
<div class="services-grid">

<?php foreach ($services as $service): ?>
    <div class="service-card">
      <?php 
          // Ảnh tự động dựa theo ID => ví dụ: 102.jpg, 103.jpg
          $img = "../assets/img/" . $service['ma_dich_vu'] . ".jpg";

          // Nếu không tìm thấy ảnh, dùng ảnh mặc định
          if (!file_exists($img)) {
              $img = "../assets/img/default.jpg";
          }
      ?>
      <img src="<?= $img ?>" alt="<?= $service['ten_dich_vu'] ?>">

      <div class="service-info">
        <div class="service-title"><?= $service['ten_dich_vu'] ?></div>
        <div class="service-price"><?= number_format($service['don_gia'], 0, ',', '.') ?> VND</div>
      </div>
    </div>
<?php endforeach; ?>

</div>

<!-- ================= FOOTER ================= -->
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
        <p>▶ Location: Quy Nhon</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
function toggleMenu() {
    document.getElementById("mySidebar").classList.toggle("active");
}
</script>

</body>
</html>
