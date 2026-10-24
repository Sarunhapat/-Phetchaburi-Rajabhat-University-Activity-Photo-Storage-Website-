<?php
session_start();
include 'config.php';

if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "user") {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['activity_id'])) {
    echo "ไม่พบกิจกรรมที่เลือก";
    exit;
}

$activity_id = $_GET['activity_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_FILES["images"]["tmp_name"] as $index => $tmpName) {
        $path = "uploads/" . uniqid() . "_" . basename($_FILES["images"]["name"][$index]);
        move_uploaded_file($tmpName, $path);

        $stmt = $pdo->prepare("INSERT INTO activity_images (activity_id, image_path, user_id) VALUES (?, ?, ?)");
        $stmt->execute([$activity_id, $path, $_SESSION["user"]["id"]]);
    }

    header("Location: user_dashboard.php");
    exit;
}

// ดึงรายละเอียดกิจกรรม
$stmt = $pdo->prepare("SELECT * FROM activities WHERE id = ?");
$stmt->execute([$activity_id]);
$activity = $stmt->fetch();

if (!$activity) {
    echo "ไม่พบกิจกรรมในระบบ";
    exit;
}

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มรูปกิจกรรม</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="upload-container">
        <div class="upload-box">
        <h2>📅 รายละเอียดกิจกรรม</h2>
            <div style="margin-bottom: 20px; padding: 10px; background-color: #f0f0f0; border-radius: 8px;">
            <strong>รายละเอียด:</strong> <?= nl2br(htmlspecialchars($activity["description"])) ?>
        </div>
            <h2>📤 เพิ่มรูปภาพในกิจกรรม</h2>
            <form method="POST" enctype="multipart/form-data">
                <label>เลือกรูปภาพ</label>
                <input type="file" name="images[]" multiple required>
                <button type="submit">อัปโหลด</button>
            </form>
            <p style="margin-top:15px;"><a href="user_dashboard.php">← กลับ</a></p>
        </div>
    </div>
</body>
</html>
