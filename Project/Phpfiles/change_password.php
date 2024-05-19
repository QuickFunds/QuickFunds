<?php
session_start(); // Start the session

include "DBConnect.php";

// Get user input
$userID = $_SESSION['userID']; // Fetch userID from the session
$oldPass = $_POST['oldPassword'];
$newPass = $_POST['newPassword'];

// Prepare and bind to get the hashed password of the logged-in user
$stmt = $DBConnect->prepare("SELECT password FROM user WHERE userID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->bind_result($hashed_pass);
$stmt->fetch();
$stmt->close();

// Verify the old password
if (password_verify($oldPass, $hashed_pass)) {
    // Password verification successful, proceed to change password

    // Hash the new password
    $hashedNewPass = password_hash($newPass, PASSWORD_DEFAULT);

    // Prepare and execute the query to update the password
    $updateStmt = $DBConnect->prepare("UPDATE user SET password = ? WHERE userID = ?");
    $updateStmt->bind_param("si", $hashedNewPass, $userID);
    $updateStmt->execute();
    $updateStmt->close();

    $response = array("status" => "success", "message" => "Password changed successfully");
} else {
    // Password verification failed
    $response = array("status" => "error", "message" => "Invalid old password");
}

$DBConnect->close();

// Return the JSON response
header('Content-Type: application/json');
echo json_encode($response);

?>