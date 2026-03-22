<?php
require_once __DIR__ . '/_bootstrap.php';
require_login();

$userId = current_user_id();
$stmt = $conn->prepare('SELECT * FROM users WHERE user_id = ? LIMIT 1');
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    json_response(['success' => false, 'message' => 'Profile not found.'], 404);
}

json_response([
    'success' => true,
    'data' => [
        'name' => $user['full_name'],
        'role' => $_SESSION['current_role'] ?? 'user',
        'studentId' => $user['student_id'],
        'email' => $user['email'],
        'course' => $user['course'],
        'yearLevel' => $user['year_level'],
        'contactNumber' => $user['contact_number'],
        'address' => $user['address'],
        'image' => $user['profile_image'],
        'college' => $user['college'],
        'userCategory' => $user['user_category'],
        'isEmployee' => (int)$user['is_employee']
    ]
]);
?>
