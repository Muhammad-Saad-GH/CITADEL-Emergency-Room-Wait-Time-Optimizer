<?php //Login form (POSTs to backend/auth/process_login.php)

/*
Login fields:
Role
    Dropdown options (because staff and admin could have patient account)    

if Admin
    Username
    Password

if Staff
    Username
    Password

if Patient
    Username
    Password
*/

//grab error messages.
require_once __DIR__."/../config/session.php";
if (isset($_SESSION['error'])) {
    echo '<p class="text-red-600 font-semibold">' . htmlspecialchars($_SESSION['error']) . '</p>';
    unset($_SESSION['error']); 
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>    
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

            <h1 class="text-4xl font-bold">Login</h1>
            <h2 class="text-lg text-white-600">Enter your credentials</h2>

            <!--form-->
            <form class="form-card" action="../backend/auth/process_login.php" method="POST">
                <label for="role">Account Type</label>
                <select name="login_role" required>
                    <option value="patient">Patient</option>
                    <option value="staff">Staff</option>
                    <option value="admin">Admin</option>
                </select><br><br>

                <label>Username</label>
                <input type="text" name="username" required><br><br>

                <label>Password</label>
                <input type="password" name="password" required><br><br>

                <button type="submit" class="primary">Log in</button>
            </form>
            <p>Don't have an account? <a href="signup.php">Sign up</a></p>
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

</body>
</html>
