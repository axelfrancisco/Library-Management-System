<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/../config/db.php';

function json_response(array $data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function get_json_input(): array {
    $raw = file_get_contents('php://input');
    if (!$raw) return [];
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function require_login(): void {
    if (!isset($_SESSION['auth_user_id'])) {
        json_response(['success' => false, 'message' => 'Unauthorized'], 401);
    }
}

function require_admin(): void {
    if (!isset($_SESSION['current_role']) || $_SESSION['current_role'] !== 'admin') {
        json_response(['success' => false, 'message' => 'Admin access required'], 403);
    }
}

function h(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function current_user_id(): int {
    return (int)($_SESSION['auth_user_id'] ?? 0);
}

function fetch_user_roles(mysqli $conn, int $userId): array {
    $roles = [];
    $stmt = $conn->prepare('SELECT role_key FROM user_roles WHERE user_id = ?');
    if ($stmt) {
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $roles[] = $row['role_key'];
        }
        $stmt->close();
    }
    return $roles;
}

function is_user_blocked(mysqli $conn, int $userId): array {
    $stmt = $conn->prepare("SELECT reason, created_at FROM blocked_users WHERE user_id = ? AND is_active = 1 ORDER BY created_at DESC LIMIT 1");
    if (!$stmt) {
        return ['blocked' => false, 'reason' => null, 'created_at' => null];
    }
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    if (!$row) {
        return ['blocked' => false, 'reason' => null, 'created_at' => null];
    }
    return ['blocked' => true, 'reason' => $row['reason'], 'created_at' => $row['created_at']];
}
?>
