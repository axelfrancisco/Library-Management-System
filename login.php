<?php
require_once __DIR__ . '/_bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Invalid request method.'], 405);
}

$data = get_json_input();

$email = trim((string)($data['email'] ?? ''));
$password = trim((string)($data['password'] ?? ''));

if ($email === '') {
    json_response(['success' => false, 'message' => 'Email is required.'], 422);
}

if ($password === '') {
    json_response(['success' => false, 'message' => 'Password is required.'], 422);
}

$stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    json_response(['success' => false, 'message' => 'User not found.'], 401);
}

if (!password_verify($password, $user['password'])) {
    json_response(['success' => false, 'message' => 'Invalid password.'], 401);
}

$blocked = is_user_blocked($conn, (int)$user['user_id']);
if ($blocked['blocked']) {
    json_response([
        'success' => false,
        'message' => 'Your account has been blocked.',
        'blocked' => $blocked
    ], 403);
}

$_SESSION['auth_user_id'] = $user['user_id'];
$_SESSION['auth_user_name'] = $user['full_name'];
$_SESSION['auth_user_email'] = $user['email'];
$_SESSION['current_role'] = 'user';
$_SESSION['user_id'] = $user['user_id'];
$_SESSION['user_name'] = $user['full_name'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['student_id'] = $user['student_id'];

json_response([
    'success' => true,
    'message' => 'Login successful.',
    'redirect' => '../Users/Dashboard/dashboard.php'
]);
?>
