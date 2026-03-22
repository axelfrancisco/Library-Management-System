<?php
require_once __DIR__ . '/_bootstrap.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Invalid request.'], 405);
}

$data = get_json_input();
$fineId = (int)($data['fine_id'] ?? 0);

if ($fineId <= 0) {
    json_response(['success' => false, 'message' => 'Invalid fine ID.'], 422);
}

$stmt = $conn->prepare("UPDATE fines SET payment_status = 'paid' WHERE fine_id = ?");
$stmt->bind_param('i', $fineId);

if ($stmt->execute()) {
    json_response(['success' => true, 'message' => 'Fine marked as paid.']);
} else {
    json_response(['success' => false, 'message' => 'Failed to update fine.', 'error' => $conn->error], 500);
}
$stmt->close();
?>
