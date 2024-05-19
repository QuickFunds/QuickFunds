<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    $response = 'User session expired or not authenticated. Please log in again.';



} else {
    include "DBConnect.php";

    $userID = $_SESSION['userID'];

    // Query to check if the user has a credit card
    $stmt = $DBConnect->prepare("SELECT ccNum FROM `user` WHERE UserID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->store_result();
    $cardCount = $stmt->num_rows;
    $stmt->close();

    $DBConnect->close();

    if ($cardCount > 0) {
        $response = 'Credit card associated with the user found in the database.';

    } else {
        $response = 'No credit card associated with the user found in the database.';

    }
}

// Return the JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>