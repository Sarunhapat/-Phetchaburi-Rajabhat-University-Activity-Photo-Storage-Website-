<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user"] = $user;
        if ($user['role'] === 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: user_dashboard.php");
        }
        exit;
    } else {
        $error = "อีเมลหรือรหัสผ่านไม่ถูกต้อง";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เข้าสู่ระบบ - ระบบกิจกรรมมหาวิทยาลัย</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="header">
                <img src="uploads/ราชภัฏเพชรบุรี.png" alt="โลโก้มหาวิทยาลัย" class="logo">
                <h2>เข้าสู่ระบบ</h2>
                <p>ระบบจัดการกิจกรรมมหาวิทยาลัย</p>
            </div>
            <form method="POST">
                <input type="email" name="email" placeholder="อีเมล" required>
                <input type="password" name="password" placeholder="รหัสผ่าน" required>
                <button type="submit">เข้าสู่ระบบ</button>
                <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            </form>
            <p class="register-link">ยังไม่มีบัญชี? <a href="register.php">สมัครสมาชิก</a></p>
        </div>
    </div>
</body>
</html>
