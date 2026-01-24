<?php
session_start();
require_once __DIR__ . '/../db.php';
header('Content-Type: application/json');

$filter = $_GET['filter'] ?? 'all';
$data   = [];

// helper: run a query and return array
function fetch_all_assoc(mysqli $conn, string $sql, ?array $params = null, string $types = '') {
    if ($params) {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result();
    } else {
        $res = $conn->query($sql);
    }
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

switch ($filter) {
    // 1) All approved (no special view)
    case 'all':
        $sql = "
            SELECT 
                c.Checkin_ID      AS id,
                p.Username        AS name,
                p.Patient_ID AS p_ID,
                c.Severity        AS severity,
                c.Wait_Time       AS wait,
                c.Status          AS status,
                c.Notes AS notes,
                csa.Staff_ID    AS assigned_staff_ID
            FROM Checkin c
            JOIN Patient p 
                ON c.Checkin_ID = p.Check_ID
            LEFT JOIN Checkin_Staff_Assignment csa
                ON csa.Checkin_ID = c.Checkin_ID
            WHERE c.Status = 'Approved'
        ";
        $data = fetch_all_assoc($conn, $sql);
        break;

    // 2) Above-average severity view 
    case 'above_avg':
        $sql = "
            SELECT 
                c.Checkin_ID      AS id,
                p.Username        AS name,
                p.Patient_ID AS p_ID,
                c.Severity        AS severity,
                c.Wait_Time       AS wait,
                c.Status          AS status,
                c.Notes AS notes,
                csa.Staff_ID    AS assigned_staff_ID
                
            FROM Checkin c
            JOIN Patient p 
                ON c.Checkin_ID = p.Check_ID
            JOIN Above_Avg_Severity_Patients a
                ON a.Patient_ID = p.Patient_ID
            LEFT JOIN Checkin_Staff_Assignment csa
                ON csa.Checkin_ID = c.Checkin_ID
            WHERE c.Status = 'Approved'
        ";
        $data = fetch_all_assoc($conn, $sql);
        break;

    // 3) Unassigned approved checkins view
    case 'unassigned':
        $sql = "
            SELECT
                c.Checkin_ID      AS id,
                u.Username        AS name,
                u.Patient_ID AS p_ID,
                c.Severity        AS severity,
                c.Wait_Time       AS wait,
                c.Status          AS status,
                c.Notes AS notes,
                NULL              AS assigned_staff_ID
            FROM Checkin c
            JOIN Unassigned_Patient_Checkins u
                ON c.Checkin_ID = u.Checkin_ID
            WHERE c.Status = 'Approved'
        ";
        $data = fetch_all_assoc($conn, $sql);
        break;

    // 4) Approved assigned to *this* staff member view
    case 'assigned_to_me':
        if (!isset($_SESSION['staff_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Not logged in as staff']);
            exit;
        }
        $staffId = $_SESSION['staff_id'];

        $sql = "
            SELECT 
                csa.Checkin_ID    AS id,
                p.Username        AS name,
                p.Patient_ID AS p_ID,
                c.Severity        AS severity,
                c.Wait_Time       AS wait,
                c.Status          AS status,
                c.Notes AS notes,
                csa.Staff_ID    AS assigned_staff_ID
            FROM Checkin_Staff_Assignment csa
            JOIN Checkin c 
                ON csa.Checkin_ID = c.Checkin_ID
            JOIN Patient p 
                ON p.Check_ID = c.Checkin_ID
            WHERE csa.Staff_ID = ?
              AND c.Status = 'Approved'
        ";
        $data = fetch_all_assoc($conn, $sql, [$staffId], 'i');
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Unknown filter']);
        exit;
}

echo json_encode($data);
