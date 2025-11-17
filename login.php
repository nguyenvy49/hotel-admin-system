<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Login</title>
    <link rel="stylesheet" href="login.css">
</head>

<body>
   <!-- Nút mở menu -->
    <div class="menu-btn" onclick="toggleMenu()">☰</div>

    <!-- Sidebar menu -->
    <div id="mySidebar" class="sidebar">
        <a href="#">Home</a>
        <a href="#">Accommodation</a>
        <a href="book.html">Booking</a>
        <a href="#">Services</a>
        <a href="lienhe..html">Contact Us</a>
        <a href="dangnhap.html">Login</a>
        <a href="dangki.html">Signin</a>
    </div>

  <!-- Form login -->
  <div class="login-box">
    <h2>Login</h2>
    <form onsubmit="return validateForm()">
      <input type="text" id="username" placeholder="Username*" required>
      <input type="password" id="password" placeholder="Password*" required>
      <button type="submit" class="btn-login">Log in</button>
    </form>
    <p>No have an account? <a href="dangki.html">Sign in</a></p>
  </div>

  <script>
    let menuOpen = false;

    function toggleMenu() {
      const sidebar = document.getElementById("mySidebar");
      if (menuOpen) {
        sidebar.style.width = "0";
      } else {
        sidebar.style.width = "250px";
      }
      menuOpen = !menuOpen;
    }

  </script>
</body>
</html>
<?php
include("config.php");
session_start(); // Bắt đầu phiên làm việc

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Dùng Prepared Statement để bảo mật
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            // Lưu thông tin người dùng vào session
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['firstname'] = $row['firstname'];

            // Chuyển hướng sang trang chủ
            header("Location: home.php");
            exit();
        } else {
            echo "<div style='color:red; text-align:center;'>Sai mật khẩu!</div>";
        }
    } else {
        echo "<div style='color:red; text-align:center;'>Email không tồn tại!</div>";
    }
}
?>
 