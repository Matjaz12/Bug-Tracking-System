<?php

    function emptyLoginForm($name, $password)
    {
        // Function checks if both provided parameters are not empty
        return empty($name) || empty($password);
    }

    function emptyRegistrationForm($userName, $password1, $password2, $firstName, $lastName,$department)
    {
        // Function checks if all provided paramters are not empty
        $result = empty($userName) ||  empty($password1)  || empty($password2) || empty($firstName) ||  empty($lastName) || empty($department);
        return $result;
    }

    function invalidUserName($name)
    {
        // Function checks if provided name is in set of legal characters
        return !preg_match("/^[a-zA-Z-0-9]*$/",$name);
    }

    function passwordsMatch($password1, $password2)
    {
        // Function checks if registration passwords match
        if($password1 !== $password2)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function invalidPassword($password)
    {
        // Function checks if length of password is in defined range
        $maxPasswordLength = 25;
        return strlen($password) > $maxPasswordLength;
    }

    function doesUserExist($conn, $name)
    {

        /*
        Function checks if provided name exists in database. In case it does,
        it returns the associated row, otherwise it returns a false statement
        */

        // Sql Injection Protected Query
        $sql = "SELECT * FROM user WHERE UserName = ?";
        $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt, $sql))
        {
            // Error executing sql statement
            header("location: ../index.php?error=sqlStmtFailed");
            exit();
        }

        mysqli_stmt_bind_param($stmt,"s", $name);
        mysqli_stmt_execute($stmt);
        $resultData = mysqli_stmt_get_result($stmt);

        if($row = mysqli_fetch_assoc($resultData))
        {
            return $row; // Return User Instance
        }
        else
        {
            return false; // Return false flag -> user does not exist in database
        }

        mysqli_stmt_close($stmt);
    }

    function register($conn, $userName, $password, $firstName, $lastName, $typeID, $deparment)
    {
        // Function tries to register a new user with provided credentials
        $userExists = doesUserExist($conn, $userName);
        if($userExists === false)
        {  
            $passwordHashed = password_hash($password, PASSWORD_DEFAULT); // Hash the Password
            // Register new user with hashed password
            $sql = "CALL register_new_user('$userName','$passwordHashed','$firstName','$lastName','$typeID','$deparment')";
            mysqli_query($conn, $sql);
            
            // Login new user
            $data = doesUserExist($conn,$userName);
         
            // Get required data and save to a session variable
            $userID = $data["ID"];
            $sql = "CALL get_user_type_id('$userID')";
            $data["userTypeID"] = mysqli_fetch_assoc(mysqli_query($conn, $sql))["TypeID"];
            intializeSessionVariables($data);
            goToHomePage($data["userTypeID"]);
            exit();
        }
        else
        {
            header("location: ../ui/registerUserForm.php?error=failedRegistrationUserAlreadyExists"); 
            exit();
        }
    }

    function login($conn, $name, $password)
    {
        /*
            Function tries to login user, in case it succedes, it starts a session and saves loggedin user data 
            to a session varible
        */
        $userExists = doesUserExist($conn, $name);
        if($userExists === false)
        {
            header("location:  ../index.php?error=failedLogin_UserNotFound");
            exit();
        }
        else
        {
            // Check if user password matches hashed password in database
            $data = $userExists; 
            $passwordHashed = $data["Password"];
            $isPasswordCorrect = password_verify($password, $passwordHashed); 
            if($isPasswordCorrect === false)
            {
                header("location:  ../index.php?error=failedLogin_WrongPW");
                exit();
            }
            else if($isPasswordCorrect === true)
            {
                // Get user data and save it to a session varible
                $userID = $data["ID"];
                $sql = "CALL get_user_type_id('$userID')";
                $data["userTypeID"] = mysqli_fetch_assoc(mysqli_query($conn, $sql))["TypeID"];
                // Redirect user to home page
                intializeSessionVariables($data);
                goToHomePage($data["userTypeID"]);
                exit();
            }
        }
    }

    function intializeSessionVariables($data)
    {
        // Function starts a session and saves data in a sessio varible
        session_start();
        $_SESSION["loggedInUserID"] = $data["ID"];
        $_SESSION["loggedInUserName"] = $data["UserName"];
        $_SESSION["loggedInUserTypeID"] = $data["userTypeID"];
    }

    function goToHomePage($userTypeID)
    {
        switch($userTypeID)
        {
            case 0:
                header("location:  ../ui/homeUser.php");
                break;
            
            case 1:
                header("location:  ../ui/homeAdmin.php");
                break;
        }
    }


