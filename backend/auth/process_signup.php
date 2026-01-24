<?php
//Inserts new user based on role

require_once __DIR__."/../db.php";
require_once __DIR__."/../../config/session.php";

//get role, this will decide where we will add the tuple and what info to expect
$role = $_POST["role"];

//incase we get some next role
$allowed = ["patient", "staff", "admin"];
if (!in_array($role, $allowed)) {
    $_SESSION['error'] = "Invalid role";
    header("Location: /Citadel/public/signup.php");
    exit;
}

//validate all input before proceeding
$requiredFields = [];

if ($role === "patient") {
    $requiredFields = ["health_card", "patient_username", "patient_email", "patient_password"];
}
elseif ($role === "staff") {
    $requiredFields = ["staff_name", "staff_role", "staff_username", "staff_password", "hospital_id"];
}
elseif ($role === "admin") {
    $requiredFields = ["admin_name", "admin_username", "admin_password"];
}
foreach ($requiredFields as $f) {
    if (empty($_POST[$f])) {
        $_SESSION['error'] = "Missing required field: $f";
        header("Location: /Citadel/public/signup.php");
        exit;
    }
}


/*if patient AND HC_num, username, and email dne then add:
    health_card (HC_Num), 
    username (Username), 
    patient_email (Email), 
    patient_password (Password_Hash) to the Patient table
*/
if($role === "patient"){
    $hc_num=$_POST["health_card"];
    $username=$_POST["patient_username"];
    $email=$_POST["patient_email"];
    $password=$_POST["patient_password"];

    //check uniqueness, store tuple in $result
    $stmt = $conn->prepare("SELECT *
                                    FROM Patient
                                    WHERE HC_Num = ? OR Username = ? OR Email = ?");
    $stmt->bind_param("sss", $hc_num, $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    if ($result->num_rows>0){
        $_SESSION['error'] = "Patient account already exists (HC, username or email taken)";
        header("Location: /Citadel/public/signup.php");
        exit;
    }

    //insert new patient
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO Patient (HC_Num, Username, Email, Password_Hash)
                                    VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $hc_num, $username, $email, $passwordHash);
    $stmt->execute();
    //save ID
    $userID = $stmt->insert_id;
    $stmt->close();

}

/*if staff AND username dne then add:
    staff_name (Name), 
    staff_role (Role), 
    staff_username (Username), 
    staff_password (Password_Hash) 
    hospital_id (Hos_ID) to the Patient table
*/
elseif($role==="staff"){
    $name = $_POST["staff_name"];
    $staff_role = $_POST["staff_role"];
    $username = $_POST["staff_username"];
    $password = $_POST["staff_password"];
    $hos_id = $_POST["hospital_id"];

    //check for uniqueness
    $stmt= $conn->prepare("SELECT *
                            FROM Staff
                            WHERE Username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    if($result->num_rows>0){
        $_SESSION['error'] = "Username taken";
        header("Location: /Citadel/public/signup.php");
        exit;
    }

    //insert into Staff table
    $passwordHash=password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO Staff (Name, Role, Username, Password_Hash, Hos_ID, Approved)
                                    VALUES (?, ?, ?, ?, ?, 0)");
    $stmt->bind_param("ssssi", $name, $staff_role, $username, $passwordHash, $hos_id);
    $stmt->execute();
    //save ID
    $userID = $stmt->insert_id;
    $stmt->close();

}


/*if admin AND username dne then add:
    admin_name (Name), 
    admin_username (Username), 
    admin_password (Password_Hash) to the Admin table
*/
elseif ($role === "admin"){
    $name = $_POST["admin_name"];
    $username = $_POST["admin_username"];
    $password = $_POST["admin_password"];

    //check uniqueness
    $stmt = $conn->prepare("SELECT *
                            FROM Admin
                            WHERE Username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    if ($result->num_rows>0){
        $_SESSION['error'] = "Username taken";
        header("Location: /Citadel/public/signup.php");
        exit;
    }

    //insert into Admin Table
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO Admin(Name, Username, Password_Hash, Approved)
                                    VALUES (?, ?, ?, 0)");
    $stmt->bind_param("sss", $name, $username, $passwordHash);
    $stmt->execute();
    //save ID
    $userID=$stmt->insert_id;
    $stmt->close();
}

//if account has been added successfully
//then set session vars, redirect to appropriate home
if($role === "patient"){
    $_SESSION["user_id"] = $userID;
    $_SESSION["role"] = $role;
    $_SESSION["username"] = $username;

    //redirect user
    header("Location: /Citadel/public/$role/home.php");
    exit;
}
else{
    $_SESSION['error'] = "Account awaiting approval. Please check back after the next work day.";
    header("Location: /Citadel/public/signup.php");
    exit;
}
?>