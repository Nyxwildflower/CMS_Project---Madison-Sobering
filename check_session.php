<?php
    session_start();

    // Verify the user has signed in at some point.
    if(!isset($_SESSION['user']) && !isset($_SESSION['admin'])){
        header('HTTP/1.1 401 Unauthorized');
        header('Location: authorize.php');
        exit("Please login.");
    }
?>