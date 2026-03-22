<?php
require_once __DIR__ . '/_bootstrap.php';

$result = $conn->query("SELECT * FROM books WHERE status = 'available'");
$books  = [];

while ($result && ($row = $result->fetch_assoc())) {
    $books[] = [
        'id'       => $row['book_code'],
        'title'    => $row['title'],
        'author'   => $row['author'],
        'category' => $row['category'],
        'cover'    => $row['cover_image']
    ];
}

json_response($books);
?>
