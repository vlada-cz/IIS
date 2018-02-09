<?php
    session_start();
    $_SESSION['menu'] = "battles";

    //defalut filter
    $_SESSION['filter']['filter_place'] = "Any";

    //default sort
    $_SESSION['sort']= " ORDER BY battle.date_happened";

    header('LOCATION:main.php');
    die();
?>
