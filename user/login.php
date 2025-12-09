<?php
session_start();
include("config.php");

// Nếu đã login → chuyển về trang chủ
if (isset($_SESSION['khach_hang_id']) || isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

// Lấy danh sách email từ cookie
$email_list = [];
if (isset($_COOKIE['saved_emails'])) {
    $email_list = explode(",", $_COOKIE['saved_emails']);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Prestige Manor - Login</title>

  <link rel="stylesheet" href="../assets/css/login.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

  <style>
    body {
      background: #f5f5f2;
      font-family: "Roboto", sans-serif;
    }

    .login-box {
      width: 380px;
      margin: 100px auto;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0px 4px 20px rgba(0,0,0,0.15);
      text-align: center;
    }

    h2 {
      font-family: "Playfair Display", serif;
      font-size: 28px;
      margin-bottom: 20px;
    }

    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border-radius: 8px;
      border: 1px solid #ccc;
    }

    .btn-login {
      width: 100%;
      padding: 12px;
      background: #A9A48F;
      border: none;
      border-radius: 8px;
      color: #fff;
      font-weight: bold;
      cursor: pointer;
      margin-top: 15px;
    }

    .btn-login:hover {
      background: #8d8874;
    }

    .remember-container {
      display: flex;
      justify-content: space-between;
      margin-top: 5px;
      font-size: 14px;
    }

    .message {
      text-align: center;
      color: green;
      margin-top: 10px;
      font-weight: bold;
    }
  </style>
</head>

<body>

<div class="login-box">
    <h2>Welcome Back</h2>

    <form method="POST" action="">
      
      <input type="text" name="email" placeholder="Email*" list="emailOptions" required />

      <datalist id="emailOptions">
        <?php foreach ($email_list as $e) echo "<option value='$e'></option>"; ?>
      </datalist>

      <div class="password-container">
        <input type="password" name="password" id="password" placeholder="Password*" required>
      </div>

      <div class="remember-container">
          <label>
              <input type="checkbox" onclick="togglePassword()"> Hiện mật khẩu
          </label>

          <label>
              <input type="checkbox" name="remember"> Ghi nhớ email
          </label>
      </div>

      <button type="submit" name="login" class="btn-login">Log in</button>
    </form>

    <p>Don't have an account? <a href="dangki.php">Sign up</a></p>

</div>

<script>
function togglePassword() {
    let pwd = document.getElementById("password");
    pwd.type = (pwd.type === "password") ? "text" : "password";
}
</script>

</body>
</html>

<?php
// XỬ LÝ ĐĂNG NHẬP
if (isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $remember = isset($_POST['remember']);

    // ========== 1. ADMIN ĐĂNG NHẬP ==========
    if ($email === "adminquanli@gmail.com" && $password === "2611080605$") {
        $_SESSION['admin'] = true;

        if ($remember) saveEmailToCookie($email);

        // Không echo trước header → tránh lỗi
        header("Location: dashboard_home.php");
        exit();
    }

    //KHÁCH HÀNG
    $stmt = $conn->prepare("SELECT * FROM khach_hang WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows == 0) {
        echo "<script>alert('❌ Email không tồn tại!');</script>";
        exit();
    }

    $user = $res->fetch_assoc();

    if (!password_verify($password, $user['mat_khau'])) {
        echo "<script>alert('❌ Sai mật khẩu!');</script>";
        exit();
    }

    // Gán session
    $_SESSION['khach_hang_id'] = $user['ma_khach_hang'];
    $_SESSION['khach_hang_ten'] = $user['ho'] . " " . $user['ten'];
    $_SESSION['khach_hang_email'] = $user['email'];

    if ($remember) saveEmailToCookie($email);

    header("Location: index.php");
    exit();
}

// HÀM LƯU EMAIL VÀO COOKIE
function saveEmailToCookie($email) {
    global $email_list;

    if (!in_array($email, $email_list)) {
        $email_list[] = $email;
    }

    $email_string = implode(",", $email_list);
    setcookie("saved_emails", $email_string, time() + (86400 * 30), "/");
}

?>
