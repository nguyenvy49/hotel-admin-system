<?php
session_start();
include 'config.php';

// Ngày checkin - checkout
$checkin = $_POST['checkin'] ?? date('Y-m-d');
$checkout = $_POST['checkout'] ?? date('Y-m-d', strtotime('+1 day'));

/* ============================
      LẤY GALLERY BAR TỪ DATABASE
===============================*/
$gallery = [];

$sql = "SELECT ma_dich_vu, ten_dich_vu 
        FROM dich_vu 
        WHERE loai_dich_vu = 'Bar'
        ORDER BY ma_dich_vu ASC";

$res = $conn->query($sql);

while ($row = $res->fetch_assoc()) {
    $gallery[] = $row;
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prestige Manor - Bar</title>

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Dancing+Script:wght@400;700&display=swap">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link rel="stylesheet" href="../assets/css/bar.css">
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
    <a href="trangchu2.php">HOME</a>
    <a href="gioithieuphong.php">ACCOMMODATION</a>
    <a href="dat_phong.php">BOOKING</a>
    <a href="gioithieudichvu.php">SERVICES</a>
    <a class="active" href="bar.php">BAR</a>
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

<!-- ================= HERO IMAGE ================= -->
<section class="bar-hero-section">
    <div class="hero-image-wrapper">
        <img src="../assets/img/bar nền.jpg" alt="Bar Lounge">
    </div>
</section>

<!-- ================= INTRO ================= -->
<section class="bar-intro-section">
    <div class="title-wrapper">
        <h3 class="section-title">PRESTIGE MANOR BAR</h3>
    </div>

    <div class="description-middle">
        <p>
            Indulge in sophisticated comfort, where expertly crafted cocktails blend with the ambiance 
            of warm lighting and refined luxury.
        </p>
    </div>
</section>

<!-- ================= GALLERY TỪ DATABASE ================= -->
<section class="bar-gallery-detail">

<?php foreach ($gallery as $index => $item): ?>

    <?php
        $img = "../assets/img/" . $item["ma_dich_vu"] . ".jpg";
        if (!file_exists($img)) {
            $img = "../assets/img/default-bar.jpg";
        }
        $reverse = $index % 2 !== 0;
    ?>

    <div class="detail-item <?= $reverse ? 'image-left' : 'image-right' ?>">

        <?php if ($reverse): ?>
            <div class="text-content text-right">
                <p><?= $item["ten_dich_vu"] ?></p>
                <div class="decoration-circle circle-large"></div>
            </div>

            <div class="image-wrapper">
                <img src="<?= $img ?>" alt="<?= $item["ten_dich_vu"] ?>">
            </div>

        <?php else: ?>
            <div class="image-wrapper">
                <img src="<?= $img ?>" alt="<?= $item["ten_dich_vu"] ?>">
            </div>

            <div class="text-content text-left">
                <p><?= $item["ten_dich_vu"] ?></p>
                <div class="decoration-circle circle-small"></div>
            </div>

        <?php endif; ?>

    </div>

<?php endforeach; ?>

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

<script>
function toggleMenu() {
    document.getElementById("mySidebar").classList.toggle("active");
}
</script>

</body>

</html>
