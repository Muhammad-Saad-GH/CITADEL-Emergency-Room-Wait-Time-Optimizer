<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);
ob_start();



require_once(__DIR__ . "/../db.php");
require_once(__DIR__ . "/../../config/session.php");
require_once(__DIR__ . "/../ai/get_wait_time.php");


header('Content-Type: application/json; charset=utf-8');

requireRole("patient");

// Get logged-in patient ID
$patientId = $_SESSION['user_id'] ?? null;
if (!$patientId) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$sql = "
    SELECT
        c.Checkin_ID,
        c.Severity,
        c.Wait_Time,
        c.Status,
        c.Notes,
        c.Hos_ID,
        c.Approved,
        h.Name     AS HospitalName,
        h.Location AS HospitalLocation
    FROM Patient p
    LEFT JOIN Checkin c
        ON p.Check_ID = c.Checkin_ID
    LEFT JOIN Hospital h
        ON c.Hos_ID = h.Hospital_ID
    WHERE p.Patient_ID = ?
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(["error" => "Database error (prepare failed)"]);
    exit;
}

//  use the correct variable here
$stmt->bind_param("i", $patientId);
$stmt->execute();
$result = $stmt->get_result();

$checkins = [];

// At most one row because of one-checkin-per-patient design
if ($row = $result->fetch_assoc()) {
    if (!is_null($row['Checkin_ID'])) {

        // Only recalculate if patient is waiting
        if ($row['Status'] === 'Waiting' && $row['Severity'] !== null) {

            $checkinId       = (int)$row['Checkin_ID'];
            $hospitalId      = (int)$row['Hos_ID'];
            $patientSeverity = (int)$row['Severity'];

            // ------------------------------------
            // Build queue severities (ahead)
            // ------------------------------------
            $queueSql = "
                SELECT Severity
                FROM Checkin
                WHERE Hos_ID = ?
                AND Status = 'Waiting'
                AND Severity <= ?
                AND Checkin_ID != ?
                ORDER BY Severity ASC
            ";

            $qStmt = $conn->prepare($queueSql);
            $qStmt->bind_param("iii", $hospitalId, $patientSeverity, $checkinId);
            $qStmt->execute();
            $qRes = $qStmt->get_result();

            $queueSeverities = [];
            while ($qRow = $qRes->fetch_assoc()) {
                $queueSeverities[] = (int)$qRow['Severity'];
            }
            $qStmt->close();

            // ------------------------------------
            // Call Python model
            // ------------------------------------
            $estimatedWait = 
            getEstimatedWaitTime(
                $queueSeverities,
                $patientSeverity
            );
            error_log("MODEL CALLED: severity=$patientSeverity wait=$estimatedWait");

            // ------------------------------------
            // Update DB with model output
            // ------------------------------------
            $updateSql = "
                UPDATE Checkin
                SET Wait_Time = ?
                WHERE Checkin_ID = ?
            ";

            $uStmt = $conn->prepare($updateSql);
            $uStmt->bind_param("ii", $estimatedWait, $checkinId);
            $uStmt->execute();
            $uStmt->close();

            // Update row so response is fresh
            $row['Wait_Time'] = $estimatedWait;
        }

        $checkins[] = $row;
    }
}


$stmt->close();
$conn->close();

ob_clean();

echo json_encode($checkins);
