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
$DBConnect = mysqli_connect("localhost", "root", "", "quickfunds");
if (!$DBConnect)
    die("<p>The server is not available.</p>");

// Retrieve data from the database
$query = "SELECT COUNT(TxnID) AS NumTransactions, DATE(time) AS Date FROM transactions GROUP BY DATE(time)";
$result = mysqli_query($DBConnect, $query);

if (!$result) {
    die("Error retrieving data: " . mysqli_error($DBConnect));
}

// Initialize an array to store daily transaction counts
$dailyTransactions = array();

// Process the retrieved data
$totalTransactions = 0;
$daysCounted = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $date = $row['Date']; // Extract the date
    $numTransactions = $row['NumTransactions']; // Get the number of transactions
    $totalTransactions += $numTransactions;
    $daysCounted++;

    // Store the number of transactions for each day
    $dailyTransactions[$date] = $numTransactions;
}

// Calculate average transactions per day
$averageTransactions = $daysCounted ? $totalTransactions / $daysCounted : 0;

// Close the database connection
mysqli_close($DBConnect);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Chart</title>
    <link rel="shortcut icon" type="image" href="./login/log.png">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {packages: ['corechart', 'bar']});
        google.charts.setOnLoadCallback(drawCharts);
        
        function drawCharts() {
            drawLineChart();
            drawBarChart();
        }

        function drawLineChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('date', 'Date');
            data.addColumn('number', 'Transactions');

            // Add data rows
            data.addRows([
                <?php foreach ($dailyTransactions as $date => $numTransactions) {
                    echo "[new Date('$date'), $numTransactions],";
                } ?>
            ]);

            var options = {
                title: 'Transactions Over Time (Line Chart)',
                titleTextStyle: { color: '#FFF' },
                legend: { position: 'none' },
                curveType: 'function',
                backgroundColor: '#444',
                colors: ['#dc3545'],
                chartArea: { width: '80%', height: '70%' },
                hAxis: {
                    title: 'Date',
                    textStyle: { color: '#ddd' },
                    titleTextStyle: { color: '#ddd' },
                    gridlines: { color: '#777' }
                },
                vAxis: {
                    title: 'Number of Transactions',
                    textStyle: { color: '#ddd' },
                    titleTextStyle: { color: '#ddd' },
                    gridlines: { color: '#777' },
                    minValue: 0 // Ensure the y-axis starts at 0
                }
            };

            var chart = new google.visualization.LineChart(document.getElementById('line_chart_div'));
            chart.draw(data, options);
        }

        function drawBarChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Date');
            data.addColumn('number', 'Transactions');

            // Add data rows
            data.addRows([
                <?php foreach ($dailyTransactions as $date => $numTransactions) {
                    echo "['$date', $numTransactions],";
                } ?>
            ]);

            var options = {
                title: 'Transactions Over Time (Bar Chart)',
                titleTextStyle: { color: '#FFF' },
                legend: { position: 'none' },
                backgroundColor: '#444',
                colors: ['#dc3545'],
                chartArea: { width: '80%', height: '70%' },
                hAxis: {
                    title: 'Date',
                    textStyle: { color: '#ddd' },
                    titleTextStyle: { color: '#ddd' },
                    gridlines: { color: '#777' }
                },
                vAxis: {
                    title: 'Number of Transactions',
                    textStyle: { color: '#ddd' },
                    titleTextStyle: { color: '#ddd' },
                    gridlines: { color: '#777' },
                    minValue: 0 // Ensure the y-axis starts at 0
                }
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('bar_chart_div'));
            chart.draw(data, options);
        }
    </script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to right, #1a1a1a, #171616);
            color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }
        h1 {
            text-align: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-weight: bold;
            font-size: 2.5rem;
            margin-bottom: 20px;
        }
        .summary {
            display: flex;
            justify-content: space-around;
            width: 80%;
            max-width: 1200px;
            margin-bottom: 2rem;
            background: #444;
            padding: 1rem;
            border-radius: 8px;
        }
        .summary div {
            text-align: center;
            flex: 1;
            margin: 0 1rem;
        }
        .summary h2 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        .summary p {
            font-size: 2rem;
            font-weight: bold;
        }
        .charts {
            display: flex;
            justify-content: space-between;
            width: 80%;
            max-width: 1200px;
        }
        .chart {
            width: 48%;
            height: 400px;
        }
    </style>
</head>
<body>
    <h1>Transaction Statistics</h1>

    <!-- Summary Section -->
    <div class="summary">
        <div>
            <h2>Total Transactions</h2>
            <p><?php echo $totalTransactions; ?></p>
        </div>
        <div>
            <h2>Average Transactions Per Day</h2>
            <p><?php echo round($averageTransactions, 2); ?></p>
        </div>
    </div>

    <!-- Flex container for charts -->
    <div class="charts">
        <!-- Container for the line chart -->
        <div id="line_chart_div" class="chart"></div>

        <!-- Container for the bar chart -->
        <div id="bar_chart_div" class="chart"></div>
    </div>
</body>
</html>
