<?php

session_start();
$username = $password = $someERR = '';

include_once 'db_init.php';

if(isset($_POST['login_btn'])) {
  $pirate_id = $_POST['pirate_id'];
  $password = $_POST['pirate_pswd'];

  //pirate_id has to be number
  if(is_numeric($pirate_id)){
      //if user is admin <= admin_id = 0
      if($pirate_id == 0) {
          if($password === 'admin') {
              $_SESSION['login'] = true;
              $_SESSION['user'] = 0;
              $_SESSION['name'] = "Admin";
          } else { //wrong password of admin
              $someERR = 'Invalid password';
          }
      } else { //IT IS NOT ADMIN => FIND in DATABASE

          // Query
          $sql_query = "SELECT passwd,name FROM pirate WHERE id_pirate=$pirate_id";
          $retval = mysql_query( $sql_query, $db );
          
          //database  error
          if (!$retval) {
              die('Could not get data: ' . mysql_error());
          }

          //If there is some row, it is mean, that user id is correct
          if ($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
            $pirate_passwd = $row["passwd"];

            //compare the input password and the password in database
            if ($pirate_passwd === $password) {
                $_SESSION['login'] = true;
                $_SESSION['user'] = $pirate_id;
                $_SESSION['name'] = $row['name'];
            } else {
                $someERR = 'Invalid password';
            }
          }
          else {
          	$someERR = 'Invalid pirate id';
          }
      }
  } else {
      $someERR = 'Invalid pirate ID';
  }
}

if(isset($_SESSION['login']) && $_SESSION['login'])
  $pass = true;
else
  $pass = false;


if(!$pass)
{

    echo   "<!DOCTYPE html>
            <html>
            <head>
            	<title>PiratIS</title>
            	<meta charset='utf-8'>
            	<link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
            	<link rel='stylesheet' type='text/css' href='styles/login.css'>
            	<link rel='shortcut icon' type='image/x-icon' href='favicon.ico' />
            </head>
            </head>
            <body>
            	<div id='content'>
            		<div id='login_box'>
            			<img src='img/logo.png'>
            			<h2>Log in</h2>
            			<form id='login_form' action='{$_SERVER['PHP_SELF']}' method='post'>
                            <input class='form_line' type='text' name='pirate_id' placeholder='Your pirate id...'>
                            <input class='form_line' type='password' name='pirate_pswd' placeholder='Your password...'>
                            <div id='error_field'>$someERR</div>
                            <input type='submit' name='login_btn' value='Log in'>
                        </form>
            		</div>
            	</div>
            </body>
            </html>";
}
else
{
    //set privilages
    $_SESSION['level'] = getMyPrivilages($db);

    header('LOCATION:pirates.php');
    die();
}
?>
