<?php
require_once __DIR__ . '/../db.php';
header('Content-Type: application/json');

// Count number of checkins by severity (1–5)
$sql = "
    SELECT 
        Severity AS severity,
        COUNT(*) AS total
    FROM Checkin 
    WHERE Status= 'Approved'
    GROUP BY Severity
    ORDER BY Severity
";

$res = $conn->query($sql);
$data = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

echo json_encode($data);
