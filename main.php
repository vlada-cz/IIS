<?php
    include 'check.php';
    include 'menu.php';

    setMenu($_SESSION['menu']);

?>

<div id='main'>
    <div id='side_box'>

<?php
    include 'siteboxes.php';
    printSitebox($_SESSION['menu']);
?>

    </div>
    <div id='content_box'>

<?php
    include 'printMain.php';
    printTable($_SESSION['menu']);
?>

    </div>

</div>

<?php

if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];
    echo "  <script>
                alert('" . $msg ."');
            </script>";
}

?>

</body>
</html>
