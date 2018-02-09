<?php

function editForm($what){
    switch ($what) {
        case "pirates":
            return editPirate();
            break;

        case "crews":
            return editCrew();
            break;

        case "boats":
            return editBoat();
            break;

        case "fleets":
            return editFleet();
            break;


    }
}


function editPirate()
{
    include 'db_init.php';

    if(!isset($_GET['id_pirate'])) {
        echo "<script> location.replace('main.php'); </script>";
        die();
    }

    $id_pirate = $_GET['id_pirate'];
    $level = getPirateLevel($id_pirate, $db);

    //check privilages
    if($_SESSION['level'] >= $level) {
        echo "<script> location.replace('main.php'); </script>";
        die();
    }

    {
        $strIndex = array(  "NAME",
                        "NICK",
                        "AGE",
                        "BEARD",
                        "LIST",
                        "RANK",
                        "BOAT");

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

            //get info about port
            if ($row['BOAT'] == null) {
                $row['BOAT'] = "NULL";
            }

        } else {
            echo "<script> location.replace('main.php'); </script>";
            die();
        }

        for ($i=0; $i < count($strIndex); $i++) {
            $value[$i] = $row[$strIndex[$i]];
        }
    }

    $ERRmsg = "";

    $input = array( "pirate_nick",
                    "pirate_character",
                    "pirate_position",
                    "pirate_boat");

    $indexAr = array(1,4,5,6);

    if(isset($_GET['updt'])) {

        for ($i=0; $i < count($input); $i++) {
            //If this input is not set -> redirect
            if (!isset($_GET[$input[$i]])) {
                echo "<script> location.replace('main.php'); </script>";
                die();
            }
            $value[$indexAr[$i]] = $_GET[$input[$i]];
        }

        for ($i=0; $i <count($value) ; $i++) {
            //obligatory
            if(($i < 4) || ($i == 5)) {
                if ($value[$i] === "") {
                    $ERRmsg = "You have to fill all obligatory fields. Thay are marked with '*'.";
                }
            }
        }



        //Every obligatory fields is fill ->we can add pirate
        if($ERRmsg === "") {
            if ($value[6] !== "NULL") {
                $value[6] = "'" . $value[6] . "'";
            }

            if ($level == 4) {
                //change boat of pirate
                $sql_query = "UPDATE pirate SET id_boat={$value[6]} WHERE id_pirate=$id_pirate";
                mysql_query( $sql_query, $db );

                //change position
                $sql_query = "UPDATE common_pirate SET position='{$value[5]}' WHERE id_pirate=$id_pirate";
                mysql_query( $sql_query, $db );
            }

            //change nick
            $sql_query = "UPDATE pirate SET nick='{$value[1]}' WHERE id_pirate=$id_pirate";
            mysql_query( $sql_query, $db );

            //change characteristics
            $sql_query = "UPDATE pirate SET characteristics_list='{$value[4]}' WHERE id_pirate=$id_pirate";
            mysql_query( $sql_query, $db );

            $dest = "main.php?msg=The+pirate+has+bee+SUCCESSFULLY+updated.";
            echo "<script> location.replace('$dest'); </script>";
            die();
        }

    } else {

        if(isset($_GET['edit'])) {

        } else {
            echo "<script> location.replace('main.php'); </script>";
            die();
        }

    }

    $beard = array("black", "brown", "blonde", "red", "white", "gray");
    $pos = array("Master Gunner", "Bootswain", "ABS", "Swab");

    echo "		<section class='update_section'>
    				<h3>Pirate:</h3>
    				<div class='update_field'>
    					<label for='pirate_name'>*Full name:</label>
                        <input type='hidden' name='id_pirate' value='$id_pirate'>
    					<input id='pirate_name' type='text' name='pirate_name' disabled maxlength='29' value='{$value[0]}' placeholder='Pirates name...'>
    				</div>
    				<div class='update_field'>
    					<label for='pirate_nick'>*Nickname:</label>
    					<input id='pirate_nick' type='text' name='pirate_nick' maxlength='29' value='{$value[1]}' placeholder='Pirates nickname...'>
    				</div>
    				<div class='update_field'>
    					<label for='pirate_birthdate'>*Date of birth:</label>
    					<input id='pirate_birthdate' type='date' disabled value='{$value[2]}' name='pirate_birthdate' >
    				</div>
    				<div class='update_field'>
    					<label for='pirate_beard'>*Beard color:</label>
    					<select id='pirate_beard' disabled name='pirate_beard'>";

                        for ($i=0; $i < count($beard); $i++) {
                            $str ="";
                            if ($beard[$i] === $value[3]){
                                $str = "selected";
                            }
                            echo "<option $str value='{$beard[$i]}'>{$beard[$i]}</option>";
                        }

                        echo "
    					</select>
    				</div>
    				<div class='update_field'>
    					<label for='pirate_character'>Characteristics:</label>
    					<input id='pirate_character' type='text' name='pirate_character' maxlength='254' value='{$value[4]}' placeholder='Pirates characteristics...'>
    				</div>
    				<div class='update_field'>
    					<label for='pirate_position'>*Position:</label>
    					<select id='pirate_position' name='pirate_position'>";

                        if($level == 4) {
                            $elem = $pos;

                            for ($i=0; $i < count($elem); $i++) {
                                $str ="";
                                if ($elem[$i] === $value[5]){
                                    $str = "selected";
                                }
                                echo "<option $str value='{$elem[$i]}'>{$elem[$i]}</option>";
                            }
                        } else {
                            echo "<option value='{$value[5]}'>{$value[5]}</option>";
                        }

                        echo "
    					</select>
    				</div>
    				<div class='update_field'>
    					<label for='pirate_boat'>Boat:</label>
    					<select id='pirate_boat' name='pirate_boat'>";

                            if($_SESSION['level'] != 0 && $level ==4) {

                                echo "<option value='NULL'><i>NONE<i></option>";
                                switch ($_SESSION['level']) {
                                    case 1:
                                        // Query for get  all crews boats
                                        $sql_query = "  SELECT boat.id_boat AS ID, boat.capacity AS CAPACITY
                                                        FROM boat INNER JOIN crew ON boat.id_crew = crew.id_crew
                                                        WHERE crew.id_captain = {$_SESSION['user']}";
                                        break;

                                    case 2:
                                        // Query for get  all crews boats
                                        $sql_query = "  SELECT boat.id_boat AS ID, boat.capacity AS CAPACITY
                                                        FROM boat INNER JOIN fleet ON boat.id_fleet=fleet.id_fleet
                                                        WHERE fleet.id_captain = {$_SESSION['user']}";
                                        break;


                                    case 3:
                                        // Query for get  all crews boats
                                        $sql_query = "  SELECT boat.id_boat AS ID, boat.capacity AS CAPACITY
                                                        FROM boat
                                                        WHERE boat.id_captain = {$_SESSION['user']}";
                                        break;

                                }


                                $retval = mysql_query( $sql_query, $db );

                                //database  error
                                if (!$retval) {
                                    die('Could not get data: ' . mysql_error());
                                }

                                while($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
                                    $id_boat = $row['ID'];
                                    $capacity = $row['CAPACITY'];

                                    if (freeBoat($id_boat,$capacity,$db) > 0) {

                                        $sql_query = "SELECT name AS NAME FROM boat WHERE id_boat=$id_boat";
                                        $name = getName($sql_query, $db);

                                        $str = "";
                                        if ($id_boat == $value[6]){
                                            $str = "selected";
                                        }

                                        echo "<option $str value='$id_boat'>$name</option>";
                                    }
                                }
                            } else {

                                $sql_query = "  SELECT id_boat AS ID
                                                FROM pirate
                                                WHERE id_pirate= $id_pirate";

                                $retval = mysql_query( $sql_query, $db );

                                //database  error
                                if (!$retval) {
                                    die('Could not get data: ' . mysql_error());
                                }

                                if ($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
                                    $id_boat = $row['ID'];

                                    if ($id_boat == null) {
                                        echo "<option value='NULL'><i>NONE<i></option>";
                                    } else {
                                        $sql_query = "SELECT name AS NAME FROM boat WHERE id_boat=$id_boat";
                                        $name = getName($sql_query, $db);

                                        echo "<option value='$id_boat'>$name</option>";
                                    }
                                }
                            }


                            echo "
    					</select>
    				</div>
    			</section>";

    return $ERRmsg;
}

function editCrew()
{
    include 'db_init.php';

    //get id of crew to be edited
    if(!isset($_GET['id_crew'])) {
        echo "<script> location.replace('main.php'); </script>";
        die();
    }
    $id_crew = $_GET['id_crew'];

    //check privileges
    if($_SESSION['level'] > 1) {
        echo "<script> location.replace('main.php'); </script>";
        die();
    }

    //check captain
    if($_SESSION['level'] == 1) {
        $sql_query = "SELECT id_captain AS ID FROM crew WHERE id_crew = $id_crew";
        $retval = mysql_query( $sql_query, $db );
        if (!$retval) {
            die('Could not get data: ' . mysql_error());
        }
        if (!(($row = mysql_fetch_array($retval, MYSQL_ASSOC)) && ($row['ID'] == $_SESSION['user']))) {
            echo "<script> location.replace('main.php'); </script>";
            die();
        }
    }

    {
        $strIndex = array("NAME", "PORT");

        // Query for details about this crew
        $sql_query = "SELECT crew.name AS NAME,
                      port.name AS PORT
                      FROM crew
                      INNER JOIN port ON crew.id_port = port.id_port
                      WHERE crew.id_crew = $id_crew";

        $retval = mysql_query( $sql_query, $db );

        //database  error
        if (!$retval) {
            die('Could not get data: ' . mysql_error());
        }

        //check if thehre is some record about crew
        if(!($row = mysql_fetch_array($retval, MYSQL_ASSOC))) {
            echo "<script> location.replace('main.php'); </script>";
            die();
        }

        for ($i=0; $i < count($strIndex); $i++) {
            $value[$i] = $row[$strIndex[$i]];
        }
    }

    $ERRmsg = "";

    $input = array( "crew_name",
                    "crew_port");

    if(isset($_GET['updt'])) {

        for ($i=0; $i < count($input); $i++) {
            //If this input is not set -> redirect
            if (!isset($_GET[$input[$i]])) {
                echo "<script> location.replace('main.php'); </script>";
                die();
            }
            $value[$i] = $_GET[$input[$i]];
            if ($value[$i] === "") {
                $ERRmsg = "You have to fill all obligatory fields. They are marked with '*'.";
            }
        }

        //Each obligatory field is filled -> we can add pirate
        if($ERRmsg === "") {
            //change name
            $sql_query = "UPDATE crew SET name='{$value[0]}' WHERE id_crew=$id_crew";
            mysql_query( $sql_query, $db );

            //change home port
            $sql_query = "UPDATE crew SET id_port='{$value[1]}' WHERE id_crew=$id_crew";
            mysql_query( $sql_query, $db );

            $dest = "main.php?msg=The+crew+has+been+SUCCESSFULLY+updated.";
            echo "<script> location.replace('$dest'); </script>";
            die();
        }

    } else {

        if(isset($_GET['edit'])) {

        } else {
            echo "<script> location.replace('main.php'); </script>";
            die();
        }

    }

    echo "		<section class='update_section'>
                    <h3>Crew:</h3>
                    <div class='update_field'>
                        <label for='crew_name'>*Name:</label>
                        <input type='hidden' name='id_crew' value='$id_crew'>
                        <input id='crew_name' type='text' name='crew_name' maxlength='29' value='{$value[0]}' placeholder='Crew name...'>
                    </div>
                    <div class='update_field'>
                        <label for='crew_port'>*Home port:</label>
                        <select id='crew_port' name='crew_port'>";
                            // Query to get all ports
                            $sql_query = "SELECT id_port AS ID, name AS NAME
                                          FROM port";
                            $retval = mysql_query( $sql_query, $db );

                            //database  error
                            if (!$retval) {
                                die('Could not get data: ' . mysql_error());
                            }

                            while($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
                                $id_port = $row['ID'];
                                $name = $row['NAME'];

                                $str = "";
                                if ($name === $value[1]){
                                    $str = "selected";
                                }

                                echo "<option $str value='$id_port'>$name</option>";
                            }
                        echo"
                        </select>
                    </div>
    			</section>";

    return $ERRmsg;
}

function editBoat()
{
    include 'db_init.php';

    //get id of boat to be edited
    if(!isset($_GET['id_boat'])) {
        echo "<script> location.replace('main.php'); </script>";
        die();
    }
    $id_boat = $_GET['id_boat'];

    if(!canIeditBoat($id_boat,$db)){
        echo "<script> location.replace('main.php'); </script>";
        die();
    }

    $ERRmsg = "";

    $input = array( "boat_name",
                    "boat_port");

    if(isset($_GET['updt'])) {

        for ($i=0; $i < count($input); $i++) {
            //If this input is not set -> redirect
            if (!isset($_GET[$input[$i]])) {
                echo "<script> location.replace('main.php'); </script>";
                die();
            }

            $value[$i] = $_GET[$input[$i]];

            if ($value[$i] === "") {
                $ERRmsg = "You have to fill all obligatory fields. They are marked with '*'.";
            }
        }

        //Each obligatory field is filled -> we can add pirate
        if($ERRmsg === "") {
            //change name
            $sql_query = "UPDATE boat SET name='{$value[0]}' WHERE id_boat=$id_boat";
            mysql_query( $sql_query, $db );

            //change port
            $sql_query = "UPDATE boat SET id_port={$value[1]} WHERE id_boat=$id_boat";
            mysql_query( $sql_query, $db );

            $dest = "main.php?msg=The+boat+has+been+SUCCESSFULLY+updated.";
            echo "<script> location.replace('$dest'); </script>";
            die();
        }

    } else {

        if(isset($_GET['edit'])) {
            $strIndex = array("NAME", "PORT");

            // Query for details about this crew
            $sql_query = "SELECT boat.name AS NAME,
                          boat.id_port AS PORT
                          FROM boat
                          WHERE boat.id_boat = $id_boat";

            $retval = mysql_query( $sql_query, $db );

            //database  error
            if (!$retval) {
                die('Could not get data: ' . mysql_error());
            }

            //check if thehre is some record about crew
            if(!($row = mysql_fetch_array($retval, MYSQL_ASSOC))) {
                echo "<script> location.replace('main.php'); </script>";
                die();
            }

            $value[0] = $row["NAME"];
            $value[1] = $row["PORT"]; //ID port
        } else {
            echo "<script> location.replace('main.php'); </script>";
            die();
        }

    }

    echo "		<section class='update_section'>
                    <h3>Boat:</h3>
                    <div class='update_field'>
                        <label for='boat_name'>*Name:</label>
                        <input type='hidden' name='id_boat' value='$id_boat'>
                        <input id='boat_name' type='text' name='boat_name' maxlength='29' value='{$value[0]}' placeholder='Boat name...'>
                    </div>
                    <div class='update_field'>
                        <label for='boat_port'>*Current port:</label>
                        <select id='boat_port' name='boat_port'>";
                        echo "<option value='NULL'>NONE</option>";
                            // Query to get all ports
                            $sql_query = "SELECT id_port AS ID, name AS NAME
                                          FROM port";
                            $retval = mysql_query( $sql_query, $db );

                            //database  error
                            if (!$retval) {
                                die('Could not get data: ' . mysql_error());
                            }

                            while($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
                                $id_port = $row['ID'];
                                $name = $row['NAME'];

                                $str = "";
                                if ($id_port == $value[1]){
                                    $str = "selected";
                                }

                                echo "<option $str value='$id_port'>$name</option>";
                            }
                        echo"
                        </select>
                    </div>
    			</section>";

    return $ERRmsg;
}

function editFleet()
{
    include 'db_init.php';

    //get id of fleet to be edited
    if(!isset($_GET['id_fleet'])) {
        echo "<script> location.replace('main.php'); </script>";
        die();
    }

    $id_fleet = $_GET['id_fleet'];

    // Get crew id of current captain for future use
    $captain_id = $_SESSION['user'];
    $sql_query = "  SELECT pirate_in_crew.id_crew AS ID FROM fleet, pirate_in_crew
                    WHERE fleet.id_captain = pirate_in_crew.id_pirate
                    AND fleet.id_fleet = $id_fleet";
    $retval = mysql_query( $sql_query, $db );
    $row = mysql_fetch_array($retval, MYSQL_ASSOC);
    $crew_id = $row['ID'];

    //Am I crew Captain or fleeet captain
    if($_SESSION['level'] != 1 && $_SESSION['level'] != 2) {
        echo "<script> location.replace('main.php'); </script>";
        die();
    }

    //Am I the right captain?
    if($_SESSION['level'] == 2) {
        $sql_query = "SELECT id_captain AS ID FROM fleet WHERE id_fleet = $id_fleet";
        $retval = mysql_query( $sql_query, $db );
        if (!$retval) {
            die('Could not get data: ' . mysql_error());
        }
        if (!(($row = mysql_fetch_array($retval, MYSQL_ASSOC)) && ($row['ID'] == $_SESSION['user']))) {
            echo "<script> location.replace('main.php'); </script>";
            die();
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
        if (!(($row = mysql_fetch_array($retval, MYSQL_ASSOC)) && ($row['ID'] == $_SESSION['user']))) {
            echo "<script> location.replace('main.php'); </script>";
            die();
        }
    }

    $ERRmsg = "";

    $input = array( "fleet_name");
                    //and also a lot of fleet_boats

    if(isset($_GET['updt'])) {
        //If this input is not set -> redirect
        if (!isset($_GET[$input[0]])) {
            echo "<script> location.replace('main.php'); </script>";
            die();
        }

        $value[0] = $_GET[$input[0]];

        //obligatory
        if ($value[0] === "") {
            $ERRmsg = "You have to fill all obligatory fields. They are marked with '*'.";
        }

        if (!isset($_GET['number_of_boats'])) {
            echo "<script> location.replace('main.php'); </script>";
            die();
        }

        $num = $_GET['number_of_boats'];

        //get number of boats
        $c = 0;
        for ($i = 0; $i < $num; $i++) {
            if (isset($_GET["fleet_boat{$i}"])) {
                $value[1][$c] = $_GET["fleet_boat{$i}"];
                $c = $c + 1;
            }
        }

        //Fleats Has to consists at least of 2 boats
        if($c < 2) {
            $ERRmsg = $ERRmsg . " You have to choos at least 2 boats. If there is less then that, go to create boat!";
        }

        //All obligatory fields have been filled -> we can add port
        if($ERRmsg === "") {
            //update fleet name
            $sql_query = "UPDATE fleet SET name = '{$value[0]}' WHERE id_fleet = $id_fleet";
            mysql_query( $sql_query, $db );

            //remove all boats from fleet
            $sql_query = "UPDATE boat SET id_fleet = NULL WHERE id_fleet = $id_fleet";
            mysql_query( $sql_query, $db );

            //add all chosen boats to fleet
            for ($i=0; $i < $c ; $i++) {
                $sql_query = "UPDATE boat SET id_fleet = $id_fleet WHERE id_boat = {$value[1][$i]}";
                mysql_query( $sql_query, $db );
            }

            $dest = "main.php?msg=The+fleet+has+been+SUCCESSFULLY+updated.";
            echo "<script> location.replace('$dest'); </script>";
            die();
        }

    } else {
        if(isset($_GET['edit'])) {
            // Query for details about specific fleet
             $sql_query = "SELECT name AS NAME
                           FROM fleet
                           WHERE id_fleet=$id_fleet";

            $retval = mysql_query( $sql_query, $db );

            //database  error
            if (!$retval) {
                die('Could not get data: ' . mysql_error());
            }

            //check if thehre is some record about fleet
            if($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
                $value[0] = $row['NAME'];
            } else {
                echo "<script> location.replace('main.php'); </script>";
                die();
            }

            //Print BOATS LIST
            $sql_query = "SELECT id_boat AS ID
            FROM boat
            WHERE id_fleet=$id_fleet";

            $retval = mysql_query( $sql_query, $db );
            //database  error
            if (!$retval) {
                die('Could not get data: ' . mysql_error());
            }

            $c = 0;
            while ($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
                $value[1][$c] = $row['ID'];
                $c = $c + 1;
            }

        } else {
            echo "<script> location.replace('main.php'); </script>";
            die();
        }
    }

    echo "      <section class='update_section'>
                    <h3>Fleet:</h3>
                    <div class='update_field'>
                        <label for='fleet_name'>*Name:</label>
                        <input id='fleet_id' type='hidden' name='id_fleet' value='$id_fleet'>
                        <input id='fleet_name' type='text' name='fleet_name' maxlength='29' value='{$value[0]}' placeholder='Fleet name...'>
                    </div>
                    <div class='update_field'>
                        <label for='fleet_boats'>*Boats:</label>
                        <div id='fleet_boats' class='checkbox_group'>";

                            // Query to get all crew boats, which havent got a fleet
                            $sql_query = "SELECT id_boat AS ID, name AS NAME, id_fleet AS FLEET
                                          FROM boat
                                          WHERE id_crew = $crew_id
                                          AND id_fleet IS NULL OR id_fleet = $id_fleet";
                            $retval = mysql_query( $sql_query, $db );

                            //database  error
                            if (!$retval) {
                                die('Could not get data: ' . mysql_error());
                            }

                            $c = 0;
                            for ($i=0; $row = mysql_fetch_array($retval, MYSQL_ASSOC) ;$i++) {
                                // Get data
                                $id_boat = $row['ID'];
                                $name = $row['NAME'];
                                $cur_fleet = $row['FLEET'];

                                $str = "";
                                if (isset($value[1][$c])) {
                                    if ($id_boat == $value[1][$c]){
                                        $str = "checked";
                                        $c++;
                                    }
                                }

                                echo "<input $str type='checkbox' name='fleet_boat{$i}' value='$id_boat'>$name<br>";
                            }

                            echo "<input type='hidden' name='number_of_boats' value='$i'>";

                            echo "
                        </div>
                    </div>

                </section>";


    return $ERRmsg;
}


?>
