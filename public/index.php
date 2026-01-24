<?php 
//Landing page. lets users either login or signup.
?>

<!DOCTYPE html>
<html>
<head>
    <title>Citadel</title>    
    <!-- Tailwind for styling-->
    <script src="https://cdn.tailwindcss.com"></script>
    <!--Pico base styling -->
    <link rel="stylesheet" href="assets/style.css">
    <!--Animated bg styling -->
    <link rel="stylesheet" href="assets/anim.css">

</head>

<body class="dark-bg">

    <!--frosted window with content-->
    <div class="min-h-screen flex items-center justify-center">
        <div class="glass text-center space-y-4">

            <h1 class="text-4xl font-bold">Citadel</h1>
            <h2 class="text-lg text-white-600">Overseeing the ER</h2>

            <!--buttons-->
            <div class="space-x-4">
                <a href="login.php">
                <button class="primary">Log In</button>
                </a>

                <a href="signup.php">
                <button class="outline">Sign Up</button>
                </a>
            </div>
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
