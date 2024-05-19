<?php
include "DBConnect.php";

// Function to sanitize input
function sanitize_input($data) {
    global $DBConnect;
    $data = mysqli_real_escape_string($DBConnect, $data);
    return $data;
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize comment input
    $comment = sanitize_input($_POST["comment"]);

    // Get current timestamp
    $time = date("Y-m-d H:i:s");

    // Prepare and execute SQL query to insert comment into database
    $sql = "INSERT INTO comments (comment, time) VALUES (?, ?)";
    $stmt = $DBConnect->prepare($sql);
    $stmt->bind_param("ss", $comment, $time);

    if ($stmt->execute()) {
        echo "Comment inserted successfully.";
    } else {
        echo "Error inserting comment: " . $stmt->error;
    }

    $stmt->close();
}

$DBConnect->close();
?>
