<?php
session_start(); // Start the session

// Include the file with the database connection
include 'DBConnect.php';

// Function to perform withdrawal from user's balance and update credit cards' balances
function userToCardTransaction($DBConnect) {
    // Check if withdrawal amount is set in POST data
    if (!isset($_POST['withdrawalAmount'])) {
        return "Withdrawal amount not provided.";
    }

    $withdrawalAmount = $_POST['withdrawalAmount'];

    // Check if userID is set in the session
    if (isset($_SESSION['userID'])) {
        // Retrieve the user ID from the session
        $userID = $_SESSION['userID'];

        // Retrieve the credit card number associated with the user
        $getUserCardQuery = "SELECT ccNum FROM user WHERE UserID = ?";
        $getUserCardStmt = $DBConnect->prepare($getUserCardQuery);
        $getUserCardStmt->bind_param("i", $userID);
        $getUserCardStmt->execute();
        $getUserCardStmt->bind_result($userCardNumber);
        $getUserCardStmt->fetch();
        $getUserCardStmt->close();

        // Retrieve the user's balance from the 'user' table using the userID
        $getUserBalanceQuery = "SELECT balance FROM user WHERE UserID = ?";
        $getUserBalanceStmt = $DBConnect->prepare($getUserBalanceQuery);
        $getUserBalanceStmt->bind_param("i", $userID);
        $getUserBalanceStmt->execute();
        $getUserBalanceStmt->bind_result($userBalance);
        $getUserBalanceStmt->fetch();
        $getUserBalanceStmt->close();

        // Retrieve the treasury balance (credit card balance)
        $getTreasuryBalanceQuery = "SELECT Balance FROM treasury WHERE ccNum = '2222222222222222'";
        $getTreasuryBalanceStmt = $DBConnect->prepare($getTreasuryBalanceQuery);
        $getTreasuryBalanceStmt->execute();
        $getTreasuryBalanceStmt->bind_result($treasuryBalance);
        $getTreasuryBalanceStmt->fetch();
        $getTreasuryBalanceStmt->close();

        // Proceed only if the user's balance, treasury balance, and user's card number are retrieved
        if ($userBalance !== null && $treasuryBalance !== null && $userCardNumber !== null) {
            // Proceed with the transaction only if the user has sufficient balance
            if ($userBalance >= $withdrawalAmount) {
                // Start a transaction
                $DBConnect->autocommit(FALSE);

                // Perform withdrawal from the user's balance
                $withdrawalQuery = "UPDATE user SET balance = balance - ? WHERE UserID = ?";
                $withdrawStmt = $DBConnect->prepare($withdrawalQuery);
                $withdrawStmt->bind_param("di", $withdrawalAmount, $userID);
                $withdrawStmt->execute();

                // Check if the withdrawal from user's balance was successful
                if ($withdrawStmt->affected_rows > 0) {
                    // Update the treasury balance (credit card balance) by adding the withdrawal amount
                    $updateTreasuryBalanceQuery = "UPDATE treasury SET Balance = Balance - ? WHERE ccNum = '2222222222222222'";
                    $updateTreasuryStmt = $DBConnect->prepare($updateTreasuryBalanceQuery);
                    $updateTreasuryStmt->bind_param("d", $withdrawalAmount);
                    $updateTreasuryStmt->execute();

                    // Check if the update of treasury balance was successful
                    if ($updateTreasuryStmt->affected_rows > 0) {
                        // Update the user's credit card balance by adding the withdrawal amount
                        $updateUserCardBalanceQuery = "UPDATE creditcards SET Balance = Balance + ? WHERE ccNum = ?";
                        $updateUserCardStmt = $DBConnect->prepare($updateUserCardBalanceQuery);
                        $updateUserCardStmt->bind_param("ds", $withdrawalAmount, $userCardNumber);
                        $updateUserCardStmt->execute();
                        //update adminaccount incase of withdrawal
                        $adminUpdate = $DBConnect->prepare("UPDATE administration SET balance = (SELECT balance FROM user WHERE UserID = 1) WHERE UserID = 1");
                        $adminUpdate->execute();
                        // Check if the update of user's credit card balance was successful
                        if ($updateUserCardStmt->affected_rows > 0) {
                            // Commit the transaction
                            $DBConnect->commit();

                            // Return success message or any relevant response
                            return " $withdrawalAmount$ has been withdrawn successfully!";
                        } else {
                            // Rollback the transaction
                            $DBConnect->rollback();

                            return "Transaction failed. User's credit card balance update unsuccessful. Transaction rolled back.";
                        }
                    } else {
                        // Rollback the transaction
                        $DBConnect->rollback();

                        return "Transaction failed. Treasury balance update unsuccessful. Transaction rolled back.";
                    }
                } else {
                    // Rollback the transaction
                    $DBConnect->rollback();

                    // Return an error message if the withdrawal from user's balance failed
                    return "Transaction failed. Withdrawal from user's balance unsuccessful. Transaction rolled back.";
                }
            } else {
                // Return an error message if the user's balance is insufficient
                return "Insufficient user balance. Transaction cannot be processed.";
            }
        } else {
            // Return an error message if user's balance or credit card number is not found
            return "User's balance or credit card number not found.";
        }
    } else {
        // Return an error message if the user is not logged in or session is not found
        return "User not logged in.";
    }
}

// Call the function and store the result
$resultMessage = userToCardTransaction($DBConnect);

// Output the result message
echo $resultMessage;

// Close the database connection
$DBConnect->close();
?>
