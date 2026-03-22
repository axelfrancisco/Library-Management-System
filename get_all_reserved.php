<?php
require_once __DIR__ . '/_bootstrap.php';
require_admin();

$result = $conn->query("
    SELECT rb.reserve_id, b.book_code, b.title, b.cover_image,
           u.full_name AS reserved_by, rb.reserve_date, rb.reserve_status
    FROM reserved_books rb
    INNER JOIN books b ON rb.book_id = b.book_id
    INNER JOIN users u ON rb.user_id = u.user_id
    WHERE rb.reserve_status = 'active'
    ORDER BY rb.reserve_id DESC
");
$data = [];
while ($result && ($row = $result->fetch_assoc())) {
    $data[] = $row;
}
json_response($data);
?>
