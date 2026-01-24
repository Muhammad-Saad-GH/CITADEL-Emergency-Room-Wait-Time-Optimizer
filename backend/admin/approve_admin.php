<?php
// Approves / rejects admin signups

require_once(__DIR__ . "/../db.php");
require_once(__DIR__ . "/../../config/session.php");
requireRole("admin");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

$adminId = isset($_POST['admin_id']) ? (int) $_POST['admin_id'] : 0;
$action  = $_POST['action'] ?? '';

if ($adminId <= 0 || !in_array($action, ['approve', 'reject'], true)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid parameters']);
    exit;
}

if ($action === 'approve') {
    $sql  = "UPDATE Admin SET Approved = TRUE WHERE Admin_ID = ?";
} else {
    $sql  = "UPDATE Admin SET Approved = FALSE WHERE Admin_ID = ?";
}

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $conn->error]);
    exit;
}

$stmt->bind_param("i", $adminId);

if ($stmt->execute()) {
    echo json_encode(['ok' => true]);
} else {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $conn->error]);
}

$stmt->close();
$conn->close();
