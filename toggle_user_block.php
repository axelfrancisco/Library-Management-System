<?php
require_once __DIR__ . '/_bootstrap.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Invalid request method.'], 405);
}

$data = get_json_input();
$userId = (int)($data['userId'] ?? 0);
$action = trim((string)($data['action'] ?? ''));
$reason = trim((string)($data['reason'] ?? ''));
$adminId = current_user_id();

if ($userId <= 0 || !in_array($action, ['block', 'unblock'], true)) {
    json_response(['success' => false, 'message' => 'Invalid payload.'], 422);
}

if ($action === 'block') {
    if ($reason === '') {
        json_response(['success' => false, 'message' => 'Block reason is required.'], 422);
    }
    $stmt = $conn->prepare('UPDATE blocked_users SET is_active = 0, updated_at = NOW() WHERE user_id = ?');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare('INSERT INTO blocked_users (user_id, blocked_by_user_id, reason, is_active, created_at, updated_at) VALUES (?, ?, ?, 1, NOW(), NOW())');
    $stmt->bind_param('iis', $userId, $adminId, $reason);
    $stmt->execute();
    $stmt->close();

    json_response(['success' => true, 'message' => 'User blocked successfully.']);
}

$stmt = $conn->prepare('UPDATE blocked_users SET is_active = 0, updated_at = NOW() WHERE user_id = ?');
$stmt->bind_param('i', $userId);
$stmt->execute();
$stmt->close();

json_response(['success' => true, 'message' => 'User unblocked successfully.']);
?>
