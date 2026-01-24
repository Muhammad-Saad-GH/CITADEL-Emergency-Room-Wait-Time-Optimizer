<?php
// Inserts new hospital into DB

require_once(__DIR__ . "/../db.php");
require_once(__DIR__ . "/../../config/session.php");
requireRole("admin");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../public/admin/addHospital.php?error=Invalid+request+method");
    exit;
}

$name     = trim($_POST['name'] ?? '');
$location = trim($_POST['location'] ?? '');
$phone    = trim($_POST['phone'] ?? '');
$rating   = isset($_POST['rating']) ? (int) $_POST['rating'] : 0;

if ($name === '' || $location === '' || $phone === '' || $rating < 1 || $rating > 5) {
    header("Location: ../../public/admin/addHospital.php?error=Please+fill+all+fields+correctly");
    exit;
}

// Use schema: Hospital( Hospital_ID, Phone_Num, Rating, Name, Location )
$sql = "INSERT INTO Hospital (Name, Location, Phone_Num, Rating)
        VALUES (?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    header("Location: ../../public/admin/addHospital.php?error=Database+prepare+failed");
    exit;
}

$stmt->bind_param("sssi", $name, $location, $phone, $rating);

if ($stmt->execute()) {
    header("Location: ../../public/admin/addHospital.php?msg=Hospital+created+successfully");
} else {
    header("Location: ../../public/admin/addHospital.php?error=Database+error");
}

$stmt->close();
$conn->close();
