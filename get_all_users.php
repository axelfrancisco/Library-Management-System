<?php
require_once __DIR__ . '/_bootstrap.php';
require_admin();

$sql = "
    SELECT
        u.user_id,
        u.full_name,
        u.email,
        u.student_id,
        u.course,
        u.year_level,
        u.college,
        u.user_category,
        u.is_employee,
        COALESCE(GROUP_CONCAT(DISTINCT ur.role_key ORDER BY ur.role_key SEPARATOR ', '), 'user') AS roles,
        MAX(vl.login_at) AS last_login,
        COUNT(vl.log_id) AS total_logins,
        MAX(CASE WHEN bu.is_active = 1 THEN 1 ELSE 0 END) AS is_blocked,
        MAX(CASE WHEN bu.is_active = 1 THEN bu.reason ELSE NULL END) AS blocked_reason
    FROM users u
    LEFT JOIN user_roles ur ON ur.user_id = u.user_id
    LEFT JOIN visitor_logs vl ON vl.user_id = u.user_id
    LEFT JOIN blocked_users bu ON bu.user_id = u.user_id
    GROUP BY u.user_id
    ORDER BY u.user_id DESC
";

$result = $conn->query($sql);
$users = [];
while ($result && ($row = $result->fetch_assoc())) {
    $row['is_employee'] = (int)$row['is_employee'];
    $row['is_blocked'] = (int)$row['is_blocked'];
    $users[] = $row;
}

json_response(['success' => true, 'users' => $users]);
?>
