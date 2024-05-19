<?php
// Include your database connection file
$DBConnect = mysqli_connect("localhost", "root", "", "quickfunds");
if (!$DBConnect)
    die("<p> The server is not available. </p>");

// Retrieve data from the database
$query = "SELECT `ID`, `comment`, DATE(`time`) AS `date` FROM `comments`"; // Modified to select only the date part
$result = mysqli_query($DBConnect, $query);

if (!$result) {
    die("Error retrieving data: " . mysqli_error($DBConnect));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Comments</title>
    <style>
        /* Custom scrollbar styling */
        /* We use the webkit scroll to change the width for the scroll bar */
        /* We use the track to make the background of the scroll be transparent */
        /* The thumb is for the scroll bar so we use thumb to change its color and radius */
        ::-webkit-scrollbar {
            width: .5rem;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #FF4D4D; /* Red color */
            border-radius: 2rem;
        }

        /* Style for the table container */
        .table-container {
            width: 323px;
            height: 269px;
            overflow-y: auto; /* Add vertical scrollbar */
            border: 1px solid #ccc;
            border-radius: 10px;
        }

        /* Style for the table */
        table {
            width: 100%;
            border-collapse: collapse;
            color: black; /* Text color */
        }

        /* Style for table header */
        th {
            background-color: #FF4D4D; /* Red color */
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            color: white;
        }

        /* Style for table rows */
        td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <h1>User Comments</h1>

    <!-- Display the comments in a scrollable table -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Comment</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Output each comment as a table row
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['ID'] . "</td>";
                    echo "<td>" . $row['comment'] . "</td>";
                    echo "<td>" . $row['date'] . "</td>"; // Use 'date' instead of 'time'
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
// Close the database connection
mysqli_close($DBConnect);
?>
