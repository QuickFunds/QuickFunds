<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #1a1a1a, #171616);
            color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: rgba(55, 55, 55, 0.9);
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            padding: 30px;
            max-width: 400px;
            width: 100%;
            text-align: center;
            animation: fadeIn 1s ease-in-out;
        }
        h2 {
            margin-top: 0;
            color: #dc3545; /* Red */
        }
        p {
            margin-bottom: 20px;
        }
        .error {
            color: #dc3545; /* Red */
        }
        .success {
            color: #28a745; /* Green */
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Email Verification</h2>
        <?php
        session_start();
        include "DBConnect.php";

        // Get email and verification code from URL parameters
        $email = $_GET['email'];
        $verification_code = $_GET['code'];

        // Check if email and verification code are set
        if(isset($email) && isset($verification_code)) {
            // Prepare and execute query to check if the email and verification code match and the email is not already verified
            $stmt = $DBConnect->prepare("SELECT UserID, UserName FROM user WHERE Email = ? AND verification_code = ? AND isVerifiedEmail = 0");
            $stmt->bind_param("ss", $email, $verification_code);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($userID, $userName);

            if ($stmt->num_rows > 0) {
                $stmt->fetch();

                // Update user's isVerifiedEmail status to 1
                $updateStmt = $DBConnect->prepare("UPDATE `user` SET `isVerifiedEmail` = 1 WHERE `Email` = ?");
                $updateStmt->bind_param("s", $email);
                $updateStmt->execute();

                // Log in the user after email verification
                session_regenerate_id(true); // Regenerate session ID to prevent session fixation attacks
                $_SESSION['userID'] = $userID;
                $_SESSION['userName'] = $userName;

                echo "<p class='success'>Email verified successfully! You are now logged in and will be redirected shortly.</p>";
                echo "<script>
                        setTimeout(function() {
                            window.location.href = '../user/all.html'; // Redirect to user page after successful login
                        }, 3000); // Redirect after 3 seconds
                      </script>";
            } else {
                echo "<p class='error'>Invalid verification link or email is already verified. Please try again.</p>";
            }

            $stmt->close();

            // Check if $updateStmt is set before closing it
            if (isset($updateStmt)) {
                $updateStmt->close();
            }
        } else {
            echo "<p class='error'>Missing email or verification code.</p>";
        }

        $DBConnect->close();
        ?>
    </div>
</body>
</html>
