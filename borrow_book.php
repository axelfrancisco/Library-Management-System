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

$bookCode   = $conn->real_escape_string($data['book_id']);
$userId     = current_user_id();
$borrowDate = date('Y-m-d');
$dueDate    = date('Y-m-d', strtotime('+7 days'));

$getBookSql = "SELECT book_id, status FROM books WHERE book_code = '$bookCode' LIMIT 1";
$getBookResult = $conn->query($getBookSql);

if (!$getBookResult || $getBookResult->num_rows === 0) {
    json_response(['success' => false, 'message' => 'Book not found.'], 404);
}

$book = $getBookResult->fetch_assoc();
$realBookId = (int)$book['book_id'];

if ($book['status'] !== 'available') {
    json_response(['success' => false, 'message' => 'Book is not available.'], 409);
}

$stmt = $conn->prepare("INSERT INTO borrowed_books (user_id, book_id, borrow_date, due_date, return_status) VALUES (?, ?, ?, ?, 'not_returned')");
$stmt->bind_param('iiss', $userId, $realBookId, $borrowDate, $dueDate);

if (!$stmt->execute()) {
    json_response(['success' => false, 'message' => 'Failed to borrow book.', 'error' => $conn->error], 500);
}
$stmt->close();

$stmt = $conn->prepare("UPDATE books SET status = 'borrowed' WHERE book_id = ?");
$stmt->bind_param('i', $realBookId);
$stmt->execute();
$stmt->close();

json_response(['success' => true, 'message' => 'Book borrowed successfully.']);
?>
