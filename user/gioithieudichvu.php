<?php
session_start();
include 'config.php';

$isLogged = isset($_SESSION['khach_hang_id']);

$checkin  = $_GET['checkin']  ?? date('Y-m-d');
$checkout = $_GET['checkout'] ?? date('Y-m-d', strtotime('+1 day'));

function getServicePrice($id, $conn) {
    $stmt = $conn->prepare("SELECT don_gia FROM dich_vu WHERE ma_dich_vu = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    return $res ? number_format($res['don_gia'], 0, ',', '.') : "Liên hệ";
}

//Mapping
$spaPrice  = getServicePrice(103, $conn);
$resPrice  = getServicePrice(108, $conn);
$carPrice  = getServicePrice(101, $conn);
$barPrice  = getServicePrice(109, $conn);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prestige Manor - Services</title>

    <link rel="stylesheet" href="../assets/css/trangchu.css">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&family=Playfair+Display:wght@400;700;900&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
    <a href="gioithieuphong.php">ACCOMMODATION</a>
    <a href="dat_phong.php">BOOKING</a>
    <a class="active" href="gioithieudichvu.php">SERVICES</a>
    <a href="lienhe.php">CONTACT US</a>

    <?php if ($isLogged): ?>
        <a href="logout.php">LOG OUT</a>
    <?php else: ?>
        <a href="login.php">LOGIN</a>
        <a href="dangki.php">SIGN IN</a>
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

<!-- ================= HERO IMAGE ================= -->
<main class="services-content">
    <div class="services-hero-image">
        <img src="../assets/img/dvu.jpg" alt="Prestige Manor Services">
        <div class="services-overlay"></div>
    </div>
</main>

<!-- ================= INTRO ================= -->
<section class="service-intro-section">
    <div class="intro-container">
        <h3 class="intro-title-script">Prestige Manor</h3>

        <p class="intro-body">
            Prestige Manor Quy Nhon is a 5-star boutique hotel blending luxury with local cultural charm.
        </p>
        <p class="intro-body">
            From spa rituals to gourmet dining and premium transportation, enjoy experiences crafted to perfection.
        </p>
    </div>
</section>

<!-- ================= SERVICE GALLERY (GIỮ NGUYÊN BỐ CỤC) ================= -->
<section class="service-gallery-section">
    <div class="gallery-container">

        <div class="service-item">
            <div class="service-image-wrapper">
                <img src="../assets/img/spaa.jpg" alt="Spa Service">
            </div>
            <a href="spa.php" class="service-btn">
                SPA <br>
                <span style="font-size:14px;"><?= $spaPrice ?> VND</span>
            </a>
        </div>

        <div class="service-item">
            <div class="service-image-wrapper">
                <img src="../assets/img/nhahang.jpg" alt="Restaurant Service">
            </div>
            <a href="nhahang.php" class="service-btn">
                RESTAURANT <br>
                <span style="font-size:14px;"><?= $resPrice ?> VND</span>
            </a>
        </div>

        <div class="service-item">
            <div class="service-image-wrapper">
                <img src="../assets/img/oto.jpg" alt="Transportation Service">
            </div>
            <a href="car.php" class="service-btn">
                TRANSPORTATION <br>
                <span style="font-size:14px;"><?= $carPrice ?> VND</span>
            </a>
        </div>

        <div class="service-item">
            <div class="service-image-wrapper">
                <img src="../assets/img/bar.jpg" alt="Bar Service">
            </div>
            <a href="bar.php" class="service-btn">
                BAR <br>
                <span style="font-size:14px;"><?= $barPrice ?> VND</span>
            </a>
        </div>

    </div>

    <div class="gallery-decoration">
        <div class="decoration-circle circle-gallery-bottom"></div>
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

    window.location.href = `dat_phong.php?checkin=${ci}&checkout=${co}`;
}
</script>

</body>
</html>
