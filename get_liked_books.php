<?php
require_once __DIR__ . '/_bootstrap.php';
require_login();

$userId = current_user_id();

$result = $conn->query("
    SELECT lb.liked_id, b.book_code, b.title, b.author, b.category, b.cover_image
    FROM liked_books lb
    INNER JOIN books b ON lb.book_id = b.book_id
    WHERE lb.user_id = $userId
    ORDER BY lb.liked_at DESC, lb.liked_id DESC
");

$likedBooks = [];
while ($result && ($row = $result->fetch_assoc())) {
    $likedBooks[] = [
        'liked_id' => $row['liked_id'],
        'id'       => $row['book_code'],
        'title'    => $row['title'],
        'author'   => $row['author'],
        'category' => $row['category'],
        'cover'    => $row['cover_image']
    ];
}

json_response($likedBooks);
?>
