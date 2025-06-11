<?php
session_start();
session_destroy(); 
header("Location: login.php");
exit(); // Ensure no further code is executed after redirection
?>