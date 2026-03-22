<?php
require_once __DIR__ . '/_bootstrap.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Invalid request method.'], 405);
}

$data = get_json_input();

if (empty($data['book_id'])) {
    json_response(['success' => false, 'message' => 'Book ID is required.'], 422);
}

$userId   = current_user_id();
$bookCode = $conn->real_escape_string($data['book_id']);

$getBookResult = $conn->query("SELECT book_id FROM books WHERE book_code = '$bookCode' LIMIT 1");

if (!$getBookResult || $getBookResult->num_rows === 0) {
    json_response(['success' => false, 'message' => 'Book not found.'], 404);
}

$bookId = (int)$getBookResult->fetch_assoc()['book_id'];

$stmt = $conn->prepare("DELETE FROM liked_books WHERE user_id = ? AND book_id = ?");
$stmt->bind_param('ii', $userId, $bookId);

if ($stmt->execute()) {
    json_response(['success' => true, 'message' => 'Book removed from liked books.']);
} else {
    json_response(['success' => false, 'message' => 'Failed to remove liked book.', 'error' => $conn->error], 500);
}
$stmt->close();
?>
