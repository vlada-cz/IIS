<?php
    include 'check.php';
    include 'menu.php';

    setMenu($_SESSION['menu']);

?>

<div id='main'>
    <div id='content_box'>
        <form id='update_form'>

<?php
    include 'printForms.php';
    $msg = printForm($_SESSION['menu']);


?>
            <div class='update_submit'>
                <input type='submit' name='add' value='ADD'>
                <p id='update_error'> <?php echo $msg; ?> </p>
            </div>
        </form>
    </div>
</div>



</body>
</html>
