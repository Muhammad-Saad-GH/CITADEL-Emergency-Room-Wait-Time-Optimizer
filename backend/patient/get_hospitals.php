<?php
// backend/patient/get_hospitals.php

require_once(__DIR__ . "/../db.php");
require_once(__DIR__ . "/../../config/session.php");

header('Content-Type: application/json; charset=utf-8');

requireRole("patient");

/*
    We compute an estimated wait time per hospital as the
    average of non-null Wait_Time values in Checkin.
*/
$sql = "
    SELECT 
        h.Hospital_ID,
        h.Name,
        h.Location,
        h.Rating,
        h.Phone_Num,
        ROUND(AVG(c.Wait_Time)) AS Avg_Wait_Time
    FROM Hospital h
    LEFT JOIN Checkin c
        ON c.Hos_ID = h.Hospital_ID
        AND c.Wait_Time IS NOT NULL
    GROUP BY 
        h.Hospital_ID,
        h.Name,
        h.Location,
        h.Rating,
        h.Phone_Num
    ORDER BY h.Name
";

$result = $conn->query($sql);
if (!$result) {
    http_response_code(500);
    echo json_encode(["error" => "Database error loading hospitals"]);
    exit;
}

$hospitals = [];
while ($row = $result->fetch_assoc()) {
    $hospitals[] = $row;
}

$conn->close();

echo json_encode($hospitals);
