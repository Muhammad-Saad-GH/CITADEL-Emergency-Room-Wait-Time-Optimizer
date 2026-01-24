<?php
session_start();
require_once __DIR__ . '/../db.php';
header('Content-Type: application/json');

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

$id     = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$status = $_POST['status'] ?? '';

$allowedStatuses = ['Waiting', 'Approved', 'Completed', 'Cancelled'];

if ($id <= 0 || !in_array($status, $allowedStatuses, true)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid input']);
    exit;
}

// Optional severity & wait time
$severity = isset($_POST['severity']) && $_POST['severity'] !== '' ? (int) $_POST['severity'] : null;
$wait     = isset($_POST['wait']) && $_POST['wait'] !== '' ? (int) $_POST['wait'] : null;

// If approving, we want severity & wait to be present
if ($status === 'Approved' && ($severity === null || $wait === null)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Severity and wait time are required when approving.']);
    exit;
}

// Build dynamic SQL so we don't overwrite severity/wait when not sent
$fields = ['Status = ?'];
$params = [$status];
$types  = 's';

if ($severity !== null) {
    $fields[] = 'Severity = ?';
    $params[] = $severity;
    $types   .= 'i';
}

if ($wait !== null) {
    $fields[] = 'Wait_Time = ?';
    $params[] = $wait;
    $types   .= 'i';
}

// Optional notes (only updated if explicitly sent)
// We treat "field is present in POST" as "update it", even if it's empty.
$notesProvided = array_key_exists('notes', $_POST);
if ($notesProvided) {
    $fields[] = 'Notes = ?';
    $params[] = $_POST['notes']; // can be empty string to clear
    $types   .= 's';
}

$params[] = $id;
$types   .= 'i';

$sql = "UPDATE Checkin SET " . implode(', ', $fields) . " WHERE Checkin_ID = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $conn->error]);
    exit;
}

$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    echo json_encode(['ok' => true]);
} else {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $conn->error]);
}
