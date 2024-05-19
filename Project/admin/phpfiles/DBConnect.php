<?php 

$DBConnect = mysqli_connect("localhost" , "root", "" , "quickfunds");
    if(!$DBConnect)
        die("<p> The server is not available. </p>");
?>