<?php


// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['adminID'])) {
    // If not logged in, redirect to the login page
    header("Location: ./login/index.html");
    exit();
}


// Include your database connection file
include "./phpfiles/DBConnect.php";

// Check connection
if (!$DBConnect) {
    die("Connection failed: " . mysqli_connect_error());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comments and Surveys</title>
    <link rel="shortcut icon" type="image" href="./login/log.png">

</head>
<body>
    <div class="header">
        <h1>Comments and Surveys</h1>
    </div>
    <div class="main-container">
        <div class="content-section">
            <h2 class="section-heading">Comments</h2>
            <div class="comments">
            <?php
                        // Fetch comments from database
                        $sql_comments = "SELECT `ID`, `comment`, `time` FROM `comments` WHERE 1";
                        $result_comments = mysqli_query($DBConnect, $sql_comments);

                        if (mysqli_num_rows($result_comments) > 0) {
                            // Output data of each row
                            while($row_comments = mysqli_fetch_assoc($result_comments)) {
                                echo "<li>" . $row_comments["comment"] . "</li>";
                            }
                        } else {
                            echo "No comments found";
                        }
                        ?>
            </div>
        </div>
        <div class="content-section">
            <h2 class="section-heading">Survey Responses</h2>
            <div class="survey-responses">
            <?php
                // Include your database connection file
                include "./phpfiles/DBConnect.php";

                // Fetch survey responses from database and sort by submission time (latest first)
                $sql_survey = "SELECT `id`, `userID`, `likes`, `improvements`, `additionalComments`, `experienceRating`, `likelihoodRecommend`, `submissionTime` FROM `surveyresponses` ORDER BY `submissionTime` DESC";
                $result_survey = mysqli_query($DBConnect, $sql_survey);

                if (mysqli_num_rows($result_survey) > 0) {
                    // Output data of each row
                    while($row_survey = mysqli_fetch_assoc($result_survey)) {
                        echo "<li>User ID: " . $row_survey["userID"] . "<br>Likes: " . $row_survey["likes"] . "<br>Improvements: " . $row_survey["improvements"] . "<br>Additional Comments: " . $row_survey["additionalComments"] . "<br>Experience Rating: " . $row_survey["experienceRating"] . "<br>Likelihood Recommend: " . $row_survey["likelihoodRecommend"] . "<br>Submission Time: " . $row_survey["submissionTime"] . "</li>";
                    }
                } else {
                    echo "No survey responses found";
                }
                ?>
                    </ul>
            </div>
        </div>
    </div>
</body>
<style>

    /* General Styles */
    body {
        margin: 0;
        padding: 0;
        font-family: sans-serif;
        background-color: #191919; /* Dark background color */
        color: #fff;
    }

    .header {
        width: 100%;
        height: 50px;
        background-color: #dc3545; /* Darker background color for header */
        color: #fff; /* White text color for header */
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .main-container {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }

    .content-section {
        width: calc(50% - 10px); /* 50% width with padding */
        height: 600px; /* Adjusted height */
        background-color: #444; /* Dark Gray */
        border-radius: 10px; /* Add rounded corners */
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.5); /* Darker shadow effect */
        overflow-y: auto; /* Add vertical scrollbar */
        padding: 20px; /* Add padding */
        margin-right: 10px; /* Adjusted margin between sections */
    }

    .content-section:last-child {
        margin-right: 0; /* Remove margin for the last section */
    }

    .section-heading {
        border-bottom: 1px solid #777; /* Gray */
        padding-bottom: 10px; /* Add padding below heading */
    }

    .comments,
    .survey-responses {
        list-style: none;
        padding: 0;
        margin: 0;
        color: #ddd; /* Light Gray */
    }

    .comments li {
        padding: 10px;
        border-bottom: 1px solid #777; /* Gray */
        margin-bottom: 10px; /* Add margin between comments */
    }

    .survey-responses li {
        padding: 15px;
        border-bottom: 1px solid #777; /* Gray */
        margin-bottom: 15px; /* Add margin between survey responses */
        background-color: #555; /* Darker background color for survey responses */
        border-radius: 5px; /* Add rounded corners */
    }

    .survey-responses li:last-child {
        border-bottom: none; /* Remove bottom border for last survey response */
    }

    /* Scrollbar Styles */
    .content-section::-webkit-scrollbar {
        width: 10px;
        border-radius: 10px; /* Rounded edges for scrollbar */
    }

    .content-section::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.1); /* Light Gray */
    }

    .content-section::-webkit-scrollbar-thumb {
        background: #777; /* Slightly Darker Gray */
        border-radius: 10px;
    }

    .content-section::-webkit-scrollbar-thumb:hover {
        background: #555; /* Dark Gray */
    }
</style>

</style>
</html>



