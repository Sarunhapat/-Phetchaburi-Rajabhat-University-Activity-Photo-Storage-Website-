<?php
include_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

$id = $_POST['id'] ?? '';
if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'ไม่มี ID']);
    exit;
}

// ลบผู้ใช้
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$id]);

echo json_encode(['status' => 'success']);
