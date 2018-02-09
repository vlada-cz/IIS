<?php
session_start();
session_destroy();
header('LOCATION:index.php');
die(); //to redirect back to "index.php" after logging out
?>
