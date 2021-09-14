<?php

    // Script Destroys Saved Session Variables on logout
    session_start();
    session_unset();
    session_destroy();

    header("location: ../index.php");

