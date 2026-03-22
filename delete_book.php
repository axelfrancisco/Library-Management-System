<?php
require_once __DIR__ . '/_bootstrap.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Invalid request.'], 405);
}

$data = get_json_input();
$bookId = (int)($data['book_id'] ?? 0);

if ($bookId <= 0) {
    json_response(['success' => false, 'message' => 'Invalid book ID.'], 422);
}

$stmt = $conn->prepare("DELETE FROM books WHERE book_id = ?");
$stmt->bind_param('i', $bookId);

if ($stmt->execute()) {
    json_response(['success' => true, 'message' => 'Book deleted successfully.']);
} else {
    json_response(['success' => false, 'message' => 'Failed to delete book.', 'error' => $conn->error], 500);
}
$stmt->close();
?>
