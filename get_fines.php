<?php
require_once __DIR__ . '/_bootstrap.php';
require_login();

$userId = current_user_id();

$result = $conn->query("
    SELECT f.fine_id, f.borrow_id, b.book_code, b.title, b.cover_image,
           u.full_name AS borrower, bb.due_date, f.days_late, f.amount, f.payment_status
    FROM fines f
    INNER JOIN books b ON f.book_id = b.book_id
    INNER JOIN users u ON f.user_id = u.user_id
    INNER JOIN borrowed_books bb ON f.borrow_id = bb.borrow_id
    WHERE f.user_id = $userId
    ORDER BY f.fine_id DESC
");

$fines = [];
while ($result && ($row = $result->fetch_assoc())) {
    $fines[] = [
        'fine_id'   => $row['fine_id'],
        'borrow_id' => $row['borrow_id'],
        'id'        => $row['book_code'],
        'title'     => $row['title'],
        'cover'     => $row['cover_image'],
        'borrower'  => $row['borrower'],
        'dueDate'   => $row['due_date'],
        'daysLate'  => $row['days_late'],
        'amount'    => $row['amount'],
        'status'    => $row['payment_status']
    ];
}

json_response($fines);
?>
