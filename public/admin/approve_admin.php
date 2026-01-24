<?php
require_once(__DIR__ . "/../../config/session.php");
requireRole("admin");
require_once(__DIR__ . "/../../backend/db.php");

// Load unapproved admins
$query = "SELECT Admin_ID, Name, Username FROM Admin WHERE Approved = 0";
$result = $conn->query($query);
$pendingAdmins = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Approve Admins | Citadel</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="../assets/anim.css">

    <style>
        table {
            width: 100%;
            color: white;
            border-collapse: collapse;
        }
        th, td {
            padding: 14px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.12);
        }
        th {
            color: #7ddfff;
            font-weight: 600;
            font-size: 1rem;
        }
        .btn-small {
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            color: white;
        }
        .approve-btn {
            background: #1e90ff;
        }
        .reject-btn {
            background: #d9534f;
        }

        /* Centering the approval tile */
        .center-container {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
            width: 100%;
        }

        .approval-card {
            width: 75%;
            max-width: 900px;
            margin: 0 auto;
            background: rgba(11, 7, 18, 0.78);
            border-radius: 1rem;
            border: 1px solid rgba(126, 92, 255, 0.18);
            box-shadow: 0 0 25px rgba(126, 92, 255, 0.15);
            backdrop-filter: blur(8px);
            padding: 2rem;
        }
    </style>
</head>

<body class="dark-bg">

<!-- Fireflies Background -->
<div id="fireflies">
    <?php for ($i = 1; $i <= 20; $i++): ?>
        <div class="firefly"></div>
    <?php endfor; ?>
</div>

<!-- Header -->
<header style="margin-bottom: 1rem;">
    <h1>Approve Admin Accounts</h1>
    <a href="./home.php" class="btn-outline">← Back to Dashboard</a>
</header>

<!-- CENTERED CARD -->
<div class="center-container">
    <div class="approval-card">

        <h2 class="mb-4 text-xl text-white">Pending Admin Accounts</h2>

        <?php if (count($pendingAdmins) === 0): ?>
            <p>No admin accounts pending approval.</p>
        <?php else: ?>

        <table>
            <tr>
                <th>Name</th>
                <th>Username</th>
                <th>Actions</th>
            </tr>

            <?php foreach ($pendingAdmins as $a): ?>
            <tr id="row-<?= $a['Admin_ID'] ?>">
                <td><?= htmlspecialchars($a['Name']) ?></td>
                <td><?= htmlspecialchars($a['Username']) ?></td>

                <td>
                    <button class="btn-small approve-btn"
                        onclick="approveAdmin(<?= $a['Admin_ID'] ?>)">Approve</button>

                    <button class="btn-small reject-btn"
                        onclick="rejectAdmin(<?= $a['Admin_ID'] ?>)">Reject</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <?php endif; ?>

    </div>
</div>

<script>
function approveAdmin(id) {
    fetch("../../backend/admin/approve_admin.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: "admin_id=" + id + "&action=approve"
    })
    .then(r => r.json())
    .then(d => {
        if (d.ok) {
            document.getElementById("row-" + id).remove();
            alert("Admin approved.");
        } else {
            alert("Error: " + d.error);
        }
    });
}

function rejectAdmin(id) {
    fetch("../../backend/admin/approve_admin.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: "admin_id=" + id + "&action=reject"
    })
    .then(r => r.json())
    .then(d => {
        if (d.ok) {
            document.getElementById("row-" + id).remove();
            alert("Admin rejected.");
        } else {
            alert("Error: " + d.error);
        }
    });
}
</script>

</body>
</html>