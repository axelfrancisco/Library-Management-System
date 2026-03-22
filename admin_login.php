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
    json_response(['success' => false, 'message' => 'Admin account not found.'], 401);
}

if ($user['role_name'] !== 'Admin') {
    json_response(['success' => false, 'message' => 'Access denied. Not an admin account.'], 403);
}

if (!password_verify($password, $user['password'])) {
    json_response(['success' => false, 'message' => 'Invalid password.'], 401);
}

$_SESSION['auth_user_id'] = $user['user_id'];
$_SESSION['auth_user_name'] = $user['full_name'];
$_SESSION['auth_user_email'] = $user['email'];
$_SESSION['current_role'] = 'admin';
$_SESSION['admin_id'] = $user['user_id'];
$_SESSION['admin_name'] = $user['full_name'];
$_SESSION['admin_email'] = $user['email'];

json_response(['success' => true, 'message' => 'Admin login successful.']);
?>
