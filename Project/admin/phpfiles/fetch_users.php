<?php
// Include the database connection file
include "./phpfiles/DBConnect.php";

// Fetch all user data from the database
$query = "SELECT UserID, UserName, Email, balance, AccStatus FROM `user`";
$result = mysqli_query($DBConnect, $query);

// Initialize an array to store user data
$users = array();

// Check if there are any users
if(mysqli_num_rows($result) > 0) {
    // Loop through each row of user data
    while($row = mysqli_fetch_assoc($result)) {
        // Add each user to the array
        $users[] = $row;
    }
}

// Close the database connection
mysqli_close($DBConnect);

// Return user data as JSON
echo json_encode($users);
?>
