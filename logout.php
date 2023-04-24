<?php
    // Clear the session variable to log out current user.
    session_start();
    $_SESSION = [];

    header("Location: index.php");
    exit("Logged out.");
?>