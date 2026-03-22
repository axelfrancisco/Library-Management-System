<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!empty($_SESSION['visitor_log_id'])) {
    $logId = (int)$_SESSION['visitor_log_id'];
    $stmt = $conn->prepare('UPDATE visitor_logs SET logout_at = NOW() WHERE log_id = ?');
    if ($stmt) {
        $stmt->bind_param('i', $logId);
        $stmt->execute();
        $stmt->close();
    }
}

session_unset();
session_destroy();
header('Location: ../../index.html');
exit;
?>
