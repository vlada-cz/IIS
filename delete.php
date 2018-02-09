<?php
    include 'check.php';

    function deletePirate()
    {
        include 'db_init.php';

        //I have to know the ID of pirate
        if (!isset($_GET['id_pirate'])) {
            header('LOCATION:main.php');
            die();
        }

        //get pirate id from url
        $id_pirate = $_GET['id_pirate'];

        //Level of privilages
        $pirate_level = getPiratePrivilages($id_pirate, $db);

        //Do I have a permition for delete?
        if ($_SESSION['level'] >= $pirate_level) {
            header('LOCATION:main.php');
            die();
        }

        //If this pirate is a only a normal pirate you can delete
        if(getPirateLevel($id_pirate, $db) != 4) {
            header('LOCATION:main.php');
            die();
        }

        //Ok so, we know, that pirate is normal and we have a permition, so -> delete him
        //Delete from pirate_in_crew table
        //Delete from common_pirate
        //Delete form pirate
        $table = array("pirate_in_crew", "common_pirate", "pirate");
        for ($i=0; $i < count($table); $i++) {
            $str = $table[$i];
            $sql_query = "  DELETE FROM $str WHERE id_pirate=$id_pirate";

            if (!mysql_query($sql_query, $db)) {
                header('LOCATION:main.php?msg=Something+WRONG+with+database!!+The+pirate+has+NOT+been+successfully+deleted!');
                die();
            }
        }

        header('LOCATION:main.php?msg=The+pirate+has+been+SUCCESSFULLY+deleted!');
        die();

    }


    //main body
    switch ($_SESSION['menu']) {
        case "pirates":
            deletePirate();
            break;

        default:
            header('LOCATION:main.php');
            die();
            break;
    }



?>
