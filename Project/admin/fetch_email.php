<?php
if (isset($_POST['email_number'])) {
    // Gmail credentials
    $username = 'quickfundslb@gmail.com';
    $password = 'saxy ozbk rwef efhf';

    // IMAP server settings
    $server = '{imap.gmail.com:993/imap/ssl}INBOX';

    // Connect to the IMAP server
    $mailbox = imap_open($server, $username, $password) or die('Cannot connect to Gmail: ' . imap_last_error());

    $email_number = $_POST['email_number'];

    // Fetch email structure
    $structure = imap_fetchstructure($mailbox, $email_number);

    // Initialize variable to store email body
    $body = '';

    // Process email body based on its structure
    if ($structure->type === 0) {
        // Plain text email
        $body = imap_fetchbody($mailbox, $email_number, 1);
    } elseif ($structure->type === 1) {
        // HTML email
        $body = imap_fetchbody($mailbox, $email_number, 1.1);
        if (empty($body)) {
            $body = imap_fetchbody($mailbox, $email_number, 1);
        }
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

    // Decode the email body if it's encoded
    if ($structure->encoding == 3) {
        $body = base64_decode($body);
    } elseif ($structure->encoding == 4) {
        $body = quoted_printable_decode($body);
    }

    // Close the IMAP connection
    imap_close($mailbox);

    // Display the email body
    echo nl2br(htmlspecialchars($body));
}
?>