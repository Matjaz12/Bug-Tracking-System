<?php
    /*
        Script is resposible for calling SQL Procedures
    */
    session_start();

    // Match a flag or button with correct Function call

    if(isset($_POST["submitAssignedBugButton"]))
    {
        assignUserToBug();
    }
    else if(isset($_POST["submitAssignProjectButton"]))
    {
        assignUserToProject();
    }
    else if(isset($_POST["submitReportNewBugButton"]))
    {
        reportNewBug();
    }
    else if(isset($_POST["bugStatusFlag"]))
    {
        getBugsWithStatusFlag();
    }
    else if(isset($_POST["closeBugID"]))
    {
        closeBug();
    }
    else if(isset($_POST["getUserProjectsFlag"]))
    {
        if(isset($_SESSION["loggedInUserID"]))  
        {
            $userID = $_SESSION["loggedInUserID"];
            getProjectsRawData($userID);
        }
    }
    else if(isset($_POST["getAllProjectsFlag"]))
    {
        getAllProjects();
    }
    else if(isset($_POST["getAllUsersFlag"]))
    {
        getAllUsers();
    }
    else if(isset($_POST["selectedUserID"]))
    {
        $userID = $_POST["selectedUserID"];
        getProjectsRawData($userID);
    }
    else if(isset($_POST["selectedProjectID"]))
    {
        getBugsInProject();
    }
    else if(isset($_POST["submitCreateProject"]))
    {
        createProject();
    }

    function isCreateProjectFormEmpty($projectTitle, $projectDescription, $projectPlatform)
    {
        $flag = empty($projectTitle) || empty($projectDescription) || empty($projectPlatform);
        return $flag;
    }

    function createProject()
    {
        // Function tries to call an sql procedure create_project,
        // in case it faills it redirects the user back to createProject.php with an error message

        include_once 'dbh.inc.php';

        $projectTitle = $_POST['projectTitle'];
        $projectDescription = $_POST['projectDescription'];
        $projectDescription = replaceSingleQuotes($projectDescription);
        $projectPlatform = $_POST['projectPlatform'];

        if(isCreateProjectFormEmpty($projectTitle, $projectDescription, $projectPlatform))
        {
            header("location: ../ui/createProject.php?error=emptyForm");
        }
        else
        {
            $sql = "CALL create_project('$projectTitle','$projectDescription','$projectPlatform');";
            mysqli_query($conn, $sql);
            header("location: ../ui/createProject.php?error=formSubmitted");
        }
        exit();
    }

    function getBugsInProject()
    {
        // Function calls procedure get_project_bugs using
        // provided projectID and bugStatus flag
        include_once 'dbh.inc.php';
        $projectID = $_POST["selectedProjectID"];
        $bugStatus = "Not Assigned";

        $sql = "CALL get_project_bugs('$projectID','$bugStatus')";
        $result = mysqli_query($conn, $sql);

        if(mysqli_num_rows($result) > 0)
        {
            // if procedure returnes data, we encode it and pass 
            // it back to the client
            $allRows = json_encode($result->fetch_all());
            echo $allRows;
        }
        exit();
    }

    function getBugsWithStatusFlag()
    {
        // Function calls get_user_bugs_with_status_flag  procedure and returns bugs 
        // associated with provided bug flag

        include_once 'dbh.inc.php';
        if(isset($_SESSION["loggedInUserID"]))
        {
            $userID = $_SESSION["loggedInUserID"];
        }
        $bugStatus = $_POST["bugStatusFlag"];
        if(empty($userID) || empty($bugStatus))
        {
            header("location: ../ui/homeUser.php?error=empty");
            exit();
        }
        else
        {
            $sql = "CALL get_user_bugs_with_status_flag('$userID','$bugStatus')";
            $result = mysqli_query($conn, $sql);
    
            if(mysqli_num_rows($result) > 0)
            {
                // Return encoded data
                $allRows = json_encode($result->fetch_all());
                echo $allRows;
            }
            exit();
        }
    }

    function getUserDetails($userID)
    {
        // Function calls getUserDetails procedure and returns 
        // encoded data in html form

        include_once 'dbh.inc.php';

        if(empty($userID))
        {
            header("location: ../index.php?error=userIDEmpty");
            exit();
        }
        else
        {
            $sql = "CALL get_user_details('$userID')";
            $result = mysqli_query($conn, $sql);
            
            if(mysqli_num_rows($result) > 0)
            {
                $allRows = json_encode($result->fetch_all());
                echo  "<div id='jsonRow' style='display:none;'>" . $allRows . "</div>";
            }
            exit();
        }
    }

    function isCreateBugFormEmpty($priority, $description)
    {
        $flag = empty($priority) || empty($description);
        return $flag;
    }

    function replaceSingleQuotes($description)
    {
        $newDescription= str_replace("'", '"', $description);
        return $newDescription;
    }

    function reportNewBug()
    {
        // Function tries to call create_new_bug procedure with provided user data

        include_once 'dbh.inc.php';
        $priority = $_POST['priority'];
        $description = $_POST['description'];
        $description = replaceSingleQuotes($description);
        $osInfo = $_POST['osInfo'];
        $hwInfo = $_POST['hwInfo'];
        if(empty($hwInfo) || empty($osInfo))
        {
            $hwInfo = "/";
            $osInfo = "/";
        }
        $projectID = $_POST["projectID"];
        if(isCreateBugFormEmpty($priority, $description) || !isset($projectID))
        {
            header("location: ../ui/createBugForm.php?error=emptyForm");
        }
        else
        {
            $sql = "CALL create_new_bug('$priority','$description','$osInfo','$hwInfo','$projectID')";
            mysqli_query($conn, $sql);
            header("location: ../ui/createBugForm.php?error=formSubmitted");
        }
        exit();
    }

    function isAssignFormEmpty($userID, $bugID, $adminID)
    {
        return empty($userID) || empty($bugID) || empty($adminID);
    }

    function assignUserToBug()
    {
        // Function tries to assign a bug to a provided user,
        // the logic for error checking is implemented in assign_bug_to_user SQL procedure 

        include_once 'dbh.inc.php';
        
        $userID = $_POST['userID'];
        $bugID = $_POST['bugID'];

        if(isset($_SESSION['loggedInUserID']))
        {
            $adminID = $_SESSION['loggedInUserID'];
        }
 
        if(isAssignFormEmpty($userID, $bugID, $adminID))
        {
            header("location: ../ui/homeAdmin.php?error=emptyForm");
            exit();
        }
        else
        {
            $sql = "CALL assign_bug_to_user('$userID','$bugID','$adminID')";
            mysqli_query($conn, $sql);
            header("location: ../ui/homeAdmin.php");
            exit();
        }
    }

    function getProjects($userID)
    {
        // Function calls get_user_projects() procedure and returns encoded data in html form
        include_once 'dbh.inc.php';

        if(empty($userID))
        {
            header("location: ../ui/homeUser.php");
            exit();
        }
        else
        {
            $sql = "CALL get_user_projects('$userID')";
            $result = mysqli_query($conn, $sql);
            if(mysqli_num_rows($result) > 0)
            {
                $allRows = json_encode($result->fetch_all());
                echo  "<div id='jsonRow' style='display:none;'>" . $allRows . "</div>";
            }
            exit();
        }
    }

    function getProjectsRawData($userID)
    {
        include_once 'dbh.inc.php';

        if(empty($userID))
        {
            header("location: ../ui/homeUser.php");
            exit();
        }
        else
        {
            $sql = "CALL get_user_projects('$userID')";
            $result = mysqli_query($conn, $sql);
            if(mysqli_num_rows($result) > 0)
            {
                $allRows = json_encode($result->fetch_all());
                echo $allRows;
            }
            exit();
        }
    }

    function getAllProjects()
    {
        // Function returns all projects in database by selecting data from SQL view: view_all_project
        include_once 'dbh.inc.php';

        $sql = "SELECT * FROM view_all_project";
        $result = mysqli_query($conn, $sql);
        if(mysqli_num_rows($result) > 0)
        {
            $allRows = json_encode($result->fetch_all());
            echo $allRows;
        }
        exit();
    }

    function isAssignProjectFormEmpty($userID, $projectID, $adminID)
    {
        return empty($userID) || empty($projectID) || empty($adminID);
    }

    function assignUserToProject()
    {
        // Function tries to assign user to provided project by the admin
        include_once 'dbh.inc.php';
    
        $userID = $_POST['userID'];
        $projectID = $_POST['projectID'];
        echo "<h1>" . $userID . "</h1>";
        echo "<h1>" . $projectID . "</h1>";

        if(isset($_SESSION['loggedInUserID']))
        {
            $adminID = $_SESSION['loggedInUserID'];
        }

        if(isAssignProjectFormEmpty($userID, $projectID, $adminID))
        {
            header("location: ../ui/assignProjectForm.php?error=emptyForm");
            exit();
        }
        else
        {
            // in case query succedes it redirects the user back to homeAdmin.php
            $sql = "CALL assign_user_to_project('$userID','$projectID','$adminID')";
            mysqli_query($conn, $sql);
            header("location: ../ui/homeAdmin.php");
            exit();
        }
    }

    function getProjectUserBugs($userID, $projectID)
    {
        // Function gets all bugs associated with userID and projectID

        include_once 'dbh.inc.php';
        if(empty($projectID) || empty($userID))
        {
            header("location: ../ui/projects.php");
            exit();
        }
        else
        {
            $sql = "CALL get_project_user_bugs('$projectID','$userID')";
            $result = mysqli_query($conn, $sql);

            
            if(mysqli_num_rows($result) > 0)
            {

                        
                $allRows = json_encode($result->fetch_all());
                echo  "<div id='jsonRow' style='display:none;'>" . $allRows . "</div>";
            }
            exit();
        }
    }

    function getBugDetails($bugID)
    {
        // Function gets bug details by calling SQL procedure get_bug_details
        include_once 'dbh.inc.php';

        if(empty($bugID))
        {
            header("location: ../ui/homeUser.php");
            exit();
        }
        else
        {
            $sql = "CALL get_bug_details('$bugID')";
            $result = mysqli_query($conn, $sql);
            if(mysqli_num_rows($result) > 0)
            {
                $allRows = json_encode($result->fetch_all());
                echo  "<div id='jsonRow' style='display:none;'>" . $allRows . "</div>";
            }
        }
    }

    function closeBug()
    {
        // Function tries to close the selected bug
        include_once 'dbh.inc.php';

        $bugID = $_POST["closeBugID"];
        if(!isset($_SESSION['loggedInUserID']))
            return;
    
        $userID = $_SESSION['loggedInUserID'];
        
        if(empty($bugID) || empty($userID))
        {
            header("location: ../ui/homeUser.php");
            exit();
        }
        else
        {
            $sql = "CALL close_bug('$userID','$bugID')";
            $result = mysqli_query($conn, $sql);
            if(mysqli_num_rows($result) > 0)
            {
                $allRows = json_encode($result->fetch_all());
                echo  "<div id='jsonRow' style='display:none;'>" . $allRows . "</div>";
            }
        }
    }

    function getAllUsers()
    {
        // Helper function that gets all users in the database
        include_once 'dbh.inc.php';

        $sql = "SELECT * FROM view_all_user";
        $result = mysqli_query($conn, $sql);
        if(mysqli_num_rows($result) > 0)
        {
            $allRows = json_encode($result->fetch_all());
            echo $allRows;
        }
    }
