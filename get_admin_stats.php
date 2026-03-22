<?php
require_once __DIR__ . '/_bootstrap.php';
require_admin();

$range = $_GET['range'] ?? 'day';
$startDate = trim((string)($_GET['start_date'] ?? ''));
$endDate = trim((string)($_GET['end_date'] ?? ''));
$reason = trim((string)($_GET['reason'] ?? ''));
$college = trim((string)($_GET['college'] ?? ''));
$employeeFilter = trim((string)($_GET['employee'] ?? 'all'));

$whereParts = ["vl.selected_role = 'user'", "vl.status = 'allowed'"];
$params = [];
$types = '';

if ($range === 'week') {
    $whereParts[] = 'DATE(vl.login_at) >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)';
    $whereParts[] = 'DATE(vl.login_at) <= CURDATE()';
} elseif ($range === 'range' && $startDate !== '' && $endDate !== '') {
    $whereParts[] = 'DATE(vl.login_at) BETWEEN ? AND ?';
    $params[] = $startDate;
    $params[] = $endDate;
    $types .= 'ss';
} else {
    $whereParts[] = 'DATE(vl.login_at) = CURDATE()';
}

if ($reason !== '') {
    $whereParts[] = 'vl.reason = ?';
    $params[] = $reason;
    $types .= 's';
}
if ($college !== '') {
    $whereParts[] = 'vl.college = ?';
    $params[] = $college;
    $types .= 's';
}
if ($employeeFilter === 'yes') {
    $whereParts[] = 'vl.is_employee = 1';
} elseif ($employeeFilter === 'no') {
    $whereParts[] = 'vl.is_employee = 0';
}

$whereSql = implode(' AND ', $whereParts);

$sql = "
    SELECT
        COUNT(*) AS total_visits,
        COUNT(DISTINCT vl.user_id) AS unique_visitors,
        SUM(CASE WHEN vl.is_employee = 1 THEN 1 ELSE 0 END) AS employee_visits,
        SUM(CASE WHEN vl.is_employee = 0 THEN 1 ELSE 0 END) AS non_employee_visits
    FROM visitor_logs vl
    WHERE $whereSql
";
$stmt = $conn->prepare($sql);
if ($types !== '') $stmt->bind_param($types, ...$params);
$stmt->execute();
$summary = $stmt->get_result()->fetch_assoc() ?: [];
$stmt->close();

$reasonSql = "SELECT reason, COUNT(*) AS total FROM visitor_logs vl WHERE $whereSql GROUP BY reason ORDER BY total DESC LIMIT 1";
$stmt = $conn->prepare($reasonSql);
if ($types !== '') $stmt->bind_param($types, ...$params);
$stmt->execute();
$topReason = $stmt->get_result()->fetch_assoc();
$stmt->close();

$collegeSql = "SELECT college, COUNT(*) AS total FROM visitor_logs vl WHERE $whereSql AND college IS NOT NULL AND college <> '' GROUP BY college ORDER BY total DESC LIMIT 1";
$stmt = $conn->prepare($collegeSql);
if ($types !== '') $stmt->bind_param($types, ...$params);
$stmt->execute();
$topCollege = $stmt->get_result()->fetch_assoc();
$stmt->close();

$listReasons = [];
$result = $conn->query("SELECT DISTINCT reason FROM visitor_logs WHERE reason IS NOT NULL AND reason <> '' ORDER BY reason ASC");
while ($result && ($row = $result->fetch_assoc())) $listReasons[] = $row['reason'];

$listColleges = [];
$result = $conn->query("SELECT DISTINCT college FROM visitor_logs WHERE college IS NOT NULL AND college <> '' ORDER BY college ASC");
while ($result && ($row = $result->fetch_assoc())) $listColleges[] = $row['college'];

json_response([
    'success' => true,
    'stats' => [
        'total_visits' => (int)($summary['total_visits'] ?? 0),
        'unique_visitors' => (int)($summary['unique_visitors'] ?? 0),
        'employee_visits' => (int)($summary['employee_visits'] ?? 0),
        'non_employee_visits' => (int)($summary['non_employee_visits'] ?? 0),
        'top_reason' => $topReason['reason'] ?? '—',
        'top_reason_total' => (int)($topReason['total'] ?? 0),
        'top_college' => $topCollege['college'] ?? '—',
        'top_college_total' => (int)($topCollege['total'] ?? 0),
    ],
    'filters' => [
        'reasons' => $listReasons,
        'colleges' => $listColleges,
    ]
]);
?>
