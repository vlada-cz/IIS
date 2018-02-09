<?php

function printSitebox($what) {
    switch ($what) {
        case "pirates":
            printSitePirates();
            break;

        case "crews":
            printSiteCrews();
            break;

        case "boats":
            printSiteBoats();
            break;

        case "ports":
            printSitePorts();
            break;

        case "battles":
            printSiteBattles();
            break;

        case "fleets":
            printSiteFleets();
            break;
    }
}

function printSitePirates()
{
    echo "  <div class='form_box'>
                <h3>Filter:</h3>
                <form>";


    //get filter_rank
    if(isset($_GET['filter_rank'])) {
        $filter_rank = $_GET['filter_rank'];
        $_SESSION['filter']['filter_rank'] = $filter_rank;
    } else {
        $filter_rank = $_SESSION['filter']['filter_rank'];
    }

    $ar = array("Any", "Crew Captain", "Fleet Captain", "Boat Captain", "Master Gunner", "Bootswain", "ABS", "Swab");
    //print filter rank
    echo "  <div class='form_field'>
                <label for='filter_rank'>Rank:</label>
                <select id='filter_rank' name='filter_rank'>";

    for ($i=0; $i < count($ar) ; $i++) {
        //if it is selected
        $sel = "";
        if($filter_rank === $ar[$i]){
            $sel = " selected ";
        }
        //print this option
        echo "<option $sel value='{$ar[$i]}'>{$ar[$i]}</option>";
    }
    // close  this div
    echo "      </select>
            </div>";




    //get filter_beard
    if(isset($_GET['filter_beard'])) {
        $filter_beard = $_GET['filter_beard'];
        $_SESSION['filter']['filter_beard'] = $filter_beard;
    } else {
        $filter_beard = $_SESSION['filter']['filter_beard'];
    }

    $ar = array("Any","black", "brown", "blonde", "red", "white", "gray");
    //print filter beard
    echo "  <div class='form_field'>
                <label for='filter_beard'>Beard:</label>
                <select id='filter_beard' name='filter_beard'>";

    for ($i=0; $i < count($ar) ; $i++) {
        //if it is selected
        $sel = "";
        if($filter_beard === $ar[$i]){
            $sel = " selected ";
        }
        //print this option
        echo "<option $sel value='{$ar[$i]}'>{$ar[$i]}</option>";
    }
    // close  this div
    echo "      </select>
            </div>";

    //show submit button
    echo "  <div class='form_submit'>
                <input type='submit' name='filter_submit' value='Filter'>
            </div>
        </form>
    </div>";


    //print sort header (container)
    echo "  <div class='form_box'>
                <h3>Sort:</h3>
                <form>
                    <div class='form_field'>
                        <label for='sort_type'>Sort by:</label>
                        <select id='sort_type' name='sort_type'>";

    //get sort value
    if(isset($_GET['sort_type'])) {
        $sort = $_GET['sort_type'];
        $_SESSION['sort']= $sort;
    } else {
        $sort = $_SESSION['sort'];
    }

    echo "<option "; if($sort === " ORDER BY pirate.name") {echo " selected ";} echo" value=' ORDER BY pirate.name'>Name</option>";
    echo "<option "; if($sort === " ORDER BY pirate.nick") {echo " selected ";} echo" value=' ORDER BY pirate.nick'>Nick</option>";
    echo "<option "; if($sort === " ORDER BY pirate.date_of_birth") {echo " selected ";} echo" value=' ORDER BY pirate.date_of_birth'>Age</option>";

    echo "     </select>
            </div>
            <div class='form_submit'>
                <input type='submit' name='sort_submit' value='Sort'>
            </div>
        </form>
    </div>";
}

//print site box for crew
function printSiteCrews()
{
    //print sort header (container)
    echo "  <div class='form_box'>
                <h3>Sort:</h3>
                <form>
                    <div class='form_field'>
                        <label for='sort_type'>Sort by:</label>
                        <select id='sort_type' name='sort_type'>";

    //get sort value
    if(isset($_GET['sort_type'])) {
        $sort = $_GET['sort_type'];
        $_SESSION['sort']= $sort;
    } else {
        $sort = $_SESSION['sort'];
    }

    echo "<option "; if($sort === " ORDER BY crew.name") {echo " selected ";} echo" value=' ORDER BY crew.name'>Name</option>";
    echo "<option "; if($sort === " ORDER BY port.name") {echo " selected ";} echo" value=' ORDER BY port.name'>Port</option>";

    echo "     </select>
            </div>
            <div class='form_submit'>
                <input type='submit' name='sort_submit' value='Sort'>
            </div>
        </form>
    </div>";
}

//print site box for Boats
function printSiteBoats()
{
    echo "  <div class='form_box'>
                <h3>Filter:</h3>
                <form>";


    //get filter_rank
    if(isset($_GET['filter_type'])) {
        $filter_type = $_GET['filter_type'];
        $_SESSION['filter']['filter_type'] = $filter_type;
    } else {
        $filter_type = $_SESSION['filter']['filter_type'];
    }

    //arra of possible type of boats
    $ar = array("Any", "Sloop", "Brigantine", "Frigate", "Spanish Galleon", "French Warship");

    //print filter rank
    echo "  <div class='form_field'>
                <label for='filter_type'>Type:</label>
                <select id='filter_type' name='filter_type'>";

    for ($i=0; $i < count($ar) ; $i++) {
        //if it is selected
        $sel = "";
        if($filter_type === $ar[$i]){
            $sel = " selected ";
        }
        //print this option
        echo "<option $sel value='{$ar[$i]}'>{$ar[$i]}</option>";
    }
    // close  this div
    echo "      </select>
            </div>";


    //show submit button
    echo "  <div class='form_submit'>
                <input type='submit' name='filter_submit' value='Filter'>
            </div>
        </form>
    </div>";


    //print sort header (container)
    echo "  <div class='form_box'>
                <h3>Sort:</h3>
                <form>
                    <div class='form_field'>
                        <label for='sort_type'>Sort by:</label>
                        <select id='sort_type' name='sort_type'>";

    //get sort value
    if(isset($_GET['sort_type'])) {
        $sort = $_GET['sort_type'];
        $_SESSION['sort']= $sort;
    } else {
        $sort = $_SESSION['sort'];
    }

    echo "<option "; if($sort === " ORDER BY boat.name") {echo " selected ";} echo" value=' ORDER BY boat.name'>Name</option>";
    echo "<option "; if($sort === " ORDER BY boat.capacity") {echo " selected ";} echo" value=' ORDER BY boat.capacity'>Capacity</option>";

    echo "     </select>
            </div>
            <div class='form_submit'>
                <input type='submit' name='sort_submit' value='Sort'>
            </div>
        </form>
    </div>";
}

//print site box for fleets
function printSiteFleets()
{
    //print sort header (container)
    echo "  <div class='form_box'>
                <h3>Sort:</h3>
                <form>
                    <div class='form_field'>
                        <label for='sort_type'>Sort by:</label>
                        <select id='sort_type' name='sort_type'>";

    //get sort value
    if(isset($_GET['sort_type'])) {
        $sort = $_GET['sort_type'];
        $_SESSION['sort']= $sort;
    } else {
        $sort = $_SESSION['sort'];
    }

    echo "<option "; if($sort === " ORDER BY fleet.name") {echo " selected ";} echo" value=' ORDER BY fleet.name'>Name</option>";

    echo "     </select>
            </div>
            <div class='form_submit'>
                <input type='submit' name='sort_submit' value='Sort'>
            </div>
        </form>
    </div>";
}

//print site box for ports
function printSitePorts()
{
    //print sort header (container)
    echo "  <div class='form_box'>
                <h3>Sort:</h3>
                <form>
                    <div class='form_field'>
                        <label for='sort_type'>Sort by:</label>
                        <select id='sort_type' name='sort_type'>";

    //get sort value
    if(isset($_GET['sort_type'])) {
        $sort = $_GET['sort_type'];
        $_SESSION['sort']= $sort;
    } else {
        $sort = $_SESSION['sort'];
    }

    echo "<option "; if($sort === " ORDER BY port.name") {echo " selected ";} echo" value=' ORDER BY port.name'>Name</option>";
    echo "<option "; if($sort === " ORDER BY port.place") {echo " selected ";} echo" value=' ORDER BY port.place'>Location name</option>";
    echo "<option "; if($sort === " ORDER BY port.capacity") {echo " selected ";} echo" value=' ORDER BY port.capacity'>Capacity</option>";


    echo "     </select>
            </div>
            <div class='form_submit'>
                <input type='submit' name='sort_submit' value='Sort'>
            </div>
        </form>
    </div>";
}

//print site box for battles
function printSiteBattles()
{
    $servername = "localhost:/var/run/mysql/mysql.sock";
    $username = "xjerab21";
    $password = "oruvuru5";
    $dbname = "xjerab21";

    $db = mysql_connect($servername, $username, $password);
    if (!$db) die('Could not connect: '.mysql_error());
    if (!mysql_select_db($dbname, $db)) die('Database is not available: '.mysql_error());

    echo "  <div class='form_box'>
                <h3>Filter:</h3>
                <form>";



    //get filter_place
    if(isset($_GET['filter_place'])) {
        $filter_place = $_GET['filter_place'];
        $_SESSION['filter']['filter_place'] = $filter_place;
    } else {
        $filter_place = $_SESSION['filter']['filter_place'];
    }

    $i = 0;
    $ar[$i] = "Any";
    $i = $i + 1;

    // Query -> get all places
    $sql_query = " SELECT name AS NAME
                   FROM port";
    $retval = mysql_query( $sql_query, $db);

    //database  error
    if (!$retval) {
        die('Could not get data: ' . mysql_error());
    }

    //Print every records
    while($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
        $ar[$i] = $row['NAME'];
        $i = $i + 1;
    }

    $ar[$i] = "ON SEA";


    //print filter place
    echo "  <div class='form_field'>
                <label for='filter_place'>Location:</label>
                <select id='filter_place' name='filter_place'>";

    for ($i=0; $i < count($ar) ; $i++) {
        //if it is selected
        $sel = "";
        if($filter_place === $ar[$i]){
            $sel = " selected ";
        }
        //print this option
        echo "<option $sel value='{$ar[$i]}'>{$ar[$i]}</option>";
    }
    // close  this div
    echo "      </select>
            </div>";


    //show submit button
    echo "  <div class='form_submit'>
                <input type='submit' name='filter_submit' value='Filter'>
            </div>
        </form>
    </div>";


    //print sort header (container)
    echo "  <div class='form_box'>
                <h3>Sort:</h3>
                <form>
                    <div class='form_field'>
                        <label for='sort_type'>Sort by:</label>
                        <select id='sort_type' name='sort_type'>";

    //get sort value
    if(isset($_GET['sort_type'])) {
        $sort = $_GET['sort_type'];
        $_SESSION['sort']= $sort;
    } else {
        $sort = $_SESSION['sort'];
    }

    echo "<option "; if($sort === " ORDER BY battle.date_happened") {echo " selected ";} echo" value=' ORDER BY battle.date_happened'>Date</option>";
    echo "<option "; if($sort === " ORDER BY battle.losses") {echo " selected ";} echo" value=' ORDER BY battle.losses'>Number of dead</option>";

    echo "     </select>
            </div>
            <div class='form_submit'>
                <input type='submit' name='sort_submit' value='Sort'>
            </div>
        </form>
    </div>";
}
