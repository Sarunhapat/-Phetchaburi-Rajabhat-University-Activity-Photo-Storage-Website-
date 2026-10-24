<?php
session_start();
include 'config.php';

if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "user") {
    header("Location: login.php");
    exit;
}

$user = $_SESSION["user"];

$stmt = $pdo->query("SELECT * FROM activities ORDER BY activity_date DESC");
$activities = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT ai.*, a.title FROM activity_images ai
    JOIN activities a ON ai.activity_id = a.id
    WHERE ai.user_id = ?
    ORDER BY a.activity_date DESC");
$stmt->execute([$user["id"]]);
$images = $stmt->fetchAll();

$grouped_images = [];
foreach ($images as $img) {
    $grouped_images[$img["title"]][] = $img["image_path"];
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แดชบอร์ดผู้ใช้งาน</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Noto Sans Thai', sans-serif;
            background-color: #f0f4f8;
        }

        .topbar {
            background-color: #003366;
            color: white;
            padding: 15px 30px;
            font-size: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .topbar a {
            color: #ffc107;
            text-decoration: none;
            font-size: 16px;
        }

        .main-content {
            max-width: 960px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        h2, h3 {
            color: #003366;
            margin-top: 0;
        }

        ul {
            padding-left: 20px;
        }

        .gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 15px;
            margin-top: 10px;
        }

        .gallery img {
            width: 100%;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        .section {
            margin-top: 40px;
        }

        @media (max-width: 600px) {
            .topbar {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>

<div class="main-content">
    <h2>📌 เลือกกิจกรรมเพื่อเพิ่มรูปภาพ</h2>
    <ul>
        <?php foreach ($activities as $act): ?>
            <li>
                <?= htmlspecialchars($act['title']) ?> (<?= $act['activity_date'] ?>) |
                <a href="upload_activity.php?activity_id=<?= $act['id'] ?>">เพิ่มรูปภาพ</a>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="section">
        <h3>🖼 รูปภาพของฉัน</h3>
        <?php if (empty($grouped_images)): ?>
            <p>ยังไม่มีการอัปโหลดรูปภาพ</p>
        <?php else: ?>
            <?php foreach ($grouped_images as $title => $imgs): ?>
                <h4><?= htmlspecialchars($title) ?></h4>
                <div class="gallery">
                    <?php foreach ($imgs as $img): ?>
                        <img src="<?= htmlspecialchars($img) ?>" alt="ภาพกิจกรรม">
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
