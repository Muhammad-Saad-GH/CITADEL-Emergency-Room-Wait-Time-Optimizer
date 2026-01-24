<?php
require_once(__DIR__ . "/../../config/session.php");
requireRole("patient");

require_once(__DIR__ . "/../../backend/db.php");

// Fetch hospital list
$hospitals = [];
$sql = "SELECT Hospital_ID, Name, Location FROM Hospital ORDER BY Name";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $hospitals[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create New Check-in</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <header>
        <div>
            <h1>Create New Check-In</h1>
            <div class="user-info">
                Logged in as:
                <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'Patient'); ?></strong>
            </div>
        </div>

        <div>
            <a href="home.php" class="btn btn-outline">Back to Dashboard</a>
            <a href="../../backend/auth/logout.php" class="btn btn-outline">Logout</a>
        </div>
    </header>

    <main>
        <div class="form-card">
            <?php if (isset($_GET['success']) && $_GET['success'] === '1'): ?>
                <p style="color:green; margin-bottom: 1rem;">Check-in created successfully!</p>
            <?php elseif (isset($_GET['error'])): ?>
                <p style="color:red; margin-bottom: 1rem;"><?php echo htmlspecialchars($_GET['error']); ?></p>
            <?php endif; ?>

            <form action="../../backend/patient/create_checkin.php" method="post">
                <!-- Hospital Selection -->
                <div style="margin-bottom: 1rem;">
                    <label for="hos_id">Select Hospital:</label>
                    <select name="hos_id" id="hos_id" required>
                        <option value="">Choose a Hospital</option>
                        <?php foreach ($hospitals as $h): ?> 
                            <option value="<?php echo (int)$h['Hospital_ID']; ?>">
                                <?php echo htmlspecialchars($h['Name'] . " - " . $h['Location']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Notes -->
                <div style="margin-bottom: 1rem; margin-right: 3rem;">
                    <label for="notes">Describe Your Symptoms with Detail:</label>
                    <textarea
                        name="notes"
                        id="notes"
                        rows="4"
                        placeholder="Describe what you are experiencing (optional)..."
                       height="120"></textarea>
                </div>
                
                <!-- No severity, no wait time, no approved here -->
                <button type="submit" class="primary">
                    Submit Check-In
                </button>
            </form>
        </div>
    </main>
</body>
</html>