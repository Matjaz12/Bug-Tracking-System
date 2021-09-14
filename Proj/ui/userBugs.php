<?php
    include_once "../includes/procedures.inc.php";
?>
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

        <div class="top_center">
            <h1>Filtriraj Izbiro</h1>
            <div class="select">
                <select id = "bugStatusSelectionFlag">
                    <option value="Assigned">Dodeljen</option>
                    <option value="Closed">Zaključen</option>
                </select>
            </div>
            <button id = "submitButtonID">Potrdi</button>   
        
        </div>

        <table id = "bugListTable">
            <caption><h2 id = "centerH2">Hrošči</h2></caption>
            <tr>
                <th>Naslov Projekta</th>
                <th>ID Hrošča</th>
                <th>Prioriteta</th>
                <th>Status</th>
            </tr>
        </table>
        <script>

            var previousBugStatusFlag; // global variable to keep track of current bugStatusFlag

            function translateToSl(wordEng)
            {
                // Since SQL Logic is implemented in English, we translate associated string to Slovene
                // if we dont find a match we simply leave the string as it was
                var wordSl;
                switch(wordEng)
                {
                    case "Assigned":
                        wordEng = "Dodeljen";
                        break;
                    case "Closed":
                        wordEng = "Zaključen";
                        break;
                    case "High":
                        wordEng = "Visoka";
                        break;
                    case "Medium":
                        wordEng = "Medium";
                        break;
                    case "Low":
                        wordEng = "Nizka";
                        break;
                    default:
                        wordEng = wordEng;
                        break;
                }
                return wordEng;
            }


            function appendToTable(row)
            {
                var projectTableBody = document.getElementById('bugListTable').getElementsByTagName('tbody')[0];
                var newTableRow = projectTableBody.insertRow();
                var bugID = row[1];

                // set atribute data-href to each table row using associated bugid!
                newTableRow.setAttribute("data-href", "bugDetails.php" + "?id=" + bugID);
                
                row.forEach(col =>{
                    var newTableCell = newTableRow.insertCell();
                    var newText = document.createTextNode(translateToSl(col));
                    newTableCell.appendChild(newText);
                    
                });
            }

            $(document).ready(function() {
                $("#submitButtonID").click(function(){
                    var bugStatusFlag =  $("#bugStatusSelectionFlag option:selected").val();
                    if(bugStatusFlag == previousBugStatusFlag)
                    {
                        // If user selected the same status flag simply return since data is already displayed!
                        return;
                    }

                    // In case user wants to see the bug list for a new bugStatusFlag
                    // remove the old table and make a post request to procedures.inc.php
                    // and pass bugStatusFlag which calls getBugsWithStatusFlag()
                    previousBugStatusFlag = bugStatusFlag;
                    $('#bugListTable').find("tr:gt(0)").remove();
                    $.post("http://localhost/Muc/includes/procedures.inc.php",
                    {
                        bugStatusFlag: bugStatusFlag
                    },
                    function(data, status){
                        if(status == "success")
                        {
                            if(data)
                            {
                                // In case we receive data, decode it and add an onclick method to it
                                var dataParsed = JSON.parse(data);
                                console.log(dataParsed);

                                dataParsed.forEach(appendToTable);
                                const rows = document.querySelectorAll("tr[data-href]"); 
                                rows.forEach(row  => {
                                    row.addEventListener("click", () => {
                                        window.location.href = row.dataset.href;
                                    });
                                });
                            }
                        }
                    });
                });
            });
        </script>
    </body>
</html>