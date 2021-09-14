<html>
    <head>
        <title>Bug Tracking System</title>
        <link rel="stylesheet" href = "styleSheet.css">
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
        <table id = "bugListTable"> 
            <caption><h2 id = "centerH2">Hrošči izbranega Projekta</h2></caption>
        <tr>
            <thead>
                <th>Naziv Projekta</th>
                <th>ID Hrošča</th>
                <th>Prioriteta</th>
                <th>Status</th>
            </thead>    
        </tr>

        <script>
            function appendToTable(row)
            {
                var projectTableBody = document.getElementById('bugListTable').getElementsByTagName('tbody')[0];
                var newTableRow = projectTableBody.insertRow();
                var bugID = row[1];
                newTableRow.setAttribute("data-href", "bugDetails.php" + "?id=" + bugID);
                
                row.forEach(col =>{
                    var newTableCell = newTableRow.insertCell();
                    var newText = document.createTextNode(col);
                    newTableCell.appendChild(newText);
                    
                });
            }
            window.addEventListener("load", () =>{
                
                var dataArray = JSON.parse(document.getElementById("jsonRow").innerHTML);
                dataArray.forEach(appendToTable); 
                
                const rows = document.querySelectorAll("tr[data-href]"); 

                rows.forEach(row  => {
                    row.addEventListener("click", () => {
                        window.location.href = row.dataset.href;
                    });
                });
            });
        </script>

    </body>
</html>

<?php
    include_once "../includes/procedures.inc.php";
    if(isset($_GET["id"]))
    {
        $projectID = $_GET["id"];
        $userID = $_SESSION["loggedInUserID"];
        getProjectUserBugs($userID, $projectID);
    }
?>