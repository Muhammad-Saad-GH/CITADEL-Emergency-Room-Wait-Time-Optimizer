<?php
require_once(__DIR__ . "/../../config/session.php");
requireRole("admin");


//UNIVERSAL VIEW LOADER (runs only if ?view=... is present)
if (isset($_GET['view'])) {
    require_once(__DIR__ . "/../../backend/db.php");

    $view = $_GET['view'];
    $allowed = [
        "Hospital_Staff_Count",
        "Completed_Cases_Per_Hospital",
        "Staff_Case_Count"
    ];

    if (!in_array($view, $allowed)) {
        echo "<p style='color:red;'>Invalid view.</p>";
        exit;
    }

    $result = $conn->query("SELECT * FROM $view");

    echo "<table class='styled-table'>";
    echo "<thead><tr>";

    foreach ($result->fetch_fields() as $field) {
        echo "<th>{$field->name}</th>";
    }
    echo "</tr></thead><tbody>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $cell) {
            echo "<td>{$cell}</td>";
        }
        echo "</tr>";
    }

    echo "</tbody></table>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Citadel</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Global Styles -->
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="../assets/anim.css">

    <style>
        .admin-grid {
            display: grid;
            gap: 2rem;
            padding: 2rem;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            max-width: 1300px;
            margin: 0 auto;
        }

        .admin-card {
            background: rgba(11, 7, 18, 0.72);
            border: 1px solid rgba(126, 92, 255, 0.18);
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 0 18px rgba(126, 92, 255, 0.12);
            backdrop-filter: blur(8px);
            transition: 0.25s ease;
        }

        .admin-card:hover {
            border-color: rgba(126, 92, 255, 0.45);
            box-shadow: 0 0 24px rgba(126, 92, 255, 0.28);
            transform: translateY(-4px);
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            background: rgba(11, 7, 18, 0.95);
            border: 1px solid rgba(126, 92, 255, 0.22);
            box-shadow: 0 0 15px rgba(126, 92, 255, 0.22);
            border-radius: 8px;
            margin-top: 10px;
            min-width: 180px;
            z-index: 9999;
        }


        .dropdown-menu button {
            width: 100%;
            background: transparent;
            border: none;
            text-align: left;
            padding: 10px 14px;
            color: #e2ecf5;
            cursor: pointer;
        }

        .dropdown-menu button:hover {
            background: rgba(126, 92, 255, 0.15);
        }

        #viewResults {
            max-width: 1300px;
            margin: 2rem auto;
            padding: 1.5rem;
            background: rgba(11, 7, 18, 0.72);
            border: 1px solid rgba(126, 92, 255, 0.18);
            border-radius: 12px;
            display: none;
        }

        .styled-table {
            width: 100%;
            border-collapse: collapse;
        }

        .styled-table th,
        .styled-table td {
            padding: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            color: #ede9fe;
        }

        .styled-table th {
            background: rgba(126, 92, 255, 0.15);
        }

    </style>
</head>

<body class="dark-bg">

    <!-- Fireflies -->
    <div id="fireflies">
        <?php for ($i = 1; $i <= 20; $i++): ?>
            <div class="firefly"></div>
        <?php endfor; ?>
    </div>

    <!-- Header -->
    <header>
        <h1>Admin Dashboard</h1>
        <div>
            <span class="user-info">Logged in as: <strong>Admin</strong></span>
            <a href="../../backend/auth/logout.php" class="btn-outline" style="margin-left: 1rem;">
                Logout
            </a>
        </div>
    </header>

    <!-- Admin Options -->
    <div class="admin-grid">

        <!-- HOSPITAL MANAGEMENT -->
        <div class="admin-card">
            <h2>Hospitals</h2>
            <p>Manage hospital records, create new hospitals, and view patient-related data.</p>
            <a class="btn" href="./addHospital.php">Manage Hospitals</a>
        </div>

        <!-- STAFF APPROVALS -->
        <div class="admin-card">
            <h2>Staff Approvals</h2>
            <p>Approve or reject staff accounts and assign access levels.</p>
            <a class="btn" href="./approve_staff.php">Approve Staff</a>
        </div>

        <!-- ADMIN APPROVALS -->
        <div class="admin-card">
            <h2>Admin Approvals</h2>
            <p>Review and approve newly registered Citadel admin accounts.</p>
            <a class="btn" href="./approve_admin.php">Approve Admins</a>
        </div>

    </div> <!-- END GRID -->

    <!-- PDF + VIEW ROW -->
    <div class="flex items-center justify-between px-12 mt-6">

        <!-- OPEN PDF BUTTON -->
        <button onclick="window.open('../../backend/export/export_pdf.php', '_blank')" 
                class="primary px-10 py-3 text-lg">
            Open PDF
        </button>

        <!-- VIEW DROPDOWN -->
        <div class="dropdown">
            <button class="btn" onclick="toggleDropdown()">View ▼</button>
            <div id="dropdownMenu" class="dropdown-menu">
                <button onclick="selectView('Hospital_Staff_Count')">Staff Count</button>
                <button onclick="selectView('Completed_Cases_Per_Hospital')">Completed Cases</button>
                <button onclick="selectView('Staff_Case_Count')">Cases Handled</button>
            </div>
        </div>

    </div>

    <!-- RESULTS AREA -->
    <div id="viewResults"></div>

<script>
let currentView = null;

function toggleDropdown() {
    const menu = document.getElementById("dropdownMenu");
    menu.style.display = menu.style.display === "block" ? "none" : "block";
}

function selectView(viewName) {
    const menu = document.getElementById("dropdownMenu");
    menu.style.display = "none";

    const results = document.getElementById("viewResults");

    if (currentView === viewName) {
        results.style.display = "none";
        results.innerHTML = "";
        currentView = null;
        return;
    }

    results.style.display = "block";
    results.innerHTML = "<p style='color:#7dd3fc;'>Loading...</p>";

    fetch(`home.php?view=${viewName}`)
        .then(res => res.text())
        .then(html => {
            results.innerHTML = html;
            currentView = viewName;
        });
}
</script>

</body>
</html>
