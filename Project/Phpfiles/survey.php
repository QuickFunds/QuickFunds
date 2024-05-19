<?php
session_start(); // Start the session

include "DBConnect.php";

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    // Redirect or handle unauthorized access
    exit("Unauthorized access");
}

// Get user ID from session
$userID = $_SESSION['userID'];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $likes = $_POST['likes'];
    $improvements = $_POST['improvements'];
    $additionalComments = $_POST['additionalComments'];
    $experienceRating = $_POST['experienceRating'];
    $likelihoodRecommend = $_POST['likelihoodRecommend'];

    // Prepare and bind the INSERT statement
    $stmt = $DBConnect->prepare("INSERT INTO surveyresponses (userID, likes, improvements, additionalComments, experienceRating, likelihoodRecommend) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssii", $userID, $likes, $improvements, $additionalComments, $experienceRating, $likelihoodRecommend);

    // Execute the statement
    if ($stmt->execute()) {
        // Insert successful
        $response = array("status" => "success", "message" => "Survey response submitted successfully");
        // Redirect back to user page
        header("Location: ../user/all.html");
        exit(); // Stop further execution
    } else {
        // Insert failed
        $response = array("status" => "error", "message" => "Failed to submit survey response");
    }

    // Close the statement
    $stmt->close();
} else {
    // If the form is not submitted
    $response = array("status" => "error", "message" => "Form submission method not allowed");
}

// Close the database connection
$DBConnect->close();

// Return the JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
