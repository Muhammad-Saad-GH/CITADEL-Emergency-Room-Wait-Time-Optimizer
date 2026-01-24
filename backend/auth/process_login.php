<?php
//Validates credentials
//Sets sessions variables (i.e. requireRole("admin"))
require_once  __DIR__."/../db.php";
require_once __DIR__."/../../config/session.php";


//get form input
$username = $_POST["username"];
$password = $_POST["password"];
$role = $_POST["login_role"];

//choose table
$table = "";
if ($role==="patient"){
    $table="Patient";
}
elseif ($role==="staff"){
    $table="Staff";
}
elseif ($role==="admin"){
    $table="Admin";
}
else{
    $_SESSION['error'] = "Invalid role";
    header("Location: /Citadel/public/login.php");
    exit;
}

//checking username and password against those in the role table
//table names: Patient, Staff, Admin

//prepared statment to check if username exists. store user tuple into $result
$stmt = $conn->prepare("SELECT * 
                        FROM `$table` 
                        WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

//if the account dne, then return to login screen with "Account does not exist"
if ($result->num_rows === 0){
    $_SESSION['error'] = "Account does not exist";
    header("Location: /Citadel/public/login.php");
    exit;
}
//store user tuple as associative array $user
$user = $result->fetch_assoc();
$stmt->close();

//check for wrong password
if(!password_verify($password, $user["Password_Hash"])){
    $_SESSION['error'] = "Incorrect password";
    header("Location: /Citadel/public/login.php");
    exit;
}

//if the account exists, then set session vars, redirect to appropriate home
//approval check (patients don't require approval)
if ($role !== "patient" && isset($user["Approve"]) && $user["Approve"] == 0) {
    $_SESSION['error'] = "Account awaiting approval. Please try again later.";
    header("Location: /Citadel/public/login.php");
    exit;
}

// login success: set session vars
$idColumn = $table . "_ID";

$_SESSION["user_id"]  = $user[$idColumn];
$_SESSION["role"]     = $role;
$_SESSION["username"] = $user["Username"];

// redirect user to their dashboard
header("Location: /Citadel/public/$role/home.php");
exit;


?>