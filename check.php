<?php
    session_start();
    if(!(isset($_SESSION['login']) && $_SESSION['login'])){
        header('LOCATION:index.php');
        die();
    }

    //TODO permision
?>
