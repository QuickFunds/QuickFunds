<?php
session_start();
include "DBConnect.php";

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    // Redirect the user to the login page if not logged in
    header("Location: login.php");
    exit();
}

// Check if the phone number is provided
if(isset($_POST['phoneNumber'])) {
    // Get the user's ID from the session
    $userID = $_SESSION['userID'];
    
    // Get the phone number from the form
    $phoneNumber = $_POST['phoneNumber'];

    // Check if the phone number starts with '+'
    if (!startsWith($phoneNumber, '+')) {
        // If not, prepend '+' to the phone number
        $phoneNumber = '+' . $phoneNumber;
    }
    
    // Prepare and bind for updating the user's phone number
    $stmt = $DBConnect->prepare("UPDATE user SET phoneNum = ? WHERE userID = ?");
    $stmt->bind_param("si", $phoneNumber, $userID);
    
    // Execute the update
    if ($stmt->execute()) {
        // Phone number successfully updated
        echo json_encode(array("status" => "success"));
    } else {
        // Error updating phone number
        echo json_encode(array("status" => "error", "message" => "Error updating phone number"));
    }
    
    $stmt->close();
} else {
    // Phone number not provided
    echo json_encode(array("status" => "error", "message" => "Phone number not provided"));
}

$DBConnect->close();

// Function to check if a string starts with a specific prefix
function startsWith($haystack, $needle) {
    return substr($haystack, 0, strlen($needle)) === $needle;
}
?>
