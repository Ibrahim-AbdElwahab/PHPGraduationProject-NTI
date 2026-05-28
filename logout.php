<?php


session_start();

//  Unset all of the session variables
$_SESSION = array();

//  Destroy the session entirely
if (session_id() != "" || isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 2592000, '/');
}
session_destroy();

//  Redirect the user back to the main login page
header("Location: login.php");
exit();

// END: Logout & Session Destruction Block
