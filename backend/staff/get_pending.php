 <?php
require_once __DIR__ . '/../db.php';
header('Content-Type: application/json');

$data = [
    "pending" => [],
    "approved" => []
];

// PENDING
$sqlPending = "
    SELECT 
        c.Checkin_ID AS id,
        p.Patient_ID AS p_ID,
        p.Username AS name,
        c.Severity AS severity,
        c.Wait_Time AS wait,
        c.Status AS status,
        c.Notes AS notes,
        csa.Staff_Name AS assigned_staff,
        csa.Staff_ID AS assigned_staff_ID
    FROM Checkin c
    JOIN Patient p 
        ON c.Checkin_ID = p.Check_ID
    LEFT JOIN Checkin_Staff_Assignment csa
        ON c.Checkin_ID = csa.Checkin_ID
    WHERE c.Status = 'Waiting' ;
";
$data["pending"] = $conn->query($sqlPending)->fetch_all(MYSQLI_ASSOC);

// APPROVED
// $sqlApproved = "
//     SELECT c.Checkin_ID AS id,
//            p.Username AS name,
//            c.Severity AS severity,
//            c.Wait_Time AS wait,
//            c.Status AS status
//     FROM Checkin c
//     JOIN Patient p ON c.Checkin_ID = p.Check_ID
//     WHERE c.Status = 'Approved';
// ";
// $data["approved"] = $conn->query($sqlApproved)->fetch_all(MYSQLI_ASSOC);

echo json_encode($data);
