<?php
session_start();
include 'config.php';

// Load dịch vụ
$sql_pickup = "SELECT * FROM dich_vu WHERE loai_dich_vu = 'transport'";
$sql_spa    = "SELECT * FROM dich_vu WHERE loai_dich_vu = 'spa'";
$sql_food   = "SELECT * FROM dich_vu WHERE loai_dich_vu = 'bar'";

$pickup = $conn->query($sql_pickup);
$spa    = $conn->query($sql_spa);
$food   = $conn->query($sql_food);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prestige Manor - Extra Services</title>

    <link rel="stylesheet" href="../assets/css/service.css">
    <link rel="stylesheet" href="../assets/css/trangchu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>

<header class="main-header">
    <button class="menu-toggle" onclick="toggleMenu()">☰</button>
    <h1 class="logo-text">Prestige Manor</h1>
</header>

<div id="mySidebar" class="sidebar">
    <a href="index.php">HOME</a>
    <a href="gioithieuphong.php">ACCOMMODATION</a>
    <a href="dat_phong.php">BOOKING</a>
    <a href="gioithieudichvu.php">SERVICES</a>
    <a href="lienhe.php">CONTACT US</a>
    <a href="login.php">LOGIN</a>
    <a href="dangki.php">SIGN UP</a>
</div>

<!-- BOOKING BAR -->
<div class="booking-bar-wrapper">
  <div class="booking-bar">
    <div class="booking-item online-info">
      <p class="label">BOOK ONLINE</p>
      <p class="guarantee">Guaranteed reservation</p>
    </div>

    <div class="booking-item date-input">
      <p class="label">CHECK-IN</p>
      <div class="input-field">
        <input type="text" id="checkin" readonly>
      </div>
    </div>

    <div class="booking-item date-input">
      <p class="label">CHECK-OUT</p>
      <div class="input-field">
        <input type="text" id="checkout" readonly>
      </div>
    </div>
  </div>
</div>

<section class="select">
  <h2>Extra Services</h2>
</section>

<!-- TRANSPORT -->
<section class="service-table">
  <h2 class="category-title">Transportation</h2>

  <?php while ($row = $pickup->fetch_assoc()): ?>
  <div class="service-row">
    <div class="service-info">
      <h3><?= $row['ten_dich_vu'] ?></h3>
      <p>Private car • Airport pickup • Premium service</p>
    </div>

    <div class="offer-footer">
      <p class="price"><?= number_format($row['don_gia']) ?>đ</p>
      <button class="service-btn"
        onclick="addService(<?= $row['ma_dich_vu'] ?>, '<?= $row['ten_dich_vu'] ?>', <?= $row['don_gia'] ?>)">
        Add
      </button>
    </div>
  </div>
  <?php endwhile; ?>
</section>

<!-- SPA -->
<section class="service-table">
  <h2 class="category-title">PM Spa</h2>

  <?php while ($row = $spa->fetch_assoc()): ?>
  <div class="service-row">
    <div class="service-info">
      <h3><?= $row['ten_dich_vu'] ?></h3>
    </div>

    <div class="service-price"><?= number_format($row['don_gia']) ?>đ</div>

    <button class="service-btn"
        onclick="addService(<?= $row['ma_dich_vu'] ?>, '<?= $row['ten_dich_vu'] ?>', <?= $row['don_gia'] ?>)">
        Add
    </button>
  </div>
  <?php endwhile; ?>
</section>

<!-- FOOD -->
<section class="service-table food-service">
  <h2 class="category-title">Bar & Included Services</h2>

  <?php while ($row = $food->fetch_assoc()): ?>
  <div class="service-row">
    <div class="service-image">
      <img src="../assets/img/services/<?= $row['hinh_anh'] ?>">
    </div>

    <div class="service-info">
      <h3><?= $row['ten_dich_vu'] ?></h3>
    </div>

    <button class="service-btn" disabled style="background:#ccc;">Included</button>
  </div>
  <?php endwhile; ?>
</section>

<!-- BILL FLOATING -->
<div id="billToggle" class="bill-toggle">🧾</div>

<div class="bill" id="billContainer">
  <h3>My Booking Summary</h3>

  <div id="room-items"></div>
  <hr>

  <div id="service-items"></div>

  <hr>
  <p><strong>Room Total:</strong> <span id="roomPrice">0đ</span></p>
  <p><strong>Services Total:</strong> <span id="servicesPrice">0đ</span></p>
  <p><strong>Grand Total:</strong> <span id="totalPrice">0đ</span></p>

  <button class="book-btn" onclick="goToPay()">Proceed to Payment</button>
</div>

<script>
// FLOATING BILL
document.getElementById("billToggle").onclick = () => {
    document.getElementById("billContainer").classList.toggle("active");
};

// Load ngày
document.getElementById("checkin").value  = sessionStorage.getItem("checkin") || "";
document.getElementById("checkout").value = sessionStorage.getItem("checkout") || "";

// Load phòng
let selectedRooms = JSON.parse(sessionStorage.getItem("selectedRooms")) || [];
let roomTotal = Number(sessionStorage.getItem("totalPriceRooms")) || 0;

// Load dịch vụ nếu user quay lại trang này
let selectedServices = JSON.parse(sessionStorage.getItem("selectedServices")) || [];

// Render phòng
function renderRooms() {
    let html = "";
    selectedRooms.forEach(r => {
        html += `
            <p>• ${r.roomName} × ${r.qty} 
            <span style="float:right;">${(r.qty * r.price).toLocaleString()}đ</span></p>
        `;
    });

    document.getElementById("room-items").innerHTML = html;
    document.getElementById("roomPrice").innerText = roomTotal.toLocaleString() + "đ";
}
renderRooms();

// Add service
function addService(id, name, price) {
    let item = selectedServices.find(s => s.id === id);

    if (item) item.qty++;
    else selectedServices.push({ id, name, price, qty: 1 });

    sessionStorage.setItem("selectedServices", JSON.stringify(selectedServices));

    renderServices();
}

// Render dịch vụ
function renderServices() {
    let html = "";
    let serviceTotal = 0;

    selectedServices.forEach(s => {
        let total = s.qty * s.price;
        serviceTotal += total;

        html += `
        <div class="bill-item">
            <div class="bill-item-left">
                <p class="bill-name">${s.name}</p>
                <p class="bill-sub">${s.price.toLocaleString()}đ × ${s.qty}</p>
            </div>

            <div class="bill-item-right">
                <p class="bill-total">${total.toLocaleString()}đ</p>

                <div class="bill-actions">
                    <button class="btn-qty" onclick="increaseService(${s.id})">+</button>
                    <button class="btn-qty" onclick="decreaseService(${s.id})">−</button>
                    <button class="btn-remove" onclick="removeService(${s.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        `;
    });

    document.getElementById("service-items").innerHTML = html;

    document.getElementById("servicesPrice").innerText = serviceTotal.toLocaleString() + "đ";
    document.getElementById("totalPrice").innerText = (roomTotal + serviceTotal).toLocaleString() + "đ";
}
renderServices();

function increaseService(id) {
    let svc = selectedServices.find(s => s.id === id);
    svc.qty++;
    sessionStorage.setItem("selectedServices", JSON.stringify(selectedServices));
    renderServices();
}

function decreaseService(id) {
    let svc = selectedServices.find(s => s.id === id);
    svc.qty--;
    if (svc.qty <= 0) selectedServices = selectedServices.filter(x => x.id !== id);
    sessionStorage.setItem("selectedServices", JSON.stringify(selectedServices));
    renderServices();
}

function removeService(id) {
    selectedServices = selectedServices.filter(x => x.id !== id);
    sessionStorage.setItem("selectedServices", JSON.stringify(selectedServices));
    renderServices();
}

function goToPay() {
    // Lưu dịch vụ
    sessionStorage.setItem("selectedServices", JSON.stringify(selectedServices));

    // Lưu ngày
    sessionStorage.setItem("checkin", document.getElementById("checkin").value);
    sessionStorage.setItem("checkout", document.getElementById("checkout").value);

    window.location.href = "thanhToan.php";
}

function toggleMenu() {
    document.getElementById("mySidebar").classList.toggle("active");
}
</script>

</body>
</html>
