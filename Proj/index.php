<!DOCTYPE html>
<html>
    <head>
        <title>Login Page</title>
        <link rel="stylesheet" href = "ui/styleSheet.css">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body class = "indexPage">
        <div class="center">
        <h1>Bug Track</h1>
            <form action = "includes/login.inc.php" method = "POST">
                <div class ="txt_field">
                    <input type = "text" name = "userName"/>
                    <span></span>
                    <label>Uporabniško Ime</label>
                </div>
                <div class ="txt_field">
                    <input type = "password" name = "userPassword">
                    <span></span>
                    <label>Geslo</label>
                </div>
                <input type = "submit" name = "submitButton" value="Prijava"></input>
                <div class = "signup_link">
                    Še nimate računa? <a href="ui/registerUserForm.php">Registriraj se</a>
                </div>
            </form>
        </div>
            <?php 
    
            // In case server script returns an error display it
            function displayPopUpWindow($text)
            {
                echo '<script language="javascript">';
                echo "alert('$text')";
                echo '</script>';
                header("index.php");
            }

            if(isset($_GET["error"]))
            {
                if($_GET["error"] == "emptyLoginForm")
                {
                    displayPopUpWindow("Izpolnite vsa polja");
                }
                else if($_GET["error"] == "invalidPassword")
                {
                    displayPopUpWindow("Geslo mora bit krajše od 25 znakov");
                }
                else if($_GET["error"] == "failedLogin_UserNotFound")
                {
                    displayPopUpWindow("Nepravilno Uporabniško Ime");
                }
                else if($_GET["error"] == "failedLogin_WrongPW")
                {
                    displayPopUpWindow("Nepravilno Geslo");
                }
            }
            ?>
    </body>
</html>


