<?php
session_start();
include("config.php");

// Lấy danh sách email đã từng đăng nhập
$email_list = [];
if (isset($_COOKIE['saved_emails'])) {
    $email_list = explode(",", $_COOKIE['saved_emails']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hotel Login</title>
  <link rel="stylesheet" href="../assets/css/login.css">
  <style>
    .password-container {
      position: relative;
      display: flex;
      align-items: center;
      gap: 5px;
    }
    .remember-container {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-top: 10px;
      font-size: 14px;
    }
    .message {
      text-align: center;
      margin-top: 10px;
      color: green;
      font-weight: bold;
    }
  </style>
</head>
<body>

  <!-- Sidebar + Toggle menu -->
<!-- <button class="menu-toggle" onclick="toggleMenu()">☰</button>

<div id="mySidebar" class="sidebar">
    <a href="trangchu2.php">HOME</a>
    <a href="gioithieuphong.php">ACCOMMODATION</a>
    <a href="dat_phong.php">BOOKING</a>
    <a href="gioithieudichvu.php">SERVICES</a>
    <a href="lienhe.php">CONTACT US</a>
    <a href="login.php">LOGIN</a>
    <a href="dangki.php">SIGN IN</a>
  </div> -->

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

<!-- Form login -->
<div class="login-box">
    <h2>Login</h2>

    <form method="POST" action="">
      <input type="text" name="email" id="email" placeholder="Email*" list="emailOptions" required>

      <!-- Datalist gợi ý email -->
      <datalist id="emailOptions">
        <?php  
        foreach ($email_list as $e) {
            echo "<option value='$e'></option>";
        }
        ?>
      </datalist>

      <div class="password-container">
        <input type="password" name="password" id="password" placeholder="Password*" required>
      </div>

      <div class="remember-container">
       <label>
  <input type="checkbox" id="showPassword" onclick="togglePassword()"> Hiện mật khẩu
</label>

        <label>
          <input type="checkbox" name="remember"> Ghi nhớ email
        </label>
      </div>

      <button type="submit" name="login" class="btn-login">Log in</button>
    </form>

    <p>No account yet? <a href="dangki.php">Sign up</a></p>
</div>
 <!-- Thay thế phần JS cuối trang bằng đoạn này -->
<script>
  // Toggle sidebar
  function toggleMenu() {
    var sb = document.getElementById("mySidebar");
    sb.classList.toggle("active");
  }

  // Toggle hiển thị mật khẩu
  function togglePassword() {
    var pwd = document.getElementById("password");
    if (pwd.type === "password") {
      pwd.type = "text";
    } else {
      pwd.type = "password";
    }
  }
</script>


</body>
</html>

<?php
// ==============================
// XỬ LÝ ĐĂNG NHẬP
// ==============================
if (isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $remember = isset($_POST['remember']);

    // ========== 1. ADMIN ==========
    if ($email === "adminquanli@gmail.com" && $password === "2611080605$") {

        $_SESSION['admin'] = true;

        // Lưu email vào cookie (không lưu mật khẩu)
        if ($remember) {
            saveEmailToCookie($email);
        }

        echo "<div class='message'>Chào mừng Admin!</div>";
        header("refresh:1;url=dashboard_home.php");
        exit();
    }

    // ========== 2. KHÁCH HÀNG ==========
    $stmt = $conn->prepare("SELECT * FROM khach_hang WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<div style='color:red;text-align:center;'>❌ Email không tồn tại!</div>";
        exit();
    }

    $row = $result->fetch_assoc();

    // Kiểm tra mật khẩu
    if (!password_verify($password, $row['mat_khau'])) {
        echo "<div style='color:red;text-align:center;'>❌ Sai mật khẩu!</div>";
        exit();
    }

    // Lưu thông tin khách hàng
    $_SESSION['khach_hang_id'] = $row['ma_khach_hang'];
    $_SESSION['khach_hang_ten'] = $row['ho'] . " " . $row['ten'];
    $_SESSION['khach_hang_email'] = $row['email'];

    // Lưu email vào cookie nếu được chọn
    if ($remember) {
        saveEmailToCookie($email);
    }

    echo "<div class='message'>Đăng nhập thành công! Chào mừng {$_SESSION['khach_hang_ten']} 🌸</div>";
    header("refresh:1.5;url=trangchu2.php");
    exit();
}

// Hàm lưu email vào cookie danh sách email
function saveEmailToCookie($email) {
    global $email_list;

    if (!in_array($email, $email_list)) {
        $email_list[] = $email;
    }

    $email_string = implode(",", $email_list);
    setcookie("saved_emails", $email_string, time() + (86400 * 30), "/");
}
?>
