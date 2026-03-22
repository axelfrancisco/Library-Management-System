<?php
require_once __DIR__ . '/_bootstrap.php';
require_admin();

$result = $conn->query("
    SELECT bb.borrow_id, b.book_code, b.title, b.cover_image,
           u.full_name AS borrower, bb.borrow_date, bb.due_date, bb.return_status
    FROM borrowed_books bb
    INNER JOIN books b ON bb.book_id = b.book_id
    INNER JOIN users u ON bb.user_id = u.user_id
    WHERE bb.return_status = 'not_returned'
    ORDER BY bb.borrow_id DESC
");
$data = [];
while ($result && ($row = $result->fetch_assoc())) {
    $data[] = $row;
}
json_response($data);
?>
