<?php
require_once __DIR__ . '/_bootstrap.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Invalid request.'], 405);
}

$data = get_json_input();

$bookId   = (int)($data['book_id'] ?? 0);
$bookCode = trim((string)($data['book_code'] ?? ''));
$title    = trim((string)($data['title'] ?? ''));
$author   = trim((string)($data['author'] ?? ''));
$category = trim((string)($data['category'] ?? ''));
$cover    = trim((string)($data['cover_image'] ?? ''));
$status   = trim((string)($data['status'] ?? 'available'));
$isNew    = !empty($data['is_new_arrival']) ? 1 : 0;

if ($bookId <= 0) {
    json_response(['success' => false, 'message' => 'Invalid book ID.'], 422);
}

$stmt = $conn->prepare("UPDATE books SET book_code=?, title=?, author=?, category=?, cover_image=?, status=?, is_new_arrival=? WHERE book_id=?");
$stmt->bind_param('ssssssii', $bookCode, $title, $author, $category, $cover, $status, $isNew, $bookId);

if ($stmt->execute()) {
    json_response(['success' => true, 'message' => 'Book updated successfully.']);
} else {
    json_response(['success' => false, 'message' => 'Failed to update book.', 'error' => $conn->error], 500);
}
$stmt->close();
?>
