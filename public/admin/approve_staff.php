<?php
require_once(__DIR__ . "/../../config/session.php");
requireRole("admin");
require_once(__DIR__ . "/../../backend/db.php");

// Load unapproved staff
$query = "SELECT Staff_ID, Name, Username, Role, Access_Level, Hos_ID 
          FROM Staff 
          WHERE Approved = 0";
$result = $conn->query($query);
$pendingStaff = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Approve Staff | Admin</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="../assets/anim.css">

    <style>
        table {
            width: 100%;
            color: white;
            border-collapse: collapse;
            margin-top: 2rem;
        }
        th, td {
            padding: 12px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        th {
            color: #7ddfff;
            font-weight: 600;
        }
        .btn-small {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
        }
        .approve-btn {
            background: #1e90ff;
        }
        .reject-btn {
            background: #d9534f;
        }
    </style>
</head>

<body class="dark-bg">

<!-- Background Fireflies -->
<div id="fireflies">
    <?php for ($i = 1; $i <= 20; $i++): ?>
        <div class="firefly"></div>
    <?php endfor; ?>
</div>

<header>
    <h1>Approve Staff Accounts</h1>
    <a href="./home.php" class="btn-outline">← Back to Dashboard</a>
</header>

<main class="centered">

<div class="card glass max-w-5xl">

<h2 class="mb-4">Pending Staff</h2>

<?php if (count($pendingStaff) === 0): ?>
    <p>No pending staff accounts.</p>
<?php else: ?>

<table>
    <tr>
        <th>Name</th>
        <th>Username</th>
        <th>Role</th>
        <th>Access Level</th>
        <th>Actions</th>
    </tr>

    <?php foreach ($pendingStaff as $s): ?>
    <tr id="row-<?= $s['Staff_ID'] ?>">
        <td><?= htmlspecialchars($s['Name']) ?></td>
        <td><?= htmlspecialchars($s['Username']) ?></td>
        <td><?= htmlspecialchars($s['Role']) ?></td>

        <td>
            <select id="access-<?= $s['Staff_ID'] ?>" class="bg-slate-800 p-2 rounded">
                <option value="1">1 – Basic</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4 – Senior</option>
                <option value="5">5 – Manager</option>
            </select>
        </td>

        <td>
            <button class="btn-small approve-btn"
                    onclick="approveStaff(<?= $s['Staff_ID'] ?>)">Approve</button>
            <button class="btn-small reject-btn"
                    onclick="rejectStaff(<?= $s['Staff_ID'] ?>)">Reject</button>
        </td>
    </tr>
    <?php endforeach; ?>

</table>

<?php endif; ?>

</div>
</main>

<script>
function approveStaff(id) {
    const level = document.getElementById("access-" + id).value;

    fetch("../../backend/admin/approve_staff.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: "staff_id=" + id + "&action=approve&access_level=" + level
    })
    .then(res => res.json())
    .then(data => {
        if (data.ok) {
            document.getElementById("row-" + id).remove();
            alert("Staff approved successfully.");
        } else {
            alert("Error: " + data.error);
        }
    });
}

function rejectStaff(id) {
    fetch("../../backend/admin/approve_staff.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: "staff_id=" + id + "&action=reject"
    })
    .then(res => res.json())
    .then(data => {
        if (data.ok) {
            document.getElementById("row-" + id).remove();
            alert("Staff rejected.");
        } else {
            alert("Error: " + data.error);
        }
    });
}
</script>

</body>
</html>
