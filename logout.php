<!-- Deletes the session information when the logout link is clicked. -->

<?php
    session_start();
    $_SESSION = [];

    header("Location: index.php");
    exit("Logged out.");
?>