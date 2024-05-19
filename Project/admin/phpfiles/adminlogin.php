<?php
session_start(); // Start the session

include "DBConnect.php";

// Get user input
$user = $_POST['username'];
$pass = $_POST['password'];

// Prepare and bind
$stmt = $DBConnect->prepare("SELECT userID, password FROM user WHERE username = ?");
$stmt->bind_param("s", $user);

// Execute
$stmt->execute();

// Bind result
$stmt->bind_result($userID, $hashed_pass);

// Fetch the result
$stmt->fetch();

// Close the statement and free the result set
$stmt->close();

// Verify the password
if (password_verify($pass, $hashed_pass)) {
    // Check if the user is an admin
    $isAdmin = false;

    // Prepare and bind for checking admin privileges
    $adminStmt = $DBConnect->prepare("SELECT UserID FROM administration WHERE UserID = ?");
    $adminStmt->bind_param("i", $userID);
    $adminStmt->execute();
    $adminStmt->store_result();

    // If the user is found in the administration table, consider them an admin
    if ($adminStmt->num_rows > 0) {
        $isAdmin = true;
        
        // Fetch the adminID
        $adminStmt->bind_result($adminID);
        $adminStmt->fetch();
        
        // Store adminID in admin session variable
        $_SESSION['adminID'] = $adminID;
        $_SESSION['isAdmin'] = true;
    }

    $adminStmt->close();

    // Store userID in user session variable if not already set
    if (!isset($_SESSION['userID'])) {
        $_SESSION['userID'] = $userID;
    }

    // Return success response
    $response = array("status" => "success", "isAdmin" => $isAdmin);
} else {
    // Return error response for invalid username or password
    $response = array("status" => "error", "message" => "Invalid username or password");
}

$DBConnect->close();

// Return the JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
