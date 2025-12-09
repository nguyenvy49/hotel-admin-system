<?php 
include("config.php"); 
session_start();

// Nếu đã đăng nhập → không cho vào trang đăng ký
if (isset($_SESSION['khach_hang_id']) || isset($_SESSION['admin'])) {
    header("Location: trangchu.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prestige Manor - Sign Up</title>

    <link rel="stylesheet" href="../assets/css/signin.css">

    <style>
        body{
            background: #f5f5f2;
            font-family: "Roboto",sans-serif;
        }
        .signin-box{
            width: 420px;
            background: #fff;
            padding: 30px;
            margin: 60px auto;
            border-radius: 12px;
            box-shadow: 0px 4px 20px rgba(0,0,0,0.15);
        }
        h2{
            text-align:center;
            font-family: "Playfair Display",serif;
            margin-bottom: 20px;
            font-size: 28px;
        }
        input{
            width:100%;
            padding:12px;
            margin:8px 0;
            border-radius:8px;
            border:1px solid #ccc;
        }
        .btn-signin{
            width:100%;
            background:#A9A48F;
            border:none;
            padding:12px;
            border-radius:8px;
            color:#fff;
            font-weight:bold;
            cursor:pointer;
            margin-top:15px;
        }
        .btn-signin:hover{
            background:#8d8874;
        }
        .alert{padding:10px;border-radius:6px;margin-bottom:10px;}
        .alert-danger{background:#f8d7da;color:#721c24;}
        .alert-success{background:#d4edda;color:#155724;}
        .password-container{position:relative;}
        .showpass{
            margin-top:5px;
            display:flex;
            gap:5px;
            align-items:center;
        }
    </style>
</head>

<body>

<div class="signin-box">
    <h2>Create an Account</h2>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {

        // Lấy dữ liệu
        $ho = trim($_POST['firstname']);
        $ten = trim($_POST['name']);
        $sdt = trim($_POST['phone']);
        $ngay_sinh = $_POST['date'];
        $email = trim($_POST['email']);
        $password_raw = $_POST['password'];

        $error = '';

        // Validate họ tên
        if (!preg_match("/^[\p{L}\s]+$/u", $ho) || !preg_match("/^[\p{L}\s]+$/u", $ten)) {
            $error = "Họ và tên chỉ được chứa chữ cái.";
        }

        // Validate email
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Email không hợp lệ.";
        }

        // Validate ngày sinh
        else {
            $dob = new DateTime($ngay_sinh);
            $today = new DateTime();
            if ($dob > $today) {
                $error = "Ngày sinh không thể ở tương lai.";
            } elseif ($today->diff($dob)->y < 13) {
                $error = "Bạn phải từ 13 tuổi trở lên để đăng ký.";
            }
        }

        // Validate mật khẩu
        if (!$error) {
            if (strlen($password_raw) < 3) {
                $error = "Mật khẩu phải tối thiểu 8 ký tự.";
            } elseif (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password_raw)) {
                $error = "Mật khẩu phải chứa ít nhất 1 ký tự đặc biệt.";
            }
        }

        // Validate số điện thoại
        if (!$error) {
            if (!preg_match('/^[0-9]{9,11}$/', $sdt)) {
                $error = "Số điện thoại phải gồm 9–11 chữ số.";
            }
        }

        // Check email tồn tại
        if (!$error) {
            $stmt = $conn->prepare("SELECT * FROM khach_hang WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $error = "Email đã được sử dụng.";
            }
        }

        // Nếu có lỗi
        if ($error) {
            echo "<div class='alert alert-danger'>$error</div>";
        } 
        else {
            // Hash password
            $mat_khau = password_hash($password_raw, PASSWORD_DEFAULT);

            // INSERT bằng prepare → chống SQL Injection
            $stmt = $conn->prepare("INSERT INTO khach_hang (ho,ten,sdt,ngay_sinh,email,mat_khau) VALUES (?,?,?,?,?,?)");
            $stmt->bind_param("ssssss", $ho, $ten, $sdt, $ngay_sinh, $email, $mat_khau);

            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Đăng ký thành công! Đang chuyển hướng...</div>";
                echo "<script>setTimeout(()=>{ window.location.href='login.php'; },1500);</script>";
            } else {
                echo "<div class='alert alert-danger'>Lỗi hệ thống: ".$conn->error."</div>";
            }
        }
    }
    ?>

    <form method="POST" action="">
        <input type="text" name="firstname" placeholder="Họ*" 
               value="<?= $_POST['firstname'] ?? '' ?>" required>

        <input type="text" name="name" placeholder="Tên*" 
               value="<?= $_POST['name'] ?? '' ?>" required>

        <input type="text" name="phone" placeholder="Số điện thoại*" 
               value="<?= $_POST['phone'] ?? '' ?>" required>

        <input type="date" name="date" 
               value="<?= $_POST['date'] ?? '' ?>" required>

        <input type="email" name="email" placeholder="Email*" 
               value="<?= $_POST['email'] ?? '' ?>" required>

        <div class="password-container">
            <input type="password" name="password" id="password" placeholder="Mật khẩu*" required>
        </div>

        <div class="showpass">
            <input type="checkbox" onclick="togglePassword()"> Hiện mật khẩu
        </div>

        <button type="submit" name="register" class="btn-signin">Sign Up</button>
    </form>

    <p>Already have an account? <a href="login.php">Log in</a></p>
</div>

<script>
function togglePassword() {
    let pwd = document.getElementById("password");
    pwd.type = (pwd.type === "password") ? "text" : "password";
}
</script>

</body>
</html>
