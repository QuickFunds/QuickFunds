<?php
// Start the session
session_start();

// Include the database connection file
include "DBConnect.php";

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    $response = 'User session expired or not authenticated. Please log in again.';
} else {
    $username = ''; // Initialize the variable to store the username

    // Check if the username is provided via POST
    if (isset($_POST['username'])) {
        $username = $_POST['username'];

        // Query to search for the user by username and retrieve the user ID
        $stmt = $DBConnect->prepare("SELECT UserID FROM `user` WHERE Username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        $userCount = $stmt->num_rows;

        if ($userCount > 0) {
            // Fetch the user ID
            $stmt->bind_result($userID);
            $stmt->fetch();
            $stmt->close();

            $response = "User found! $username's ID is: $userID";
        } else {
            $stmt->close();
            $response = "User $username has not been found :(";
        }
    } else {
        $response = "Username not provided.";
    }
}

// Return the JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>