<?php

if(isset($_POST["submitButton"]))
{
    // check if user got to this script by pressing a submitLoginButton, if not redirect user to registerUserForm.php
    require_once "dbh.inc.php";
    require_once "loginFunctions.inc.php";

    $userName = $_POST["userName"];
    $password1 = $_POST["userPassword1"];
    $password2 = $_POST["userPassword2"];
    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $typeID = 0;
    $department = $_POST["department"];

   // Check if Provided data is valid
   if(emptyRegistrationForm($userName, $password1, $password2, $firstName, $lastName,$department) !== false)
   {
       header("location: ../ui/registerUserForm.php?error=emptyRegistrationForm");
       exit(); 
   }
   if(invalidUserName($userName) !== false)
   {
       header("location: ../ui/registerUserForm.php?error=invalidUserName");
       exit(); 
   }
   // Check if any field conists of char "'".
   // in case it does return user back to registerUserForm.php with
   // error message invalidInput!
   if(passwordsMatch($password1, $password2) !== false)
   {
      header("location: ../ui/registerUserForm.php?error=passwordsDontMatch");
       exit(); 
   }
   if(invalidPassword($password1) !== false)
   {
       header("location: ../ui/registerUserForm.php?error=invalidPassword");
       exit(); 
   }
   register($conn, $userName, $password1, $firstName, $lastName, $typeID, $department);
}
else
{
    header("location: ../ui/registerUserForm.php");
    exit(); 
}
