<?php
// Start the session
session_start();

include "DBConnect.php";
require '../vendor/autoload.php'; // Include PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Function to send email using PHPMailer
function sendEmail($toEmail, $subject, $body) {
    $mail = new PHPMailer(true); // Passing `true` enables exceptions
    try {
        // Configure PHPMailer using provided mailer info
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'quickfundslb@gmail.com'; // Use provided Gmail email address
        $mail->Password = 'saxy ozbk rwef efhf'; // Use provided Gmail password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        // Disable SSL certificate verification
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Send email
        $mail->setFrom('quickfundslb@gmail.com', 'Quick Funds');
        $mail->addAddress($toEmail);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->send();
    } catch (Exception $e) {
        // Handle email sending errors
        echo "Error sending email: {$mail->ErrorInfo}";
    }
}

// Check if the user ID is provided in the URL
if (isset($_GET['userid'])) {
    // Extract user ID from the URL
    $userID = $_GET['userid'];

    // Update the user's account status to "Restricted"
    $status = "Restricted";
    $comment = "User account restricted by system due to suspicious activity";

    // Prepare and execute SQL statement to update the user's account status
    $updateStmt = $DBConnect->prepare("UPDATE user SET AccStatus = ? WHERE UserID = ?");
    $updateStmt->bind_param("si", $status, $userID);
    $updateStmt->execute();

    // Check if the update was successful
    if ($updateStmt->affected_rows > 0) {
        // Get the admin ID (assuming it's 1 for this example)
        $adminID = 1; // Change this according to your admin ID logic

        // Insert a new log entry into adminlogs table
        $insertStmt = $DBConnect->prepare("INSERT INTO adminlogs (AdminID, UserID, Action, Comment) VALUES (?, ?, ?, ?)");
        $insertStmt->bind_param("iiss", $adminID, $userID, $status, $comment);
        $insertStmt->execute();

        // Check if the insertion was successful
        if ($insertStmt->affected_rows > 0) {
            // Retrieve the user's email address from the database
            $getUserEmailStmt = $DBConnect->prepare("SELECT Email FROM user WHERE UserID = ?");
            $getUserEmailStmt->bind_param("i", $userID);
            $getUserEmailStmt->execute();
            $getUserEmailStmt->bind_result($userEmail);
            $getUserEmailStmt->fetch();

            // Send email to the user notifying about the account restriction
            $subject = "Account Restriction Notification";
            $body = "Dear User,<br><br>Your account has been temporarily restricted due to suspicious activity. Our support team will investigate the matter, and you will be notified via email within 1-3 business days.<br><br>Thank you for your understanding.<br>Quick Funds Support Team";
            sendEmail($userEmail, $subject, $body);

            // Redirect to the home page
            header("Location: ../index.html");
            exit();
        } else {
            echo "Failed to insert record into admin logs.";
        }
    } else {
        echo "Failed to update user's account status.";
    }

    // Close the prepared statements
    $updateStmt->close();
    if (isset($insertStmt)) {
        $insertStmt->close();
    }

    // Close the database connection
    $DBConnect->close();
} else {
    echo "User ID not provided.";
}
?>
