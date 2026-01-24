<?php 
//starts session + provides requireRole() access control helper

//start session
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

//checks if user is logged in (true/false)
function isLoggedIn(){
    return isset($_SESSION["user_id"]) && isset($_SESSION["role"]);
}

//redirects user if not logged in or logged in w/ diff role
function requireRole($expectedRole){
    if(!isLoggedIn()){
        header("Location: /Citadel/public/index.php");
        exit;
    }
    if($_SESSION["role"] !== $expectedRole){
        header("Location: /Citadel/public/".$_SESSION["role"]."/home.php");
        exit;
    }
}

//logout function
function logout() {
    session_unset();
    session_destroy();
}

?>