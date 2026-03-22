<?php
require_once __DIR__ . '/_bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Invalid request method.'], 405);
}

$pending = $_SESSION['pending_google_user'] ?? null;
if (!$pending || !is_array($pending)) {
    json_response(['success' => false, 'message' => 'Google verification session expired. Please sign in again.'], 401);
}

$data = get_json_input();
$selectedRole = trim((string)($data['selectedRole'] ?? 'user'));
$userCategory = trim((string)($data['userCategory'] ?? ''));
$reason = trim((string)($data['reason'] ?? ''));
$college = trim((string)($data['college'] ?? ''));
$isEmployee = !empty($data['isEmployee']) ? 1 : 0;

$allowedRoles = $pending['roles'] ?? ['user'];
if (!in_array($selectedRole, $allowedRoles, true)) {
    json_response(['success' => false, 'message' => 'Selected role is not allowed for this account.'], 403);
}

if ($selectedRole === 'user') {
    if ($userCategory === '' || $reason === '') {
        json_response(['success' => false, 'message' => 'Category and reason are required.'], 422);
    }

    if (($pending['blocked']['blocked'] ?? false) === true) {
        json_response([
            'success' => false,
            'message' => 'This user is currently blocked.',
            'blocked' => $pending['blocked']
        ], 403);
    }
}

$userId = (int)$pending['user_id'];
$email = (string)$pending['email'];
$name = (string)$pending['full_name'];
$avatar = (string)($pending['avatar'] ?? '');

$stmt = $conn->prepare('UPDATE users SET full_name = ?, email = ?, profile_image = ?, college = ?, user_category = ?, is_employee = ? WHERE user_id = ?');
$stmt->bind_param('sssssii', $name, $email, $avatar, $college, $userCategory, $isEmployee, $userId);
$stmt->execute();
$stmt->close();

$logStatus = ($selectedRole === 'user' && ($pending['blocked']['blocked'] ?? false)) ? 'blocked' : 'allowed';
$blockedReason = ($pending['blocked']['reason'] ?? null);
$stmt = $conn->prepare('INSERT INTO visitor_logs (user_id, google_email, full_name, selected_role, user_category, reason, college, is_employee, status, blocked_reason, login_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())');
$stmt->bind_param('issssssiss', $userId, $email, $name, $selectedRole, $userCategory, $reason, $college, $isEmployee, $logStatus, $blockedReason);
$stmt->execute();
$logId = (int)$stmt->insert_id;
$stmt->close();

$_SESSION['auth_user_id'] = $userId;
$_SESSION['auth_user_name'] = $name;
$_SESSION['auth_user_email'] = $email;
$_SESSION['auth_avatar'] = $avatar;
$_SESSION['current_role'] = $selectedRole;
$_SESSION['user_id'] = $selectedRole === 'user' ? $userId : null;
$_SESSION['admin_id'] = $selectedRole === 'admin' ? $userId : null;
$_SESSION['user_name'] = $name;
$_SESSION['admin_name'] = $name;
$_SESSION['visitor_log_id'] = $logId;
unset($_SESSION['pending_google_user']);

json_response([
    'success' => true,
    'message' => 'Login completed successfully.',
    'redirect' => $selectedRole === 'admin' ? '../../Admin/Dashboard/dashboard.php' : '../Dashboard/dashboard.php'
]);
?>
