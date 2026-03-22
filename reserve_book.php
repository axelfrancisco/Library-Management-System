<?php
require_once __DIR__ . '/_bootstrap.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Invalid request.'], 405);
}

$data = get_json_input();

if (empty($data['book_id'])) {
    json_response(['success' => false, 'message' => 'Book ID required.'], 422);
}

$bookCode    = $conn->real_escape_string($data['book_id']);
$userId      = current_user_id();
$reserveDate = date('Y-m-d');

$getBook = $conn->query("SELECT book_id, status FROM books WHERE book_code = '$bookCode' LIMIT 1");

if (!$getBook || $getBook->num_rows === 0) {
    json_response(['success' => false, 'message' => 'Book not found.'], 404);
}

$book   = $getBook->fetch_assoc();
$bookId = (int)$book['book_id'];

$check = $conn->query("SELECT reserve_id FROM reserved_books WHERE user_id = $userId AND book_id = $bookId AND reserve_status = 'active' LIMIT 1");

if ($check && $check->num_rows > 0) {
    json_response(['success' => false, 'message' => 'Already reserved.'], 409);
}

$stmt = $conn->prepare("INSERT INTO reserved_books (user_id, book_id, reserve_date, reserve_status) VALUES (?, ?, ?, 'active')");
$stmt->bind_param('iis', $userId, $bookId, $reserveDate);

if (!$stmt->execute()) {
    json_response(['success' => false, 'message' => 'Reserve failed.', 'error' => $conn->error], 500);
}
$stmt->close();

$conn->query("UPDATE books SET status = 'reserved' WHERE book_id = $bookId");

json_response(['success' => true, 'message' => 'Book reserved successfully.']);
?>
