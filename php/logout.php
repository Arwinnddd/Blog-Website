<?php



session_start();

// Check if the CSRF token is valid
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header("Location: userLogin.php");
    die("CSRF validation failed.");
    
}

session_destroy();

// Redirect to the login page
header("Location: userLogin.php");
exit();



?>