<?php
    include 'check.php';
    include 'menu.php';

    if($_SESSION['level'] == 0 ) {
        echo "<script> location.replace('pirates.php'); </script>";
        die();
    }

    setMenu($_SESSION['menu']);

?>

<div id='main'>
    <div id='content_box'>

<?php
    echo "<form id='update_form' action='{$_SERVER['PHP_SELF']}' method='post'>";

    include 'db_init.php';

    $msg = "";

    if(isset($_POST['updt'])) {
        $pirate_id = $_SESSION['user'];

        $old_passwd = $_POST['old'];
        $new1 = $_POST['new1'];
        $new2 = $_POST['new2'];

        // Query
        $sql_query = "SELECT passwd,name FROM pirate WHERE id_pirate=$pirate_id";
        $retval = mysql_query( $sql_query, $db );

        //database  error
        if (!$retval) {
            die('Could not get data: ' . mysql_error());
        }

        //If there is some row, it is mean, that user id is correct
        if ($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
          $old_db = $row["passwd"];

          //compare the input password and the password in database
          if ($old_db === $old_passwd) {
              if ($new1 === $new2) {
                    if (ctype_alnum($new1)) {
                        //this state mean that the password is correct, so we can put it into the databes.
                        $sql_query = "UPDATE pirate SET passwd = '$new1' WHERE id_pirate = $pirate_id";
                        mysql_query( $sql_query, $db );

                        $dest = "main.php?msg=The+password+has+been+SUCCESSFULLY+changed.";
                        echo "<script> location.replace('$dest'); </script>";
                        die();
                    } else {
                        $msg = 'New password does not consist of letters or digits only. Try AGAIN.';
                    }
              } else {
                  $msg = 'Reapeted passwrod doesnt match new password. Try AGAIN.';
              }
          } else {
              $msg = 'Invalid old password. Try AGAIN.';
          }
      }

    }


?>
                <section class='update_section'>
                    <h3>Change password:</h3>
                    <div class='update_field'>
                        <label for='old_passwd'>*Old password:</label>
                        <input id='old_passwd' type='password' maxlength='9' name='old' placeholder='Your old password...'>
                    </div>
                    <div class='update_field'>
                        <label for='new1_passwd'>*New password:</label>
                        <input id='new1_passwd' type='password' maxlength='9' name='new1'  placeholder='Your new password...'>
                    </div>
                    <div class='update_field'>
                        <label for='new2_passwd'>*New password again:</label>
                        <input id='new2_passwd' type='password' maxlength='9' name='new2'  placeholder='Repeat new password...'>
                    </div>
                </section>
            <div class='update_submit'>
                <input type='submit' name='updt' value='CHANGE'>
                <p id='update_error'> <?php echo $msg; ?> </p>
            </div>
        </form>
    </div>
</div>



</body>
</html>
