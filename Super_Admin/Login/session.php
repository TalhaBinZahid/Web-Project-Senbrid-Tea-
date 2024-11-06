<?php
session_start();

// Define the session timeout duration (15 minutes)
$timeoutDuration = 900; // 15 minutes in seconds

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if the user is not logged in
    header("Location: ../Login/login.php");
    exit();
}

// Check if the session has timed out
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeoutDuration)) {
    // Destroy session and redirect to login if timeout exceeded
    session_unset(); // Unset session variables
    session_destroy(); // Destroy session data
    header("Location: ../Login/login.php?timeout=true"); // Redirect to login page with timeout message
    exit();
}

// Update the last activity time to extend the session
$_SESSION['last_activity'] = time();
?>
