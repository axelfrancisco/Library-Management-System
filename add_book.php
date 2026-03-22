<?php
require_once __DIR__ . '/_bootstrap.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Invalid request.'], 405);
}

$data = get_json_input();

$bookCode = trim((string)($data['book_code'] ?? ''));
$title    = trim((string)($data['title'] ?? ''));
$author   = trim((string)($data['author'] ?? ''));
$category = trim((string)($data['category'] ?? ''));
$cover    = trim((string)($data['cover_image'] ?? ''));
$isNew    = !empty($data['is_new_arrival']) ? 1 : 0;
$status   = $isNew ? 'new_arrival' : 'available';

if ($bookCode === '' || $title === '' || $author === '') {
    json_response(['success' => false, 'message' => 'Book code, title, and author are required.'], 422);
}

$stmt = $conn->prepare("INSERT INTO books (book_code, title, author, category, cover_image, status, is_new_arrival) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param('ssssssi', $bookCode, $title, $author, $category, $cover, $status, $isNew);

if ($stmt->execute()) {
    json_response(['success' => true, 'message' => 'Book added successfully.']);
} else {
    json_response(['success' => false, 'message' => 'Failed to add book.', 'error' => $conn->error], 500);
}
$stmt->close();
?>
