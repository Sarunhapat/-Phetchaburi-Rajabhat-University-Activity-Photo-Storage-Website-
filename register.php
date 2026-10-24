<?php
include 'config.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    if ($stmt->execute([$name, $email, $password])) {
        header("Location: login.php");
        exit;
    } else {
        $error = "ไม่สามารถลงทะเบียนได้";
    }
}
?>
<?php include 'templates/header.php'; ?>
<div class="login-box">
    <h2>สมัครสมาชิก</h2>
    <form method="POST">
        <input type="text" name="name" placeholder="ชื่อ-สกุล" required>
        <input type="email" name="email" placeholder="อีเมล" required>
        <input type="password" name="password" placeholder="รหัสผ่าน" required>
        <button type="submit">สมัครสมาชิก</button>
    </form>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
</div>
<?php include 'templates/footer.php'; ?>
