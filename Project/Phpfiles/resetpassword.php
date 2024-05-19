<?php
session_start();
include "DBConnect.php";

// Function to check if a password is strong
function isPasswordStrong($password) {
    // Regular expressions to check for different character types
    $regex = [
        '/[A-Z]/', // Uppercase letters
        '/[a-z]/', // Lowercase letters
        '/[0-9]/', // Numbers
        '/[^A-Za-z0-9]/' // Special characters
    ];

    $strength = 0;
    foreach ($regex as $pattern) {
        if (preg_match($pattern, $password)) {
            $strength++;
        }
    }

    return ($strength >= 3 && strlen($password) >= 8);
}

// Check if email and code are provided in the URL
if (isset($_GET['email']) && isset($_GET['code'])) {
    $email = $_GET['email'];
    $code = $_GET['code'];

    // Check if the email and code combination exists in the database
    $stmt = $DBConnect->prepare("SELECT UserID FROM user WHERE Email = ? AND verification_code = ?");
    $stmt->bind_param("ss", $email, $code);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Email and code combination is valid, process password reset if form is submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $newPassword = $_POST['newPassword'];
            
            if (isPasswordStrong($newPassword)) {
                // Password is strong, update it in the database
                $encrypted_password = password_hash($newPassword, PASSWORD_DEFAULT);
                $updateStmt = $DBConnect->prepare("UPDATE user SET Password = ?, verification_code = NULL WHERE Email = ?");
                $updateStmt->bind_param("ss", $encrypted_password, $email);
                $updateStmt->execute();
                
                // Log in the user after password reset
                $loginStmt = $DBConnect->prepare("SELECT UserID, UserName FROM user WHERE Email = ?");
                $loginStmt->bind_param("s", $email);
                $loginStmt->execute();
                $loginStmt->bind_result($userID, $userName);
                $loginStmt->fetch();

                // Start a new session for the user
                session_regenerate_id(true); // Regenerate session ID to prevent session fixation attacks
                $_SESSION['userID'] = $userID;
                $_SESSION['userName'] = $userName;

                // Return JSON response indicating success
                echo json_encode(array("status" => "success"));
                exit();
            } else {
                // Return JSON response with error message
                echo json_encode(array("status" => "error", "message" => "Password should be at least 8 characters long and contain at least 3 of the following: uppercase letters, lowercase letters, numbers, special characters."));
                exit();
            }
        }

        // Display the password reset form
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Reset Password</title>
            <link rel="stylesheet" href="resetpassword.css">
        </head>
        <style>
            /* General Styles */
            body {
                font-family: 'Arial', sans-serif;
                background: linear-gradient(to right, #1a1a1a, #171616);
                color: #fff;
                margin: 0;
                padding: 0;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }

            /* Main Container Styles */
            .container {
                background-color: rgba(137, 137, 137, 0.25);
                border-radius: 8px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
                padding: 30px;
                width: 350px;
                box-sizing: border-box;
            }

            /* Header Styles */
            h2 {
                margin-top: 0;
                margin-bottom: 20px;
                color: #dc3545;
                text-align: center;
            }

            /* Form Styles */
            label {
                display: block;
                font-weight: bold;
                color: #ddd; /* Light Gray */
                margin-bottom: 10px;
            }

            input[type="password"] {
                width: calc(100% - 20px);
                padding: 10px;
                border-radius: 8px;
                border: 1px solid #777; /* Gray */
                background-color: #555; /* Dark Gray */
                color: #ddd; /* Light Gray */
                margin-bottom: 20px;
                transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
            }

            input[type="password"]:focus {
                background-color: #666; /* Darker Gray */
                transform: scale(1.02);
                box-shadow: 0 0 15px rgba(239, 253, 238, 0.7);
            }

            .error {
                color: #dc3545;
                margin-top: -10px;
                margin-bottom: 10px;
                text-align: center;
            }

            button {
                width: 100%;
                background: #dc3545; /* Red */
                color: #fff;
                border: none;
                padding: 10px 0;
                border-radius: 8px;
                cursor: pointer;
                font-weight: bold;
                transition: background 0.3s ease, transform 0.3s ease;
                box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            }

            button:hover {
                background: #c82333; /* Slightly Darker Red */
                transform: scale(1.05);
                box-shadow: 0 0 25px rgba(0, 0, 0, 0.7);
            }
        </style>
        <body>
        <div class="container">
            <h2>Reset Your Password</h2>
            <form id="resetPasswordForm">
                <label for="newPassword">New Password:</label>
                <input type="password" id="newPassword" name="newPassword" required>
                <div id="passwordError" class="error"></div>
                <button type="submit">Reset Password</button>
            </form>
        </div>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#resetPasswordForm').submit(function(e) {
                    e.preventDefault();
                    var newPassword = $('#newPassword').val();
                    $.ajax({
                        type: 'POST',
                        url: 'resetpassword.php?email=<?php echo $email; ?>&code=<?php echo $code; ?>',
                        data: { newPassword: newPassword },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                alert('Your password has been reset successfully. You will now be redirected to the login page.');
                                window.location.href = '../user/all.html'; // Redirect to login page after successful password reset
                            } else {
                                $('#passwordError').text(response.message);
                            }
                        },
                        error: function() {
                            alert('An error occurred while resetting your password.');
                        }
                    });
                });
            });
        </script>
        </body>
        </html>
        <?php
    } else {
        // Invalid email or code
        echo "Invalid email or code. Please try again.";
    }

    $stmt->close();
    $DBConnect->close();
} else {
    // Email or code not provided in the URL
    echo "Email or code not provided. Please try again.";
}
?>
