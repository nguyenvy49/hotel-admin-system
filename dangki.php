<?php include("config.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Signin</title>
    <link rel="stylesheet" href="signin.css">
    <style>
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
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
    </style>
</head>
<body>
    <div class="menu-btn" onclick="toggleMenu()">☰</div>
    <div id="mySidebar" class="sidebar">
        <a href="#">Home</a>
        <a href="#">Accommodation</a>
        <a href="book.html">Booking</a>
        <a href="#">Services</a>
        <a href="lienhe.html">Contact Us</a>
        <a href="dangnhap.html">Login</a>
        <a href="dangki.html">Signin</a>
    </div>
    <div class="signin-box">
        <h2>Sign in</h2>
        <?php if (isset($_POST['register'])): ?>
            <?php
            $password_raw = $_POST['password'];
            $error = '';
            if (strlen($password_raw) < 8) {
                $error = "Mật khẩu phải đủ 8 ký tự";
            } elseif (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password_raw)) {
                $error = "Mật khẩu phải chứa ít nhất 1 ký tự đặc biệt (ví dụ: ! @ # $ % ^ & * ( ) , . ?)";
            }
            if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php else: ?>
                <?php
                $firstname = $_POST['firstname'];
                $name = $_POST['name'];
                $phone = $_POST['phone'];
                $dob = $_POST['date'];
                $email = $_POST['email'];
                $password = password_hash($password_raw, PASSWORD_DEFAULT);
                $sql = "INSERT INTO users (firstname, name, phone, dob, email, password) VALUES ('$firstname', '$name', '$phone', '$dob', '$email', '$password')";
                if ($conn->query($sql) === TRUE) {
                    echo '<div class="alert alert-success">Đăng ký thành công! Đang chuyển hướng...</div>';
                    echo '<script>setTimeout(function(){ window.location.href = "index.php"; }, 1500);</script>';
                } else {
                    echo '<div class="alert alert-danger">Lỗi: ' . $conn->error . '</div>';
                }
                ?>
            <?php endif; ?>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-grid">
                <input type="text" name="firstname" id="firstname" placeholder="Firstname*" required>
                <input type="text" name="name" id="name" placeholder="Name*" required>
                <input type="text" name="phone" id="phone" placeholder="Phone Number*" required>
                <input type="date" name="date" id="date" placeholder="Date of birth*" required>
                <input type="email" name="email" id="email" placeholder="Email*" required>
                <div class="password-container">
                    <input type="password" name="password" id="password" placeholder="Password*" required>
                    <span class="toggle-password" onclick="togglePassword()">👁️</span>
                </div>
            </div>
            <button type="submit" name="register" class="btn-signin">Sign in</button>
        </form>
        <p>Have an account? <a href="dangnhap.html">Login</a></p>
    </div>
    <script>
        let menuOpen = false;
        function toggleMenu() {
            const sidebar = document.getElementById("mySidebar");
            sidebar.style.width = menuOpen ? "0" : "250px";
            menuOpen = !menuOpen;
        }
        function togglePassword() {
            const passwordInput = document.getElementById("password");
            const toggleIcon = document.querySelector(".toggle-password");
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                toggleIcon.textContent = "👁️‍🗨️";
            } else {
                passwordInput.type = "password";
                toggleIcon.textContent = "👁️";
            }
        }
    </script>
</body>
</html>