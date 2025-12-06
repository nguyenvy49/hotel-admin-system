<?php include("config.php"); ?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Signin</title>
    <link rel="stylesheet" href="signin.css">
    <style>
        .alert { 
            padding: 10px; 
            margin-bottom: 15px; 
            border-radius: 5px; 
        }
        .alert-success { background-color: #d4edda; color: #155724; }
        .alert-danger { background-color: #f8d7da; color: #721c24; }

        .password-container { position: relative; }
        .toggle-password { 
            position: absolute; 
            right: 10px; 
            top: 50%; 
            transform: translateY(-50%); 
            cursor: pointer; 
        }

        input:invalid {
            border: 1px solid red;
        }
    </style>
</head>
<body>
    <!-- Sidebar + Toggle menu -->
<button class="menu-toggle" onclick="toggleMenu()">☰</button>

<div id="mySidebar" class="sidebar">
    <a href="trangchu2.php">HOME</a>
    <a href="gioithieuphong.php">ACCOMMODATION</a>
    <a href="dat_phong.php">BOOKING</a>
    <a href="gioithieudichvu.php">SERVICES</a>
    <a href="lienhe.php">CONTACT US</a>
    <a href="login.php">LOGIN</a>
    <a href="dangki.php">SIGN IN</a>
  </div>

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
    <div class="signin-box">
        <h2>Sign in</h2>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
            $ho = trim($_POST['firstname']);
            $ten = trim($_POST['name']);
            $sdt = trim($_POST['phone']);
            $ngay_sinh = $_POST['date'];
            $email = trim($_POST['email']);
            $password_raw = $_POST['password'];

            $error = '';

            // 🧩 Kiểm tra họ tên chỉ có chữ
            if (!preg_match("/^[\p{L}\s]+$/u", $ho) || !preg_match("/^[\p{L}\s]+$/u", $ten)) {
                $error = "Họ và tên chỉ được chứa ký tự chữ cái (không có số hoặc ký tự đặc biệt).";
            }
            // 📧 Kiểm tra email hợp lệ
            elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Email không hợp lệ. Vui lòng nhập đúng định dạng (ví dụ: ten@gmail.com).";
            }
            // 📅 Kiểm tra ngày sinh hợp lý (>=13 tuổi và không vượt quá hiện tại)
            else {
                $dob = new DateTime($ngay_sinh);
                $today = new DateTime();
                $age = $today->diff($dob)->y;
                if ($dob > $today) {
                    $error = "Ngày sinh không thể ở tương lai.";
                } elseif ($age < 13) {
                    $error = "Bạn phải từ 13 tuổi trở lên mới được đăng ký.";
                }
            }
            // 🔒 Kiểm tra mật khẩu
            if (!$error) {
                if (strlen($password_raw) < 8) {
                    $error = "Mật khẩu phải đủ ít nhất 8 ký tự.";
                } elseif (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password_raw)) {
                    $error = "Mật khẩu phải chứa ít nhất 1 ký tự đặc biệt (ví dụ: ! @ # $ % ^ & * ( ) , . ?).";
                }
            }

            if ($error) {
                echo '<div class="alert alert-danger">'.$error.'</div>';
            } else {
                $mat_khau = password_hash($password_raw, PASSWORD_DEFAULT);

                // Kiểm tra email trùng
                $check_email = "SELECT * FROM khach_hang WHERE email = '$email'";
                $result = $conn->query($check_email);

                if ($result->num_rows > 0) {
                    echo '<div class="alert alert-danger">Email này đã được đăng ký. Vui lòng dùng email khác.</div>';
                } else {
                    $sql = "INSERT INTO khach_hang (ho, ten, sdt, ngay_sinh, email, mat_khau)
                            VALUES ('$ho', '$ten', '$sdt', '$ngay_sinh', '$email', '$mat_khau')";

                    if ($conn->query($sql) === TRUE) {
                        echo '<div class="alert alert-success">Đăng ký thành công! Đang chuyển hướng...</div>';
                        echo '<script>setTimeout(function(){ window.location.href = "trangchu2.php"; }, 1500);</script>';
                    } else {
                        echo '<div class="alert alert-danger">Lỗi: '.$conn->error.'</div>';
                    }
                }
            }
        }
        ?>

        <form method="POST" action="">
            <div class="form-grid">
                <input type="text" name="firstname" placeholder="Họ*" pattern="[\p{L}\s]+" title="Chỉ được nhập chữ cái" required>
                <input type="text" name="name" placeholder="Tên*" pattern="[\p{L}\s]+" title="Chỉ được nhập chữ cái" required>
                <input type="text" name="phone" placeholder="Số điện thoại*" required pattern="[0-9]{9,11}" title="Chỉ được nhập số, từ 9–11 chữ số">
                <input type="date" name="date" required>
                <input type="email" name="email" placeholder="Email*" required>
                <div class="password-container">
                    <input type="password" name="password" id="password" placeholder="Mật khẩu*" required>
                    <label>
  <input type="checkbox" id="showPassword" onclick="togglePassword()"> Hiện mật khẩu
</label>

                </div>
            </div>
            <button type="submit" name="register" class="btn-signin">Sign in</button>
        </form>

        <p>Have an account? <a href="login.php">Login</a></p>
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
