<?php

function printDetail($what) {
    switch ($what) {
        case "pirates":
            printPirate();
            break;

        case "crews":
            printCrew();
            break;

        case "boats":
            printBoat();
            break;

        case "ports":
            printPort();
            break;

        case "battles":
            printBattle();
            break;

        case "fleets":
            printFleet();
            break;
    }
}

function printPirate()
{
    include 'db_init.php';

    if (!isset($_GET['id_pirate'])) {
        echo "<script> location.replace('main.php'); </script>";
        die();
    }

    //get pirate id from url
    $id_pirate = $_GET['id_pirate'];

    //Level of privilages
    $pirate_level = getPiratePrivilages($id_pirate, $db);

    //If my level is more than his -> so you can modify
    if ($_SESSION['level'] < $pirate_level) {
        echo "  <div id='btn_menu'>
                    <a class='btn_link' href='edit.php?edit=&id_pirate=$id_pirate'>
                        <button class='update_btn'>Edit pirate</button>
                    </a>";

        //If this pirate is a normal you can delete
        if(getPirateLevel($id_pirate, $db) == 4) {
            echo "  <a class='btn_link' href='delete.php?id_pirate=$id_pirate'>
                        <button class='update_btn'>Delete pirate</button>
                    </a>";
        }

        echo " </div>";
    }


    //Print label and begin of tabel
    echo "  <div id='details_label'>
                <p>Pirate</p>
            </div>
            <table id='details_table'>";

    // Query for details about specific pirate
    $sql_query = " SELECT  pirate.name AS NAME,
                           pirate.nick AS NICK,
                           pirate.date_of_birth AS AGE,
                           crew.name AS CREW,
                           pirate.id_boat AS BOAT,
                           pirate.beard_color AS BEARD,
                           pirate.date_joined_crew AS DUTY,
                           pirate.characteristics_list AS LIST
                   FROM crew, pirate, pirate_in_crew
                   WHERE pirate_in_crew.id_pirate = pirate.id_pirate
                   AND pirate_in_crew.id_crew = crew.id_crew
                   AND pirate.id_pirate = $id_pirate";

    $retval = mysql_query( $sql_query, $db );

    //database  error
    if (!$retval) {
        die('Could not get data: ' . mysql_error());
    }

    //check if thehre is some record about pirate
    if($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
        $row['RANK'] = getRank($id_pirate, $db);
        $row['AGE'] = "" . date("d M Y", strtotime($row['AGE'])) . " (" . getAge($row['AGE']) . ") ";
        $row['DUTY'] = getAge($row['DUTY']);

        //get info about port
        if ($row['BOAT'] == null) {
            $row['BOAT'] = "<i>ON SHORE</i>";
        } else {
            $id_boat = $row['BOAT'];
            $sql_query = "SELECT name AS NAME FROM boat WHERE id_boat=$id_boat";
            $row['BOAT'] = getName($sql_query, $db);
        }

    } else {
        echo "<script> location.replace('main.php'); </script>";
        die();
    }

    $strIndex = array(  "NAME",
                        "RANK",
                        "AGE",
                        "NICK",
                        "BEARD",
                        "CREW",
                        "BOAT",
                        "DUTY",
                        "LIST");

    $strOutput =  array("Name:",
                        "Rank:",
                        "Date of birth (Age):",
                        "Nickname:",
                        "Beard color:",
                        "Crew:",
                        "Boat:",
                        "How long in duty (years):",
                        "List of characteristics:");

    for ($i=0; $i < count($strIndex) ; $i++) {
        echo "  <tr>
                    <td class='attribute_name'>" . $strOutput[$i] . "</td>
                    <td class='attribute_value'>" . $row[$strIndex[$i]] . "</td>
                </tr>";
    }

    //close table
    echo "</table>";

}

function printCrew()
{
    include 'db_init.php';

    if (!isset($_GET['id_crew'])) {
        echo "<script> location.replace('main.php'); </script>";
        die();
    }

    //get crew_id id from url
    $id_crew = $_GET['id_crew'];


    //If my level is more than his -> so you can modify
    if ($_SESSION['level'] <= 1) {
        //check captain
        if($_SESSION['level'] == 1) {
            $sql_query = "SELECT id_captain AS ID FROM crew WHERE id_crew = $id_crew";
            $retval = mysql_query( $sql_query, $db );
            if (!$retval) {
                die('Could not get data: ' . mysql_error());
            }
            if ((($row = mysql_fetch_array($retval, MYSQL_ASSOC)) && ($row['ID'] == $_SESSION['user']))) {
                echo "  <div id='btn_menu'>
                            <a class='btn_link' href='edit.php?edit=&id_crew=$id_crew'>
                                <button class='update_btn'>Edit crew</button>
                            </a>
                        </div>";

            }
        } else {
            echo "  <div id='btn_menu'>
                        <a class='btn_link' href='edit.php?edit=&id_crew=$id_crew'>
                            <button class='update_btn'>Edit crew</button>
                        </a>
                    </div>";
        }
    }

    //Print label and begin of tabel
    echo "  <div id='details_label'>
                <p>Crew</p>
            </div>
            <table id='details_table'>";

    // Query for details about specific pirate
    $sql_query = " SELECT  crew.name AS NAME,
                           port.name AS PORT,
                           pirate.name AS CAPTAIN
                   FROM (  crew
                   INNER JOIN port ON crew.id_port = port.id_port)
                   INNER JOIN pirate ON crew.id_captain = pirate.id_pirate
                   WHERE id_crew=$id_crew";

    $retval = mysql_query( $sql_query, $db );

    //database  error
    if (!$retval) {
        die('Could not get data: ' . mysql_error());
    }

    //check if thehre is some record about pirate
    if($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
        //get number of pirates
        $sql_query = "SELECT COUNT(*) AS NUM FROM pirate_in_crew WHERE id_crew=$id_crew";
        $row['PIRATES'] = getNumber($sql_query, $db);

        //get number of boats
        $sql_query = "SELECT COUNT(*) AS NUM FROM boat WHERE id_crew=$id_crew";
        $row['BOATS'] = getNumber($sql_query, $db);

        //get number of battles
        $sql_query = "SELECT COUNT(*) AS NUM FROM crew_participated_in_battle WHERE id_crew=$id_crew";
        $row['BATTLES'] = getNumber($sql_query, $db);

        //get number of battles won
        $sql_query = "SELECT COUNT(*) AS NUM FROM battle WHERE who_won=$id_crew";
        $row['BATTLES'] = "" . $row['BATTLES'] . " (" . getNumber($sql_query, $db) . ")";
    } else {
        echo "<script> location.replace('main.php'); </script>";
        die();
    }

    $strIndex = array(  "NAME",
                        "CAPTAIN",
                        "PIRATES",
                        "BOATS",
                        "PORT",
                        "BATTLES");

    $strOutput =  array("Name:",
                        "Captain:",
                        "Number of Pirates:",
                        "Number of Boats:",
                        "Base Territory:",
                        "Participated in Battles (won):");

    for ($i=0; $i < count($strIndex) ; $i++) {
        echo "  <tr>
                    <td class='attribute_name'>" . $strOutput[$i] . "</td>
                    <td class='attribute_value'>" . $row[$strIndex[$i]] . "</td>
                </tr>";
    }

    //close table
    echo "</table>";

}

function printBoat()
{
    include 'db_init.php';

    if (!isset($_GET['id_boat'])) {
        echo "<script> location.replace('main.php'); </script>";
        die();
    }

    //get pirate id from url
    $id_boat = $_GET['id_boat'];

    if (canIeditBoat($id_boat,$db)) {

        echo "  <div id='btn_menu'>
                    <a class='btn_link' href='edit.php?edit=&id_boat=$id_boat'>
                        <button class='update_btn'>Edit boat</button>
                    </a>
                </div>";
    }

    //Print label and begin of tabel
    echo "  <div id='details_label'>
                <p>Boat</p>
            </div>
            <table id='details_table'>";

    // Query for details about specific pirate
    $sql_query = " SELECT  boat.name AS NAME,
                           boat.type AS TYPE,
                           boat.capacity AS CAPACITY,
                           boat.id_port AS PORT,
                           crew.name AS CREW,
                           pirate.name AS CAPTAIN,
                           boat.id_fleet AS FLEET
                   FROM boat, crew, pirate
                   WHERE boat.id_crew=crew.id_crew
                   AND boat.id_captain=pirate.id_pirate
                   AND boat.id_boat = $id_boat";

    $retval = mysql_query( $sql_query, $db );

    //database  error
    if (!$retval) {
        die('Could not get data: ' . mysql_error());
    }

    //check if thehre is some record about pirate
    if($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
        //get free space in boat
        $row['CAPACITY'] = "" . $row['CAPACITY'] . " (" . freeBoat($id_boat, $row['CAPACITY'], $db) . ")";

        //get info about port
        if ($row['PORT'] == null) {
            $row['PORT'] = "<i>ON SEA</i>";
        } else {
            $id_port = $row['PORT'];
            $sql_query = "SELECT name AS NAME FROM port WHERE id_port=$id_port";
            $row['PORT'] = getName($sql_query, $db);
        }

        //get number of battles
        $sql_query = "SELECT COUNT(*) AS NUM FROM boat_participated_in_battle WHERE id_boat=$id_boat";
        $row['BATTLES'] = getNumber($sql_query, $db);

        $row['FLEET'];

        if($row['FLEET'] == null) {
            $row['FLEET'] = "<i>NONE</i>";
        } else {
            $sql_query = "SELECT name AS NAME FROM fleet WHERE id_fleet={$row['FLEET']}";
            $row['FLEET'] = getName($sql_query, $db);
        }

    } else {
        echo "<script> location.replace('main.php'); </script>";
        die();
    }

    $strIndex = array(  "NAME",
                        "TYPE",
                        "CAPTAIN",
                        "CAPACITY",
                        "CREW",
                        "FLEET",
                        "PORT",
                        "BATTLES");

    $strOutput =  array("Name:",
                        "Type:",
                        "Captain:",
                        "Capacity (free):",
                        "Crew:",
                        "Part of fleet:",
                        "Port:",
                        "Participated in Battles:");

    for ($i=0; $i < count($strIndex) ; $i++) {
        echo "  <tr>
                    <td class='attribute_name'>" . $strOutput[$i] . "</td>
                    <td class='attribute_value'>" . $row[$strIndex[$i]] . "</td>
                </tr>";
    }

    //close table
    echo "</table>";

}

function printPort()
{
    include 'db_init.php';

    if (!isset($_GET['id_port'])) {
        echo "<script> location.replace('main.php'); </script>";
        die();
    }

    //get pirate id from url
    $id_port = $_GET['id_port'];

    //Print label and begin of tabel
    echo "  <div id='details_label'>
                <p>Port</p>
            </div>
            <table id='details_table'>";

    // Query for details about specific pirate
    $sql_query = " SELECT  name AS NAME,
                           capacity AS CAPACITY,
                           place AS PLACE
                   FROM port
                   WHERE id_port = $id_port";

    $retval = mysql_query( $sql_query, $db );

    //database  error
    if (!$retval) {
        die('Could not get data: ' . mysql_error());
    }

    if($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
        //get free space in boat
        $row['CAPACITY'] = "" . $row['CAPACITY'] . " (" . freePort($id_port, $row['CAPACITY'], $db) . ")";

        //get number of battles
        $sql_query = "SELECT COUNT(*) AS NUM FROM battle WHERE id_port=$id_port";
        $row['BATTLES'] = getNumber($sql_query, $db);

        $sql_query = "SELECT COUNT(*) AS NUM FROM crew WHERE id_port=$id_port";
        $row['CREWS'] = getNumber($sql_query, $db);

    } else {
        echo "<script> location.replace('main.php'); </script>";
        die();
    }

    $strIndex = array(  "NAME",
                        "CAPACITY",
                        "PLACE",
                        "BATTLES",
                        "CREWS");

    $strOutput =  array("Name:",
                        "Capacity (free):",
                        "Location:",
                        "Number of battles in this port:",
                        "Number of crews who have territory here:");

    for ($i=0; $i < count($strIndex) ; $i++) {
        echo "  <tr>
                    <td class='attribute_name'>" . $strOutput[$i] . "</td>
                    <td class='attribute_value'>" . $row[$strIndex[$i]] . "</td>
                </tr>";
    }

    //close table
    echo "</table>";

}

function printBattle()
{
    include 'db_init.php';

    if (!isset($_GET['id_battle'])) {
        echo "<script> location.replace('main.php'); </script>";
        die();
    }

    //get pirate id from url
    $id_battle = $_GET['id_battle'];

    //Print label and begin of tabel
    echo "  <div id='details_label'>
                <p>Battle</p>
            </div>
            <table id='details_table'>";

    // Query for details about specific pirate
    $sql_query = " SELECT  battle.date_happened AS HAPP,
                           port.name AS PORT,
                           battle.losses as LOSS,
                           battle.who_won as WON
                   FROM battle
                   LEFT JOIN port ON battle.id_port = port.id_port
                   WHERE battle.id_battle = $id_battle";

    $retval = mysql_query( $sql_query, $db );

    //database  error
    if (!$retval) {
        die('Could not get data: ' . mysql_error());
    }

    if($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
        //change format of date
        $row['HAPP'] = date("d M Y", strtotime($row['HAPP']));

        //Who won
        $id_crew = $row['WON'];
        $sql_query = "SELECT name AS NAME FROM crew WHERE id_crew=$id_crew";
        $row['WON'] = getName($sql_query, $db);

        if ($row['PORT'] == null) {
            $row['PORT'] = "<i>ON SEA</i>";
        }

        //get number of boats, which  Participated in battle
        $sql_query = "SELECT COUNT(*) AS NUM FROM boat_participated_in_battle WHERE id_battle=$id_battle";
        $row['BOATS'] = getNumber($sql_query, $db);

        //get number of crews who Participated in battle
        $sql_query = "SELECT COUNT(*) AS NUM FROM crew_participated_in_battle WHERE id_battle=$id_battle";
        $row['NUM_CREWS'] = getNumber($sql_query, $db);

    } else {
        echo "<script> location.replace('main.php'); </script>";
        die();
    }

    $strIndex = array(  "HAPP",
                        "WON",
                        "LOSS",
                        "PORT",
                        "BOATS",
                        "NUM_CREWS");

    $strOutput =  array("Date of battle:",
                        "Who won (crew):",
                        "Losses:",
                        "Location (port name):",
                        "Number of boats that participated:",
                        "Number of crews who had participated:");

    for ($i=0; $i < count($strIndex) ; $i++) {
        echo "  <tr>
                    <td class='attribute_name'>" . $strOutput[$i] . "</td>
                    <td class='attribute_value'>" . $row[$strIndex[$i]] . "</td>
                </tr>";
    }

    //Print CREWS LIST
    $sql_query = "  SELECT crew.name AS CREW
                    FROM crew_participated_in_battle
                    INNER JOIN crew ON crew_participated_in_battle.id_crew = crew.id_crew
                    WHERE id_battle = $id_battle";

    $retval = mysql_query( $sql_query, $db );

    //database  error
    if (!$retval) {
        die('Could not get data: ' . mysql_error());
    }

    $first = True;

    while($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {

        if ($first) {
            $name = "List of participated crews:";
            $first = False;
        } else {
            $name = "";
        }

        echo "  <tr>
                    <td class='attribute_name'>" . $name . "</td>
                    <td class='attribute_value'>" . $row['CREW'] . "</td>
                </tr>";
    }

    //close table
    echo "</table>";
}

function printFleet()
{
    include 'db_init.php';

    if (!isset($_GET['id_fleet'])) {
        echo "<script> location.replace('main.php'); </script>";
        die();
    }

    //get fleet id from url
    $id_fleet = $_GET['id_fleet'];

    //Am I the right captain?
    if($_SESSION['level'] == 2) {
        $sql_query = "SELECT id_captain AS ID FROM fleet WHERE id_fleet = $id_fleet";
        $retval = mysql_query( $sql_query, $db );
        if (!$retval) {
            die('Could not get data: ' . mysql_error());
        }
        if (($row = mysql_fetch_array($retval, MYSQL_ASSOC)) && ($row['ID'] == $_SESSION['user'])) {
            echo "  <div id='btn_menu'>
                        <a class='btn_link' href='edit.php?edit=&id_fleet=$id_fleet'>
                            <button class='update_btn'>Edit fleet</button>
                        </a>
                    </div>";
        }
    }


    if($_SESSION['level'] == 1) {
        $sql_query = "  SELECT crew.id_captain AS ID
                        FROM fleet, pirate_in_crew, crew
                        WHERE fleet.id_captain = pirate_in_crew.id_pirate
                        AND pirate_in_crew.id_crew = crew.id_crew
                        AND id_fleet = $id_fleet";
        $retval = mysql_query( $sql_query, $db );
        if (!$retval) {
            die('Could not get data: ' . mysql_error());
        }
        if (($row = mysql_fetch_array($retval, MYSQL_ASSOC)) && ($row['ID'] == $_SESSION['user'])) {
            echo "  <div id='btn_menu'>
                        <a class='btn_link' href='edit.php?edit=&id_fleet=$id_fleet'>
                            <button class='update_btn'>Edit fleet</button>
                        </a>
                    </div>";
        }
    }


    //Print label and begin of tabel
    echo "  <div id='details_label'>
                <p>Fleet</p>
            </div>
            <table id='details_table'>";

    // Query for details about specific fleet
     $sql_query = "SELECT fleet.id_fleet AS ID,
                        fleet.name AS NAME,
                        crew.name AS CREW,
                        pirate.name AS CAPTAIN
                   FROM (((fleet INNER JOIN captain ON fleet.id_captain = captain.id_pirate)
                   INNER JOIN pirate ON captain.id_pirate = pirate.id_pirate)
                   INNER JOIN pirate_in_crew ON pirate.id_pirate = pirate_in_crew.id_pirate)
                   INNER JOIN crew ON pirate_in_crew.id_crew = crew.id_crew
                   WHERE fleet.id_fleet=$id_fleet";

    $retval = mysql_query( $sql_query, $db );

    //database  error
    if (!$retval) {
        die('Could not get data: ' . mysql_error());
    }

    //check if thehre is some record about fleet
    if($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
        //get number of boats
        $sql_query = "SELECT COUNT(id_boat) AS NUM FROM boat WHERE id_fleet=$id_fleet";
        $row['NUM'] = getNumber($sql_query, $db);
    } else {
        echo "<script> location.replace('main.php'); </script>";
        die();
    }

    $strIndex = array(  "NAME",
                        "NUM",
                        "CAPTAIN",
                        "CREW");

    $strOutput =  array("Name:",
                        "Number of boats:",
                        "Captain:",
                        "Crew:");

    for ($i=0; $i < count($strIndex) ; $i++) {
        echo "  <tr>
                    <td class='attribute_name'>" . $strOutput[$i] . "</td>
                    <td class='attribute_value'>" . $row[$strIndex[$i]] . "</td>
                </tr>";
    }

    //Print BOATS LIST
    $sql_query = "SELECT name AS BOAT
                  FROM boat
                  WHERE id_fleet=$id_fleet";

    $retval = mysql_query( $sql_query, $db );

    //database  error
    if (!$retval) {
        die('Could not get data: ' . mysql_error());
    }

    $first = True;

    while($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {

        if ($first) {
            $name = "List of boats:";
            $first = False;
        } else {
            $name = "";
        }

        echo "  <tr>
                    <td class='attribute_name'>" . $name . "</td>
                    <td class='attribute_value'>" . $row['BOAT'] . "</td>
                </tr>";
    }

    //close table
    echo "</table>";

}
?>
