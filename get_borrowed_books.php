<?php
require_once __DIR__ . '/_bootstrap.php';
require_login();

$userId = current_user_id();

$result = $conn->query("
    SELECT bb.borrow_id, b.book_code, b.title, b.author, b.category, b.cover_image,
           u.full_name AS borrower, bb.borrow_date, bb.due_date, bb.return_status
    FROM borrowed_books bb
    INNER JOIN books b ON bb.book_id = b.book_id
    INNER JOIN users u ON bb.user_id = u.user_id
    WHERE bb.return_status = 'not_returned' AND bb.user_id = $userId
    ORDER BY bb.borrow_date DESC
");

$borrowedBooks = [];
while ($result && ($row = $result->fetch_assoc())) {
    $borrowedBooks[] = [
        'borrow_id'  => $row['borrow_id'],
        'id'         => $row['book_code'],
        'title'      => $row['title'],
        'author'     => $row['author'],
        'category'   => $row['category'],
        'cover'      => $row['cover_image'],
        'borrower'   => $row['borrower'],
        'borrowDate' => $row['borrow_date'],
        'dueDate'    => $row['due_date'],
        'status'     => $row['return_status']
    ];
}

json_response($borrowedBooks);
?>
