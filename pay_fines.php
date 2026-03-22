<?php
require_once __DIR__ . '/_bootstrap.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Invalid request method.'], 405);
}

$data = get_json_input();

if (empty($data['fine_id'])) {
    json_response(['success' => false, 'message' => 'Fine ID is required.'], 422);
}

$fineId = (int)$data['fine_id'];
$userId = current_user_id();

$stmt = $conn->prepare("SELECT * FROM fines WHERE fine_id = ? AND user_id = ? LIMIT 1");
$stmt->bind_param('ii', $fineId, $userId);
$stmt->execute();
$fine = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$fine) {
    json_response(['success' => false, 'message' => 'Fine not found.'], 404);
}

if ($fine['payment_status'] === 'paid') {
    json_response(['success' => false, 'message' => 'Fine is already paid.'], 409);
}

$stmt = $conn->prepare("UPDATE fines SET payment_status = 'paid' WHERE fine_id = ?");
$stmt->bind_param('i', $fineId);

if ($stmt->execute()) {
    json_response(['success' => true, 'message' => 'Fine paid successfully.']);
} else {
    json_response(['success' => false, 'message' => 'Failed to pay fine.', 'error' => $conn->error], 500);
}
$stmt->close();
?>
