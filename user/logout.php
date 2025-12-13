<?php
session_start();

/* =============================
   XỬ LÝ KHI BẤM ĐĂNG XUẤT
   ============================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Xóa toàn bộ session
    $_SESSION = [];
    session_destroy();

    // Xóa cookie session nếu có
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    // Quay về trang chủ CHƯA đăng nhập
    header("Location: index.php");
    exit();
}

/* =============================
   NẾU CHƯA ĐĂNG NHẬP → VỀ TRANG CHỦ
   ============================= */
if (!isset($_SESSION['khach_hang_id']) && !isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Prestige Manor - Logout</title>

  <link rel="stylesheet" href="../assets/css/login.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

  <style>
    .login-box {
      width: 380px;
      margin: 120px auto;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0px 4px 20px rgba(0,0,0,0.15);
      text-align: center;
    }

    h2 {
      font-family: "Playfair Display", serif;
      font-size: 26px;
      margin-bottom: 15px;
    }

    .logout-msg {
      font-size: 15px;
      color: #555;
      margin-bottom: 25px;
    }

    .btn-group {
      display: flex;
      gap: 15px;
    }

    .btn-logout {
      flex: 1;
      padding: 12px;
      background: #A9A48F;
      border: none;
      border-radius: 8px;
      color: #fff;
      font-weight: bold;
      cursor: pointer;
    }

    .btn-logout:hover {
      background: #8d8874;
    }

    .btn-cancel {
      flex: 1;
      padding: 12px;
      background: #eee;
      border-radius: 8px;
      color: #333;
      text-decoration: none;
      font-weight: bold;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .btn-cancel:hover {
      background: #ddd;
    }
  </style>
</head>

<body>

<div class="login-box">
    <h2>Sign out</h2>
    <p class="logout-msg">
        Bạn có chắc chắn muốn đăng xuất khỏi hệ thống không?
    </p>

    <div class="btn-group">
        <!-- POST về CHÍNH FILE NÀY -->
        <form method="POST" style="flex:1;">
            <button type="submit" class="btn-logout">Đăng xuất</button>
        </form>

        <a href="index.php" class="btn-cancel">Hủy</a>
    </div>
</div>

</body>
</html>
