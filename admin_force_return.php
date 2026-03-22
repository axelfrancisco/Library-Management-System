<?php
require_once __DIR__ . '/_bootstrap.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Invalid request.'], 405);
}

$data = get_json_input();
$borrowId = (int)($data['borrow_id'] ?? 0);
$returnDate = date('Y-m-d');

$stmt = $conn->prepare("SELECT borrow_id, user_id, book_id, due_date, return_status FROM borrowed_books WHERE borrow_id = ? LIMIT 1");
$stmt->bind_param('i', $borrowId);
$stmt->execute();
$borrowRow = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$borrowRow) {
    json_response(['success' => false, 'message' => 'Borrow record not found.'], 404);
}

if ($borrowRow['return_status'] === 'returned') {
    json_response(['success' => false, 'message' => 'Already returned.'], 409);
}

$userId = (int)$borrowRow['user_id'];
$bookId = (int)$borrowRow['book_id'];
$dueDate = $borrowRow['due_date'];

$stmt = $conn->prepare("UPDATE borrowed_books SET return_status = 'returned' WHERE borrow_id = ?");
$stmt->bind_param('i', $borrowId);
if (!$stmt->execute()) {
    json_response(['success' => false, 'message' => 'Failed to update borrow record.'], 500);
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
if (!$stmt->execute()) {
    json_response(['success' => false, 'message' => 'Failed to update book status.'], 500);
}
$stmt->close();

$daysLate = 0;
$amount = 0;

if (!empty($dueDate) && strtotime($returnDate) > strtotime($dueDate)) {
    $daysLate = (int)floor((strtotime($returnDate) - strtotime($dueDate)) / 86400);
    $amount = $daysLate * 10;

    $checkStmt = $conn->prepare("SELECT fine_id FROM fines WHERE borrow_id = ? LIMIT 1");
    $checkStmt->bind_param('i', $borrowId);
    $checkStmt->execute();
    $existingFine = $checkStmt->get_result()->fetch_assoc();
    $checkStmt->close();

    if (!$existingFine) {
        $stmt = $conn->prepare("INSERT INTO fines (borrow_id, user_id, book_id, days_late, amount, payment_status) VALUES (?, ?, ?, ?, ?, 'unpaid')");
        $stmt->bind_param('iiiid', $borrowId, $userId, $bookId, $daysLate, $amount);
        $stmt->execute();
        $stmt->close();
    }
}

json_response(['success' => true, 'message' => 'Book force-returned successfully.']);
?>
