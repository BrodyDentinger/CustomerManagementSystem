<?php
/*
Name: Brody Dentinger
File: logout.sql
Date: September 11, 2023
Course Code: INFT2100
*/

include("./includes/functions.php");

// start the session to track user variables
session_start();

// fetch current time
$currentTimestamp = $currentTimestamp = date("Y-m-d H:i:s");

// Record the signout.
log_activity("\n" . $currentTimestamp . ". Successful sign out. User: " . $_SESSION['email_address'] . ". \n");

// Unset all session variables (if needed)
$_SESSION = array();

// Unset all session variables
unset($_SESSION);
//unset($_SESSION['user']);

// Destroy the session
session_destroy();

//Restart the session
session_reset();
session_start();

//Logout message stored ... This message will be displayed on sign-in.php
$_SESSION['logout_message'] = "Logout Successful";

// Redirect the user to a login page 
header("Location: ./sign-in.php"); 

// exit this script
exit();
?>