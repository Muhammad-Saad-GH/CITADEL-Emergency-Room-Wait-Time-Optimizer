<?php
require_once(__DIR__ . "/../db.php");
require_once(__DIR__ . "/../../config/session.php");
// requireRole("admin");

// ------------------------------------
// GET HOSPITALS
// ------------------------------------
$hQuery = "SELECT Hospital_ID, Name, Location, Phone_Num, Rating FROM Hospital";
$hResult = $conn->query($hQuery);

// ------------------------------------
// GET STAFF (FIXED SQL)
// ------------------------------------
$sQuery = "SELECT Staff_ID, Name, Access_Level, Role, Username, Hos_ID, Approved FROM Staff";
$sResult = $conn->query($sQuery);

?>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { margin-bottom: 5px; }
        h2 {
            background: #ddd;
            padding: 6px;
            border-radius: 4px;
            margin-top: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        th, td {
            border: 1px solid #444;
            padding: 6px;
            font-size: 12px;
        }
        th {
            background: #333;
            color: white;
        }
    </style>
</head>
<body>

<h1>Citadel System Report</h1>
<hr>

<h2>Hospitals</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Location</th>
        <th>Phone</th>
        <th>Rating</th>
    </tr>

    <?php while ($h = $hResult->fetch_assoc()) { ?>
        <tr>
            <td><?= $h['Hospital_ID'] ?></td>
            <td><?= $h['Name'] ?></td>
            <td><?= $h['Location'] ?></td>
            <td><?= $h['Phone_Num'] ?></td>
            <td><?= $h['Rating'] ?></td>
        </tr>
    <?php } ?>
</table>


<h2>Staff</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Username</th>
        <th>Role</th>
        <th>Access Level</th>
        <th>Hospital ID</th>
        <th>Approved</th>
    </tr>

    <?php while ($s = $sResult->fetch_assoc()) { ?>
        <tr>
            <td><?= $s['Staff_ID'] ?></td>
            <td><?= $s['Name'] ?></td>
            <td><?= $s['Username'] ?></td>
            <td><?= $s['Role'] ?></td>
            <td><?= $s['Access_Level'] ?></td>
            <td><?= $s['Hos_ID'] ?></td>
            <td><?= $s['Approved'] == 1 ? "Approved" : "Pending" ?></td>
        </tr>
    <?php } ?>
</table>

</body>
</html>
