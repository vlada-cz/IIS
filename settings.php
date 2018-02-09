<?php
session_start();
$_SESSION['menu'] = "settings";

header('LOCATION:setPasswd.php');
die();
?>
