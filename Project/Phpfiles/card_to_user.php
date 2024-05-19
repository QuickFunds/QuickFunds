<?php
session_start(); // Start the session

// Include the file with the database connection
include 'DBConnect.php';

// Function to perform withdrawal from credit card's balance and update user's balance
function cardToUserTransaction($DBConnect) {
    // Check if withdrawal amount is set in POST data
    if (!isset($_POST['withdrawalAmount'])) {
        return "Error: Withdrawal amount not provided.";
    }

    $withdrawalAmount = (float)$_POST['withdrawalAmount']; // Cast withdrawal amount to float

    // Check if userID is set in the session
    if (isset($_SESSION['userID'])) {
        // Retrieve the user ID from the session
        $userID = $_SESSION['userID'];

        // Initialize variables to store credit card number and balance
        $userCCNum = null;
        $ccBalance = null;

        // Retrieve the credit card's balance from the 'creditcards' table using the user's ID
        $getCreditCardBalanceQuery = "SELECT Balance FROM creditcards WHERE ccNum IN (SELECT ccNum FROM user WHERE UserID = ?)";
        $getBalanceStmt = $DBConnect->prepare($getCreditCardBalanceQuery);
        $getBalanceStmt->bind_param("i", $userID);
        $getBalanceStmt->execute();
        $getBalanceStmt->bind_result($ccBalance);
        $getBalanceStmt->fetch();
        $getBalanceStmt->close();

        // Proceed if the credit card balance is retrieved and sufficient
        if ($ccBalance !== null && $ccBalance >= $withdrawalAmount) {
            // Begin a transaction
            $DBConnect->begin_transaction();

            // Perform withdrawal from the credit card's balance
            $withdrawalQuery = "UPDATE creditcards SET Balance = Balance - ? WHERE ccNum IN (SELECT ccNum FROM user WHERE UserID = ?)";
            $withdrawStmt = $DBConnect->prepare($withdrawalQuery);
            $withdrawStmt->bind_param("di", $withdrawalAmount, $userID);
            $withdrawSuccess = $withdrawStmt->execute();
            $withdrawStmt->close();

            if ($withdrawSuccess) {
                // Update the user's balance by adding the withdrawal amount
                $updateUserBalanceQuery = "UPDATE user SET balance = balance + ? WHERE UserID = ?";
                $updateUserStmt = $DBConnect->prepare($updateUserBalanceQuery);
                $updateUserStmt->bind_param("di", $withdrawalAmount, $userID);
                $updateUserSuccess = $updateUserStmt->execute();
                $updateUserStmt->close();

                // Update the treasury (credit card) balance by deducting the withdrawal amount
                $updateTreasuryBalanceQuery = "UPDATE treasury SET Balance = Balance + ? WHERE ccNum = '2222222222222222'";
                $updateTreasuryStmt = $DBConnect->prepare($updateTreasuryBalanceQuery);
                $updateTreasuryStmt->bind_param("d", $withdrawalAmount);
                $updateTreasurySuccess = $updateTreasuryStmt->execute();
                $updateTreasuryStmt->close();

                if ($updateUserSuccess && $updateTreasurySuccess) {
                    // Commit the transaction
                    $DBConnect->commit();
                    return "$withdrawalAmount$ deposited succesfully!";
                } else {
                    // Rollback the transaction if any update fails
                    $DBConnect->rollback();
                    return "Error: Unable to update balances.";
                }
            } else {
                // Return an error message if the withdrawal from credit card's balance failed
                return "Error: Withdrawal from credit card's balance unsuccessful.";
            }
        } else {
            if ($ccBalance === null) {
                return "Error: Credit card details not found.";
            } else {
                return "Error: Insufficient balance in the credit card.";
            }
        }
    } else {
        return "Error: User not logged in.";
    }
}

// Call the function and store the result
$resultMessage = cardToUserTransaction($DBConnect);

// Output the result message
echo $resultMessage;

// Close the database connection
$DBConnect->close();
?>
