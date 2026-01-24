<?php

require_once(__DIR__ . "/../db.php");
require_once(__DIR__ . "/../../config/session.php");
requireRole("patient");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../public/patient/checkin.php?error=Invalid+request+method");
    exit;
}

// Logged in patient ID
$patient_id = $_SESSION['user_id'] ?? null;
if (!$patient_id) {
    header("Location: ../../public/login.php");
    exit;
}

// Get form data
$hosId = isset($_POST['hos_id']) ? (int)$_POST['hos_id'] : 0;
$notes = trim($_POST['notes'] ?? '');

if ($hosId <= 0) {
    header("Location: ../../public/patient/checkin.php?error=Please+select+a+valid+hospital");
    exit;
}

if ($notes === '') {
    $notes = 'None';
}

// Check if patient created a check in already
$sql = "SELECT Check_ID FROM Patient WHERE Patient_ID = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    header("Location: ../../public/patient/checkin.php?error=Database+error");
    exit;
}

$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$patientRow = $result->fetch_assoc();
$stmt->close();

if (!$patientRow) {
    header("Location: ../../public/patient/checkin.php?error=Patient+record+not+found");
    exit;
}

$currentCheckinId = $patientRow['Check_ID'];

// No multiple check-ins allowed
if (!is_null($currentCheckinId)) {
    header("Location: ../../public/patient/checkin.php?error=You+already+have+an+active+check-in");
    exit;
}

$insertSql = "INSERT INTO Checkin (Hos_ID, Status, Notes, Approved)
              VALUES (?, 'Waiting', ?, 0)";

$insertStmt = $conn->prepare($insertSql);
if (!$insertStmt) {
    header("Location: ../../public/patient/checkin.php?error=Server+error+during+insert");
    exit;
}

$insertStmt->bind_param("is", $hosId, $notes);

if (!$insertStmt->execute()) {
    $insertStmt->close();
    header("Location: ../../public/patient/checkin.php?error=Could+not+create+check-in");
    exit;
}

$newCheckinId = $insertStmt->insert_id;
$insertStmt->close();

// Update patient so Check_ID points to this new check-in
$updateSql = "UPDATE Patient SET Check_ID = ? WHERE Patient_ID = ?";
$updateStmt = $conn->prepare($updateSql);
if (!$updateStmt) {
    header("Location: ../../public/patient/checkin.php?error=Server+error+during+update");
    exit;
}

$updateStmt->bind_param("ii", $newCheckinId, $patient_id);
$updateStmt->execute();
$updateStmt->close();

$conn->close();

header("Location: ../../public/patient/checkin.php?success=1");
exit;