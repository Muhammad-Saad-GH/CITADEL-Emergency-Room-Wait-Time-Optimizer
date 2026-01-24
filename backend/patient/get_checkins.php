<?php

require_once(__DIR__ . "/../db.php");
require_once(__DIR__ . "/../../config/session.php");

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
        $checkins[] = $row;
    }
}

$stmt->close();
$conn->close();

echo json_encode($checkins);
