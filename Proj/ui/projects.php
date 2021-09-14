<?php
    include_once "../includes/procedures.inc.php";
?>

<!DOCTYPE html>
    <head>
        <title>Your Projects</title>
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
                    <li><a href="homeUser.php">Domov</a></li>
                    <li><a href="profile.php">Osebni Profil</a></li>
                    <li><a href="../includes/logout.inc.php">Odjava</a></li>
                </ul>
            </nav>
        </header>
        
        <table id = "projectListTable"> 
            <caption><h2 id = "centerH2">Osebni Projekti</h2></caption>
        <tr>
            <thead>
                <th>ID Projekta</th>
                <th>Naslov Projekta</th>
                <th>Operacijski Sistem</th>
            </thead>    
        </tr>
        </table>
        <script>
            function appendToTable(row)
            {
                //Function appends data to the projectListTable and sets unqiue link for each row in the table
                
                var projectTableBody = document.getElementById('projectListTable').getElementsByTagName('tbody')[0];
                var newTableRow = projectTableBody.insertRow();
                var projectID = row[0];
                newTableRow.setAttribute("data-href", "projectUserBugs.php" + "?id=" + projectID);
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
        // Call get projects function and pass session varible loggedInUserID
        if(isset($_SESSION["loggedInUserID"]))
        {
            getProjects($_SESSION["loggedInUserID"]);
        }
?>
