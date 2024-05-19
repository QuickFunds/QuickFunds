<?php
include "DBConnect.php";

// Get user input
$username = $_POST['username'];
$email = $_POST['email'];

// Check if username or email exists in the database
$stmt = $DBConnect->prepare("SELECT COUNT(*) FROM `user` WHERE `UserName` = ? OR `Email` = ?");
$stmt->bind_param("ss", $username, $email);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();

// Check the count of matching records
if ($count > 0) {
    echo 'exists'; // Username or Email already exists
} else {
    echo 'not_exists'; // Username and Email do not exist
}

$stmt->close();
$DBConnect->close();
?>