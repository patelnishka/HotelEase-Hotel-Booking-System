<?php 
    session_start();
    session_unset(); // Removes all session variables
    session_destroy(); // Destroys the session
    header("Location: login.php"); // Sends you back to the login page
    exit;
?>