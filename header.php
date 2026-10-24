<?php
if (!isset($_SESSION)) session_start();
include_once 'config.php';

$user = $_SESSION['user'] ?? null;
$role = $user['role'] ?? 'guest';
$username = $user['username'] ?? 'ไม่ระบุ';
?>
<div class="header">
    <div class="left">
        <img src="uploads/ราชภัฏเพชรบุรี.png" alt="Logo" class="logo">
        <span class="system-title">ระบบจัดการกิจกรรมมหาวิทยาลัย</span>
    </div>
    <div class="right">
        <div class="user-info">
            <?= ($role === 'admin') ? 'ผู้ดูแลระบบ: ' : 'ผู้ใช้งาน: ' ?>
            <strong><?= htmlspecialchars($username) ?></strong>
            <a href="logout.php" class="logout-btn">ออกจากระบบ</a>
        </div>
    </div>
</div>

<style>
.header {
    background-color: #1c1f2e;
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 30px;
    font-family: 'Noto Sans Thai', sans-serif;
    box-shadow: 0 2px 6px rgba(0,0,0,0.3);
}

.header .left {
    display: flex;
    align-items: center;
}

.header .logo {
    width: 100px;
    height: 100px;
    margin-right: 15px;
}

.header .system-title {
    font-size: 20px;
    font-weight: bold;
    color: #00d9ff;
}

.header .right {
    display: flex;
    align-items: center;
}

.user-info {
    font-size: 14px;
    color: #ccc;
    display: flex;
    align-items: center;
    gap: 15px;
}

.logout-btn {
    background-color: #ff5252;
    padding: 6px 12px;
    border-radius: 6px;
    color: white;
    text-decoration: none;
    font-weight: bold;
    transition: background 0.3s;
}

.logout-btn:hover {
    background-color: #e60000;
}
</style>
