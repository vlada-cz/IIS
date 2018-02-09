<?php

function printTable($what) {
    switch ($what) {
        case "pirates":
            printPirates();
            break;

        case "crews":
            printCrews();
            break;

        case "boats":
            printBoats();
            break;

        case "ports":
            printPorts();
            break;

        case "battles":
            printBattles();
            break;

        case "fleets":
            printFleets();
            break;

        case "settings":
            echo "<script> location.replace('pirates.php'); </script>";
            die();
            break;
    }
}

//function For print pirates
function printPirates()
{
    include 'db_init.php';

    //If I have privilages I can add pirate
    if ($_SESSION['level'] == 1) {
        echo "  <div id='btn_menu'>
                    <a class='btn_link' href='add.php'>
                        <button class='update_btn'>Add pirate</button>
                    </a>
                </div>";
    }


    echo "  <table id='main_table'>
                <tr>
                    <th>Name</th>
                    <th>Rank</th>
                    <th>Nick</th>
                    <th>Age</th>
                    <th>Crew</th>
                    <th>Boat</th>
                </tr>";

    if ($_SESSION['filter']['filter_beard'] === "Any") {
        $filter = "";
    } else {
        $filter = " AND pirate.beard_color=\"{$_SESSION['filter']['filter_beard']}\"";
    }


    // Query
    $sql_query = " SELECT  pirate.id_pirate AS ID,
                           pirate.name AS NAME,
                           pirate.nick AS NICK,
                           pirate.date_of_birth AS AGE,
                           crew.name AS CREW,
                           pirate.id_boat AS BOAT
                   FROM crew, pirate, pirate_in_crew
                   WHERE pirate_in_crew.id_pirate = pirate.id_pirate
                   AND pirate_in_crew.id_crew = crew.id_crew" . $filter . $_SESSION['sort'];


    $retval = mysql_query( $sql_query, $db );

     //database  error
     if (!$retval) {
         die('Could not get data: ' . mysql_error());
     }

     //Print every record
     while($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
        $id = $row['ID'];
        $name = $row['NAME'];
        $nick = $row['NICK'];
        $age = $row['AGE'];
        $crew = $row['CREW'];


        $age = getAge($row['AGE']);
        $rank = getRank($id, $db);

        //filter by rank
        if($_SESSION['filter']['filter_rank'] !== "Any"){
            if ($_SESSION['filter']['filter_rank'] !== $rank) {
                continue;
            }
        }

        //get info about port
        if ($row['BOAT'] == null) {
            $row['BOAT'] = "<i>ON SHORE</i>";
        } else {
            $id_boat = $row['BOAT'];
            $sql_query = "SELECT name AS NAME FROM boat WHERE id_boat=$id_boat";
            $row['BOAT'] = getName($sql_query, $db);
        }

        $boat = $row['BOAT'];

        echo "  <tr>
                    <td> <a class='table_link' href='details.php?id_pirate=$id'>$name</a> </td>
                    <td> <a class='table_link' href='details.php?id_pirate=$id'>$rank</a> </td>
                    <td> <a class='table_link' href='details.php?id_pirate=$id'>$nick</a> </td>
                    <td> <a class='table_link' href='details.php?id_pirate=$id'>$age</a> </td>
                    <td> <a class='table_link' href='details.php?id_pirate=$id'>$crew</a> </td>
                    <td> <a class='table_link' href='details.php?id_pirate=$id'>$boat</a> </td>
                </tr>";
     }

    //close table
    echo "</table>";
}

//function For print crews
function printCrews()
{
    include 'db_init.php';

    //Only admin can add crew
    if ($_SESSION['level'] == 0) {
        echo "  <div id='btn_menu'>
                    <a class='btn_link' href='add.php'>
                        <button class='update_btn'>Add crew</button>
                    </a>
                </div>";
    }

    echo "  <table id='main_table'>
                <tr>
                    <th>Name</th>
                    <th>Number of Pirates</th>
                    <th>Port</th>
                    <th>Captain</th>
                    <th>Battles Won</th>
                </tr>";


     // Query
     $sql_query = " SELECT  crew.id_crew AS ID,
                            crew.name AS CREW,
                            port.name AS PORT,
                            pirate.name AS CAPTAIN
                    FROM (  crew
                    INNER JOIN port ON crew.id_port = port.id_port)
                    INNER JOIN pirate ON crew.id_captain = pirate.id_pirate" . $_SESSION['sort'];

     $retval = mysql_query( $sql_query, $db );

     //database  error
     if (!$retval) {
         die('Could not get data: ' . mysql_error());
     }

     //Print every records
     while($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
        $id = $row['ID'];
        $crew = $row['CREW'];
        $port = $row['PORT'];
        $cap = $row['CAPTAIN'];

        $sql_query = "SELECT COUNT(*) AS NUM FROM pirate_in_crew WHERE id_crew=$id";
        $num = getNumber($sql_query, $db);

        $sql_query = "SELECT COUNT(*) AS NUM FROM battle WHERE who_won=$id";
        $Wbattles = getNumber($sql_query, $db);

        echo "  <tr>
                    <td> <a class='table_link' href='details.php?id_crew=$id'>$crew</a> </td>
                    <td> <a class='table_link' href='details.php?id_crew=$id'>$num</a> </td>
                    <td> <a class='table_link' href='details.php?id_crew=$id'>$port</a> </td>
                    <td> <a class='table_link' href='details.php?id_crew=$id'>$cap</a> </td>
                    <td> <a class='table_link' href='details.php?id_crew=$id'>$Wbattles</a> </td>
                </tr>";
     }

    //close table
    echo "</table>";
}

//function for print table about boats
function printBoats()
{
    include 'db_init.php';

    //If I have privilages I can add pirate
    if ($_SESSION['level'] == 1) {
        echo "  <div id='btn_menu'>
                    <a class='btn_link' href='add.php'>
                        <button class='update_btn'>Add boat</button>
                    </a>
                </div>";
    }

    echo "  <table id='main_table'>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Capacity (free)</th>
                    <th>Crew</th>
                    <th>Captain</th>
                </tr>";


    if ($_SESSION['filter']['filter_type'] === "Any") {
        $filter = "";
    } else {
        $filter = " AND boat.type=\"{$_SESSION['filter']['filter_type']}\"";
    }

    // Query
    $sql_query = " SELECT   boat.id_boat AS ID,
                            boat.name AS NAME,
                            boat.type AS TYPE,
                            boat.capacity AS CAPACITY,
                            crew.name AS CREW,
                            pirate.name AS CAPTAIN
                    FROM boat, crew, pirate
                    WHERE boat.id_crew=crew.id_crew
                    AND boat.id_captain=pirate.id_pirate" . $filter . $_SESSION['sort'];

     $retval = mysql_query( $sql_query, $db );

     //database  error
     if (!$retval) {
         die('Could not get data: ' . mysql_error());
     }

     //Print every records
     while($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
        $id = $row['ID'];
        $name = $row['NAME'];
        $type = $row['TYPE'];
        $capacity = $row['CAPACITY'];
        $crew = $row['CREW'];
        $cap = $row['CAPTAIN'];

        $free  = freeBoat($id, $capacity, $db);

        echo "  <tr>
                    <td> <a class='table_link' href='details.php?id_boat=$id'>$name</a> </td>
                    <td> <a class='table_link' href='details.php?id_boat=$id'>$type</a> </td>
                    <td> <a class='table_link' href='details.php?id_boat=$id'>$capacity ($free)</a> </td>
                    <td> <a class='table_link' href='details.php?id_boat=$id'>$crew</a> </td>
                    <td> <a class='table_link' href='details.php?id_boat=$id'>$cap</a> </td>
                </tr>";
     }

    //close table
    echo "</table>";
}

//function for print table about ports
function printPorts()
{
    include 'db_init.php';

    //Only admin can add crew
    if ($_SESSION['level'] == 0) {
        echo "  <div id='btn_menu'>
                    <a class='btn_link' href='add.php'>
                        <button class='update_btn'>Add port</button>
                    </a>
                </div>";
    }

    echo "  <table id='main_table'>
                <tr>
                    <th>Name</th>
                    <th>Capacity (free)</th>
                    <th>Place</th>
                </tr>";


     // Query
     $sql_query = " SELECT  id_port AS ID,
                            name AS NAME,
                            capacity AS CAPACITY,
                            place AS PLACE
                    FROM port" . $_SESSION['sort'];

     $retval = mysql_query( $sql_query, $db );

     //database  error
     if (!$retval) {
         die('Could not get data: ' . mysql_error());
     }

     //Print every records
     while($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
        $id = $row['ID'];
        $name = $row['NAME'];
        $capacity = $row['CAPACITY'];
        $place = $row['PLACE'];

        $free  = freePort($id, $capacity, $db);

        echo "  <tr>
                    <td> <a class='table_link' href='details.php?id_port=$id'>$name</a> </td>
                    <td> <a class='table_link' href='details.php?id_port=$id'>$capacity ($free)</a> </td>
                    <td> <a class='table_link' href='details.php?id_port=$id'>$place</a> </td>
                </tr>";
     }

    //close table
    echo "</table>";
}

//function for print table about ports
function printBattles()
{
    include 'db_init.php';

    //If I have privilages I can add pirate
    if ($_SESSION['level'] == 0) {
        echo "  <div id='btn_menu'>
                    <a class='btn_link' href='add.php'>
                        <button class='update_btn'>Add battle</button>
                    </a>
                </div>";
    }

    echo "  <table id='main_table'>
                <tr>
                    <th>Date</th>
                    <th>Location</th>
                    <th>Losses</th>
                    <th>Number of Crews</th>
                </tr>";


     // Query
     $sql_query = " SELECT  battle.id_battle AS ID,
                            battle.date_happened AS HAPP,
                            port.name AS PORT,
                            battle.losses as LOSS
                    FROM battle
                    LEFT JOIN port ON battle.id_port = port.id_port" . $_SESSION['sort'];

     $retval = mysql_query( $sql_query, $db );

     //database  error
     if (!$retval) {
         die('Could not get data: ' . mysql_error());
     }

     //Print every records
     while($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
        $id = $row['ID'];
        $date_happ = date("d M Y", strtotime($row['HAPP']));
        $port = $row['PORT'];
        $losses = $row['LOSS'];

        if ($port == null) {
            $port = "<i>ON SEA</i>";
        }

        //filter
        if ($_SESSION['filter']['filter_place'] !== "Any" ) {
            if ($_SESSION['filter']['filter_place'] === "ON SEA") {
                if("<i>ON SEA</i>" !== $port) {
                    continue;
                }

            } else {
                if ($_SESSION['filter']['filter_place'] !== $port) {
                    continue;
                }
            }
        }



        //get number of crews who Participated in battle
        $sql_query = "SELECT COUNT(*) AS NUM FROM crew_participated_in_battle WHERE id_battle=$id";
        $num = getNumber($sql_query, $db);

        echo "  <tr>
                    <td> <a class='table_link' href='details.php?id_battle=$id'>$date_happ</a> </td>
                    <td> <a class='table_link' href='details.php?id_battle=$id'>$port</a> </td>
                    <td> <a class='table_link' href='details.php?id_battle=$id'>$losses</a> </td>
                    <td> <a class='table_link' href='details.php?id_battle=$id'>$num</a> </td>
                </tr>";
     }

    //close table
    echo "</table>";
}

//function for printing table of fleets
function printFleets()
{
    include 'db_init.php';

    //If I have privilages I can add pirate
    if ($_SESSION['level'] == 1) {
        echo "  <div id='btn_menu'>
                    <a class='btn_link' href='add.php'>
                        <button class='update_btn'>Add fleet</button>
                    </a>
                </div>";
    }

    echo "  <table id='main_table'>
                <tr>
                    <th>Name</th>
                    <th>Number of boats</th>
                    <th>Captain</th>
                    <th>Crew</th>
                </tr>";


     // Query
     $sql_query = "SELECT fleet.id_fleet AS ID,
                        fleet.name AS NAME,
                        crew.name AS CREW,
                        pirate.name AS CAPTAIN
                   FROM (((fleet INNER JOIN captain ON fleet.id_captain = captain.id_pirate)
                   INNER JOIN pirate ON captain.id_pirate = pirate.id_pirate)
                   INNER JOIN pirate_in_crew ON pirate.id_pirate = pirate_in_crew.id_pirate)
                   INNER JOIN crew ON pirate_in_crew.id_crew = crew.id_crew" . $_SESSION['sort'];

     $retval = mysql_query( $sql_query, $db );

     //database  error
     if (!$retval) {
         die('Could not get data: ' . mysql_error());
     }

     //Print every records
     while($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
        $id = $row['ID'];
        $name = $row['NAME'];
        $crew = $row['CREW'];
        $captain = $row['CAPTAIN'];

        //Get number of boats
        $sql_query = "SELECT COUNT(id_boat) AS NUM FROM boat WHERE id_fleet=$id";
        $num = getNumber($sql_query, $db);

        echo "  <tr>
                    <td> <a class='table_link' href='details.php?id_fleet=$id'>$name</a> </td>
                    <td> <a class='table_link' href='details.php?id_fleet=$id'>$num</a> </td>
                    <td> <a class='table_link' href='details.php?id_fleet=$id'>$captain</a> </td>
                    <td> <a class='table_link' href='details.php?id_fleet=$id'>$crew</a> </td>
                </tr>";
     }

    //close table
    echo "</table>";
}
?>
