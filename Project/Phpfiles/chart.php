<?php
// Include your database connection file
$DBConnect = mysqli_connect("localhost", "root", "", "quickfunds");
if (!$DBConnect)
    die("<p> The server is not available. </p>");

// Retrieve data from the database
$query = "SELECT MAX(TxnID) AS LatestTxnID, DATE(time) AS Date FROM transactions GROUP BY DATE(time)";
$result = mysqli_query($DBConnect, $query);

if (!$result) {
    die("Error retrieving data: " . mysqli_error($DBConnect));
}

// Initialize an array to store daily profits
$dailyProfits = array();

// Process the retrieved data
while ($row = mysqli_fetch_assoc($result)) {
    $date = $row['Date']; // Extract the date
    $latestTxnID = $row['LatestTxnID']; // Get the latest transaction ID

    // Assign profit (number of transactions) for each day
    $dailyProfits[$date] = $latestTxnID;
}

// Close the database connection
mysqli_close($DBConnect);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profit Chart</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {packages: ['corechart', 'bar']});
        google.charts.setOnLoadCallback(drawChart);
        
        function drawChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('date', 'Date');
            data.addColumn('number', 'Profits');

            // Add data rows
            data.addRows([
                <?php foreach ($dailyProfits as $date => $txnID) {
                    echo "[new Date('$date'), $txnID],";
                } ?>
            ]);

            var options = {
                title: 'Profits Over Time',
                legend: { position: 'none' },
                bars: 'horizontal',
                bar: { groupWidth: '90%' },
                backgroundColor: 'white',
                colors: ['#FF4D4D']
            };

            var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
            chart.draw(data, options);
        }
    </script>
</head>
<body>
    <h1>Profit Chart</h1>

    <!-- Container for the chart -->
    <div id="chart_div" style="width: 900px; height: 500px;"></div>
</body>
</html>
