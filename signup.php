<?php 
//Signup form UI (POSTs to backend/auth/process_signup.php)

/*Signup fields:
Role
    Dropdown options

if Admin (Create Hospitals via admin/addHospital)
    Approved via backend/admin/approve_admin
    Name
    Username
    Password

if Staff
    Approved via backend/admin/approve_staff
    Name
    Role
    Password
    Hospital ID

if Patient
    Health Card Number  
    Username
    Email
    Password
*/

//grab error messages
require_once __DIR__."/../config/session.php";
if (isset($_SESSION['error'])) {
    echo '<p class="text-red-600 font-semibold">' . htmlspecialchars($_SESSION['error']) . '</p>';
    unset($_SESSION['error']); 
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>    
    <!-- Tailwind for styling-->
    <script src="https://cdn.tailwindcss.com"></script>
    <!--Pico base styling -->
    <link rel="stylesheet" href="assets/style.css">
    <!--Animated bg styling -->
    <link rel="stylesheet" href="assets/anim.css">

</head>

<body class="dark-bg">

    <!--frosted window with form-->
    <div class="min-h-screen flex items-center justify-center">
        <div class="glass space-y-4">

            <h1 class="text-4xl font-bold">Sign Up</h1>
            <h2 class="text-lg text-white-600">Enter your information</h2>

            <!--form-->
            <form class="form-card" action="../backend/auth/process_signup.php" method="POST">
                
                <!--select role-->
                <label for="role">Account Type</label>
                <select id="role" name="role" >
                    <option value="">Select a role</option>
                    <option value="patient">Patient</option>
                    <option value="staff">Staff</option>
                    <option value="admin">Admin</option>
                </select>

                <!--PATIENT FIELDS-->
                <div id="patient-fields" class="hidden mt-4">
                    <label>Health Card Number</label>
                    <input type="text" name="health_card" id="health_card" ><br><br>
                    <label>Email</label>
                    <input type="email" id="patient_email" name="patient_email"><br><br>
                    <label>Username</label>
                    <input type="text" name="patient_username" id="patient_username" ><br><br>
                    <label>Password</label>
                    <input type="password" name="patient_password" id="patient_password" ><br><br>
                </div>

                <!--STAFF FIELDS-->
                <div id="staff-fields" class="hidden mt-4">
                    <label>Name</label>
                    <input type="text" name="staff_name" id="staff_name" ><br><br>
                    <label>Staff Role</label>
                    <input type="text" name="staff_role" id="staff_role" ><br><br>
                    <label>Username</label>
                    <input type="text" name="staff_username" id="staff_username" ><br><br>
                    <label>Password</label>
                    <input type="password" name="staff_password" id="staff_password" ><br><br>
                    <label>Hospital ID</label>
                    <input type="number" name="hospital_id" id="hospital_id" ><br><br>
                </div>

                <!--ADMIN FIELDS-->
                <div id="admin-fields" class="hidden mt-4">
                    <label>Name</label>
                    <input type="text" name="admin_name" id="admin_name" ><br><br>
                    <label>Username</label>
                    <input type="text" name="admin_username" id="admin_username" ><br><br>
                    <label>Password</label>
                    <input type="password" name="admin_password" id="admin_password" ><br><br>
                </div>

               <button type="submit" class="primary mt-4">Sign Up</button>
            </form>
            <p>Already have an account? <a href="login.php">Log in</a></p>
        </div>
    </div>

    <!-- for anim bg -->
    <div id="fireflies">
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
        <div class="firefly"></div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {

            const roleSelect = document.getElementById("role");
            if (!roleSelect) return;

            roleSelect.addEventListener("change", function () {
                const role = this.value;

                // Clear the form whenever a new role is selected (except the dropdown)
                document.querySelector(".form-card").reset();
                this.value = role; // keep selected role after reset

                // hide all field groups
                document.getElementById("patient-fields").classList.add("hidden");
                document.getElementById("staff-fields").classList.add("hidden");
                document.getElementById("admin-fields").classList.add("hidden");

                //clear fields
                clearFields([
                "admin_name","admin_username","admin_password",
                "staff_name","staff_role","staff_username","staff_password","hospital_id",
                "health_card","patient_email","patient_username","patient_password"
                ]);

                // remove required from all fields
                setRequired([
                    "admin_name","admin_username","admin_password",
                    "staff_name","staff_role","staff_username","staff_password","hospital_id",
                    "health_card","patient_email","patient_username","patient_password"
                ], false);

                // show + re-add required depending on role
                if (role === "patient") {
                    document.getElementById("patient-fields").classList.remove("hidden");
                    setRequired(["health_card","patient_email","patient_username","patient_password"], true);
                }

                if (role === "staff") {
                    document.getElementById("staff-fields").classList.remove("hidden");
                    setRequired(["staff_name","staff_role","staff_username","staff_password","hospital_id"], true);
                }

                if (role === "admin") {
                    document.getElementById("admin-fields").classList.remove("hidden");
                    setRequired(["admin_name","admin_username","admin_password"], true);
                }
            });

            //helper to clear input
            function clearFields(fields) {
            fields.forEach(id => {
                const input = document.getElementById(id);
                if (input) input.value = "";
            });
    }

            // helper to toggle required
            function setRequired(fields, state) {
                fields.forEach(id => {
                    const input = document.getElementById(id);
                    if (input) input.required = state;
                });
            }

        });
    </script>

</body>
</html>
