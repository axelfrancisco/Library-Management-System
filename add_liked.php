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

$userId = current_user_id();
$bookCode = $conn->real_escape_string($data['book_id']);

$getBookSql = "SELECT book_id FROM books WHERE book_code = '$bookCode' LIMIT 1";
$getBookResult = $conn->query($getBookSql);

if (!$getBookResult || $getBookResult->num_rows === 0) {
    json_response(['success' => false, 'message' => 'Book not found.'], 404);
}

$book = $getBookResult->fetch_assoc();
$bookId = (int)$book['book_id'];

$checkSql = "SELECT liked_id FROM liked_books WHERE user_id = $userId AND book_id = $bookId LIMIT 1";
$checkResult = $conn->query($checkSql);

if ($checkResult && $checkResult->num_rows > 0) {
    json_response(['success' => false, 'message' => 'Book already liked.'], 409);
}

$insertSql = "INSERT INTO liked_books (user_id, book_id) VALUES ($userId, $bookId)";

if ($conn->query($insertSql)) {
    json_response(['success' => true, 'message' => 'Book added to liked books.']);
} else {
    json_response(['success' => false, 'message' => 'Failed to like book.', 'error' => $conn->error], 500);
}
?>
