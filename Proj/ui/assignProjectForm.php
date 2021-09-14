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
            <h1>Dodeli Projekt</h1>
            <form action = "../includes/procedures.inc.php" method = "POST">        
            <label >Ime Uporabnika</label>
            <select id = "userIDSelection" name="userID" onclick='getUserList()'></select>
            <br></br>
            <label >Naziv Projekta</label>
            <select id = "projectIDSelection" name="projectID" onclick='getProjectList()'></select>
            <br></br>
            <input type = "submit" name = "submitAssignProjectButton" value="Potrdi"></input>
            </form>
        </div>
        <script>
            var projectListReceived = false;
            var userListReceived = false;

            function appendToProjectList(projectList)
            {
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

            function appendToUserList(userList)
            {
                var userSelection = document.getElementById("userIDSelection");
                userList.forEach(project =>
                {
                    var userID = project[0];
                    var firstName = project[1];
                    
                    var option = document.createElement("option");
                    option.value = userID;
                    option.text = firstName;
                    userSelection.add(option);
                });
            }

            function getUserList()
            {
                if(!userListReceived)
                {
                    userListReceived = true;
                    var getAllUsersFlag = true;
                    $.post("../includes/procedures.inc.php",
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

            function getProjectList()
            {
                if(!projectListReceived)
                {
                    projectListReceived = true;
                    var getAllProjectsFlag = true;
                    $.post("../includes/procedures.inc.php",
                    {
                        getAllProjectsFlag: getAllProjectsFlag
                    },
                    function(data, status){

                        if(status == "success")
                        {
                            var parsedData = JSON.parse(data);
                            console.log(parsedData);
                            appendToProjectList(parsedData);
                        }
                    });
                }
            } 
        
        </script>
    </body>
</html>