<?php
require_once __DIR__ . '/_bootstrap.php';
require_login();

$userId = current_user_id();

$result = $conn->query("
    SELECT rb.reserve_id, b.book_code, b.title, b.cover_image,
           u.full_name AS reservedBy, rb.reserve_date
    FROM reserved_books rb
    JOIN books b ON rb.book_id = b.book_id
    JOIN users u ON rb.user_id = u.user_id
    WHERE rb.reserve_status = 'active' AND rb.user_id = $userId
    ORDER BY rb.reserve_date DESC
");

$data = [];
while ($result && ($row = $result->fetch_assoc())) {
    $data[] = [
        'reserve_id'  => $row['reserve_id'],
        'id'          => $row['book_code'],
        'title'       => $row['title'],
        'cover'       => $row['cover_image'],
        'reservedBy'  => $row['reservedBy'],
        'reserveDate' => $row['reserve_date']
    ];
}

json_response($data);
?>
