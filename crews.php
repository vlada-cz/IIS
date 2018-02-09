<?php
    session_start();
    $_SESSION['menu'] = "crews";

    //default value of sorting
    $_SESSION['sort'] = " ORDER BY crew.name";

    header('LOCATION:main.php');
    die();
?>
