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
                    <li><a href="homeAdmin.php">Domov</a></li>
                    <li><a href="../includes/logout.inc.php">Odjava</a></li>
                    </ul>
                </nav>
        </header>
        <div class = "center">
            <h1>Ustvari Projekt</h1>
            <h2>Izpoljena morajo biti vsa polja</h2>
            <form action = "../includes/procedures.inc.php" method = "POST">
                            
                <label>Naziv Projekta</label>
                <textarea name="projectTitle" rows="2" cols="50"></textarea>
                <br></br>

                <label>Opis Projekta</label>
                <textarea name="projectDescription" rows="10" cols="50"></textarea>
                <br></br>

                <label>Platforma</label>
                <textarea name="projectPlatform" rows="2" cols="50"></textarea>
                <br></br>
                <h2>Res želite ustvariti nov Projekt?</h2>
                <input type = "submit" name = "submitCreateProject" value="Potrdi"></input>
            </form>
        </div>
    </body>
</html>
<?php
    // In case server script reutrns an error display it
    function displayPopUpWindow($text)
    {
        echo '<script language="javascript">';
        echo "alert('$text')";
        echo '</script>';
        header("index.php");
    }

    if(isset($_GET["error"]))
    {
        if($_GET["error"] == "emptyForm")
        {
            displayPopUpWindow("Izpoljena morajo biti vsa polja.");
        }
        if($_GET["error"] == "formSubmitted")
        {
            displayPopUpWindow("Projekt Uspešno Ustvarjen");
        }
    }
?>