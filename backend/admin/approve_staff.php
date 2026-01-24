<?php
// Approves / rejects staff signups and sets access level

require_once(__DIR__ . "/../db.php");
require_once(__DIR__ . "/../../config/session.php");
requireRole("admin");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

$staffId      = isset($_POST['staff_id']) ? (int) $_POST['staff_id'] : 0;
$action       = $_POST['action'] ?? '';
$accessLevel  = isset($_POST['access_level']) ? (int) $_POST['access_level'] : null;

if ($staffId <= 0 || !in_array($action, ['approve', 'reject'], true)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid parameters']);
    exit;
}

if ($action === 'approve') {
    if ($accessLevel === null || $accessLevel < 1 || $accessLevel > 5) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Invalid access level']);
        exit;
    }

    $sql  = "UPDATE Staff
             SET Approved = TRUE, Access_Level = ?
             WHERE Staff_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $accessLevel, $staffId);

} else { // reject
    // You can also choose to DELETE instead of just marking not approved.
    $sql  = "UPDATE Staff SET Approved = FALSE WHERE Staff_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $staffId);
}

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $conn->error]);
    exit;
}

if ($stmt->execute()) {
    echo json_encode(['ok' => true]);
} else {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $conn->error]);
}

$stmt->close();
$conn->close();
