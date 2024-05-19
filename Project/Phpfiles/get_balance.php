<?php
session_start(); // Start the session

include "DBConnect.php";

// Check if userID is set in the session
if (isset($_SESSION['userID'])) {
    // Access the userID from the session variable
    $userID = $_SESSION['userID'];

    // Fetch user's balance from the database
    $stmt = $DBConnect->prepare("SELECT balance FROM user WHERE userID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo $row['balance'];
    } else {
        echo "N/A";
    }

    $stmt->close();
} else {
    echo "Session expired or not logged in.";
}

$DBConnect->close();
?>
