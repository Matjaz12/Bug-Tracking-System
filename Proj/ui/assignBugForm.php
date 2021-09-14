<!DOCTYPE html>
    <head>
        <title>Bug Tracking System</title>
        <link rel="stylesheet" href = "styleSheet.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body>  
        <header>
            <nav>
                <div class = "logo">
                    <h4>Bug Track</h4>
                </div>
                <ul class = "nav_links">
                    <li><a href="homeAdmin.php">Domov</a></li>
                    <li><a href="../includes/logout.inc.php">Odjava</a></li>
                </ul>
            </nav>
        </header>
        
        <div class = "center">
            <h1>Dodeli Hrošča</h1>
            <form action = "../includes/procedures.inc.php" method = "POST">

                <label >Ime Uporabnika</label>
                <select id = "userIDSelection" name="userID" onclick='getUserList()' onchange="onUserSelectionChange()"></select>
                <br></br>

                <label >Naziv Projekta</label>
                <select id = "projectIDSelection" name="projectID" onclick='getSelectedUserProjectList()' onchange="onProjectSelectionChange()"></select>
                <br></br>
                
                <label>ID Hrošča</label>
                <select id = "bugIDSelection" name="bugID" onclick='getBugList()'></select>
                <br></br>

                <input type = "submit" name = "submitAssignedBugButton" value="Potrdi"></input>
            </form>
        </div>
        <script>

            var userListReceived = false;
            var projectListReceived = false;
            var bugListReceived = false;

            // Helper Functions

            function clearSelectionList(selectionId)
            {
                // Function clears the selection list when new project or bug is selected
                var select = document.getElementById(selectionId);
                var length = select.options.length;
                for (i = length-1; i >= 0; i--)
                {
                    select.options[i] = null;
                }
            }

            // On selection changed
            function onUserSelectionChange()
            {
                projectListReceived = false;
                bugListReceived = false;

                clearSelectionList("projectIDSelection");
                clearSelectionList("bugIDSelection");
            }

            function onProjectSelectionChange()
            {
                bugListReceived = false;
                clearSelectionList("bugIDSelection");
            }

            function appendToUserList(userList)
            {
                // Function appends users to the user list
                var userSelection = document.getElementById("userIDSelection");
                userList.forEach(user =>
                {
                    var userID = user[0];
                    var firstName = user[1];
                    
                    var option = document.createElement("option");
                    option.value = userID;
                    option.text = firstName;
                    userSelection.add(option);
                });
            }

            function getUserList()
            {
                // Function checks if userList was not yet received and 
                // makes a post request to script procedures.inc.php
                // It passes a boolean flag to trigger getAllUsers() function

                if(!userListReceived)
                {
                    userListReceived = true;
                    var getAllUsersFlag = true;
                    $.post("http://localhost/Muc/includes/procedures.inc.php",
                    {
                        getAllUsersFlag: getAllUsersFlag
                    },
                    function(data, status){

                        if(status == "success")
                        {
                            var parsedData = JSON.parse(data);
                            console.log(parsedData);
                            appendToUserList(parsedData);
                        }
                    });
                }
            }

            function appendToProjectList(projectList)
            {
                // Function appends projects to the project list

                var projectSelection = document.getElementById("projectIDSelection");
                projectList.forEach(project =>
                {
                    var projectID = project[0];
                    var projectTitle = project[1];
                    
                    var option = document.createElement("option");
                    option.value = projectID;
                    option.text = projectTitle;
                    projectSelection.add(option);
                });
            }
            
            function getSelectedUserProjectList()
            {
                // Function checks if projectListReceived was not yet received and 
                // makes a post request to script procedures.inc.php
                // It passes along selectedUserID, the php script than calls getProjectsRawData()
                // which returns all user projects, the projects are than displayed

                if(!userListReceived)
                {
                    alert("Select User First! So i can filter out Projects");
                    return;
                }
                if(!projectListReceived)
                {
                    projectListReceived = true;
                    var selectedUserID = document.getElementById("userIDSelection").value;
                    $.post("http://localhost/Muc/includes/procedures.inc.php",
                    {
                        selectedUserID: selectedUserID
                    },
                    function(data, status){

                        if(status == "success")
                        {
                            // if we got back some data, display it
                            var parsedData = JSON.parse(data);
                            console.log(parsedData);
                            appendToProjectList(parsedData);
                        }
                    });
                }
            } 

            function appendToBugList(bugList)
            {
                var bugSelection = document.getElementById("bugIDSelection");
                bugList.forEach(bug =>
                {
                    var bugID = bug[0];
                    var option = document.createElement("option");
                    option.value = bugID;
                    option.text = bugID;
                    bugSelection.add(option);
                });
            }

            function getBugList()
            {
                // Function checks if bugListReceived was not yet received and 
                // makes a post request to script procedures.inc.php
                // It passes along selectedProjectID, the php script than calls getBugsInProject()
                // which returns all Not Yet Assigned bugs.

                if(!userListReceived || !projectListReceived)
                {
                    alert("Select User and Project First! So i can filter out Not Yet Assigned Bugs");
                    return;
                }
                if(!bugListReceived)
                {
                    bugListReceived = true;
                    var selectedProjectID = document.getElementById("projectIDSelection").value
                    $.post("http://localhost/Muc/includes/procedures.inc.php",
                    {
                        selectedProjectID: selectedProjectID
                    },
                    function(data, status){

                        if(status == "success")
                        {
                            // if we got back some data, display it
                            var parsedData = JSON.parse(data);
                            console.log(parsedData);
                            appendToBugList(parsedData);
                        }
                    });
                }
            }
        </script>
    </body>
</html>