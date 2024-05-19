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
if (!$DBConnect) {
    die("<p>The server is not available.</p>");
}

// Retrieve user data from the database
$userQuery = "SELECT UserID, UserName, balance, AccStatus, DATE(created_at) AS creationDate FROM user";
$userResult = mysqli_query($DBConnect, $userQuery);

if (!$userResult) {
    die("Error retrieving user data: " . mysqli_error($DBConnect));
}

// Retrieve transaction data from the database
$transactionQuery = "SELECT TxnID, FromID, ToID FROM transactions";
$transactionResult = mysqli_query($DBConnect, $transactionQuery);

if (!$transactionResult) {
    die("Error retrieving transaction data: " . mysqli_error($DBConnect));
}

// Initialize arrays to store user information
$totalUsers = 0;
$activeUsers = 0;
$inactiveUsers = 0;
$restrictedUsers = 0;
$suspendedUsers = 0;
$users = [];
$userCreationDates = [];

// Process the user data
while ($userRow = mysqli_fetch_assoc($userResult)) {
    $totalUsers++;
    $users[$userRow['UserID']] = [
        'UserName' => $userRow['UserName'],
        'balance' => $userRow['balance'],
        'hasTransaction' => false,
        'AccStatus' => $userRow['AccStatus']
    ];

    // Track user account creation dates
    $creationDate = $userRow['creationDate'];
    if (!isset($userCreationDates[$creationDate])) {
        $userCreationDates[$creationDate] = 0;
    }
    $userCreationDates[$creationDate]++;
    
    // Count restricted and suspended users
    if ($userRow['AccStatus'] == 'Restricted') {
        $restrictedUsers++;
    } elseif ($userRow['AccStatus'] == 'Suspended') {
        $suspendedUsers++;
    }
}

// Process the transaction data to find active users
while ($txnRow = mysqli_fetch_assoc($transactionResult)) {
    $fromID = $txnRow['FromID'];
    $toID = $txnRow['ToID'];

    if (isset($users[$fromID])) {
        $users[$fromID]['hasTransaction'] = true;
    }
    if (isset($users[$toID])) {
        $users[$toID]['hasTransaction'] = true;
    }
}

// Calculate active and inactive users
foreach ($users as $user) {
    if ($user['hasTransaction'] || $user['balance'] > 0) {
        $activeUsers++;
    } else {
        $inactiveUsers++;
    }
}

// Close the database connection
mysqli_close($DBConnect);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Statistics</title>
    <link rel="shortcut icon" type="image" href="./login/log.png">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {packages: ['corechart', 'bar']});
        google.charts.setOnLoadCallback(drawCharts);

        function drawCharts() {
            drawUserChart();
            drawUserCreationChart();
        }

        function drawUserChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'User Status');
            data.addColumn('number', 'Count');

            data.addRows([
                ['Active Users', <?php echo $activeUsers; ?>],
                ['Inactive Users', <?php echo $inactiveUsers; ?>],
                ['Restricted Users', <?php echo $restrictedUsers; ?>],
                ['Suspended Users', <?php echo $suspendedUsers; ?>]
            ]);

            var options = {
                title: 'User Activity',
                titleTextStyle: {
                    color: '#FFF'
                },
                backgroundColor: '#444',
                colors: ['#28a745', '#dc3545', '#ffc107', '#6c757d'],
                chartArea: {
                    width: '80%',
                    height: '70%'
                },
                legend: {
                    textStyle: {
                        color: '#FFF'
                    }
                },
                hAxis: {
                    textStyle: {
                        color: '#ddd'
                    },
                    titleTextStyle: {
                        color: '#ddd'
                    },
                    gridlines: {
                        color: '#777'
                    }
                },
                vAxis: {
                    textStyle: {
                        color: '#ddd'
                    },
                    titleTextStyle: {
                        color: '#ddd'
                    },
                    gridlines: {
                        color: '#777'
                    },
                    minValue: 0
                }
            };

            var chart = new google.visualization.PieChart(document.getElementById('user_chart_div'));
            chart.draw(data, options);
        }

        function drawUserCreationChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('date', 'Date');
            data.addColumn('number', 'User Registrations');

            data.addRows([
                <?php
                foreach ($userCreationDates as $date => $count) {
                    echo "[new Date('$date'), $count],";
                }
                ?>
            ]);

            var options = {
                title: 'User Registrations Over Time',
                titleTextStyle: {
                    color: '#FFF'
                },
                backgroundColor: '#444',
                colors: ['#007bff'],
                chartArea: {
                    width: '80%',
                    height: '70%'
                },
                legend: {
                    textStyle: {
                        color: '#FFF'
                    }
                },
                hAxis: {
                    title: 'Date',
                    textStyle: {
                        color: '#ddd'
                    },
                    titleTextStyle: {
                        color: '#ddd'
                    },
                    gridlines: {
                        color: '#777'
                    }
                },
                vAxis: {
                    title: 'Number of Registrations',
                    textStyle: {
                        color: '#ddd'
                    },
                    titleTextStyle: {
                        color: '#ddd'
                    },
                    gridlines: {
                        color: '#777'
                    },
                    minValue: 0
                }
            };

            var chart = new google.visualization.LineChart(document.getElementById('user_creation_chart_div'));
            chart.draw(data, options);
        }
    </script>
</head>
<body style="font-family: 'Arial', sans-serif; background: linear-gradient(to right, #1a1a1a, #171616); color: #fff; margin: 0; padding: 0; display: flex; flex-direction: column; align-items: center; min-height: 100vh;">
<h1 style="text-align: center; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: bold; font-size: 2.5rem;">QuickFunds User Statistics</h1>

<!-- Container for user statistics boxes -->
<div style="display: flex; justify-content: space-around; width: 80%; max-width: 1200px; margin-bottom: 2rem;">
    <div style="background: #444; padding: 1rem; border-radius: 8px; text-align: center; flex: 1; margin: 0 1rem;">
        <h2>Total Users</h2>
        <p style="font-size: 2rem; font-weight: bold;"><?php echo $totalUsers; ?></p>
    </div>
    <div style="background: #444; padding: 1rem; border-radius: 8px; text-align: center; flex: 1; margin: 0 1rem;">
        <h2>Active Users</h2>
        <p style="font-size: 2rem; font-weight: bold;"><?php echo $activeUsers; ?></p>
    </div>
    <div style="background: #444; padding: 1rem; border-radius: 8px; text-align: center; flex: 1; margin: 0 1rem;">
        <h2>Inactive Users</h2>
        <p style="font-size: 2rem; font-weight: bold;"><?php echo $inactiveUsers; ?></p>
    </div>
    <div style="background: #444; padding: 1rem; border-radius: 8px; text-align: center; flex: 1; margin: 0 1rem;">
        <h2>Restricted Users</h2>
        <p style="font-size: 2rem; font-weight: bold;"><?php echo $restrictedUsers; ?></p>
    </div>
    <div style="background: #444; padding: 1rem; border-radius: 8px; text-align: center; flex: 1; margin: 0 1rem;">
        <h2>Suspended Users</h2>
        <p style="font-size: 2rem; font-weight: bold;"><?php echo $suspendedUsers; ?></p>
    </div>
</div>

<!-- Container for the user charts -->
<div id="user_chart_div" style="width: 80%; max-width: 1200px; height: 500px; margin-bottom: 2rem;"></div>
<div id="user_creation_chart_div" style="width: 80%; max-width: 1200px; height: 500px;"></div>
</body>

<Style>
    html::-webkit-scrollbar {
    width: .8rem;
    position: fixed;
    top: 50px; /* Position scrollbar below the nav */
    z-index: 998; /* Set lower z-index than the nav */
}

html::-webkit-scrollbar-track {
    background: transparent;
}

html::-webkit-scrollbar-thumb {
    background: #dc3545;
    border-radius: 1rem;
}
</style>
</html>
