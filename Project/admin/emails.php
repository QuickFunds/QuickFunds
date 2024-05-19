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
</head>
<body>
<div class="email-table">
    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th>From</th>
                <th>Date</th>
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
                        echo '<td>' . $header->subject . '</td>';
                        echo '<td>' . $header->fromaddress . '</td>';
                        echo '<td>' . date('Y-m-d H:i:s', strtotime($header->date)) . '</td>';

                        // Fetch email structure
                        $structure = imap_fetchstructure($mailbox, $email_number);

                        // Initialize variables to store email body
                        $body = '';

                        // Process email body based on its structure
                        if ($structure->type === 0) {
                            // Plain text email
                            $body = imap_fetchbody($mailbox, $email_number, 1);
                        } elseif ($structure->type === 1) {
                            // HTML email
                            $body = imap_fetchbody($mailbox, $email_number, 1.1);
                        } elseif ($structure->type === 2) {
                            // Multi-part email (e.g., text and HTML parts)
                            // Loop through each part to find the text and HTML parts
                            foreach ($structure->parts as $part_number => $part) {
                                // Check if the part is text or HTML
                                if ($part->type === 0 || $part->type === 1) {
                                    // Fetch text or HTML part
                                    $body = imap_fetchbody($mailbox, $email_number, $part_number + 1);
                                    break; // Exit the loop after finding the first text or HTML part
                                }
                            }
                        }

                    
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


<style>
   /* Email Table Styles */
.email-table {
    margin-top: 30px; /* Increased spacing for email table */
    margin-bottom: 30px; /* Increased spacing for email table */
    padding: 30px;
    border-radius: 8px;
    background-color: rgba(55, 55, 55, 0.25); /* Dark Gray */
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
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
    background: #dc3545; /* Red - PayPal's primary color */
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

.email-table .highlight-red {
    color: #dc3545; /* Red */
}

.email-table .highlight-green {
    color: #28a745; /* Green */
}

body{
    background: #191919;
}


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



</style>
    
</body>
</html>