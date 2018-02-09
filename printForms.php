<?php

function printForm($what){
    switch ($what) {
        case "pirates":
            return addPirate();
            break;

        case "crews":
            return addCrew();
            break;

        case "boats":
            return addBoat();
            break;

        case "ports":
            return addPort();
            break;

        case "battles":
            return addBattle();
            break;

        case "fleets":
            return addFleet();
            break;
    }
}


function addPirate()
{
    include 'db_init.php';
    $today = date("Y-m-d");

    //Am I the crew captain
    if($_SESSION['level'] != 1) {
        echo "<script> location.replace('main.php'); </script>";
        die();
    }

    $ERRmsg = "";

    $input = array( "pirate_name",
                    "pirate_nick",
                    "pirate_birthdate",
                    "pirate_beard",
                    "pirate_character",
                    "pirate_position",
                    "pirate_boat");


    if(isset($_GET['add'])) {
        for ($i=0; $i < count($input); $i++) {
            //If this input is not set -> redirect
            if (!isset($_GET[$input[$i]])) {
                echo "<script> location.replace('main.php'); </script>";
                die();
            }

            $value[$i] = $_GET[$input[$i]];

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

            //put the new pirate into the pirate table
            $sql_query = "INSERT INTO pirate VALUES(NULL ,'password','{$value[0]}', '{$value[1]}', '{$value[2]}', '{$value[3]}', '$today', {$value[6]}, '{$value[4]}')";

            mysql_query( $sql_query, $db );
            $id_new = mysql_insert_id();

            //put the new pirate into the common_pirate table
            $sql_query = "INSERT INTO common_pirate VALUES('$id_new','{$value[5]}')";
            mysql_query( $sql_query, $db );

            // get my crew id
            $sql_query = "SELECT id_crew AS ID FROM pirate_in_crew WHERE id_pirate={$_SESSION['user']}";
            $retval = mysql_query( $sql_query, $db );

            //database  error
            if (!$retval) {
                die('Could not get data: ' . mysql_error());
            }

            //Get
            if ($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
                $crew_id= $row["ID"];
            } else {
                die("It is not true ... It cannot be :/");
            }

            $sql_query = "INSERT INTO pirate_in_crew VALUES('$id_new', '$crew_id');";
            mysql_query( $sql_query, $db );

            $dest = "main.php?msg=The+pirate+has+bee+SUCCESSFULLY+created:+login:+{$id_new}+password:+password";
            echo "<script> location.replace('$dest'); </script>";
            die();
        }

    } else {
        for ($i=0; $i < count($input); $i++) {
            $value[$i] = "";
        }
    }

    $beard = array("black", "brown", "blonde", "red", "white", "gray");
    $pos = array("Master Gunner", "Bootswain", "ABS", "Swab");

    echo "		<section class='update_section'>
    				<h3>Pirate:</h3>
    				<div class='update_field'>
    					<label for='pirate_name'>*Full name:</label>
    					<input id='pirate_name' type='text' name='pirate_name' maxlength='29' value='{$value[0]}' placeholder='Pirates name...'>
    				</div>
    				<div class='update_field'>
    					<label for='pirate_nick'>*Nickname:</label>
    					<input id='pirate_nick' type='text' name='pirate_nick' maxlength='29' value='{$value[1]}' placeholder='Pirates nickname...'>
    				</div>
    				<div class='update_field'>
    					<label for='pirate_birthdate'>*Date of birth:</label>
    					<input id='pirate_birthdate' type='date' max='$today' value='{$value[2]}' name='pirate_birthdate' >
    				</div>
    				<div class='update_field'>
    					<label for='pirate_beard'>*Beard color:</label>
    					<select id='pirate_beard' name='pirate_beard'>";

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

                        $elem = $pos;

                        for ($i=0; $i < count($elem); $i++) {
                            $str ="";
                            if ($elem[$i] === $value[5]){
                                $str = "selected";
                            }
                            echo "<option $str value='{$elem[$i]}'>{$elem[$i]}</option>";
                        }

                        echo "
    					</select>
    				</div>
    				<div class='update_field'>
    					<label for='pirate_boat'>Boat:</label>
    					<select id='pirate_boat' name='pirate_boat'>
                            <option value='NULL'><i>NONE<i></option>";

                            // Query for get  all crews boats
                            $sql_query = "  SELECT boat.id_boat AS ID, boat.capacity AS CAPACITY
                                            FROM boat INNER JOIN crew ON boat.id_crew = crew.id_crew
                                            WHERE crew.id_captain = {$_SESSION['user']}";
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

                            echo "
    					</select>
    				</div>
    			</section>";

    return $ERRmsg;
}

function addCrew()
{
    include 'db_init.php';
    //$today = date("Y-m-d");
    $today = date("Y-m-d");

    //Am I the admin
    if($_SESSION['level'] != 0) {
        echo "<script> location.replace('main.php'); </script>";
        die();
    }

    $ERRmsg = "";

    $input = array( "crew_name",
                    "crew_port",
                    "captain_name",
                    "captain_nick",
                    "captain_birthdate",
                    "captain_beard",
                    "captain_character");


    if(isset($_GET['add'])) {
        for ($i=0; $i < count($input); $i++) {
            //If this input is not set -> redirect
            if (!isset($_GET[$input[$i]])) {
                echo "<script> location.replace('main.php'); </script>";
                die();
            }

            $value[$i] = $_GET[$input[$i]];

            //obligatory
            if($i < 6) {
                if ($value[$i] === "") {
                    $ERRmsg = "You have to fill all obligatory fields. Thay are marked with '*'.";
                }
            }
        }


        // !!!!!!!!! THIS IS NOT EDITED !!!!!!!!!!!
        //Every obligatory fields is fill ->we can add pirate
        if($ERRmsg === "") {
            //put the captain into pirate table
           $sql_query = "INSERT INTO pirate VALUES(NULL, 'password', '{$value[2]}', '{$value[3]}', '{$value[4]}', '{$value[5]}', '$today', NULL, '{$value[6]}');";
           mysql_query( $sql_query, $db );
           $id_new_cap = mysql_insert_id();

           //put the new captain into captain table
           $sql_query = "INSERT INTO captain VALUES('$id_new_cap', 'Crew Captain')";
           mysql_query( $sql_query, $db );

           //create new crew for the captain
           $sql_query = "INSERT INTO crew VALUES(NULL, '{$value[0]}', '$id_new_cap', '{$value[1]}')";
           mysql_query( $sql_query, $db );
           $id_new_crew = mysql_insert_id();

           //put captain in his new crew
           $sql_query = "INSERT INTO pirate_in_crew VALUES('$id_new_cap', '$id_new_crew')";
           mysql_query( $sql_query, $db );

           $dest = "main.php?msg=The+crew+and+captain+have+been+SUCCESSFULLY+created:+captains+login:+{$id_new_cap}+captains+password:+password";
           echo "<script> location.replace('$dest'); </script>";
           die();
        }
        // !!!!!!!!! THIS IS NOT EDITED !!!!!!!!!!!



    } else {
        for ($i=0; $i < count($input); $i++) {
            $value[$i] = "";
        }
    }

    $beard = array("black", "brown", "blonde", "red", "white", "gray");


    echo "		<section class='update_section'>
    				<h3>Crew:</h3>
    				<div class='update_field'>
    					<label for='crew_name'>*Name:</label>
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
                                if ($id_port == $value[1]){
                                    $str = "selected";
                                }

                                echo "<option $str value='$id_port'>$name</option>";
                            }


                            echo "
    					</select>
    				</div>
				</section>
				<section class='update_section'>
    				<h3>Captain:</h3>
    				<div class='update_field'>
    					<label for='captain_name'>*Full name:</label>
    					<input id='captain_name' type='text' name='captain_name' maxlength='29' value='{$value[2]}' placeholder='Captains name...'>
    				</div>
    				<div class='update_field'>
    					<label for='captain_nick'>*Nickname:</label>
    					<input id='captain_nick' type='text' name='captain_nick' maxlength='29' value='{$value[3]}'  placeholder='Captains nickname...'>
    				</div>
    				<div class='update_field'>
    					<label for='captain_birthdate'>*Date of birth:</label>
    					<input id='captain_birthdate' type='date' max='$today' value='{$value[4]}' name='captain_birthdate'>
    				</div>
    				<div class='update_field'>
    					<label for='captain_beard'>*Beard color:</label>
    					<select id='captain_beard' name='captain_beard'>";
    					$elem = $beard;

                        for ($i=0; $i < count($elem); $i++) {
                            $str ="";
                            if ($elem[$i] === $value[5]){
                                $str = "selected";
                            }
                            echo "<option $str value='{$elem[$i]}'>{$elem[$i]}</option>";
                        }

    					echo "
    					</select>
    				</div>
    				<div class='update_field'>
    					<label for='captain_character'>Characteristics:</label>
    					<input id='captain_character' type='text' name='captain_character' maxlength='254' value='{$value[6]}' placeholder='Captains characteristics...'>
    				</div>
				</section>";


    return $ERRmsg;
}

function addBoat()
{
    include 'db_init.php';
    $today = date("Y-m-d");

    //Am I the crew captain
    if($_SESSION['level'] != 1) {
        echo "<script> location.replace('main.php'); </script>";
        die();
    }

    $ERRmsg = "";

    $input = array( "boat_name",
                    "boat_type",
                    "boat_capacity",
                    "boat_port",
                    "boat_fleet",
                    "boat_captain");

    // Get crew id of current captain for future use
    $captain_id = $_SESSION['user'];
    $sql_query = "SELECT id_crew AS ID FROM crew WHERE id_captain=$captain_id";
    $retval = mysql_query( $sql_query, $db );
    $row = mysql_fetch_array($retval, MYSQL_ASSOC);
    $crew_id = $row['ID'];


    if(isset($_GET['add'])) {
        for ($i=0; $i < count($input); $i++) {
            //If this input is not set -> redirect
            if (!isset($_GET[$input[$i]])) {
                echo "<script> location.replace('main.php'); </script>";
                die();
            }

            $value[$i] = $_GET[$input[$i]];

            //obligatory
            if(($i < 3)) {
                if ($value[$i] === "") {
                    $ERRmsg = "You have to fill all obligatory fields. Thay are marked with '*'.";
                }
            }

            if(($i == 5)) {
                if ($value[$i] === "") {
                    $ERRmsg = "You have to fill all obligatory fields. Thay are marked with '*'. You dont have a valid captain! If thare is not valid pirate, you have to recruit the new one.";
                }
            }
        }

        //All obligatory fields have been filled -> we can add crew and captain
        if($ERRmsg === "") {

            if ($value[3] !== "NULL") {
                $value[3] = "'" . $value[3] . "'";
            }

            if ($value[4] !== "NULL") {
                $value[4] = "'" . $value[4] . "'";
            }

            $sql_query = "SET foreign_key_checks = 0;";
            if(!mysql_query( $sql_query, $db )) {
                die("Something wrong:0 " . mysql_error());
            }

            //put the new boat in the boat table
            $sql_query = "INSERT INTO boat VALUES(NULL, '{$value[0]}', '{$value[1]}', '{$value[2]}', {$value[3]}, {$value[4]}, '$crew_id', '{$value[5]}');";
            if(!mysql_query( $sql_query, $db )) {
                die("Something wrong:1 " . mysql_error());
            }
            $id_new_boat = mysql_insert_id();

            //move the pirate from common to captain table
            $sql_query = "DELETE FROM common_pirate WHERE id_pirate={$value[5]};";
            if(!mysql_query( $sql_query, $db )) {
                die("Something wrong:2 " . mysql_error());
            }
            $sql_query = "INSERT INTO captain VALUES('{$value[5]}', 'Boat Captain');";
            if(!mysql_query( $sql_query, $db )) {
                die("Something wrong:3 " . mysql_error());
            }

            //put the new captain on his boat
            $sql_query = "UPDATE pirate SET id_boat = '$id_new_boat' WHERE id_pirate = {$value[5]};";
            if(!mysql_query( $sql_query, $db )) {
                die("Something wrong:4 " . mysql_error());
            }

            $dest = "main.php?msg=The+boat+has+been+SUCCESSFULLY+created.";
            echo "<script> location.replace('$dest'); </script>";
            die();
        }

    } else {
        for ($i=0; $i < count($input); $i++) {
            $value[$i] = "";
        }
    }

    $type = array("Sloop", "Brigantine", "Frigate", "Spanish Galleon", "French Warship");

    echo "      <section class='update_section'>
                    <h3>Boat:</h3>
                    <div class='update_field'>
                        <label for='boat_name'>*Name:</label>
                        <input id='boat_name' type='text' name='boat_name' maxlength='29' value='{$value[0]}' placeholder='Boat name...'>
                    </div>
                    <div class='update_field'>
                        <label for='boat_type'>*Type:</label>
                        <select id='boat_type' name='boat_type'>";
                        $elem = $type;

                        for ($i=0; $i < count($elem); $i++) {
                            $str ="";
                            if ($elem[$i] === $value[1]){
                                $str = "selected";
                            }
                            echo "<option $str value='{$elem[$i]}'>{$elem[$i]}</option>";
                        }

                        echo "
                        </select>
                    </div>
                    <div class='update_field'>
                        <label for='boat_capacity'>*Capacity:</label>
                        <input id='boat_capacity' type='number' min='1' name='boat_capacity' value='{$value[2]}' placeholder='Capacity...'>
                    </div>
                    <div class='update_field'>
                        <label for='boat_port'>Port:</label>
                        <select id='boat_port' name='boat_port'>";
                            // NONE option
                            echo "<option $str value='NULL'>NONE</option>";

                            // Query to get all ports
                            $sql_query = "SELECT id_port AS ID, name AS NAME, capacity AS CAPACITY
                                          FROM port";
                            $retval = mysql_query( $sql_query, $db );

                            //database  error
                            if (!$retval) {
                                die('Could not get data: ' . mysql_error());
                            }

                            while($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
                                // Get data
                                $id_port = $row['ID'];
                                $name = $row['NAME'];
                                $capacity = $row['CAPACITY'];

                                // Check if port is free
                                $free  = freePort($id_port, $capacity, $db);

                                if ($free > 0) {
                                    $str = "";
                                    if ($id_port == $value[3]){
                                        $str = "selected";
                                    }
                                    echo "<option $str value='$id_port'>$name</option>";
                                }
                            }


                            echo "
                        </select>
                    </div>
                    <div class='update_field'>
                        <label for='boat_fleet'>Fleet:</label>
                        <select id='boat_fleet' name='boat_fleet'>";
                                // NONE option
                                echo "<option $str value='NULL'>NONE</option>";

                            // Query to get all crew fleets
                            $sql_query = "SELECT fleet.id_fleet AS ID, fleet.name AS NAME
                                          FROM (((fleet INNER JOIN captain ON fleet.id_captain = captain.id_pirate)
                                          INNER JOIN pirate ON captain.id_pirate = pirate.id_pirate)
                                          INNER JOIN pirate_in_crew ON pirate.id_pirate = pirate_in_crew.id_pirate)
                                          WHERE pirate_in_crew.id_crew = $crew_id";
                            $retval = mysql_query( $sql_query, $db );

                            //database  error
                            if (!$retval) {
                                die('Could not get data: ' . mysql_error());
                            }

                            while($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
                                // Get data
                                $id_fleet = $row['ID'];
                                $name = $row['NAME'];

                                $str = "";
                                if ($id_fleet == $value[4]){
                                    $str = "selected";
                                }
                                echo "<option $str value='$id_fleet'>$name</option>";

                            }


                            echo "
                        </select>
                    </div>
                    <div class='update_field'>
                        <label for='boat_captain'>*Captain:</label>
                        <select id='boat_captain' name='boat_captain'>";
                            // NONE option
                            echo "<option $str value=''>NONE</option>";

                            // Query to get all common crew pirates
                            $sql_query = "SELECT pirate.id_pirate AS ID, pirate.name AS NAME
                                          FROM ((pirate INNER JOIN common_pirate ON pirate.id_pirate = common_pirate.id_pirate)
                                          INNER JOIN pirate_in_crew ON pirate.id_pirate = pirate_in_crew.id_pirate)
                                          WHERE pirate_in_crew.id_crew = $crew_id";
                            $retval = mysql_query( $sql_query, $db );

                            //database  error
                            if (!$retval) {
                                die('Could not get data: ' . mysql_error());
                            }

                            while($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
                                // Get data
                                $id_pirate = $row['ID'];
                                $name = $row['NAME'];

                                $str = "";
                                if ($id_pirate == $value[5]){
                                    $str = "selected";
                                }
                                echo "<option $str value='$id_pirate'>$name</option>";

                            }


                            echo "
                        </select>
                    </div>
                </section>";


    return $ERRmsg;
}

function addPort()
{
    include 'db_init.php';
    $today = date("Y-m-d");

    //Am I admin
    if($_SESSION['level'] != 0) {
        echo "<script> location.replace('main.php'); </script>";
        die();
    }

    $ERRmsg = "";

    $input = array( "port_name",
                    "port_capacity",
                    "port_place");

    if(isset($_GET['add'])) {
        for ($i=0; $i < count($input); $i++) {
            //If this input is not set -> redirect
            if (!isset($_GET[$input[$i]])) {
                echo "<script> location.replace('main.php'); </script>";
                die();
            }

            $value[$i] = $_GET[$input[$i]];

            //obligatory
            if ($value[$i] === "") {
                $ERRmsg = "You have to fill all obligatory fields. They are marked with '*'.";
            }
        }

        //All obligatory fields have been filled -> we can add port
        if($ERRmsg === "") {

            //put the new boat in the boat table
            $sql_query = "INSERT INTO port VALUES(NULL, '{$value[0]}', '{$value[1]}', '{$value[2]}');";
            mysql_query( $sql_query, $db );

            $dest = "main.php?msg=The+dock+has+been+SUCCESSFULLY+created.";
            echo "<script> location.replace('$dest'); </script>";
            die();
        }

    } else {
        for ($i=0; $i < count($input); $i++) {
            $value[$i] = "";
        }
    }

    echo "      <section class='update_section'>
                    <h3>Port:</h3>
                    <div class='update_field'>
                        <label for='port_name'>*Name:</label>
                        <input id='port_name' type='text' name='port_name' maxlength='29' value='{$value[0]}' placeholder='Port name...'>
                    </div>
                    <div class='update_field'>
						<label for='port_capacity'>*Capacity:</label>
						<input id='port_capacity' type='number' min='1' name='port_capacity' value='{$value[1]}' placeholder='Capacity...'>
					</div>
					<div class='update_field'>
						<label for='port_place'>*Location:</label>
						<input id='port_place' type='text' name='port_place' maxlength='29' value='{$value[2]}' placeholder='Name of location...'>
					</div>
                </section>";


    return $ERRmsg;
}

//function for adding the fleets
function addFleet()
{
    include 'db_init.php';

    //Am I crew Captain
    if($_SESSION['level'] != 1) {
        echo "<script> location.replace('main.php'); </script>";
        die();
    }

    $ERRmsg = "";

    $input = array( "fleet_name",
                    "fleet_captain");
                    //and also a lot of fleet_boats

    // Get crew id of current captain for future use
    $captain_id = $_SESSION['user'];
    $sql_query = "SELECT id_crew AS ID FROM crew WHERE id_captain=$captain_id";
    $retval = mysql_query( $sql_query, $db );
    $row = mysql_fetch_array($retval, MYSQL_ASSOC);
    $crew_id = $row['ID'];

    if(isset($_GET['add'])) {

        for ($i=0; $i < 2; $i++) {
            //If this input is not set -> redirect
            if (!isset($_GET[$input[$i]])) {
                echo "<script> location.replace('main.php'); </script>";
                die();
            }

            $value[$i] = $_GET[$input[$i]];

            //obligatory
            if ($value[$i] === "") {
                $ERRmsg = "You have to fill all obligatory fields. They are marked with '*'.";
            }
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
                $value[2][$c] = $_GET["fleet_boat{$i}"];
                $c = $c + 1;
            }
        }

        //Fleats Has to consists at least of 2 boats
        if($c < 2) {
            $ERRmsg = $ERRmsg . " You have to choos at least 2 boats. If there is less then that, go to create boat!";
        }

        //All obligatory fields have been filled -> we can add port
        if($ERRmsg === "") {
            //move the pirate from common to captain table
            $sql_query = "DELETE FROM common_pirate WHERE id_pirate={$value[1]};";
            if(!mysql_query( $sql_query, $db )) {
                die("Something wrong:2 " . mysql_error());
            }
            $sql_query = "INSERT INTO captain VALUES('{$value[1]}', 'Fleet Captain');";
            if(!mysql_query( $sql_query, $db )) {
                die("Something wrong:3 " . mysql_error());
            }

            //put the new fleet into the table
            $sql_query = "INSERT INTO fleet VALUES(NULL, '{$value[0]}', '{$value[1]}');";
            mysql_query( $sql_query, $db );
            $id_fleet = mysql_insert_id();

            for ($i=0; $i < $c ; $i++) {
                $sql_query = "UPDATE boat SET id_fleet = '$id_fleet' WHERE id_boat = {$value[2][$i]}";
                mysql_query( $sql_query, $db );
            }

            $dest = "main.php?msg=The+fleet+has+been+SUCCESSFULLY+created.";
            echo "<script> location.replace('$dest'); </script>";
            die();
        }

    } else {
        for ($i=0; $i < count($input); $i++) {
            $value[$i] = "";
        }
    }

    echo "      <section class='update_section'>
                    <h3>Fleet:</h3>
                    <div class='update_field'>
                        <label for='fleet_name'>*Name:</label>
                        <input id='fleet_name' type='text' name='fleet_name' maxlength='29' value='{$value[0]}' placeholder='Fleet name...'>
                    </div>
                    <div class='update_field'>
                        <label for='fleet_captain'>*Captain:</label>
                        <select id='fleet_captain' name='fleet_captain'>";

                            // Query to get all common crew pirates
                            $sql_query = "SELECT pirate.id_pirate AS ID, pirate.name AS NAME
                                          FROM ((pirate INNER JOIN common_pirate ON pirate.id_pirate = common_pirate.id_pirate)
                                          INNER JOIN pirate_in_crew ON pirate.id_pirate = pirate_in_crew.id_pirate)
                                          WHERE pirate_in_crew.id_crew = $crew_id";
                            $retval = mysql_query( $sql_query, $db );

                            //database  error
                            if (!$retval) {
                                die('Could not get data: ' . mysql_error());
                            }

                            while($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
                                // Get data
                                $id_pirate = $row['ID'];
                                $name = $row['NAME'];

                                $str = "";
                                if ($id_pirate == $value[1]){
                                    $str = "selected";
                                }
                                echo "<option $str value='$id_pirate'>$name</option>";
                            }

                            echo "
                        </select>
                    </div>
                    <div class='update_field'>
                        <label for='fleet_boats'>*Boats:</label>
                        <div id='fleet_boats' class='checkbox_group'>";

                            // Query to get all crew boats, which havent a fleet
                            $sql_query = "SELECT id_boat AS ID, name AS NAME
                                          FROM boat
                                          WHERE id_crew = $crew_id
                                          AND id_fleet IS NULL";
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

                                $str = "";
                                if (isset($value[2][$c])) {
                                    if ($id_boat == $value[2][$c]){
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

function addBattle()
{
    include 'db_init.php';

    //Am I the admin
    if($_SESSION['level'] != 0) {
        echo "<script> location.replace('main.php'); </script>";
        die();
    }

    $today = date("Y-m-d");

    $ERRmsg = "";

    $input = array( "battle_winner",
                    "battle_losses",
                    "battle_date",
                    "battle_port");
                    //and also crews
                    //and also boats

    if(isset($_GET['add'])) {

        for ($i=0; $i < 4; $i++) {
            //If this input is not set -> redirect
            if (!isset($_GET[$input[$i]])) {
                echo "<script> location.replace('main.php'); </script>";
                die();
            }

            $value[$i] = $_GET[$input[$i]];

            //obligatory
            if ($i < 3) {
                if ($value[$i] === "") {
                    $ERRmsg = "You have to fill all obligatory fields. They are marked with '*'.";
                }
            }
        }

        // CREWS
        if (!isset($_GET['number_of_crews'])) {
            echo "<script> location.replace('main.php'); </script>";
            die();
        }

        $num = $_GET['number_of_crews'];

        //get number of crews
        $c = 0;
        $win_check = 0;
        for ($i = 0; $i < $num; $i++) {
            if (isset($_GET["battle_crew{$i}"])) {
                $value[4][$c] = $_GET["battle_crew{$i}"];
                if ($value[0] == $value[4][$c]) {
                    $win_check = 1;
                }
                $c += 1;
            }
        }

        //At least one crew has to be in battle
        if($c < 1) {
            $ERRmsg = $ERRmsg . " You have to choose at least 1 crew!";
        }

        //The winning crew must be selected as participant
        if($win_check != 1) {
            $ERRmsg = $ERRmsg . " You must select the winning crew as participant!";
        }

        // BOATS
        if (!isset($_GET['number_of_boats'])) {
            echo "<script> location.replace('main.php'); </script>";
            die();
        }

        $num = $_GET['number_of_boats'];

        //get number of boats
        $b = 0;
        for ($i = 0; $i < $num; $i++) {
            if (isset($_GET["battle_boat{$i}"])) {
                $value[5][$b] = $_GET["battle_boat{$i}"];
                $b += 1;
            }
        }

        //Battle has to be held on at least one boat
        if($b < 1) {
            $ERRmsg = $ERRmsg . " You have to choose at least 1 boat!";
        }

        //Check if chosen boats belong to chosen crews
        for ($i=0; $i < $b; $i++) {
            // get boats crew id
            $sql_query = "SELECT id_crew AS CREW FROM boat WHERE id_boat = {$value[5][$i]};";
            $retval = mysql_query( $sql_query, $db );
            $row = mysql_fetch_array($retval, MYSQL_ASSOC);
            $boat_crew = $row['CREW'];

            // check if it matches any of the selected crews
            $it_matches = 0;
            for ($j=0; $j < $c; $j++) {
                if ($value[4][$j] == $boat_crew) {
                    $it_matches = 1;
                    break;
                }
            }
            if ($it_matches != 1) {
                $ERRmsg = $ERRmsg . " You can only choose boats that belong to participated crews!";
                break;
            }
        }

        //All obligatory fields have been filled -> we can add port
        if($ERRmsg === "") {
            //put the new battle in the battle table
            $sql_query = "INSERT INTO battle VALUES(NULL, '{$value[0]}', '{$value[1]}', '{$value[2]}', {$value[3]});";
            mysql_query( $sql_query, $db );
            $id_battle = mysql_insert_id();

            for ($i=0; $i < $c ; $i++) {
                $sql_query = "INSERT INTO crew_participated_in_battle VALUES({$value[4][$i]}, $id_battle);";
                mysql_query( $sql_query, $db );
            }

            for ($i=0; $i < $b ; $i++) {
                $sql_query = "INSERT INTO boat_participated_in_battle VALUES({$value[5][$i]}, $id_battle);";
                mysql_query( $sql_query, $db );
            }

            $dest = "main.php?msg=The+battle+has+been+SUCCESSFULLY+recorded.";
            echo "<script> location.replace('$dest'); </script>";
            die();
        }

    } else {
        for ($i=0; $i < count($input); $i++) {
            $value[$i] = "";
        }
    }

    echo "      <section class='update_section'>
                    <h3>Battle:</h3>
                    <div class='update_field'>
                        <label for='battle_winner'>*Winning crew:</label>
                        <select id='battle_winner' name='battle_winner'>";

                            // Query to get all crews
                            $sql_query = "SELECT id_crew AS ID, name AS NAME
                                          FROM crew";
                            $retval = mysql_query( $sql_query, $db );

                            //database  error
                            if (!$retval) {
                                die('Could not get data: ' . mysql_error());
                            }

                            while($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
                                $id_crew = $row['ID'];
                                $name = $row['NAME'];

                                $str = "";
                                if ($id_crew == $value[0]){
                                    $str = "selected";
                                }

                                echo "<option $str value='$id_crew'>$name</option>";
                            }
                        echo"
                        </select>
                    </div>
                    <div class='update_field'>
                        <label for='battle_losses'>*Losses:</label>
                        <input id='battle_losses' type='number' min='0' name='battle_losses' value='{$value[1]}' placeholder='Number of dead pirates...'>
                    </div>
                    <div class='update_field'>
                        <label for='battle_date'>*Date:</label>
                        <input id='battle_date' type='date' max='$today' value='{$value[2]}' name='battle_date'>
                    </div>
                    <div class='update_field'>
                        <label for='battle_port'>Port:</label>
                        <select id='battle_port' name='battle_port'>";
                            // ON SEA option
                            echo "<option $str value='NULL'>NONE</option>";
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
                                if ($id_port == $value[3]){
                                    $str = "selected";
                                }

                                echo "<option $str value='$id_port'>$name</option>";
                            }
                        echo"
                        </select>
                    </div>
                    <div class='update_field'>
                        <label for='battle_boats'>*Crews:</label>
                        <div id='battle_boats' class='checkbox_group'>";

                            // Query to get all crews
                            $sql_query = "SELECT id_crew AS ID, name AS NAME
                                          FROM crew";
                            $retval = mysql_query( $sql_query, $db );

                            //database  error
                            if (!$retval) {
                                die('Could not get data: ' . mysql_error());
                            }

                            $c = 0;
                            for ($i=0; $row = mysql_fetch_array($retval, MYSQL_ASSOC) ;$i++) {
                                // Get data
                                $id_crew = $row['ID'];
                                $name = $row['NAME'];

                                $str = "";
                                if (isset($value[4][$c])) {
                                    if ($id_crew == $value[4][$c]){
                                        $str = "checked";
                                        $c++;
                                    }
                                }

                                echo "<input $str type='checkbox' name='battle_crew{$i}' value='$id_crew'>$name<br>";
                            }

                            echo "<input type='hidden' name='number_of_crews' value='$i'>";

                            echo "
                        </div>
                    </div>
                    <div class='update_field'>
                        <label for='battle_boats'>*Boats:</label>
                        <div id='battle_boats' class='checkbox_group'>";

                            // Query to get all boats
                            $sql_query = "SELECT id_boat AS ID, name AS NAME
                                          FROM boat";
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

                                $str = "";
                                if (isset($value[5][$c])) {
                                    if ($id_boat == $value[5][$c]){
                                        $str = "checked";
                                        $c++;
                                    }
                                }

                                echo "<input $str type='checkbox' name='battle_boat{$i}' value='$id_boat'>$name<br>";
                            }

                            echo "<input type='hidden' name='number_of_boats' value='$i'>";

                            echo "
                        </div>
                    </div>
                </section>";


    return $ERRmsg;
}


?>
