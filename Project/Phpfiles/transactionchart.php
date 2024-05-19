
<?php
// Include your database connection file
$DBConnect = mysqli_connect("localhost", "root", "", "quickfunds");
if (!$DBConnect)
    die("<p> The server is not available. </p>");

// Retrieve data from the database
$query = "SELECT COUNT(TxnID) AS NumTransactions, DATE(time) AS Date FROM transactions GROUP BY DATE(time)";
$result = mysqli_query($DBConnect, $query);

if (!$result) {
    die("Error retrieving data: " . mysqli_error($DBConnect));
}

// Initialize an array to store daily transaction counts
$dailyTransactions = array();

// Process the retrieved data
while ($row = mysqli_fetch_assoc($result)) {
    $date = $row['Date']; // Extract the date
    $numTransactions = $row['NumTransactions']; // Get the number of transactions

    // Store the number of transactions for each day
    $dailyTransactions[$date] = $numTransactions;
}

// Close the database connection
mysqli_close($DBConnect);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Chart</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {packages: ['corechart']});
        google.charts.setOnLoadCallback(drawChart);
        
        function drawChart() {
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
                title: 'Transactions Over Time',
                legend: { position: 'none' },
                curveType: 'function',
                backgroundColor: 'white',
                colors: ['#FF4D4D']
            };

            var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
            chart.draw(data, options);
        }
    </script>
</head>
<body>
    <h1>Transaction Chart</h1>

    <!-- Container for the chart -->
    <div id="chart_div" style="width: 900px; height: 500px;"></div>
</body>
</html>
