<?php
// backend/patient/get_waitlist.php

require_once(__DIR__ . "/../db.php");
require_once(__DIR__ . "/../../config/session.php");

header('Content-Type: application/json; charset=utf-8');

requireRole("patient"); // or "staff" if you later move this to staff dashboard

/*
    Waitlist = all check-ins with Status = 'Approved'
    (using the columns you specified)
*/

$sql = "
    SELECT 
        p.Patient_ID      AS p_ID,
        c.Wait_Time       AS wait,
    FROM Checkin c
    JOIN Patient p 
        ON c.Checkin_ID = p.Check_ID
    LEFT JOIN Checkin_Staff_Assignment csa
        ON csa.Checkin_ID = c.Checkin_ID
    WHERE c.Status = 'Approved'
";

$result = $conn->query($sql);
if (!$result) {
    http_response_code(500);
    echo json_encode(["error" => "Database error loading waitlist"]);
    exit;
}

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}

$conn->close();

echo json_encode($rows);
