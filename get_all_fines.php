<?php
require_once __DIR__ . '/_bootstrap.php';
require_admin();

$result = $conn->query("
    SELECT f.fine_id, b.book_code, b.title, b.cover_image,
           u.full_name AS borrower, f.days_late, f.amount, f.payment_status
    FROM fines f
    INNER JOIN books b ON f.book_id = b.book_id
    INNER JOIN users u ON f.user_id = u.user_id
    ORDER BY f.fine_id DESC
");
$data = [];
while ($result && ($row = $result->fetch_assoc())) {
    $data[] = $row;
}
json_response($data);
?>
