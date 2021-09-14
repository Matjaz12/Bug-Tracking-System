<!DOCTYPE html>
    <head>
        <title>Bug Tracking System</title>
        <link rel="stylesheet" href = "styleSheet.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body>
        <header>
                <nav>
                    <div class = "logo">
                        <h4>Bug Track</h4>
                    </div>
                    <ul class = "nav_links">
                    <li><a href="homeUser.php">Domov</a></li>
                    <li><a href="profile.php">Osebni Profil</a></li>
                    <li><a href="../includes/logout.inc.php">Odjava</a></li>
                    </ul>
                </nav>
        </header>
        <div class = "center">
            <h1>Prijavi Hrošča</h1>
            <h2>Izpoljena morajo biti vsa polja razen polje Operacijski Sistem in polje Strojna Oprema.</h2>

            <form action = "../includes/procedures.inc.php" method = "POST">
             
                <label>Naziv projekta</label>
                <select id = "projectIDSelection" name="projectID" onclick='getProjectList()'></select>
                <br></br>

                <label>Prioriteta</label>
                <select name="priority">
                    <option value="High">Visoka</option>
                    <option value="Medium">Srednja</option>
                    <option value="Low">Nizka</option>
                </select>
                <br></br>
                
                <label>Opis</label>
                <textarea name="description" rows="10" cols="50"></textarea>
                <br></br>

                <label>Operacijski Sistem</label>
                <textarea name="osInfo" rows="3" cols="50"></textarea>
                <br></br>

                <label>Strojna Oprema</label>
                <textarea name="hwInfo" rows="3" cols="50"></textarea>
                <br></br>
                <h2>Res želite prijaviti Hrošča oz. Napako?</h2>
                <input type = "submit" name = "submitReportNewBugButton" value="Potrdi"></input>
            </form>
        </div>
        <script>
            var projectListReceived = false;
            function appendProjectsToSelectionList(projectList)
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

            function getProjectList()
            {
                // Function gets calle when user click the projectID selection list
                // The function makes a post request to procedures.inc.php
                // and passes along a boolean flag getUserProjectsFlag, which calls 
                // getProjectsRawData(), the received data is than displayed in projectID selection list 
                
                if(!projectListReceived)
                {
                    projectListReceived = true;
                    var getUserProjectsFlag = true;
                    $.post("http://localhost/Muc/includes/procedures.inc.php",
                    {
                        getUserProjectsFlag: getUserProjectsFlag
                    },
                    function(data, status){

                        if(status == "success")
                        {
                            var parsedData = JSON.parse(data);
                            console.log(parsedData);
                            appendProjectsToSelectionList(parsedData);
                        }
                    });
                }
            }
        </script>
    </body>
</html>
<?php
    // In case server script returns an error display it
    function displayPopUpWindow($text)
    {
        echo '<script language="javascript">';
        echo "alert('$text')";
        echo '</script>';
    }

    if(isset($_GET["error"]))
    {
        if($_GET["error"] == "emptyForm")
        {
            displayPopUpWindow("Izpoljena morajo biti vsa polja razen polje Operacijski Sistem in polje Strojna Oprema.");
        }
        if($_GET["error"] == "formSubmitted")
        {
            displayPopUpWindow("Prijava Uspešno Oddana.");
        }
    }
?>