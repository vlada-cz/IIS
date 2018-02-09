<?php
// Get access to database
$servername = "localhost:/var/run/mysql/mysql.sock";
$username = "xjerab21";
$password = "oruvuru5";
$dbname = "xjerab21";

$db = mysql_connect($servername, $username, $password);
if (!$db) die('Could not connect: '.mysql_error());
if (!mysql_select_db($dbname, $db)) die('Database is not available: '.mysql_error());


function freeBoat($id_boat, $capacity, $db)
{
    // Query
    $sql_query = "SELECT COUNT(*) AS NUM FROM pirate WHERE id_boat=$id_boat";
    $retval = mysql_query( $sql_query, $db );

    //database  error
    if (!$retval) {
        die('Could not get data: ' . mysql_error());
    }

    //Gett
    if ($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
        $occupied = $row["NUM"];
    } else {
        die("DATABASE ERROR: Something wrong in function freeBoat: ".$id_boat);
    }

    return(($capacity-$occupied));
}

function freePort($id_port, $capacity, $db)
{
    // Query
    $sql_query = "SELECT COUNT(*) AS NUM FROM boat WHERE id_port=$id_port";
    $retval = mysql_query( $sql_query, $db );

    //database  error
    if (!$retval) {
        die('Could not get data: ' . mysql_error());
    }

    //Gett
    if ($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
        $occupied = $row["NUM"];
    } else {
        die("DATABASE ERROR: Something wrong in function freeBoat: ".$id_boat);
    }

    return(($capacity-$occupied));

}

function getAge($birth_date)
{
    $date_of_birth = date_create($birth_date);
    $date_today = date_create("now");
    $years = date_diff($date_of_birth, $date_today);
    $years = $years->format('%y');

    return($years);
}

function getRank($id_pirate, $db)
{
    // Query
    $sql_query = "SELECT degree AS DEG FROM captain WHERE id_pirate=$id_pirate";
    $retval = mysql_query( $sql_query, $db );

    //database  error
    if (!$retval) {
        die('Could not get data: ' . mysql_error());
    }

    //Gett
    if ($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
        $rank = $row['DEG'];
    } else {

        // Query about common pirate
        $sql_query = "SELECT position AS POS FROM common_pirate WHERE id_pirate=$id_pirate";
        $retval = mysql_query( $sql_query, $db );

        //database  error
        if (!$retval) {
            die('Could not get data: ' . mysql_error());
        }

        //Gett
        if ($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
            $rank = $row['POS'];
        } else {
            echo "<script> location.replace('main.php'); </script>";
            die();
        }
    }

    return($rank);
}


function getNumber($query, $db)
{
    //query
    $retval = mysql_query( $query, $db );

    //database  error
    if (!$retval) {
        die('Could not get data: ' . mysql_error());
    }

    //Gett
    if ($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
        $number= $row["NUM"];
    } else {
        die("DATABASE ERROR: Something wrong in function freeBoat: ");
    }

    return($number);
}

//$sql_query = "SELECT name AS NAME FROM port WHERE id_port=$id_port";
//$row['PORT'] = getName($sql_query, $db);
function getName($query, $db)
{
    //query
    $retval = mysql_query( $query, $db );

    //database  error
    if (!$retval) {
        die('Could not get data: ' . mysql_error());
    }

    //Gett
    if ($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
        $name= $row["NAME"];
    } else {
        die("DATABASE ERROR: Something wrong in function freeBoat: ");
    }

    return($name);
}


//FUNCTION FOR GETTING PRIVILAGES
//function for my privilages
function getMyPrivilages($db)
{
    if ($_SESSION['user'] == 0) { //I am admin
        return 0;
    } else {    //I am pirates
        $my_id = $_SESSION['user'];
        $my_rank = getRank($my_id, $db);
        $my_level = rankToPriv($my_rank);

        return $my_level;
    }
}

function getPirateLevel($id_pirate, $db)
{
    //I am pirates
    $rank = getRank($id_pirate, $db);
    $level = rankToPriv($rank);

    return $level;
}

//function for mapping rank to privilages
function rankToPriv($rank)
{
    $level = 5;

    switch ($rank) {
        case "Crew Captain":
            $level = 1;
            break;

        case "Fleet Captain":
            $level = 2;
            break;

        case "Boat Captain":
            $level = 3;
            break;

        default:
            $level = 4;
            break;
    }

    return $level;
}

function getPiratePrivilages($id_pirate, $db)
{
    if($_SESSION['level'] == 4){
        return 4;
    }

    if ($_SESSION['level'] == 0) {
        return getPirateLevel($id_pirate, $db);

    } else {
        $me = getSuperID($_SESSION['user'], $db);
        $him = getSuperID($id_pirate ,$db);

        if ($me['CREW_ID'] != $him['CREW_ID']) {
            return 1;
        }

        if ($me['FLEET_ID'] != $him['FLEET_ID']) {
            return 2;
        }

        if ($me['BOAT_ID'] != $him['BOAT_ID']) {
            return 3;
        }

        if ($me['BOAT_ID'] != $him['BOAT_ID']) {
            return 3;
        }

        return getPirateLevel($id_pirate, $db);
    }
}

function getSuperID($id_pirate, $db)
{
    $sql_query = "  SELECT  pirate_in_crew.id_crew AS CREW_ID,
                            pirate.id_boat AS BOAT_ID,
                            boat.id_fleet AS FLEET_ID
                    FROM (pirate_in_crew
                    INNER JOIN pirate ON pirate_in_crew.id_pirate = pirate.id_pirate)
                    LEFT JOIN boat ON pirate.id_boat = boat.id_boat
                    WHERE pirate.id_pirate = $id_pirate";

    $retval = mysql_query( $sql_query, $db );

    //database  error
    if (!$retval) {
        die('Could not get data: ' . mysql_error());
    }

    //check if thehre is some record about pirate
    if($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
        return $row;
    } else {
        echo "<script> location.replace('main.php'); </script>";
        die();
    }
}

function canIeditBoat($id_boat, $db)
{
    //check privileges -> Am I admin or normal pirate? -> than nooo you can not edit boat
    if($_SESSION['level'] < 1 || $_SESSION['level'] == 4) {
        return False;
    }

    if ($_SESSION['level'] == 1) {
        $sql_query = "  SELECT crew.id_captain AS ID FROM boat,crew
                        WHERE crew.id_crew = boat.id_crew
                        AND id_boat= $id_boat";
        $retval = mysql_query( $sql_query, $db );
        if (!$retval) {
            die('Could not get data: ' . mysql_error());
        }
        if (($row = mysql_fetch_array($retval, MYSQL_ASSOC)) && ($row['ID'] == $_SESSION['user'])) {
            return True;
        } else {
            return False;
        }
    }

    if ($_SESSION['level'] == 2) {
        $sql_query = "  SELECT fleet.id_captain AS ID FROM boat, fleet
                        WHERE boat.id_fleet = fleet.id_fleet
                        AND id_boat= $id_boat";
        $retval = mysql_query( $sql_query, $db );
        if (!$retval) {
            die('Could not get data: ' . mysql_error());
        }
        if (($row = mysql_fetch_array($retval, MYSQL_ASSOC)) && ($row['ID'] == $_SESSION['user'])) {
            return True;
        } else {
            return False;
        }
    }

    if ($_SESSION['level'] == 2) {
        $sql_query = "SELECT id_captain AS ID FROM boat WHERE id_boat= $id_boat";
        $retval = mysql_query( $sql_query, $db );
        if (!$retval) {
            die('Could not get data: ' . mysql_error());
        }
        if (($row = mysql_fetch_array($retval, MYSQL_ASSOC)) && ($row['ID'] == $_SESSION['user'])) {
            return True;
        } else {
            return False;
        }
    }

    return False;

}
?>
