<?php

    // Script tries to connect to the database
    
    $dbServerName = "localhost";
    $dbUserName = "root";
    $dbPassword = "";
    $dbName = "bugdatabase2";

    $conn = mysqli_connect($dbServerName, $dbUserName, $dbPassword, $dbName);
    if(!$conn)
    {
       die("Connection Failed: " . mysqli_connect_error()); 
    }
