<?php
require_once __DIR__ . '/_bootstrap.php';
require_admin();

$search = trim((string)($_GET['search'] ?? ''));
$params = [];
$types = '';
$where = '1=1';

if ($search !== '') {
    $where .= ' AND (vl.full_name LIKE ? OR vl.google_email LIKE ? OR vl.reason LIKE ? OR vl.college LIKE ?)';
    $like = '%' . $search . '%';
    $params = [$like, $like, $like, $like];
    $types = 'ssss';
}

$sql = "
    SELECT
        vl.log_id,
        vl.full_name,
        vl.google_email,
        vl.selected_role,
        vl.user_category,
        vl.reason,
        vl.college,
        vl.is_employee,
        vl.status,
        vl.blocked_reason,
        vl.login_at,
        vl.logout_at
    FROM visitor_logs vl
    WHERE $where
    ORDER BY vl.login_at DESC
    LIMIT 500
";

$stmt = $conn->prepare($sql);
if ($types !== '') {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$logs = [];
while ($row = $result->fetch_assoc()) {
    $row['is_employee'] = (int)$row['is_employee'];
    $logs[] = $row;
}
$stmt->close();

json_response(['success' => true, 'logs' => $logs]);
?>
