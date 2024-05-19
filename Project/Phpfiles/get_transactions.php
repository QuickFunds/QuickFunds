<?php
session_start(); // Start the session

include "DBConnect.php";

// Check if userID is set in the session
if (isset($_SESSION['userID'])) {
    // Access the userID from the session variable
    $userID = $_SESSION['userID'];

    // Fetch the last 10 transactions for the user in descending order
    $stmt = $DBConnect->prepare("SELECT FromID, ToID, time, Ammount FROM transactions WHERE FromID = ? OR ToID = ? ORDER BY time DESC LIMIT 10");
    $stmt->bind_param("ii", $userID, $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Display transaction history in HTML format with CSS
        echo '<table class="transaction-table">
                <thead>
                    <tr>
                        <th>From ID</th>
                        <th>To ID</th>
                        <th>Time</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>';

        while ($row = $result->fetch_assoc()) {
            // Check if FromID matches userID and set class and format accordingly
            $amountClass = ($row['FromID'] === $userID) ? 'highlight-red' : 'highlight-green';
            $amountSign = ($row['FromID'] === $userID) ? '- $' : '+ $';

            // Apply the class only to the amount cell
            echo "<tr>
                    <td>" . $row['FromID'] . "</td>
                    <td>" . $row['ToID'] . "</td>
                    <td>" . $row['time'] . "</td>
                    <td><span class='$amountClass'>$amountSign" . $row['Ammount'] . "</span></td>
                  </tr>";
        }

        echo '</tbody></table>';
    } else {
        echo "No transaction history available.";
    }

    $stmt->close();
} else {
    echo "Session expired or not logged in.";
}

$DBConnect->close();
?>
