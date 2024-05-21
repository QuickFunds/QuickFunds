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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/d3@7"></script>
    <script src="https://cdn.jsdelivr.net/npm/d3-cloud@1"></script>
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
                $sql_comments = "SELECT ID, comment, time FROM comments WHERE 1";
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
                // Fetch survey responses from database and sort by submission time (latest first)
                $sql_survey = "SELECT id, userID, likes, improvements, additionalComments, experienceRating, likelihoodRecommend, submissionTime FROM surveyresponses ORDER BY submissionTime DESC";
                $result_survey = mysqli_query($DBConnect, $sql_survey);

                $experienceRatings = [];
                $likelihoodRecommend = [];
                $likes = [];
                $improvements = [];

                if (mysqli_num_rows($result_survey) > 0) {
                    // Output data of each row
                    while($row_survey = mysqli_fetch_assoc($result_survey)) {
                        echo "<li>User ID: " . $row_survey["userID"] . "<br>Likes: " . $row_survey["likes"] . "<br>Improvements: " . $row_survey["improvements"] . "<br>Additional Comments: " . $row_survey["additionalComments"] . "<br>Experience Rating: " . $row_survey["experienceRating"] . "<br>Likelihood Recommend: " . $row_survey["likelihoodRecommend"] . "<br>Submission Time: " . $row_survey["submissionTime"] . "</li>";
                        
                        $experienceRatings[] = $row_survey["experienceRating"];
                        $likelihoodRecommend[] = $row_survey["likelihoodRecommend"];
                        $likes[] = $row_survey["likes"];
                        $improvements[] = $row_survey["improvements"];
                    }
                } else {
                    echo "No survey responses found";
                }
            ?>
            </div>
        </div>
    </div>
    <div class="chart-container">
        <canvas id="experienceRatingChart"></canvas>
        <canvas id="likelihoodRecommendChart"></canvas>
        <div id="wordMap"></div>
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

        .chart-container {
            display: flex;
            justify-content: space-around;
            margin: 20px;
            flex-wrap: wrap;
        }

        canvas {
            max-width: 700px; /* Adjusted width */
            max-height: 370px; /* Adjusted height */
            margin-bottom: 20px;
        }

        #wordMap {
            width: 95%;
            height: 500px;
            background-color: #444; /* Dark Gray */
            border-radius: 10px; /* Add rounded corners */
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5); /* Darker shadow effect */
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
    </style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const experienceRatings = <?php echo json_encode($experienceRatings); ?>;
        const likelihoodRecommend = <?php echo json_encode($likelihoodRecommend); ?>;
        const likes = <?php echo json_encode($likes); ?>;
        const improvements = <?php echo json_encode($improvements); ?>;

        // Experience Rating Chart
        const ctxExperience = document.getElementById('experienceRatingChart').getContext('2d');
        new Chart(ctxExperience, {
            type: 'bar',
            data: {
                labels: Array.from({length: experienceRatings.length}, (_, i) => i + 1),
                datasets: [{
                    label: 'Experience Rating',
                    data: experienceRatings,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Likelihood to Recommend Chart
        const ctxRecommend = document.getElementById('likelihoodRecommendChart').getContext('2d');
        new Chart(ctxRecommend, {
            type: 'bar',
            data: {
                labels: Array.from({length: likelihoodRecommend.length}, (_, i) => i + 1),
                datasets: [{
                    label: 'Likelihood to Recommend',
                    data: likelihoodRecommend,
                    backgroundColor: 'rgba(225, 60, 80,0.5)',
                    borderColor: 'rgba(255, 255, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Word Map
        const words = [...likes, ...improvements].reduce((acc, word) => {
            const splitWords = word.split(' ');
            splitWords.forEach(w => {
                if (acc[w]) {
                    acc[w]++;
                } else {
                    acc[w] = 1;
                }
            });
            return acc;
        }, {});

        const stopwords = ["the", "and", "a", "is", "it", "to", "in", "of", "for", "that", "on", "with", "as", "at", "be", "would", "are", "who"];

        const wordEntries = Object.entries(words)
            .filter(([text]) => !stopwords.includes(text.toLowerCase()))
            .map(([text, size]) => ({ text, size }));

        const layout = d3.layout.cloud()
            .size([document.getElementById('wordMap').clientWidth, document.getElementById('wordMap').clientHeight])
            .words(wordEntries)
            .padding(5)
            .rotate(() => ~~(Math.random() * 2) * 90)
            .font("Impact")
            .fontSize(d => d.size * 10)
            .on("end", draw);

        layout.start();

        function draw(words) {
            d3.select("#wordMap").append("svg")
                .attr("width", layout.size()[0])
                .attr("height", layout.size()[1])
                .append("g")
                .attr("transform", "translate(" + layout.size()[0] / 2 + "," + layout.size()[1] / 2 + ")")
                .selectAll("text")
                .data(words)
                .enter().append("text")
                .style("font-size", d => d.size + "px")
                .style("font-family", "Impact")
                .style("fill", (d, i) => d3.schemeCategory10[i % 10])
                .attr("text-anchor", "middle")
                .attr("transform", d => "translate(" + [d.x, d.y] + ")rotate(" + d.rotate + ")")
                .text(d => d.text);
        }
    });
</script>
</html>
