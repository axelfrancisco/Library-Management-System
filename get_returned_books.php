<?php
require_once __DIR__ . '/_bootstrap.php';
require_login();

$userId = current_user_id();

$result = $conn->query("
    SELECT rb.return_id, b.book_code, b.title, b.author, b.category, b.cover_image,
           u.full_name AS borrower, rb.return_date
    FROM returned_books rb
    INNER JOIN books b ON rb.book_id = b.book_id
    INNER JOIN users u ON rb.user_id = u.user_id
    WHERE rb.user_id = $userId
    ORDER BY rb.return_date DESC, rb.return_id DESC
");

$returnedBooks = [];
while ($result && ($row = $result->fetch_assoc())) {
    $returnedBooks[] = [
        'return_id'  => $row['return_id'],
        'id'         => $row['book_code'],
        'title'      => $row['title'],
        'author'     => $row['author'],
        'category'   => $row['category'],
        'cover'      => $row['cover_image'],
        'borrower'   => $row['borrower'],
        'returnDate' => $row['return_date']
    ];
}

json_response($returnedBooks);
?>
