<?php
    session_start();
    $_SESSION['menu'] = "pirates";

    //set default filter
    $_SESSION['filter']['filter_rank'] = "Any";
    $_SESSION['filter']['filter_beard'] = "Any";

    //set defaoult filter
    $_SESSION['sort'] = " ORDER BY pirate.name";

    header('LOCATION:main.php');
    die();
?>
