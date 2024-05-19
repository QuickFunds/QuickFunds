<?php
// Start the session
session_start();

// Check if userID is set in the session
if(isset($_SESSION['userID'])) {
    include "DBConnect.php";

    // Retrieve userID from the session
    $userID = $_SESSION['userID'];

    // Update isActive status to 0 for the logged-out user
    $updateIsActive = $DBConnect->prepare("UPDATE user SET isActive = 0 WHERE userID = ?");
    $updateIsActive->bind_param("i", $userID);
    $updateIsActive->execute();
    $updateIsActive->close();

    $DBConnect->close();
}

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the login page or any other desired page after logout
header("Location: ../index.html"); // Replace 'login.php' with your actual login page URL
exit;
?>
