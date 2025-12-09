<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Booking</title>

  <link rel="stylesheet" href="../assets/css/thanhToan.css">
  <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&family=Playfair+Display:wght@400;700;900&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
</header>

<!-- ================= SIDEBAR ================= -->
<div id="mySidebar" class="sidebar">
    <a href="index.php">HOME</a>
    <a href="gioithieuphong.php">ACCOMMODATION</a>
    <a href="dat_phong.php">BOOKING</a>
    <a href="gioithieudichvu.php">SERVICES</a>
    <a href="lienhe.php">CONTACT US</a>

    <?php if (!isset($_SESSION['khach_hang_id'])): ?>
        <a href="login.php">LOGIN</a>
        <a href="dangki.php">SIGN UP</a>
    <?php else: ?>
        <a href="logout.php">LOG OUT</a>
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
          <div class="input-field"><input type="text" id="checkin" readonly></div>
        </div>

        <div class="booking-item date-input">
          <p class="label">CHECK-OUT</p>
          <div class="input-field"><input type="text" id="checkout" readonly></div>
        </div>
    </div>
</div>

<!-- ================= TITLE ================= -->
<section class="select">
  <h2>Details of your stay</h2>
</section>

<!-- ================= MAIN CONTENT ================= -->
<div class="booking-wrapper">

    <!-- CUSTOMER FORM -->
    <div class="customer-section">
      <h2>Customer</h2>

      <form class="booking-form" id="customerForm">
        <div class="form-row">
          <div class="form-group"><label>First name</label><input id="fname" required></div>
          <div class="form-group"><label>Last name</label><input id="lname" required></div>
        </div>

        <div class="form-row">
          <div class="form-group"><label>Phone</label><input id="phone"></div>
          <div class="form-group"><label>Email</label><input id="email" required></div>
        </div>

        <div class="form-group">
          <label>Country</label>
          <select id="country">
            <option>Vietnam</option>
            <option>USA</option>
            <option>France</option>
          </select>
        </div>

        <div class="consent">
          <input type="checkbox" id="consent"> 
          <label for="consent">I agree to receive news</label>
        </div>

      </form>
    </div>

    <!-- PAYMENT -->
    <div class="payment-methods">
      <h2>Payment Methods</h2>

      <div class="terms-area">
        <input type="checkbox" id="agree-terms">
        <label>I agree with <a href="#">Terms</a> & <a href="#">Cancellation Policy</a></label>
      </div>

      <div class="cancel-box">
        <h3>Cancellation Policy</h3>
        <p>Free cancellation up to 48 hours before arrival.</p>
      </div>  

      <h3>Payment Options</h3>

      <label class="payment-choice">
          <input type="radio" name="payment" value="counter" checked> 💵 Pay at counter
      </label>

      <label class="payment-choice">
          <input type="radio" name="payment" value="vnpay"> 🌐 VNPay (Pay 20% deposit)
      </label>


      <div class="book-box">
        <p class="price-total" id="totalDisplay">0 ₫</p>
        <button class="btn-book" id="bookNow">BOOK</button>
      </div>
    </div>

</div>

<!-- ================= BILL FLOATING ================= -->
<div class="bill-bubble" id="billBubble">🧾 My Bill</div>

<div class="bill" id="billBox">
    <h3>My Booking Bill</h3>
    <div id="bill-items"></div>
    <p id="roomPrice"></p>
    <p id="servicesPrice"></p>
    <p id="totalPrice"></p>
</div>

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
        <p>Hotline: +84 94271</p>
        <p>Location: Quy Nhon</p>
    </div>
</footer>

<!-- ================= JS ================= -->
<script>
function toggleMenu(){
  document.getElementById("mySidebar").classList.toggle("active");
}

// Floating bill toggle
document.getElementById("billBubble").onclick = () => {
    document.getElementById("billBox").classList.toggle("show");
};


// ---------------- LOAD SESSION DATA ----------------
let checkin  = sessionStorage.getItem("checkin")  || "";
let checkout = sessionStorage.getItem("checkout") || "";

document.getElementById("checkin").value = checkin;
document.getElementById("checkout").value = checkout;

let rooms    = JSON.parse(sessionStorage.getItem("selectedRooms"))    || [];
let services = JSON.parse(sessionStorage.getItem("selectedServices")) || [];

let roomTotal = Number(sessionStorage.getItem("totalPriceRooms")) || 0;


// ---------------- BILL RENDER ----------------
function renderBill(){
    let html = "";
    let serviceTotal = 0;

    rooms.forEach(r=>{
        html += `
            <p>• ${r.roomName} × ${r.qty}
            <span style="float:right">${(r.qty*r.price).toLocaleString()} ₫</span>
            </p>`;
    });

    services.forEach(s=>{
        let t = s.qty * s.price;
        serviceTotal += t;

        html += `
        <p>${s.name} × ${s.qty}
        <span style="float:right">${t.toLocaleString()} ₫</span></p>`;
    });

    document.getElementById("bill-items").innerHTML = html;
    document.getElementById("roomPrice").innerHTML    = "<b>Rooms:</b> "+roomTotal.toLocaleString()+" ₫";
    document.getElementById("servicesPrice").innerHTML = "<b>Services:</b> "+serviceTotal.toLocaleString()+" ₫";
    document.getElementById("totalPrice").innerHTML    = "<b>Total:</b> "+(roomTotal + serviceTotal).toLocaleString()+" ₫";

    document.getElementById("totalDisplay").innerText = (roomTotal + serviceTotal).toLocaleString() + " ₫";
}
renderBill();


// ---------------- SUBMIT BOOKING ----------------
document.getElementById("bookNow").onclick = async (e) => {
    e.preventDefault();

    if (!document.getElementById("agree-terms").checked) {
        alert("Please accept Terms & Conditions");
        return;
    }

    let method = document.querySelector("input[name='payment']:checked").value;

    // gọi backend lưu booking
    let res = await fetch("save_booking.php", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({
            checkin, checkout, rooms, services
        })
    });

    let d = await res.json();

    if (!d.success) {
        alert(d.message);
        return;
    }

    // Nếu trả tại quầy
    if (method === "counter") {
        window.location.href = "hoa_don.php?ma=" + d.ma_hoa_don;
        return;
    }

    // Nếu VNPay cọc
    let total = Number(d.tong_tien);

    if (!total || isNaN(total)) {
        alert("Lỗi hệ thống: không lấy được tổng tiền!");
        return;
    }

    let deposit = Math.max(5000, Math.round(total * 0.5)); // Cọc 50%, min 5.000đ

    window.location.href =
        `vnpay_create.php?hoa_don=${d.ma_hoa_don}&amount=${deposit}`;
};

</script>

</body>
</html>
