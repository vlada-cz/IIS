<?php
    include 'check.php';
    include 'menu.php';

    setMenu($_SESSION['menu']);

?>

<div id='main'>
    <div id='content_box'>

<?php
    include 'printDetails.php';
    printDetail($_SESSION['menu']);
?>


    </div>
</div>

</body>
</html>
