<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['adminID'])) {
    // If not logged in, redirect to the login page
    header("Location: ./login/index.html");
    exit();
}

// Include the database connection file
include "./phpfiles/DBConnect.php";
require '../vendor/autoload.php'; // Include PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get data from the form
    $userID = $_POST['userid'];
    $status = $_POST['status'];
    $comment = $_POST['comment'];

    // Check if admin ID is set in the session
    if (isset($_SESSION['adminID'])) {
        // Reopen the database connection
        include "./phpfiles/DBConnect.php";
        
        $adminID = $_SESSION['adminID'];

        // Fetch the user's email and username
        $queryUserData = "SELECT Email, UserName FROM user WHERE UserID = '$userID'";
        $resultUserData = mysqli_query($DBConnect, $queryUserData);
        $userData = mysqli_fetch_assoc($resultUserData);
        $userEmail = $userData['Email'];
        $username = $userData['UserName'];

        // Update user status in the database
        $query = "UPDATE user SET AccStatus = '$status' WHERE UserID = '$userID'";
        mysqli_query($DBConnect, $query);

        // Insert record into admin logs
        $queryLog = "INSERT INTO adminlogs (AdminID, UserID, Action, Comment) 
                    VALUES ('$adminID', '$userID', '$status', '$comment')";
        mysqli_query($DBConnect, $queryLog);

        // Close the database connection
        mysqli_close($DBConnect);

        // Send notification email to the user
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
                        <p>Your account status has been updated to <strong>$status</strong>. Due to $comment </p>
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

        // Redirect back to the main page
        header("Location: adminpanel.php");
        exit(); // Make sure nothing else is executed after the redirect
    } else {
        // Handle the case where admin ID is not set in the session
        echo "Admin ID is not set in the session.";
        exit(); // Terminate script execution
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="dashstyle.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="shortcut icon" type="image" href="./login/log.png">
</head>

<body>
    <header class="header">
        <p id="qfunds" class="qfunds">QuickFunds</p>
        <form action="./phpfiles/admin_logout.php" method="post">
            <button id="logoutButton" type="submit">Logout</button>
            <img id="logoImg" src="/images/about1.png" alt="test" style="display: none;">

        </form>
    </header>
    <main class="main">
        <aside class="sidebar">
            <div class="sidebar-section section-1">

                <form class="profit-form" method="GET">
                    <label for="time_period">Select Time Period:</label>
                    <select id="time_period" name="time_period">
                        <option value="today">Today</option>
                        <option value="last_week">Last Week</option>
                        <option value="last_month">Last Month</option>
                        <option value="last_3_months">Last 3 Months</option>
                        <option value="last_6_months">Last 6 Months</option>
                        <option value="last_year">Last Year</option>
                        <option value="all_time">All Time</option>
                    </select>
                    <button type="submit">Calculate Profit</button>
                </form>
                <?php
// Include your database connection file
include "./phpfiles/DBConnect.php";

if (isset($_GET['time_period'])) {
    $timePeriod = $_GET['time_period'];

    // Initialize variables for start and end dates
    $startDate = '';
    $endDate = '';

    // Calculate start and end dates based on selected time period
    switch ($timePeriod) {
        case 'today':
            // Set start date to the beginning of today (12:00 AM)
            $startDate = date('Y-m-d 00:00:00');
            // Set end date to the end of today
            $endDate = date('Y-m-d 23:59:59');
            $timePeriodText = 'today';
            break;
        case 'last_week':
            // Calculate start date as 1 week ago from today
            $startDate = date('Y-m-d', strtotime('-1 week'));
            // Set end date to the end of today
            $endDate = date('Y-m-d 23:59:59');
            $timePeriodText = 'this week';
            break;
        case 'last_month':
            // Calculate start date as 1 month ago from today
            $startDate = date('Y-m-d', strtotime('-1 month'));
            // Set end date to the end of today
            $endDate = date('Y-m-d 23:59:59');
            $timePeriodText = 'this month';
            break;
        case 'last_3_months':
            // Calculate start date as 3 months ago from today
            $startDate = date('Y-m-d', strtotime('-3 months'));
            // Set end date to the end of today
            $endDate = date('Y-m-d 23:59:59');
            $timePeriodText = 'the last 3 months';
            break;
        case 'last_6_months':
            // Calculate start date as 6 months ago from today
            $startDate = date('Y-m-d', strtotime('-6 months'));
            // Set end date to the end of today
            $endDate = date('Y-m-d 23:59:59');
            $timePeriodText = 'the last 6 months';
            break;
        case 'last_year':
            // Calculate start date as 1 year ago from today
            $startDate = date('Y-m-d', strtotime('-1 year'));
            // Set end date to the end of today
            $endDate = date('Y-m-d 23:59:59');
            $timePeriodText = 'the last year';
            break;
        case 'all_time':
            // Set start date to the earliest possible date
            $startDate = '1970-01-01 00:00:00';
            // Set end date to the end of today
            $endDate = date('Y-m-d 23:59:59');
            $timePeriodText = 'overall';
            break;
        default:
            // Default to today's date
            $startDate = date('Y-m-d 00:00:00');
            $endDate = date('Y-m-d H:i:s');
            $timePeriodText = 'today';
            break;
    }

    // Retrieve the transactions within the specified time range
    $query = "SELECT COUNT(`TxnID`) AS `NumTransactions` FROM `transactions` WHERE `time` BETWEEN '$startDate' AND '$endDate'";
    $result = mysqli_query($DBConnect, $query);

    if (!$result) {
        die("Error retrieving data: " . mysqli_error($DBConnect));
    }

    // Get the number of transactions in the specified time range
    $numTransactions = mysqli_fetch_assoc($result)['NumTransactions'];

    // Close the result set
    mysqli_free_result($result);

    echo "<p class='profit-result'>You have earned $".number_format($numTransactions, 2)." $timePeriodText.</p>";
}
?>
            </div>

            <div class="sidebar-section section-2">
                <?php
// Include your database connection file
include "./phpfiles/DBConnect.php";

// Execute SQL query to fetch experienceRating
$query = "SELECT AVG(experienceRating) AS overallRating FROM surveyresponses";
$result = mysqli_query($DBConnect, $query);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $overallRating = $row['overallRating'];

    // Close the result set
    mysqli_free_result($result);

    // Generate star rating HTML
    $starRating = '';
    $roundedRating = round($overallRating); // Round the rating to the nearest whole number

    // Append filled star icons for the rounded rating
    for ($i = 1; $i <= $roundedRating; $i++) {
        $starRating .= '<i class="fas fa-star gold"></i>'; // Assuming you're using Font Awesome for icons
    }

    // Append empty star icons for the remaining stars
    for ($i = $roundedRating + 1; $i <= 5; $i++) {
        $starRating .= '<i class="far fa-star gold"></i>'; // Assuming you're using Font Awesome for icons
    }

    // Output the star rating HTML inside the sidebar section
    echo '<div class="sidebar-section section-2">';
    echo '<h3>Overall Rating</h3>';
    echo '<div class="star-rating-container">';
    echo '<div class="star-rating">' . $starRating . '</div>';
    // Output the average rating next to the stars
    echo '<span class="average-rating">Average Rating: ' . number_format($overallRating, 1) . '</span>';
    echo '</div>';
    echo '</div>';
} else {
    // Handle error if SQL query fails
    echo "Error executing SQL query: " . mysqli_error($DBConnect);
}
?>

            </div>
            <div class="sidebar-section section-3">
    <div class="btn-container">
        <div class="btn-wrapper">
            <button class="btn" onclick="window.location.href = 'transactionchart.php';">Transaction Statistics</button>
        </div>
        <div class="btn-wrapper">
            <button class="btn" onclick="window.location.href = 'surveycom.php';">User Feedback</button>
        </div>
        <div class="btn-wrapper">
            <button class="btn" onclick="window.location.href = 'userstats.php';">User Statistics</button>
        </div>
        <div class="btn-wrapper">
            <button class="btn" onclick="window.location.href = 'emails.php';">Emails</button>
        </div>
    </div>
</div>
        </aside>
        <div class="content-section">
            <div id="userTableContainer">
                <?php
                // Fetch all user data from the database
                $query = "SELECT UserID, UserName, Email, balance, AccStatus FROM user";
                $result = mysqli_query($DBConnect, $query);

                // Check if there are any users
                if(mysqli_num_rows($result) > 0) {
                    echo "<table id='userTable'>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>";

                    // Loop through each row of user data
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                                <td>{$row['UserID']}</td>
                                <td>{$row['UserName']}</td>
                                <td>{$row['Email']}</td>
                                <td>{$row['balance']}</td>
                                <td>{$row['AccStatus']}</td>
                                <td><button class='edit-user-btn' data-userid='{$row['UserID']}'>Edit</button></td>
                            </tr>";
                    }

                    echo "</tbody>
                        </table>";
                } else {
                    echo "No users found";
                }
                ?>
            </div>
            <input type="text" id="searchInput" onkeyup="searchUser()" placeholder="Search for usernames..">
        </div>

        <aside class="right-sidebar">
            <!-- Search Input for Case ID -->
            <ul id="adminLogsList" class="admin-logs">
            <?php
            // Include your database connection file
            include "./phpfiles/DBConnect.php";

            // Fetch all admin logs from the database, ordered by CaseID in descending order
            $queryLogs = "SELECT * FROM adminlogs ORDER BY CaseID DESC";
            $resultLogs = mysqli_query($DBConnect, $queryLogs);

            // Check if there are any logs
            if(mysqli_num_rows($resultLogs) > 0) {
                // Loop through each log entry
                while($rowLog = mysqli_fetch_assoc($resultLogs)) {
                    // Display each element of the log entry
                    echo "<li>Case ID: {$rowLog['CaseID']} - Admin ID: {$rowLog['AdminID']} - User ID: {$rowLog['UserID']} - Action: {$rowLog['Action']} - Comment: {$rowLog['Comment']}</li>";
                }
            } else {
                echo "<li>No admin logs found</li>";
            }

            // Close the database connection
            mysqli_close($DBConnect);
            ?>
            </ul>
            <input type="text" id="searchCaseInput" onkeyup="searchCase()" placeholder="Search by Case ID..">
            
        </aside>


        <script>
            // Function to search for logs by case ID
            function searchCase() {
                var input, filter, ul, li, i, txtValue;
                input = document.getElementById("searchCaseInput");
                filter = input.value.toUpperCase();
                ul = document.getElementById("adminLogsList");
                li = ul.getElementsByTagName("li");

                // Loop through all list items, and hide those that do not match the search query
                for (i = 0; i < li.length; i++) {
                    txtValue = li[i].textContent || li[i].innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        li[i].style.display = "";
                    } else {
                        li[i].style.display = "none";
                    }
                }
            }
        </script>

    </main>

    <div id="editUserPopup" style="display:none;">
    <div style="padding: 10px;">
        <span style="float: right; cursor: pointer;" onclick="closePopup()">&times;</span>
        <h2 style="margin-top: 0;">Edit User Status</h2>
    </div>
    <div style="padding: 20px;">
        <form id="edit-user-form" action="" method="post" onsubmit="return submitForm();">
            <input type="hidden" id="userid" name="userid">
            <label for="status">Select Status:</label>
            <select id="status" name="status">
                <option value="Active">Active</option>
                <option value="Restricted">Restricted</option>
                <option value="Suspended">Suspended</option>
            </select><br>
            <label for="comment">Comment:</label><br>
            <textarea id="comment" name="comment" rows="4" cols="50"></textarea><br>
            <button type="submit" id="submit-btn">Update Status</button>
        </form>
    </div>
</div>


    <script>
        // Function to close the popup
        function closePopup() {
            $('#editUserPopup').hide();
        }

        // Function to disable the submit button to prevent multiple submissions
        function submitForm() {
            document.getElementById("submit-btn").disabled = true;
            return true;
        }

        // Function to search for users and highlight the matching rows
        function searchUser() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("userTable");
            tr = table.getElementsByTagName("tr");

            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[1]; // Change index to match the column containing username
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
    </script>

    <script>
        $(document).ready(function () {
            $('.edit-user-btn').click(function () {
                var userID = $(this).data('userid');
                $('#userid').val(userID);
                $('#editUserPopup').show();
            });
        });
    </script>
</body>

</html>