<?php
require_once(__DIR__ . "/../../config/session.php");
requireRole("admin");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Hospital | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="../assets/anim.css">
</head>
<body class="dark-bg">
    <div id="fireflies">
        <?php for ($i = 1; $i <= 20; $i++): ?>
            <div class="firefly"></div>
        <?php endfor; ?>
    </div>

    <main class="centered">
        <div class="card glass max-w-xl">
            <h1>Add Hospital</h1>
            <p class="subtitle">
                Enter details for a new hospital in the Citadel system.
            </p>

            <?php if (isset($_GET['msg'])): ?>
                <p class="success-msg"><?= htmlspecialchars($_GET['msg']) ?></p>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <p class="error-msg"><?= htmlspecialchars($_GET['error']) ?></p>
            <?php endif; ?>

            <form action="../../backend/admin/add_hospital.php" method="post" class="form">
                <label>
                    Name
                    <input type="text" name="name" required>
                </label>

                <label>
                    Location
                    <input type="text" name="location" required>
                </label>

                <label>
                    Phone Number
                    <input type="text" name="phone" maxlength="10" required>
                </label>

                <label>
                    Rating (1–5)
                    <input type="number" name="rating" min="1" max="5" required>
                </label>

                <button type="submit" class="primary w-full">
                    Create Hospital
                </button>
            </form>

            <div style="margin-top: 1rem;">
                <a href="./home.php" class="btn-outline">← Back to Dashboard</a>
            </div>
        </div>
    </main>
</body>
</html>
