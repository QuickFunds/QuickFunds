<?php
session_start(); // Start the session

include "DBConnect.php";
require '../vendor/autoload.php'; // Include PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if userID is set in the session
if (!isset($_SESSION['userID'])) {
    // Redirect the user to the login page if the session variable is not set
    header("Location: ../login/dist/index.html");
    exit();
}

$userID = $_SESSION['userID'];

// Verify that the userID exists in the user table
$stmt = $DBConnect->prepare("SELECT balance FROM user WHERE userID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->bind_result($balance);
$stmt->fetch();
$stmt->close();

// Handle gift card purchase
if (isset($_POST['purchaseAmount'])) {
    $amount = filter_var($_POST['purchaseAmount'], FILTER_VALIDATE_FLOAT);
    if ($amount !== false && $amount > 0) {
        $totalAmount = $amount + 1; // Include the $1 fee

        if ($balance >= $totalAmount) {
            $DBConnect->begin_transaction();

            try {
                // Deduct total amount from user balance
                $deductStmt = $DBConnect->prepare("UPDATE user SET balance = balance - ? WHERE userID = ?");
                $deductStmt->bind_param("di", $totalAmount, $userID);
                $deductStmt->execute();

                if ($deductStmt->affected_rows > 0) {
                    // Generate a random 10-digit code
                    $cardNum = strtoupper(bin2hex(random_bytes(5)));

                    // Insert gift card into database with redeemedBy as 1 (system)
                    $insertStmt = $DBConnect->prepare("INSERT INTO giftcards (cardnum, balance, isRedeemed, createdby, redeemedBy) VALUES (?, ?, 0, ?, 1)");
                    $insertStmt->bind_param("sdi", $cardNum, $amount, $userID);
                    $insertStmt->execute();

                    if ($insertStmt->affected_rows > 0) {
                        // Email the gift card details to the user
                        sendGiftCardEmail($userID, $cardNum, $amount);

                        // Add $1 fee to the administration account
                        $adminFeeStmt = $DBConnect->prepare("UPDATE administration SET balance = balance + 1 WHERE UserID = 1");
                        $adminFeeStmt2 = $DBConnect->prepare("UPDATE user SET balance = (SELECT balance FROM administration WHERE UserID = 1) WHERE UserID = 1");
                        $adminFeeStmt->execute();
                        $adminFeeStmt2->execute();

                        $DBConnect->commit();
                        $_SESSION['success_message'] = "Gift card purchased successfully. The code has been emailed to you.";
                        header("Location: ".$_SERVER['PHP_SELF']);
                        exit;
                    } else {
                        throw new Exception("Failed to insert gift card into database.");
                    }
                    $insertStmt->close();
                } else {
                    throw new Exception("Insufficient balance.");
                }
                $deductStmt->close();
            } catch (Exception $e) {
                $DBConnect->rollback();
                $error_message = $e->getMessage();
            }
        } else {
            $error_message = "Insufficient balance.";
        }
    } else {
        $error_message = "Invalid amount.";
    }
}

// Handle gift card redemption
if (isset($_POST['redeemCode'])) {
    $redeemCode = strtoupper(trim($_POST['redeemCode']));
    if (preg_match('/^[A-Z0-9]{10}$/', $redeemCode)) {
        $stmt = $DBConnect->prepare("SELECT balance, isRedeemed, createdby FROM giftcards WHERE cardnum = ?");
        $stmt->bind_param("s", $redeemCode);
        $stmt->execute();
        $stmt->bind_result($giftCardBalance, $isRedeemed, $createdBy);
        $stmt->fetch();
        $stmt->close();

        if ($isRedeemed == 0) {
            $DBConnect->begin_transaction();

            try {
                // Update the gift card to set it as redeemed
                $updateStmt = $DBConnect->prepare("UPDATE giftcards SET isRedeemed = 1, redeemedBy = ? WHERE cardnum = ?");
                $updateStmt->bind_param("is", $userID, $redeemCode);
                $updateStmt->execute();

                if ($updateStmt->affected_rows > 0) {
                    // Add the gift card balance to the user's balance
                    $addStmt = $DBConnect->prepare("UPDATE user SET balance = balance + ? WHERE userID = ?");
                    $addStmt->bind_param("di", $giftCardBalance, $userID);
                    $addStmt->execute();

                    if ($addStmt->affected_rows > 0) {
                        $DBConnect->commit();
                        sendRedemptionEmail($userID, $redeemCode);
                        $_SESSION['success_message'] = "Gift card redeemed successfully.";
                        header("Location: ".$_SERVER['PHP_SELF']);
                        exit;
                    } else {
                        throw new Exception("Failed to update user balance.");
                    }
                    $addStmt->close();
                } else {
                    throw new Exception("Failed to redeem gift card.");
                }
                $updateStmt->close();
            } catch (Exception $e) {
                $DBConnect->rollback();
                $error_message = $e->getMessage();
            }
        } else {
            $error_message = "Gift card has already been redeemed.";
        }
    } else {
        $error_message = "Invalid gift card code.";
    }
}

// Function to send gift card email
function sendGiftCardEmail($userID, $cardNum, $amount) {
    global $DBConnect;
    $stmt = $DBConnect->prepare("SELECT Email, Username FROM user WHERE userID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->bind_result($email, $username);
    $stmt->fetch();
    $stmt->close();

    $mail = new PHPMailer(true);
    try {
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

        $mail->setFrom('quickfundslb@gmail.com', 'Quick Funds');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Gift Card Purchase Confirmation';
        $mail->Body = "
        <html>
        <head>
        <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #373737e6; color: #fff; }
        .email-container { max-width: 600px; margin: 0 auto; background-color: #373737e6; border: 1px solid #ddd; border-radius: 5px; overflow: hidden; }
        .email-header img {
            max-width: 100px;
            margin-bottom: 10px;
        }
        .email-header {
            background-color: #00000;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .email-content { padding: 20px; background-color: #373737e6; color: #fff; }
        .email-footer { background-color: #373737e6; padding: 20px; text-align: center; font-size: 12px; color: white; }
        .content-title { font-size: 20px; margin-bottom: 10px; color: #ff4c4c; }
        </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='email-header'>
                    <img src='https://i.ibb.co/y6FBJbT/about1.png' alt='QuickFunds Logo'>
                </div>
                <div class='email-content'>
                    <h2 class='content-title'>Gift Card Purchase Confirmation</h2>
                    <p>Dear $username,</p>
                    <p>You have successfully purchased a gift card with the following details:</p>
                    <p><strong>Amount:</strong> $$amount</p>
                    <p><strong>Code:</strong> $cardNum</p>
                    <p><strong>Purchase Time:</strong> " . date("Y-m-d H:i:s") . "</p>
                    <p>Thank you for using our service.</p>
                </div>
                <div class='email-footer'>
                    © 2024 QuickFunds. All rights reserved.
                </div>
            </div>
        </body>
        </html>";

        $mail->send();
    } catch (Exception $e) {
        echo "Error sending email: {$mail->ErrorInfo}";
    }
}

// Function to send redemption email
function sendRedemptionEmail($userID, $redeemCode) {
    global $DBConnect;
    $stmt = $DBConnect->prepare("SELECT Email, Username, balance FROM user WHERE userID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->bind_result($email, $username, $userBalance);
    $stmt->fetch();
    $stmt->close();

    $stmt = $DBConnect->prepare("SELECT balance FROM giftcards WHERE cardnum = ?");
    $stmt->bind_param("s", $redeemCode);
    $stmt->execute();
    $stmt->bind_result($redeemedAmount);
    $stmt->fetch();
    $stmt->close();

    $mail = new PHPMailer(true);
    try {
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

        $mail->setFrom('quickfundslb@gmail.com', 'Quick Funds');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Gift Card Redemption Confirmation';
        $mail->Body = "
        <html>
        <head>
        <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #373737e6; color: #fff; }
        .email-container { max-width: 600px; margin: 0 auto; background-color: #373737e6; border: 1px solid #ddd; border-radius: 5px; overflow: hidden; }
        .email-header img {
            max-width: 100px;
            margin-bottom: 10px;
        }
        .email-header {
            background-color: #00000;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .email-content { padding: 20px; background-color: #373737e6; color: #fff; }
        .email-footer { background-color: #373737e6; padding: 20px; text-align: center; font-size: 12px; color: white; }
        .content-title { font-size: 20px; margin-bottom: 10px; color: #ff4c4c; }
        </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='email-header'>
                    <img src='https://i.ibb.co/y6FBJbT/about1.png' alt='QuickFunds Logo'>
                </div>
                <div class='email-content'>
                    <h2 class='content-title'>Gift Card Redemption Confirmation</h2>
                    <p>Dear $username,</p>
                    <p>You have successfully redeemed a gift card with the following details:</p>
                    <p><strong>Code:</strong> $redeemCode</p>
                    <p><strong>Amount Redeemed:</strong> $$redeemedAmount</p>
                    <p><strong>Redemption Time:</strong> " . date("Y-m-d H:i:s") . "</p>
                    <p>Thank you for using our service.</p>
                </div>
                <div class='email-footer'>
                    © 2024 QuickFunds. All rights reserved.
                </div>
            </div>
        </body>
        </html>";

        $mail->send();
    } catch (Exception $e) {
        echo "Error sending email: {$mail->ErrorInfo}";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Gift Cards</title>
    <style>
        /* Add CSS styling here */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #1a1a1a, #171616);
            color: #fff;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background-color: rgba(55, 55, 55, 0.25); /* Dark Gray */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
                /* Header Styles */
        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            max-width: 150px;
        }

        /* Balance Styles */
        .balance {
            text-align: center;
            margin-bottom: 20px;
            font-size: 20px;
            font-weight: bold;
        }

        /* Gift Card Styles */
        .gift-card {
            display: flex;
            align-items: center;
            border: 1px solid #777; /* Gray border */
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
            background-color: #444; /* Dark Gray */
        }

        .gift-card img {
            max-width: 50px;
            margin-right: 15px;
        }

        .gift-card h3 {
            margin: 0;
            flex-grow: 1;
        }

        .gift-card button {
            background-color: #dc3545; /* Red color */
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .gift-card button:hover {
            background-color: #c82333; /* Slightly Darker Red on hover */
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #ddd; /* Light Gray */
        }

        .form-group input {
            width: calc(100% - 22px);
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #777; /* Gray border */
            background-color: #555; /* Dark Gray */
            color: #ddd; /* Light Gray */
        }

        .form-group button {
            background-color: #dc3545; /* Red color */
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-group button:hover {
            background-color: #c82333; /* Slightly Darker Red on hover */
        }

        /* Message Styles */
        .message {
            text-align: center;
            margin-bottom: 20px;
        }

        .success {
            color: #28a745; /* Green color for success message */
        }

        .error {
            color: #dc3545; /* Red color for error message */
        }
         {
            color: #fff;
        }
        /* 
 we use the webkit scroll to change the width for the scroll bar
*/
html::-webkit-scrollbar {
    width: .8rem;
    position: fixed;
    top: 50px; /* Position scrollbar below the nav */
    z-index: 998; /* Set lower z-index than the nav */
}

html::-webkit-scrollbar-track {
    background: transparent;
}

html::-webkit-scrollbar-thumb {
    background: #dc3545;
    border-radius: 1rem;
}

input.is-invalid {
    border-color: #dc3545; /* Red border color for invalid input */
    background-color: #fff0f1; /* Light red background color for invalid input */
}

input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}



    </style>
</head>
<body>

    <div class="container">
        <div class="header">
            <img src="https://i.ibb.co/y6FBJbT/about1.png" alt="QuickFunds Logo">
               
            <h1 style="color: white;">Gift Cards</h1>
            <a href="../user/all.html" style="color: white;">
                <i class="fas fa-home"></i>
            </a>

        </div>

        <?php if (isset($error_message)): ?>
            <div class="message error"><?= $error_message ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="message success"><?= $_SESSION['success_message'] ?></div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <div class="balance">
            Your Balance: $<?= number_format($balance, 2) ?>
        </div>

        <div class="gift-cards">
            <h2>Available Gift Cards</h2>
            <?php
            $amounts = [10, 25, 50, 100, 250, 500];
            foreach ($amounts as $amount) {
                echo "<div class='gift-card'>
                        <img src='https://i.ibb.co/y6FBJbT/about1.png' alt='Gift Card Logo'>
                        <h3>$$amount Gift Card</h3>
                        <form method='post'>
                            <input type='hidden' name='purchaseAmount' value='$amount'>
                            <button type='submit'>Buy for $$amount + $1 fee</button>
                        </form>
                      </div>";
            }
            ?>
        </div>

        <div class="redeem-card">
            <h2>Redeem Gift Card</h2>
            <form method="post">
                <div class="form-group">
                    <label for="redeemCode">Enter 10-digit Gift Card Code:</label>
                    <input type="text" name="redeemCode" id="redeemCode" maxlength="10" required>
                </div>
                <div class="form-group">
                    <button type="submit">Redeem</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>