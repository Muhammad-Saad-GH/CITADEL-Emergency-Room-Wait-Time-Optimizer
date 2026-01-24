<?php
// backend/patient/create_checkin.php

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

// ======================================================
// START AI INTEGRATION (CONFIG VERSION)
// ======================================================
$severity = 3; 
$ai_reasoning = "Pending Triage";

// Ensure config is loaded (adjust path if your config.php is elsewhere)
// require_once(__DIR__ . "/../../config/config.php"); 
// ^^^ Uncomment above line if config.php isn't already loaded by session.php

if ($notes !== 'None') {
    $safe_notes = escapeshellarg($notes);
    $scriptPath = __DIR__ . "/../ai/triage_processor.py"; 

    // 1. USE VARIABLE FROM CONFIG.PHP
    // We use the global keyword just in case we are inside a function scope, 
    // though in this file it's likely global anyway.
    global $PYTHON_PATH; 
    
    // Fallback if config is missing (Safety Net)
    $pythonExec = isset($PYTHON_PATH) ? $PYTHON_PATH : 'python';

    // 2. Build Command
    $command = "\"$pythonExec\" \"$scriptPath\" $safe_notes 2>&1";

    // 3. Execute
    $output = shell_exec($command);
    
    // 4. Decode
    $ai_result = json_decode($output, true);

    if ($ai_result) {
        $severity = (int)$ai_result['severity_score'];
        $ai_reasoning = $ai_result['medical_reasoning']; 
    } else {
        error_log("AI Triage Failed. Command: $command Output: $output");
        $ai_reasoning = "AI Service Unavailable - Manual Review Required";
    }
}
// ======================================================
// END AI INTEGRATION
// ======================================================

// Updated SQL to include Severity and AI_reasoning
$insertSql = "INSERT INTO Checkin (Hos_ID, Status, Notes, Approved, Severity, AI_reasoning)
              VALUES (?, 'Waiting', ?, 0, ?, ?)";

$insertStmt = $conn->prepare($insertSql);
if (!$insertStmt) {
    header("Location: ../../public/patient/checkin.php?error=Server+error+during+insert");
    exit;
}

// Bind params: i (int), s (string), i (int), s (string)
$insertStmt->bind_param("isis", $hosId, $notes, $severity, $ai_reasoning);

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
?>