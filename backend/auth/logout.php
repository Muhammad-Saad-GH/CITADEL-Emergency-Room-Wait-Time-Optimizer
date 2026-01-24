<?php 
//Clears sessions and logs user out

require_once __DIR__."/../../config/session.php";

logout();

header("Location: /Citadel/public/index.php");
exit;
?>