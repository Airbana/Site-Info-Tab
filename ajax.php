<?php
	if(isset($_GET['tid']))
	{
    	$fbTabID = $_GET['tid'];

        require './libs/facebook.php';
        require './libs/infotab.class.php';
        require './libs/db.php';

        $infotab = new infotab();
        $UpdateResult = $infotab->Toggle($fbTabID,$_GET['type']);
	}
	else
	{
		header("HTTP/1.0 400 Bad Request");
	}
?>
