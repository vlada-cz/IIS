<?php
    session_start();
    $_SESSION['menu'] = "boats";

    //defalut filter
    $_SESSION['filter']['filter_type'] = "Any";

    //default sort
    $_SESSION['sort']= " ORDER BY boat.name";

    header('LOCATION:main.php');
    die();
?>
