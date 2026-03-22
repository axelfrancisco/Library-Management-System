<?php
require_once __DIR__ . '/_bootstrap.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Invalid request method.'], 405);
}

$data = get_json_input();

if (empty($data['borrow_id'])) {
    json_response(['success' => false, 'message' => 'Borrow ID is required.'], 422);
}

$borrowId      = (int)$data['borrow_id'];
$returnDate    = date('Y-m-d');
$currentUserId = current_user_id();

$stmt = $conn->prepare("SELECT borrow_id, user_id, book_id, due_date, return_status FROM borrowed_books WHERE borrow_id = ? LIMIT 1");
$stmt->bind_param('i', $borrowId);
$stmt->execute();
$borrowRow = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$borrowRow) {
    json_response(['success' => false, 'message' => 'Borrow record not found.'], 404);
}

if ((int)$borrowRow['user_id'] !== $currentUserId) {
    json_response(['success' => false, 'message' => 'You are not allowed to return this book.'], 403);
}

if ($borrowRow['return_status'] === 'returned') {
    json_response(['success' => false, 'message' => 'Book is already returned.'], 409);
}

$userId = (int)$borrowRow['user_id'];
$bookId = (int)$borrowRow['book_id'];
$dueDate = $borrowRow['due_date'];

$stmt = $conn->prepare("UPDATE borrowed_books SET return_status = 'returned' WHERE borrow_id = ?");
$stmt->bind_param('i', $borrowId);
if (!$stmt->execute()) {
    json_response(['success' => false, 'message' => 'Failed to update borrowed record.'], 500);
}
$stmt->close();

$stmt = $conn->prepare("INSERT INTO returned_books (borrow_id, user_id, book_id, return_date) VALUES (?, ?, ?, ?)");
$stmt->bind_param('iiis', $borrowId, $userId, $bookId, $returnDate);
if (!$stmt->execute()) {
    json_response(['success' => false, 'message' => 'Failed to insert returned record.', 'error' => $conn->error], 500);
}
$stmt->close();

$stmt = $conn->prepare("UPDATE books SET status = 'available' WHERE book_id = ?");
$stmt->bind_param('i', $bookId);
$stmt->execute();
$stmt->close();

$daysLate = 0;
$amount   = 0;

if (!empty($dueDate) && strtotime($returnDate) > strtotime($dueDate)) {
    $daysLate = (int)floor((strtotime($returnDate) - strtotime($dueDate)) / 86400);
    $amount   = $daysLate * 10;

    $stmt = $conn->prepare("INSERT INTO fines (borrow_id, user_id, book_id, days_late, amount, payment_status) VALUES (?, ?, ?, ?, ?, 'unpaid')");
    $stmt->bind_param('iiiid', $borrowId, $userId, $bookId, $daysLate, $amount);
    if (!$stmt->execute()) {
        json_response(['success' => false, 'message' => 'Book returned, but failed to create fine.', 'error' => $conn->error], 500);
    }
    $stmt->close();
}

json_response([
    'success' => true,
    'message' => $amount > 0
        ? 'Book returned successfully. Fine added: ₱' . $amount
        : 'Book returned successfully.'
]);
?>
