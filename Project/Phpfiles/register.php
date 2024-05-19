<?php
session_start();
include "DBConnect.php";
require '../vendor/autoload.php'; // Include PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Get user input
$user = $_POST['username'];
$pass = $_POST['password'];
$email = $_POST['email'];

// Check if the username or email already exists in the database
$stmt = $DBConnect->prepare("SELECT UserID FROM user WHERE UserName = ? OR Email = ?");
$stmt->bind_param("ss", $user, $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Handle existing username or email
    echo "exists";
} else {
    // Encrypt password
    $encrypted_pass = password_hash($pass, PASSWORD_DEFAULT);

    // Generate a random verification code
    $verification_code = bin2hex(random_bytes(16));

    // Prepare and bind for inserting new user
    $insertStmt = $DBConnect->prepare("INSERT INTO `user` (`UserID`, `UserName`, `Password`, `Email`, `ccNum`, `LastLogOn`, `isActive`, `isVerifiedEmail`, `verification_code`, `AccStatus`) VALUES (NULL, ?, ?, ?, NULL, NOW(), '0', '0', ?, 'Active')");
    $insertStmt->bind_param("ssss", $user, $encrypted_pass, $email, $verification_code);

    // Execute insertion
    if ($insertStmt->execute()) {

        // Send verification email
        $mail = new PHPMailer(true); // Passing `true` enables exceptions
        try {
            // Configure PHPMailer (adjust as needed)
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'quickfundslb@gmail.com';
            $mail->Password = 'saxy ozbk rwef efhf';
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

            $mail->setFrom('quickfundslb@gmail.com', 'noreply@quickfunds.com');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Email Verification';

            $mail->Body = "
            <html>
            <head>
            <style>
            /* Inline CSS styles */
            body {
                background-color: #373737e6;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                margin: 0;
                padding: 0;
                color: #fff;
            }
            .email-container {
                max-width: 600px;
                margin: 0 auto;
                background-color: #373737e6;
                border: 1px solid #ddd;
                border-radius: 5px;
                overflow: hidden;
            }
            .email-header {
                background-color: #00000;
                color: white;
                padding: 20px;
                text-align: center;
            }
            .email-header img {
                max-width: 100px;
                margin-bottom: 10px;
            }
            .email-content {
                padding: 20px;
                background-color: #373737e6;
                color: #fff;
            }
            .email-footer {
                background-color: #373737e6;
                padding: 20px;
                text-align: center;
                font-size: 12px;
                color: white;
            }
            .button {
                display: inline-block;
                padding: 10px 20px;
                margin: 20px 0;
                font-size: 16px;
                color: #fff;
                background-color: #ff4c4c;
                border: none;
                border-radius: 5px;
                text-decoration: none;
                text-align: center;
            }
            .button:hover {
                background-color: #fff;
            }
            .content-title {
                font-size: 20px;
                margin-bottom: 10px;
                color: #ff4c4c;
            }
            </style>
            </head>
            <body>
                <div class='email-container'>
                    <div class='email-header'>
                        <img src='https://i.ibb.co/y6FBJbT/about1.png' alt='QuickFunds Logo'>
                    </div>
                    <div class='email-content'>
                        <h2 class='content-title'>Email Verification</h2>
                        <p>Please click on the following link to verify your email:</p>
                        <a href='http://localhost/Project/Phpfiles/verifymail.php?email=$email&code=$verification_code' class='button'>Verify Email</a>
                    </div>
                    <div class='email-footer'>
                        © 2024 QuickFunds. All rights reserved.
                    </div>
                </div>
            </body>
            </html>";
            $mail->send();
            
            // Respond with a success message
            echo "success";
        } catch (Exception $e) {
            // Handle email sending errors
            echo "Error sending verification email";
        }
    } else {
        echo "Error creating user";
        // Handle error (show error message or redirect)
    }

    $insertStmt->close();
}

$stmt->close();
$DBConnect->close();
?>
