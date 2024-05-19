<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    $response = array("status" => "error", "message" => "User not logged in");
} else {
    // Assuming the credit card number, CCV, and expiry date are submitted via POST method
    if (isset($_POST['ccNumber']) && isset($_POST['ccv']) && isset($_POST['expiryDate'])) {
        $ccNumber = $_POST['ccNumber'];
        $ccv = $_POST['ccv'];
        $expiryDate = $_POST['expiryDate'];

        function validateAndSanitizeCCNumber($ccNumber) {
            // Remove any non-digit characters from the credit card number and make sure it's 16 digits
            $ccNumber = preg_replace('/\D/', '', $ccNumber);
            if (strlen($ccNumber) !== 16) {
                return false;
            }

            return $ccNumber;
        }

        include "DBConnect.php";

        // Query to check if the credit card exists in the pre-made credit cards database
        $stmt = $DBConnect->prepare("SELECT ccNum, ccv, `Expiry date` FROM `creditcards` WHERE ccNum = ?");
        $stmt->bind_param("s", $ccNumber);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($dbCCNum, $dbCCV, $dbExpiryDate);
        $stmt->fetch();
        $cardCount = $stmt->num_rows;
        $stmt->close();

        if ($cardCount > 0 && $dbCCV === $ccv && $dbExpiryDate === $expiryDate) {
            // Credit card exists in the pre-made credit cards database and CCV & expiry date match, associate it with the user

            $userID = $_SESSION['userID'];

            // Query to add the credit card to the user in the database
            $stmt = $DBConnect->prepare("UPDATE `user` SET ccNum = ? WHERE UserID = ?");
            $stmt->bind_param("si", $ccNumber, $userID);
            $success = $stmt->execute();
            $stmt->close();

            if ($success) {
                $response = array("status" => "success", "message" => "Credit card added successfully");
            } else {
                $response = array("status" => "error", "message" => "Failed to add credit card");
            }
        } else {
            $response = array("status" => "error", "message" => "Credit card details do not match or do not exist in the database");
        }

        $DBConnect->close();
    } else {
        $response = array("status" => "error", "message" => "Credit card details not provided");
    }
}

// Return the JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>