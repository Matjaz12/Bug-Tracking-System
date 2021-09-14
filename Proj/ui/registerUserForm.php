<!DOCTYPE html>
<html>
    <head>
        <title>Register Page</title>
        <link rel="stylesheet" href = "styleSheet.css">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body class="indexPage">
        <div class="center">
        <h1>Registracija</h1>
            <form action = "../includes/register.inc.php" method = "POST">
                <div class ="txt_field">
                    <input type = "text" name = "userName"/>
                    <span></span>
                    <label>Uporabniško Ime</label>
                </div>
                <div class ="txt_field">
                    <input type = "password" name = "userPassword1">
                    <span></span>
                    <label>Geslo</label>
                </div>
                <div class ="txt_field">
                    <input type = "password" name = "userPassword2">
                    <span></span>
                    <label>Geslo</label>
                </div>
                <div class ="txt_field">
                    <input type = "text" name = "firstName">
                    <span></span>
                    <label>Ime</label>
                </div>
                <div class ="txt_field">
                    <input type = "text" name = "lastName">
                    <span></span>
                    <label>Priimek</label>
                </div>
                <div class ="txt_field">
                    <input type = "text" name = "department">
                    <span></span>
                    <label>Oddelek</label>
                </div>
                <input type = "submit" name = "submitButton" value="Potrdi"></input>
                <div class = "signup_link">
                    Že imate račun? <a href="../index.php">Prijavi se</a>
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
                header("registerUserForm.php");
            }

            if(isset($_GET["error"]))
            {
                if($_GET["error"] == "emptyRegistrationForm")
                {
                    displayPopUpWindow("Izpolnite vsa polja");
                }
                else if($_GET["error"] == "invalidUserName")
                {
                    displayPopUpWindow("Neustrezno Uporabniško Ime");
                }
                else if($_GET["error"] == "passwordsDontMatch")
                {
                    displayPopUpWindow("Gesli se ne ujemata");
                }
                else if($_GET["error"] == "invalidPassword")
                {
                    displayPopUpWindow("Geslo mora bit krajše od 25 znakov");
                }
                else if($_GET["error"] == "failedRegistrationUserAlreadyExists")
                {
                    displayPopUpWindow("Uporabnik že obstaja");
                }
            }
        ?>
    </body>
</html>

