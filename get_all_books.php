<?php
require_once __DIR__ . '/_bootstrap.php';
require_admin();

$result = $conn->query("SELECT book_id, book_code, title, author, category, cover_image, status, is_new_arrival FROM books ORDER BY book_id DESC");
$books = [];
while ($result && ($row = $result->fetch_assoc())) {
    $books[] = $row;
}
json_response($books);
?>
