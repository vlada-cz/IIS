<?php
    session_start();
    $_SESSION['menu'] = "fleets";

    //default sort
    $_SESSION['sort']= " ORDER BY fleet.name";

    header('LOCATION:main.php');
    die();
?>
