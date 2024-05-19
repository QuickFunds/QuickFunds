<?php
session_start(); // Start the session

include "DBConnect.php";
require '../vendor/autoload.php'; // Include PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if userID is set in the session
if (isset($_SESSION['userID'])) {
    // Access the userID from the session variable
    $userID = $_SESSION['userID'];

    // Get the amount and the recipient's userID from the form
    if (isset($_POST['toID'], $_POST['amount'])) {
        // Sanitize and validate input for toID and amount
        $toID = filter_var($_POST['toID'], FILTER_VALIDATE_INT);
        $amount = filter_var($_POST['amount'], FILTER_VALIDATE_FLOAT);

        if ($toID !== false && $toID > 0 && $amount !== false && $amount > 0 && $userID !== $toID) {
            // Calculate the transfer amount after deducting the fee
            $transferAmount = $amount - 1; // Deduct 1 as a fee

            // Check if the recipient's ID exists in the user table
            $checkRecipientStmt = $DBConnect->prepare("SELECT userID, Email, Username, PhoneNum FROM user WHERE userID = ?");
            $checkRecipientStmt->bind_param("i", $toID);
            $checkRecipientStmt->execute();
            $checkRecipientStmt->store_result();

            if ($checkRecipientStmt->num_rows > 0) {
                $checkRecipientStmt->bind_result($toUserID, $toUserEmail, $toUsername, $toUserPhoneNumber);
                $checkRecipientStmt->fetch();

                // Prepare SQL statement to deduct amount (including the fee) from the sender's balance
                $deductStmt = $DBConnect->prepare("UPDATE user SET balance = balance - ? WHERE userID = ? AND balance >= ?");
                $deductStmt->bind_param("ddd", $amount, $userID, $amount);
                $deductStmt->execute();

                // Check if the deduction was successful
                if ($deductStmt->affected_rows > 0) {
                    // Prepare SQL statement to add the transfer amount to the recipient's balance
                    $addStmt = $DBConnect->prepare("UPDATE user SET balance = balance + ? WHERE userID = ?");
                    $addStmt->bind_param("di", $transferAmount, $toID);
                    $addStmt->execute();

                    // Check if the addition was successful
                    if ($addStmt->affected_rows > 0) {
                        // Deduct the fee from the sender and credit it to the admin account
                        $adminFeeStmt = $DBConnect->prepare("UPDATE administration SET balance = balance + 1 WHERE UserID = 1");
                        $adminFeeStmt2 = $DBConnect->prepare("UPDATE user SET balance = (SELECT balance FROM administration WHERE UserID = 1) WHERE UserID = 1");
                        $adminFeeStmt->execute();
                        $adminFeeStmt2->execute();

                        // Prepare and execute SQL statement to insert a new transaction
                        $insertStmt = $DBConnect->prepare("INSERT INTO transactions (FromID, ToID, time, Ammount) VALUES (?, ?, current_timestamp(), ?)");
                        $insertStmt->bind_param("iii", $userID, $toID, $transferAmount);
                        $insertStmt->execute();

                        if ($insertStmt->affected_rows > 0) {
                            // Money transfer successful. Transaction recorded.
                            echo "<script>alert('Money transfer successful. Transaction recorded.');</script>";

                            // Send email to sender
                            sendEmailToUser($userID, $toUserID, $toUserEmail, $toUsername, $transferAmount, "sent");

                            // Send email to receiver
                            sendEmailToUser($toID, $userID, $toUserEmail, $toUsername, $transferAmount, "received");

                            // Get WhatsApp link
                            $whatsappLink = sendWhatsAppAlert($toUserPhoneNumber, $toUsername, $transferAmount, $toUserID);

                            // Echo JavaScript code to open WhatsApp link in a new tab
                            echo "<script>window.open('$whatsappLink', '_blank');</script>";
                        } else {
                            echo "<script>alert('Failed to record the transaction.');</script>";
                        }

                        $insertStmt->close();
                    } else {
                        echo "<script>alert('Failed to transfer money to the recipient.');</script>";
                    }
                } else {
                    echo "<script>alert('Insufficient balance.');</script>";
                }

                $deductStmt->close();
            } else {
                echo "<script>alert('Recipient ID does not exist in the database.');</script>";
            }

            $checkRecipientStmt->close();
        } else {
            echo "<script>alert('Invalid amount or recipient ID.');</script>";
        }
    } else {
        echo "<script>alert('Please fill in both recipient ID and amount.');</script>";
    }
} else {
    // Redirect the user to the login page if the session variable is not set
    header("Location: login.php");
    exit;
}

$DBConnect->close();
// Flag variable to keep track of whether the email has been sent
$emailSent = false;

// Function to send email to the user
function sendEmailToUser($userID, $toUserID, $toUserEmail, $toUsername, $transferAmount, $type) {
    global $DBConnect, $emailSent;

    // Check if the email has already been sent
    if (!$emailSent) {
        // Retrieve sender's username and email
        $senderInfoStmt = $DBConnect->prepare("SELECT Username, Email FROM user WHERE userID = ?");
        $senderInfoStmt->bind_param("i", $userID);
        $senderInfoStmt->execute();
        $senderInfoStmt->store_result();

        // Check if the query was successful
        if ($senderInfoStmt->num_rows > 0) {
            $senderInfoStmt->bind_result($senderUsername, $senderEmail);
            $senderInfoStmt->fetch();

            // Sender's email message
            $senderMessage = "
            <html>
            <head>
            <style>
            /* Add the CSS styles here */
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
                        <h2 class='content-title'>Money Transfer Confirmation</h2>
                        <p>Dear $senderUsername,</p>";

            // Receiver's email message
            $receiverMessage = "
            <html>
            <head>
            <style>
            /* Add the CSS styles here */
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
                        <h2 class='content-title'>Money Received Confirmation</h2>
                        <p>Dear $toUsername,</p>";

            if ($type === "sent") {
                $senderMessage .= "<p>You have successfully transferred $$transferAmount to $toUsername at " . date("Y-m-d H:i:s") . ".</p>";
                $receiverMessage .= "<p>You have received $$transferAmount  from $senderUsername at " . date("Y-m-d H:i:s") . ".</p>";
            } elseif ($type === "received") {
                $senderMessage .= "<p>You have received $$transferAmount  from $toUsername at " . date("Y-m-d H:i:s") . ".</p>";
                $receiverMessage .= "<p>You have sent $$transferAmount  to $senderUsername at " . date("Y-m-d H:i:s") . ".</p>";
            }

            // Construct the link to restrict the account
            $restrictLink = "http://localhost/Project/Phpfiles/restrict_account.php?userid=$userID";
            $senderMessage .= "<br><a href='$restrictLink'>Did you not complete this transaction?</a>";

            $senderMessage .= "<p>Thank you for using our service.</p>
                    </div>
                    <div class='email-footer'>
                        © 2024 QuickFunds. All rights reserved.
                    </div>
                </div>
            </body>
            </html>";

            $receiverMessage .= "<p>Thank you for using our service.</p>
                    </div>
                    <div class='email-footer'>
                        © 2024 QuickFunds. All rights reserved.
                    </div>
                </div>
            </body>
            </html>";

            // Send email using PHPMailer to sender
            sendEmail($senderEmail, "Money Transfer Confirmation", $senderMessage);

            // Send email using PHPMailer to receiver
            sendEmail($toUserEmail, "Money Received Confirmation", $receiverMessage);

            // Set the flag to indicate that the email has been sent
            $emailSent = true;
        } else {
            echo "<script>alert('Error: Failed to retrieve sender's information.');</script>";
        }

        // Close the statement
        $senderInfoStmt->close();
    }
}



// Function to send WhatsApp alert
function sendWhatsAppAlert($toUserPhoneNumber, $toUsername, $transferAmount, $toUserID) {
    // Check if the user has a phone number
    if (!empty($toUserPhoneNumber)) {
        // Construct WhatsApp message
        $message = urlencode("Dear $toUsername, you have just received $$transferAmount from UserID: $toUserID at " . date("Y-m-d H:i:s") . ".");
        
        // Construct WhatsApp link
        $whatsappLink = "https://wa.me/$toUserPhoneNumber/?text=$message";

        // Return WhatsApp link
        return $whatsappLink;
    } else {
        // If no phone number exists, return a success message
        return "Money transfer successful. Transaction recorded.";
    }
}

// Function to send email using PHPMailer
function sendEmail($toEmail, $subject, $body) {
    $mail = new PHPMailer(true); // Passing true enables exceptions
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
        echo "<script>alert('Error sending email: {$mail->ErrorInfo}');</script>";
    }
}
?>