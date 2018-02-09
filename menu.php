<?php

    function setMenu($selected) {
        //TODO settings

        $name = $_SESSION['name'];
        $id = $_SESSION['user'];

        echo "      <!DOCTYPE html>
                    <html>
                    <head>
                    	<title>PiratIS</title>
                    	<meta charset='utf-8'>
                    	<link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
                    	<link rel='stylesheet' type='text/css' href='styles/main.css'>
                    	<link rel='shortcut icon' type='image/x-icon' href='favicon.ico' />
                    </head>
                    <body>

                    	<nav>
                    		<div id='menu_logo' class='menu_block'>
                    			<div class='menu_block_padd'></div>
                    			<img class='menu_block_content' src='img/menu/logo.png'>
                    			<div class='menu_block_padd'></div>
                    		</div>
                    		<a class='menu_link"; if ($selected === "pirates") {echo " selected";} echo "' href='pirates.php'>
                    			<div class='menu_block'>
                    				<div class='menu_block_padd'></div>
                    				<div class='menu_block_content'>
                    					<div class='menu_icon_block'>
                    						<img class='menu_icon' src='img/menu/pirates.png'>
                    					</div>
                    					<p class='menu_text'>Pirates</p>
                    				</div>
                    				<div class='menu_block_padd'></div>
                    			</div>
                    		</a>
                    		<a class='menu_link"; if ($selected === "crews") {echo " selected";} echo "' href='crews.php'>
                    			<div class='menu_block'>
                    				<div class='menu_block_padd'></div>
                    				<div class='menu_block_content'>
                    					<div class='menu_icon_block'>
                    						<img class='menu_icon' src='img/menu/crews.png'>
                    					</div>
                    					<p class='menu_text'>Crews</p>
                    				</div>
                    				<div class='menu_block_padd'></div>
                    			</div>
                    		</a>
                    		<a class='menu_link"; if ($selected === "boats") {echo " selected";} echo "' href='boats.php'>
                    			<div class='menu_block'>
                    				<div class='menu_block_padd'></div>
                    				<div class='menu_block_content'>
                    					<div class='menu_icon_block'>
                    						<img class='menu_icon' src='img/menu/boats.png'>
                    					</div>
                    					<p class='menu_text'>Boats</p>
                    				</div>
                    				<div class='menu_block_padd'></div>
                    			</div>
                    		</a>
                            <a class='menu_link"; if ($selected === "fleets") {echo " selected";} echo "' href='fleets.php'>
                                <div class='menu_block'>
                                    <div class='menu_block_padd'></div>
                                    <div class='menu_block_content'>
                                        <div class='menu_icon_block'>
                                            <img class='menu_icon' src='img/menu/fleets.png'>
                                        </div>
                                        <p class='menu_text'>Fleets</p>
                                    </div>
                                    <div class='menu_block_padd'></div>
                                </div>
                            </a>
                    		<a class='menu_link"; if ($selected === "ports") {echo " selected";} echo "' href='docks.php'>
                    			<div class='menu_block'>
                    				<div class='menu_block_padd'></div>
                    				<div class='menu_block_content'>
                    					<div class='menu_icon_block'>
                    						<img class='menu_icon' src='img/menu/docks.png'>
                    					</div>
                    					<p class='menu_text'>Ports</p>
                    				</div>
                    				<div class='menu_block_padd'></div>
                    			</div>
                    		</a>
                    		<a class='menu_link"; if ($selected === "battles") {echo " selected";} echo "' href='battles.php'>
                    			<div class='menu_block'>
                    				<div class='menu_block_padd'></div>
                    				<div class='menu_block_content'>
                    					<div class='menu_icon_block'>
                    						<img class='menu_icon' src='img/menu/battles.png'>
                    					</div>
                    					<p class='menu_text'>Battles</p>
                    				</div>
                    				<div class='menu_block_padd'></div>
                    			</div>
                    		</a>
                    		<a class='menu_link menu_float' href='logout.php'>
                    			<div class='menu_block'>
                    				<div class='menu_block_padd'></div>
                    				<div class='menu_block_content'>
                    					<div class='menu_icon_block'>
                    						<img class='menu_icon' src='img/menu/logout.png'>
                    					</div>
                    					<p class='menu_text'>Log out</p>
                    				</div>
                    				<div class='menu_block_padd'></div>
                    			</div>
                    		</a>"; if ($_SESSION['level'] != 0) {echo "
                    		<a class='menu_link menu_float"; if ($selected === "settings") {echo " selected";} echo "' href='settings.php'>
                    			<div class='menu_block'>
                    				<div class='menu_block_padd'></div>
                    				<div class='menu_block_content'>
                    					<div class='menu_icon_block'>
                    						<img class='menu_icon' src='img/menu/settings.png'>
                    					</div>
                    					<p class='menu_text'>Settings</p>
                    				</div>
                    				<div class='menu_block_padd'></div>
                    			</div>
                    		</a> ";} echo"
                            <div id='logged_person'>
                    			<div></div>
                    			<span>Welcome $name ($id)</span>
                    		</div>
                    	</nav>";
    }

    //TODO admin couldnt change password -> no settngs :D

?>
