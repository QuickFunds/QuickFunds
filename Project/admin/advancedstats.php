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

// Retrieve transaction data from the database
$queryTransactions = "SELECT COUNT(TxnID) AS NumTransactions, DATE(time) AS Date FROM transactions GROUP BY DATE(time)";
$resultTransactions = mysqli_query($DBConnect, $queryTransactions);

if (!$resultTransactions) {
    die("Error retrieving transaction data: " . mysqli_error($DBConnect));
}

// Initialize an array to store daily transaction counts
$dailyTransactions = array();

// Process the retrieved transaction data
$totalTransactions = 0;
$daysCounted = 0;
while ($row = mysqli_fetch_assoc($resultTransactions)) {
    $date = $row['Date']; // Extract the date
    $numTransactions = $row['NumTransactions']; // Get the number of transactions
    $totalTransactions += $numTransactions;
    $daysCounted++;

    // Store the number of transactions for each day
    $dailyTransactions[$date] = $numTransactions;
}

// Calculate average transactions per day
$averageTransactions = $daysCounted ? $totalTransactions / $daysCounted : 0;

// Retrieve gift card data from the database
$queryGiftCards = "SELECT COUNT(cardnum) AS NumGiftCards, DATE(createdAt) AS Date FROM giftcards GROUP BY DATE(createdAt)";
$resultGiftCards = mysqli_query($DBConnect, $queryGiftCards);

if (!$resultGiftCards) {
    die("Error retrieving gift card data: " . mysqli_error($DBConnect));
}

// Initialize an array to store daily gift card counts
$dailyGiftCards = array();

// Process the retrieved gift card data
$totalGiftCards = 0;
$daysCountedGiftCards = 0;
while ($rowGiftCards = mysqli_fetch_assoc($resultGiftCards)) {
    $date = $rowGiftCards['Date']; // Extract the date
    $numGiftCards = $rowGiftCards['NumGiftCards']; // Get the number of gift cards
    $totalGiftCards += $numGiftCards;
    $daysCountedGiftCards++;

    // Store the number of gift cards for each day
    $dailyGiftCards[$date] = $numGiftCards;
}

// Calculate average gift cards per day
$averageGiftCards = $daysCountedGiftCards ? $totalGiftCards / $daysCountedGiftCards : 0;

// Retrieve gift card data for each amount
$queryGiftCardAmounts = "SELECT balance, COUNT(cardnum) AS NumGiftCards FROM giftcards GROUP BY balance";
$resultGiftCardAmounts = mysqli_query($DBConnect, $queryGiftCardAmounts);

if (!$resultGiftCardAmounts) {
    die("Error retrieving gift card amount data: " . mysqli_error($DBConnect));
}

// Initialize an array to store gift card amounts and their counts
$giftCardAmounts = array();

// Process the retrieved gift card amount data
while ($rowGiftCardAmounts = mysqli_fetch_assoc($resultGiftCardAmounts)) {
    $balance = $rowGiftCardAmounts['balance']; // Extract the balance
    $numGiftCards = $rowGiftCardAmounts['NumGiftCards']; // Get the number of gift cards

    // Store the number of gift cards for each balance
    $giftCardAmounts[$balance] = $numGiftCards;
}

// Retrieve data for redeemed and not redeemed gift cards
$queryRedeemedGiftCards = "SELECT COUNT(cardnum) AS NumRedeemed FROM giftcards WHERE IsRedeemed = 1";
$resultRedeemedGiftCards = mysqli_query($DBConnect, $queryRedeemedGiftCards);
if (!$resultRedeemedGiftCards) {
    die("Error retrieving redeemed gift card data: " . mysqli_error($DBConnect));
}
$rowRedeemedGiftCards = mysqli_fetch_assoc($resultRedeemedGiftCards);
$redeemedCount = $rowRedeemedGiftCards['NumRedeemed'];

$queryNotRedeemedGiftCards = "SELECT COUNT(cardnum) AS NumNotRedeemed FROM giftcards WHERE IsRedeemed = 0";
$resultNotRedeemedGiftCards = mysqli_query($DBConnect, $queryNotRedeemedGiftCards);
if (!$resultNotRedeemedGiftCards) {
    die("Error retrieving not redeemed gift card data: " . mysqli_error($DBConnect));
}
$rowNotRedeemedGiftCards = mysqli_fetch_assoc($resultNotRedeemedGiftCards);
$notRedeemedCount = $rowNotRedeemedGiftCards['NumNotRedeemed'];


// Close the result sets
mysqli_free_result($resultTransactions);
mysqli_free_result($resultGiftCards);
mysqli_free_result($resultGiftCardAmounts);
mysqli_close($DBConnect);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Statistics</title>
    <link rel="shortcut icon" type="image" href="./login/log.png">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {packages: ['corechart', 'bar']});
        google.charts.setOnLoadCallback(drawCharts);
        
        function drawCharts() {
            drawLineChart();
            drawBarChart();
            drawGiftCardLineChart();
            drawGiftCardBarChart();
            drawGiftCardPieChart();
            drawGiftCardRedemptionPieChart();
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

        function drawGiftCardLineChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('date', 'Date');
            data.addColumn('number', 'Gift Cards');

            // Add data rows
            data.addRows([
                <?php foreach ($dailyGiftCards as $date => $numGiftCards) {
                    echo "[new Date('$date'), $numGiftCards],";
                } ?>
            ]);

            var options = {
                title: 'Gift Cards Over Time (Line Chart)',
                titleTextStyle: { color: '#FFF' },
                legend: { position: 'none' },
                curveType: 'function',
                backgroundColor: '#444',
                colors: ['#28a745'],
                chartArea: { width: '80%', height: '70%' },
                hAxis: {
                    title: 'Date',
                    textStyle: { color: '#ddd' },
                    titleTextStyle: { color: '#ddd' },
                    gridlines: { color: '#777' }
                },
                vAxis: {
                    title: 'Number of Gift Cards',
                    textStyle: { color: '#ddd' },
                    titleTextStyle: { color: '#ddd' },
                    gridlines: { color: '#777' },
                    minValue: 0 // Ensure the y-axis starts at 0
                }
            };

            var chart = new google.visualization.LineChart(document.getElementById('gift_card_line_chart_div'));
            chart.draw(data, options);
        }

        function drawGiftCardBarChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Date');
            data.addColumn('number', 'Gift Cards');

            // Add data rows
            data.addRows([
                <?php foreach ($dailyGiftCards as $date => $numGiftCards) {
                    echo "['$date', $numGiftCards],";
                } ?>
            ]);

            var options = {
                title: 'Gift Cards Over Time (Bar Chart)',
                titleTextStyle: { color: '#FFF' },
                legend: { position: 'none' },
                backgroundColor: '#444',
                colors: ['#28a745'],
                chartArea: { width: '80%', height: '70%' },
                hAxis: {
                    title: 'Date',
                    textStyle: { color: '#ddd' },
                    titleTextStyle: { color: '#ddd' },
                    gridlines: { color: '#777' }
                },
                vAxis: {
                    title: 'Number of Gift Cards',
                    textStyle: { color: '#ddd' },
                    titleTextStyle: { color: '#ddd' },
                    gridlines: { color: '#777' },
                    minValue: 0 // Ensure the y-axis starts at 0
                }
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('gift_card_bar_chart_div'));
            chart.draw(data, options);
        }

        function drawGiftCardPieChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Balance');
            data.addColumn('number', 'Number of Cards');

            // Add data rows
            data.addRows([
                ['10$', <?php echo isset($giftCardAmounts['10']) ? $giftCardAmounts['10'] : 0; ?>],
                ['25$', <?php echo isset($giftCardAmounts['25']) ? $giftCardAmounts['25'] : 0; ?>],
                ['50$', <?php echo isset($giftCardAmounts['50']) ? $giftCardAmounts['50'] : 0; ?>],
                ['100$', <?php echo isset($giftCardAmounts['100']) ? $giftCardAmounts['100'] : 0; ?>],
                ['250$', <?php echo isset($giftCardAmounts['250']) ? $giftCardAmounts['250'] : 0; ?>],
                ['500$', <?php echo isset($giftCardAmounts['500']) ? $giftCardAmounts['500'] : 0; ?>]
            ]);

            var options = {
                title: 'Gift Card Amount Distribution (Pie Chart)',
                titleTextStyle: { color: '#FFF' },
                backgroundColor: '#444',
                colors: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6c757d', '#17a2b8'],
                chartArea: { width: '80%', height: '70%' },
                legend: { textStyle: { color: '#ddd' } }
            };

            var chart = new google.visualization.PieChart(document.getElementById('gift_card_pie_chart_div'));
            chart.draw(data, options);
        }

        function drawGiftCardRedemptionPieChart() {
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Redemption Status');
    data.addColumn('number', 'Count');

    // Add data rows
    data.addRows([
        ['Redeemed', <?php echo $redeemedCount; ?>],
        ['Not Redeemed', <?php echo $notRedeemedCount; ?>]
    ]);

    var options = {
        title: 'Redeemed vs. Not Redeemed Gift Cards (Pie Chart)',
        titleTextStyle: { color: '#FFF' },
        backgroundColor: '#444',
        colors: ['#28a745', '#dc3545'],
        chartArea: { width: '80%', height: '70%' },
        legend: { textStyle: { color: '#ddd' } }
    };

    var chart = new google.visualization.PieChart(document.getElementById('gift_card_redemption_pie_chart_div'));
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
            flex-direction: column;
            align-items: center;
            width: 80%;
            max-width: 1200px;
        }
        .chart-container {
    display: flex;
    justify-content: space-between;
    width: 100%;
    margin-bottom: 2rem;
}

.chart {
    width: 48%;
    height: 400px;
}

        /* Scrollbar Styles */
::-webkit-scrollbar {
    width: 10px;
    border-radius: 10px; /* Rounded edges for scrollbar */
}

::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 255); /* Light Gray */
}

::-webkit-scrollbar-thumb {
    background: #777; /* Slightly Darker Gray */
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #555; /* Dark Gray */
}

    </style>
</head>
<body>
    <h1>Transaction Statistics</h1>

    <!-- Transaction Summary Section -->
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

    <!-- Transaction Charts -->
    <div class="charts">
        <div class="chart-container">
            <!-- Container for the line chart -->
            <div id="line_chart_div" class="chart"></div>

            <!-- Container for the bar chart -->
            <div id="bar_chart_div" class="chart"></div>
        </div>
    </div>

    <h1>GiftCrard Statistics</h1>

    <!-- Gift Card Summary Section -->
    <div class="summary">
        <div>
            <h2>Total Gift Cards</h2>
            <p><?php echo $totalGiftCards; ?></p>
        </div>
        <div>
            <h2>Average Gift Cards Per Day</h2>
            <p><?php echo round($averageGiftCards, 2); ?></p>
        </div>
    </div>

    <!-- Gift Card Charts -->
    <div class="charts">
        <div class="chart-container">
            <!-- Container for the gift card line chart -->
            <div id="gift_card_line_chart_div" class="chart"></div>

            <!-- Container for the gift card bar chart -->
            <div id="gift_card_bar_chart_div" class="chart"></div>
        </div>
    </div>

    <!-- Gift Card Amount Distribution and Redemption Pie Chart -->
    <div class="charts">
        <div class="chart-container">
            <!-- Container for the pie chart -->
            <div id="gift_card_pie_chart_div" class="chart"></div>

            <!-- Container for the redemption pie chart -->
            <div id="gift_card_redemption_pie_chart_div" class="chart"></div>
        </div>
    </div>

</body>
</html>
