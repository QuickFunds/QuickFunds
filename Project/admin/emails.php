<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['adminID'])) {
    // If not logged in, redirect to the login page
    header("Location: ./login/index.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emails</title>
    <link rel="shortcut icon" type="image" href="./login/log.png">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        function showEmailBody(email_number) {
            $.ajax({
                url: 'fetch_email.php',
                type: 'POST',
                data: { email_number: email_number },
                success: function(response) {
                    document.getElementById('email-body').innerHTML = response;
                    document.getElementById('email-modal').style.display = 'block';
                }
            });
        }

        function closeEmailModal() {
            document.getElementById('email-modal').style.display = 'none';
        }

        $(document).ready(function() {
            if ("Notification" in window) {
                Notification.requestPermission().then(function(permission) {
                    if (permission === "granted") {
                        new Notification("You have new emails!");
                    }
                });
            }
        });
    </script>
    <style>
        /* General Styles */
        body {
            margin: 0;
            padding: 0;
            font-family: sans-serif;
            background-color: #191919; /* Dark background color */
            color: #fff;
        }

        /* Email Table Styles */
        .email-table {
            margin: 30px auto;
            padding: 30px;
            max-width: 80%;
            border-radius: 8px;
            background-color: rgba(55, 55, 55, 0.25); /* Dark Gray */
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            overflow-x: auto;
        }

        .email-table table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 8px;
            overflow: hidden;
        }

        .email-table th,
        .email-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 2px solid #777; /* Gray */
            color: #ddd; /* Light Gray */
            transition: background 0.3s ease;
        }

        .email-table th {
            background: #dc3545; /* Red */
            color: #fff;
            font-weight: bold;
        }

        .email-table tbody tr:nth-child(even) {
            background: #515151; /* Darker Gray */
        }

        .email-table tbody tr:nth-child(odd) {
            background: #3f3f3f; /* Darker Gray */
        }

        .email-table tbody tr:hover {
            background: #777; /* Slightly Darker Gray */
        }

        .email-table tbody tr:last-child {
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
        }

        /* Scrollbar Styles */
        ::-webkit-scrollbar {
            width: 10px;
            border-radius: 10px; /* Rounded edges for scrollbar */
        }

        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1); /* Light Gray */
        }

        ::-webkit-scrollbar-thumb {
            background: #777; /* Slightly Darker Gray */
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555; /* Dark Gray */
        }

        /* Email Modal Styles */
        #email-modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.7);
        }

        .modal-content {
            background-color: #444;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            color: #fff;
            border-radius: 8px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: #fff;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="email-table">
    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th>From</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Gmail credentials
            $username = 'quickfundslb@gmail.com';
            $password = 'saxy ozbk rwef efhf';

            // IMAP server settings
            $server = '{imap.gmail.com:993/imap/ssl}INBOX';

            // Connect to the IMAP server
            $mailbox = imap_open($server, $username, $password) or die('Cannot connect to Gmail: ' . imap_last_error());

            // Search for unseen emails
            $search = 'UNSEEN';
            $emails = imap_search($mailbox, $search);

            // Check if there are any unseen emails
            if ($emails) {
                // Loop through each email
                foreach ($emails as $email_number) {
                    // Fetch email headers
                    $header = imap_headerinfo($mailbox, $email_number);

                    // Check if the email is not an auto response (by checking the subject)
                    if (strpos(strtolower($header->subject), 'auto response') === false) {
                        // Display email subject, sender, and date
                        echo '<tr>';
                        echo '<td>' . htmlentities($header->subject) . '</td>';
                        echo '<td>' . htmlentities($header->fromaddress) . '</td>';
                        echo '<td>' . date('Y-m-d H:i:s', strtotime($header->date)) . '</td>';
                        echo '<td><button onclick="showEmailBody(' . $email_number . ')">Read</button></td>';
                        echo '</tr>';
                    }
                }
            } else {
                echo '<tr><td colspan="4">No new emails found.</td></tr>';
            }

            // Close the IMAP connection
            imap_close($mailbox);
            ?>
        </tbody>
    </table>
</div>

<div id="email-modal">
    <div class="modal-content">
        <span class="close" onclick="closeEmailModal()">&times;</span>
        <div id="email-body"></div>
    </div>
</div>
</body>
</html>
