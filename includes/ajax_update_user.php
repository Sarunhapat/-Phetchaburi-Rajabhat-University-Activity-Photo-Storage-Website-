<?php
include_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

$id = $_POST['id'] ?? '';
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$role = $_POST['role'] ?? '';

if (!$id || !$name || !$email || !$role) {
    echo json_encode(['status' => 'error', 'message' => 'ข้อมูลไม่ครบ']);
    exit;
}

// ตรวจสอบว่า email ซ้ำกับคนอื่นหรือไม่
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
$stmt->execute([$email, $id]);
if ($stmt->fetch()) {
    echo json_encode(['status' => 'error', 'message' => 'อีเมลนี้ถูกใช้แล้วโดยผู้ใช้อื่น']);
    exit;
}

// อัปเดตข้อมูล
$stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?");
$stmt->execute([$name, $email, $role, $id]);

echo json_encode(['status' => 'success']);
