<?php
require_once __DIR__ . "/../config/config.php";

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$newPassword = "123";
$newHash = password_hash($newPassword, PASSWORD_DEFAULT);

$tables = ["Admin", "Staff", "Patient"];

foreach ($tables as $table) {
    $sql = "UPDATE {$table} SET Password_Hash = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed for {$table}: " . $conn->error);
    }

    $stmt->bind_param("s", $newHash);

    if (!$stmt->execute()) {
        die("Execute failed for {$table}: " . $stmt->error);
    }

    $stmt->close();
}

$conn->close();
echo "Done. All Admin, Staff, and Patient passwords set to: 123";
