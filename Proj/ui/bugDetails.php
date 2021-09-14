
<?php
    // When user gets to this page we call getBugDetails function with provided bug id from the url
    include_once "../includes/procedures.inc.php";
    if(isset($_GET["id"]))
    {
        $bugID = $_GET["id"];
        getBugDetails($bugID);
    }
?>

<html>
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
                <div class="container">

                    <img src="../images/bug.png" width=20%>
                    <br></br>
                    <label id="bugIDLabel">ID Hrošča:</label>
                    <br></br>
                    <label id="statusLabel">Status:</label>
                    <br></br>
                    <label id="priorityLabel">Prioriteta:</label>
                    <br></br>

                    <label>Opis</label>
                    <textarea id="descriptionTextBox" rows="10" cols="50" disabled></textarea>
                    <br></br>

                    <label>Operacijski Sistem</label>
                    <textarea id="osInfoTextBox" rows="3" cols="50" disabled></textarea>
                    <br></br>

                    <label>Strojna Oprema</label>
                    <textarea id="hwInfoTextBox" rows="3" cols="50" disabled></textarea>
                    <br></br>
                    <h2 id="closeBugInfo" hidden>Želite zaključiti Hrošča oz. Napako?</h2>
                    <input id="closeBugButton" type = "submit" name = "submitReportNewBugButton" value="Potrdi" hidden></input>
                </div>
            </div>
        <script>

            var bugID;
            function confirmedSelection()
            {
                // Function promts the user for approval before closing the bug
                var input = confirm("Res želite zaključiti napako");
                var output;
                input ? output =  true : output = false;
                return output;
            }
            function refreshPage()
            {
                location.reload();
            }
            function closeBug()
            {
                // function gets called when close bug button is pressed
                var closeBugID = bugID;
       
                if(!confirmedSelection())
                {
                    // In case user decided not to close the selected bug, we simply return
                    return;
                }
                else
                {
                    // Make a post request to procedures.inc.php, and pass along closeBugID
                    $.post("http://localhost/Muc/includes/procedures.inc.php",
                    {
                        closeBugID: closeBugID
                    },
                    function(data, status){

                        if(status == "success")   
                        {
                            alert("Napaka Uspešno zaključena");
                            refreshPage();
                        }
                        else
                        {
                            alert("Napaka Ne Uspešno zaključena");
                        }
                    });
                }
            }

            function displayButton()
            {
                // Function displays the button and append an onclick method to it
                var submitButton = document.getElementById('closeBugButton');
                submitButton.style.display="block";
                submitButton.setAttribute("onclick", "closeBug()");
            }

            function displayCloseBugInfo()
            {
                // Function displays close bug instruction information
                var closeBugInfo = document.getElementById('closeBugInfo');
                closeBugInfo.style.display="block";
            }

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
                        wordEng = status;
                        break;
                }
                return wordEng;
            }

            function displayData(data)
            {
                var bugIDLabel = document.getElementById("bugIDLabel");
                bugIDLabel.innerHTML += " " + data[0];
                bugID = data[0];

                var bugStatusLabel = document.getElementById("statusLabel");
                bugStatusLabel.innerHTML += " " + translateToSl(data[2]);

                var bugPriorityLabel = document.getElementById("priorityLabel");
                bugPriorityLabel.innerHTML += " " + translateToSl(data[1]);

                var descriptionTextBox = document.getElementById("descriptionTextBox");
                descriptionTextBox.innerHTML += " " + data[3];

                var osInfoTextBox = document.getElementById("osInfoTextBox");
                osInfoTextBox.innerHTML += " " + data[4];

                var hwInfoTextBox = document.getElementById("hwInfoTextBox");
                hwInfoTextBox.innerHTML += " " + data[5];

            }

            window.addEventListener("load", () =>{

                // When page loads we grab the encode data from server 
                var dataArray = JSON.parse(document.getElementById("jsonRow").innerHTML)[0];

                // check if bug can be closed
                var bugState = dataArray[2];
                displayData(dataArray);
                if(bugState != "Closed")
                {
                    //In case bug is not closed yet, we display a close button and close bug information
                    displayButton();
                    displayCloseBugInfo()
                }
            });
        </script>
    </tbody>
</html>
