<?php
require_once __DIR__ . '/_bootstrap.php';

$result = $conn->query("
    SELECT book_id, book_code, title, author, category, cover_image, status, is_new_arrival
    FROM books
    WHERE is_new_arrival = 1
    ORDER BY book_id DESC
");

$books = [];
while ($result && ($row = $result->fetch_assoc())) {
    $books[] = [
        'db_id'    => $row['book_id'],
        'id'       => $row['book_code'],
        'title'    => $row['title'],
        'author'   => $row['author'],
        'category' => $row['category'],
        'cover'    => $row['cover_image'],
        'status'   => $row['status']
    ];
}

json_response($books);
?>
