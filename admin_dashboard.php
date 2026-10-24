<?php
session_start();
include 'config.php';
if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "admin") {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            margin: 0;
            font-family: 'Noto Sans Thai', sans-serif;
            background-color: #f0f2f5;
        }

        .topbar {
            background-color: #1c1f2e;
            color: white;
            padding: 10px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .topbar-left {
            display: flex;
            align-items: center;
        }

        .topbar-left img {
            height: 100px;
            margin-right: 20px;
        }

        .topbar-left h1 {
            display: block;
        font-size: 2em;
        margin-block-start: 0.67em;
        margin-block-end: 0.67em;
        margin-inline-start: 0px;
        margin-inline-end: 0px;
        font-weight: bold;
        unicode-bidi: isolate;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .username {
            color: #ccc;
        }

        .logout-btn {
            background: #ff4d4f;
            border: none;
            padding: 6px 14px;
            border-radius: 4px;
            color: white;
            cursor: pointer;
        }

        .dashboard-container {
            display: flex;
            height: calc(100vh - 70px);
        }

        .sidebar {
            width: 300px;
            background-color: #1c1f2e;
            color: white;
            padding: 20px;
        }

        .sidebar h2 {
            color: #00d9ff;
        }

        .sidebar a {
            display: block;
            padding: 10px;
            color: white;
            text-decoration: none;
            margin: 10px 0;
            border-left: 4px solid transparent;
        }

        .sidebar a.active,
        .sidebar a:hover {
            background-color: #00bcd4;
            border-left: 4px solid #fff;
        }

        .main-content {
            flex-grow: 1;
            padding: 30px;
            overflow-y: auto;
        }
    </style>
</head>
<body>

    <div class="topbar">
        <div class="topbar-left">
            <img src="uploads/ราชภัฏเพชรบุรี.png" alt="logo">
            <h1>ระบบจัดการกิจกรรมมหาวิทยาลัย</h1>
        </div>
        <div class="topbar-right">
            <span class="username">ผู้ใช้งาน: <strong><?= htmlspecialchars($_SESSION["user"]["username"] ?? 'Admin') ?></strong></span>
            <a href="logout.php"><button class="logout-btn">ออกจากระบบ</button></a>
        </div>
    </div>

    <div class="dashboard-container">
        <div class="sidebar">
            <h2>Admin Panal</h2>
            <a href="#" class="menu-link active" data-page="manage_users.php">👤 จัดการผู้ใช้</a>
            <a href="#" class="menu-link" data-page="manage_news.php">📰 จัดการข่าวสาร</a>
            <a href="#" class="menu-link" data-page="manage_activities.php">📅 จัดการกิจกรรม</a>
        </div>

        <div class="main-content">
            <div id="content-area">
                <!-- เนื้อหาจะถูกโหลดตรงนี้ -->
            </div>
        </div>
    </div>

<script src="assets/js/dashboard.js"></script>
</body>
</html>
