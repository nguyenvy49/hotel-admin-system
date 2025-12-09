<?php
session_start();
include 'config.php';

// Ngày checkin - checkout
$checkin = $_POST['checkin'] ?? date('Y-m-d');
$checkout = $_POST['checkout'] ?? date('Y-m-d', strtotime('+1 day'));

/* ============================
      LẤY GALLERY TỪ DATABASE
===============================*/
$gallery = [];

$sqlGallery = "SELECT ma_dich_vu, ten_dich_vu 
               FROM dich_vu 
               WHERE loai_dich_vu = 'Restaurant'
               ORDER BY ma_dich_vu ASC";

$resultGallery = $conn->query($sqlGallery);

while ($row = $resultGallery->fetch_assoc()) {
    $gallery[] = $row;
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prestige Manor - Restaurant</title>

    <link rel="stylesheet" href="../assets/css/nhahang.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&family=Playfair+Display:wght@400;700;900&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
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
    <a class="active" href="nhahang.php">RESTAURANT</a>
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
<section class="restaurant-hero-section">
    <div class="hero-image-wrapper">
        <img src="../assets/img/nhahang.jpg" alt="Restaurant view">
    </div>
</section>

<!-- ================= DESCRIPTION ================= -->
<section class="main-description-section">
    <div class="description-text">
        <p>
            Guests enjoy a curated menu of fusion cuisine, served in an atmosphere 
            of quiet refinement and sun-drenched sophistication.
        </p>
    </div>
</section>

<!-- ================= GALLERY FROM DATABASE ================= -->
<section class="gallery-content-section">

<?php foreach ($gallery as $index => $item): ?>

    <?php
        $imagePath = "../assets/img/bo.jpg"; // Ví dụ đường dẫn hình ảnh
        if (!file_exists($imagePath)) {
            $imagePath = "../assets/img/default-gallery.jpg";
        }
        $reverse = $index % 2 !== 0;
    ?>

    <div class="content-row <?= $reverse ? 'row-reverse' : '' ?>">

        <div class="image-wrapper <?= $reverse ? 'image-right' : 'image-left' ?>">
            <img src="<?= $imagePath ?>" alt="<?= $item['ten_dich_vu'] ?>">
        </div>

        <div class="text-wrapper <?= $reverse ? 'text-left' : 'text-right' ?>">
            <p><?= $item['ten_dich_vu'] ?></p>
        </div>

    </div>

<?php endforeach; ?>

</section>

<!-- ================= MENU HIGHLIGHTS ================= -->
<section class="menu-highlight-section">
    <div class="menu-description">
        <p>A luxurious culinary selection reflecting artistry, precision, and impeccable taste</p>
    </div>

    <div class="dish-gallery">
        <div class="dish-item"><img src="../assets/img/ngan.jpg"></div>
        <div class="dish-item"><img src="../assets/img/bo.jpg"></div>
        <div class="dish-item"><img src="../assets/img/scl.jpg"></div>
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
