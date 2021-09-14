<?php

if(isset($_POST["submitButton"]))
{
    //Check if user got to this script by pressing a submitLoginButton, if not redirect user back to index.php

    require_once "dbh.inc.php";
    require_once "loginFunctions.inc.php";

    $userName = $_POST["userName"];
    $password = $_POST["userPassword"];

    // Check if Provided Login Data is valid

    if(emptyLoginForm($userName,$password) !== false)
    {
        header("location: ../index.php?error=emptyLoginForm"); 
        exit(); 
    }
    if(invalidUserName($userName) !== false)
    {
        header("location: ../index.php?error=invalidUserName");
        exit(); 
    }
    if(invalidPassword($password) !== false)
    {
        header("location: ../index.php?error=invalidPassword");
        exit(); 
    }
    login($conn, $userName, $password);
}
else
{
    header("location: ../index.php");
    exit(); 
}
