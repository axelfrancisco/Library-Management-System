<?php
require_once __DIR__ . '/_bootstrap.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Invalid request.'], 405);
}

$data   = get_json_input();
$userId = current_user_id();

$name    = trim((string)($data['name'] ?? ''));
$course  = trim((string)($data['course'] ?? ''));
$year    = trim((string)($data['year'] ?? ''));
$contact = trim((string)($data['contact'] ?? ''));
$address = trim((string)($data['address'] ?? ''));

$stmt = $conn->prepare("UPDATE users SET full_name=?, course=?, year_level=?, contact_number=?, address=? WHERE user_id=?");
$stmt->bind_param('sssssi', $name, $course, $year, $contact, $address, $userId);

if ($stmt->execute()) {
    json_response(['success' => true, 'message' => 'Profile updated.']);
} else {
    json_response(['success' => false, 'message' => 'Failed to update profile.', 'error' => $conn->error], 500);
}
$stmt->close();
?>
