<?php
session_start(); // Start the session

include "DBConnect.php";
require '../vendor/autoload.php'; // Include PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if the username and password are provided
if (isset($_POST['username']) && isset($_POST['password'])) {
    // Get user input
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Prepare and bind
    $stmt = $DBConnect->prepare("SELECT userID, password, isVerifiedEmail, AccStatus, loginAttempts FROM user WHERE username = ?");
    $stmt->bind_param("s", $user);

    // Execute
    $stmt->execute();

    // Bind result
    $stmt->bind_result($userID, $hashed_pass, $isVerifiedEmail, $status, $loginAttempts);

    // Fetch the result
    $stmt->fetch();

    // Close the statement
    $stmt->close();

    if ($userID) {
        if ($isVerifiedEmail == 1 && $status == "Active" && $loginAttempts < 5 && password_verify($pass, $hashed_pass)) {
            // Reset login attempts on successful login
            $stmt = $DBConnect->prepare("UPDATE user SET loginAttempts = 0 WHERE userID = ?");
            $stmt->bind_param("i", $userID);
            $stmt->execute();
            $stmt->close();

            // Store userID in a session variable
            $_SESSION['userID'] = $userID;

            $response = array("status" => "success");
        } else {
            // Handle different error cases
            if ($loginAttempts >= 5) {
                $status = "Restricted";

                // Update account status and reset login attempts
                $stmt = $DBConnect->prepare("UPDATE user SET AccStatus = ?, loginAttempts = 0 WHERE userID = ?");
                $stmt->bind_param("si", $status, $userID);
                $stmt->execute();
                $stmt->close();

                // Insert log into admin logs
                $comment = "Account Restricted by system, too many login attempts";

                // Assuming you have an AdminID (e.g., from session or other logic)
                $adminID = 1; // Replace this with the actual AdminID

                $stmt = $DBConnect->prepare("INSERT INTO adminlogs (AdminID, userID, comment) VALUES (?, ?, ?)");
                $stmt->bind_param("iis", $adminID, $userID, $comment);
                if ($stmt->execute()) {
                    $stmt->close();
                } else {
                    // Debugging: capture the error and output it
                    $error = $stmt->error;
                    $stmt->close();
                    $response = array("status" => "error", "message" => "Failed to log the restriction in admin logs: $error");
                    echo json_encode($response);
                    exit;
                }

                // Send email notification to the user
                $mail = new PHPMailer(true);
                try {
                    // Configure PHPMailer using existing mailer info
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'quickfundslb@gmail.com'; // Replace with your Gmail email address
                    $mail->Password = 'saxy ozbk rwef efhf'; // Replace with your Gmail password
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

                    $mail->setFrom('quickfundslb@gmail.com', 'QuickFunds Admin');
                    $mail->addAddress($userEmail);
                    $mail->isHTML(true);
                    $mail->Subject = 'Account Status Update';

                    // Email body with consistent styling
                    $mail->Body = "
                    <html>
                    <head>
                    <style>
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
                                <h2 class='content-title'>Account Status Update</h2>
                                <p>Dear $username,</p>
                                <p>Your account status has been updated to <strong>$status</strong>. This action has been taken by the system due to many failed login in attempts.</p>
                                <p>If you have any questions, please contact our support team.</p>
                                <a href='mailto:quickfundslb@gmail.com?subject=Account Status Inquiry for user:$userID' class='button'>Contact Support</a>
                                <p>Thank you,<br>QuickFunds Team</p>
                            </div>
                            <div class='email-footer'>
                                © 2024 QuickFunds. All rights reserved.
                            </div>
                        </div>
                    </body>
                    </html>";

                    $mail->send();
                } catch (Exception $e) {
                    // Handle email sending errors
                    echo "Error sending email: " . $mail->ErrorInfo;
                }
                
                 } else {
                // Handle different error cases
                if ($isVerifiedEmail == 0) {
                    $response = array("status" => "error", "message" => "Your email is not verified. Please check your email for verification instructions.");
                } elseif ($status == "Restricted") {
                    $response = array("status" => "error", "message" => "Your account is restricted. Please contact support for further information.");
                } elseif ($status == "Suspended") {
                    $response = array("status" => "error", "message" => "Your account is suspended until further notice.");
                } else {
                    // Increment login attempts on failed login
                    $stmt = $DBConnect->prepare("UPDATE user SET loginAttempts = loginAttempts + 1 WHERE userID = ?");
                    $stmt->bind_param("i", $userID);
                    $stmt->execute();
                    $stmt->close();

                    $response = array("status" => "error", "message" => "Invalid username or password");
                }
            }
        }
    } else {
        $response = array("status" => "error", "message" => "Invalid username or password");
    }
} else {
    $response = array("status" => "error", "message" => "Username or password not provided");
}

$DBConnect->close();

// Return the JSON response
header('Content-Type: application/json');
echo json_encode($response);

?>


