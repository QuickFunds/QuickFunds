<?php
session_start(); // Start the session
include "DBConnect.php";

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    echo json_encode(array("error" => "User not logged in"));
    exit;
}

$userID = $_SESSION['userID'];

// Fetch user details from the database
$stmt = $DBConnect->prepare("SELECT UserID, UserName, Balance FROM user WHERE UserID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result(); // Get the result

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Return the user details as JSON
    echo json_encode($row);
} else {
    echo json_encode(array("error" => "User not found"));
}

$stmt->close();
$DBConnect->close();
?>
