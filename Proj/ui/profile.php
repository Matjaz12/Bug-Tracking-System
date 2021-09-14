<?php
    include_once "../includes/procedures.inc.php";
?>

<!DOCTYPE html>
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

        <div class = "center">
                <div class="container">
                    <img src="../images/user.png" width=20%>
                    <br></br>

                    <label>ID Uporabnika</label>
                    <textarea id="userIDTextArea" rows="2" cols="50" disabled></textarea>
                    <br></br>

                    <label>Tip Uporabnika</label>
                    <textarea id="userTypeTextArea" rows="2" cols="50" disabled></textarea>
                    <br></br>

                    <label>Ime</label>
                    <textarea id="userNameTextArea" rows="2" cols="50" disabled></textarea>
                    <br></br>

                    <label>Priimek</label>
                    <textarea id="userLastNameTextArea" rows="2" cols="50" disabled></textarea>
                    <br></br>

                    <label>Oddelek</label>
                    <textarea id="userDeparmentTextArea" rows="2" cols="50" disabled></textarea>
                </div>
        </div>


        <script>
            function displayData(data)
            {
                var userIDLabel = document.getElementById("userIDTextArea");
                userIDTextArea.innerHTML += " " + data[0];

                var userTypeTextArea = document.getElementById("userTypeTextArea");
                userTypeTextArea.innerHTML += " " + data[7];

                var userNameTextArea = document.getElementById("userNameTextArea");
                userNameTextArea.innerHTML += " " + data[2];

                var userLastNameTextArea = document.getElementById("userLastNameTextArea");
                userLastNameTextArea.innerHTML += " " + data[3];

                var userDeparmentTextArea = document.getElementById("userDeparmentTextArea");
                userDeparmentTextArea.innerHTML += " " + data[8];

            }
            window.addEventListener("load", () =>{
                
                var dataArray = JSON.parse(document.getElementById("jsonRow").innerHTML)[0];
                displayData(dataArray);
            });
        </script>
    </body>
</html>
<?php
    // Call getUserDetails() using session variable loggedInUserID
    if(isset($_SESSION["loggedInUserName"]))
    {
        getUserDetails($_SESSION["loggedInUserID"]);
    }
?>