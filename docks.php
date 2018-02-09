<?php
    session_start();
    $_SESSION['menu'] = "ports";

    //default sort
    $_SESSION['sort']= " ORDER BY port.name";

    header('LOCATION:main.php');
    die();
?>
