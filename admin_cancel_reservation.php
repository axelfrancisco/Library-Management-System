<?php
require_once __DIR__ . '/_bootstrap.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Invalid request.'], 405);
}

$data = get_json_input();
$reserveId = (int)($data['reserve_id'] ?? 0);

if ($reserveId <= 0) {
    json_response(['success' => false, 'message' => 'Invalid reservation ID.'], 422);
}

$stmt = $conn->prepare("SELECT book_id FROM reserved_books WHERE reserve_id = ? LIMIT 1");
$stmt->bind_param('i', $reserveId);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    json_response(['success' => false, 'message' => 'Reservation not found.'], 404);
}

$bookId = (int)$row['book_id'];

$stmt = $conn->prepare("UPDATE reserved_books SET reserve_status = 'cancelled' WHERE reserve_id = ?");
$stmt->bind_param('i', $reserveId);
if (!$stmt->execute()) {
    json_response(['success' => false, 'message' => 'Failed to cancel reservation.'], 500);
}
$stmt->close();

// Restore book to 'available' (not 'new_arrival') after cancellation
$conn->query("UPDATE books SET status = 'available' WHERE book_id = $bookId");

json_response(['success' => true, 'message' => 'Reservation cancelled.']);
?>
