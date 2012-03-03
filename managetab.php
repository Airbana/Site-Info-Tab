<?php
/*
* Copyright (C) 2012 - Gareth Llewellyn
*
* This file is part of the Airbana Site Info Tab Project
*
* This program is free software: you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful, but WITHOUT
* ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
* FOR A PARTICULAR PURPOSE. See the GNU General Public License
* for more details.
*
* You should have received a copy of the GNU General Public License along with
* this program. If not, see <http://www.gnu.org/licenses/>
*/
	require './libs/facebook.php';
	require './libs/infotab.class.php';
	require './libs/db.php';
	
	$app_id = '****APP ID****';
	
	$SiteName = $_POST['SiteName'];
	$SiteID = $_POST['SiteID'];
	$fbTabID = $_POST['fbTabID'];
	
	if(isset($_GET['fbTabID']))
		$fbTabID = $_GET['fbTabID'];
	
	if(isset($_GET['SiteName']))
		$SiteName = $_GET['SiteName'];
	
	$infotab = new infotab();
	
	if($_POST['firstAdd'] == 'true')
	$infotab->AssignSiteToTab($_POST['fbTabID'],$_POST['SiteID'],$_POST['SiteName']);
	
	$TabInfo = $infotab->GetTabInfo($fbTabID);


?><!DOCTYPE html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
<title>php-sdk</title>
<link type="text/css" rel="stylesheet" href="css/style.css" />
<link type="text/css" rel="stylesheet" href="css/fb-buttons.css" />
<link type="text/css" rel="stylesheet"
	href="css/redmond/jquery-ui-1.8.18.custom.css" />
<script
	src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="js/jquery-ui-1.8.18.custom.min.js"></script>
<script src="js/managetab.js"></script>
</head>
<body>
	<div id="fb-root"></div>
	<script src="http://connect.facebook.net/en_US/all.js"></script>
	<script>
var gCan=[<?php echo $fbTabID; ?>,"<?php echo md5(date('y-m-d')); ?>",<?php echo $SiteID; ?>];
FB.init({
appId : '<?php echo $app_id; ?>',
status : true, // check login status
cookie : true, // enable cookies to allow the server to access the session
xfbml : true // parse XFBML
});
</script>

	<div class="container">

		<div class="element">
			<h3>
				Manage
				<?php echo $SiteName; ?>
				Page Tab
			</h3>
			<p>
				The tab you chose is configured to displayed details about
				<?php echo $SiteName; ?>
				. If you want to change this then you will need to remove the tab
				from the fan page in question and start the process again
			</p>
			<a class="uibutton icon prev" style="float: left;" target="_top"
				href="http://apps.facebook.com/airsoftsiteinfotab/">Go Back</a> <a
				class="uibutton icon next" style="float: right;" target="_top"
				href="http://www.facebook.com/<?php echo $fbTabID; ?>">Visit Page</a>
			<br />
		</div>

		<div class="element">
			<h3>Help</h3>
			<p>Choose which Airbana features you'd like to display on your tab.
				Enabling features such as Reviews will allow people to add reviews.</p>
			<p>The Map feature cannot be disabled.</p>
			<p>
				A <a class="uibutton" href="#">Gray Button</a> indicates a feature
				is disabled and a <a class="uibutton special" href="#">Green Button</a>
				indicates it is enabled.
			</p>
			<p>Simply click the button to toggle the option.</p>
		</div>

		<div class="element">
			<div id="tabs">
				<ul>
					<li><a href="#tabs-1">Reviews</a></li>
					<li><a href="#tabs-2">Contact Details</a></li>
					<li><a href="#tabs-3">Upcoming Events</a></li>
				</ul>
				<div id="tabs-1">
					<div
						style="margin-left: auto; margin-right: auto; text-align: center;">
						<a id='reviews' onclick="toggle('reviews');"
							class="uibutton <?php if($TabInfo['allowReviews'] == 1){ echo 'special'; } ?>"
							href="#">Reviews <?php if($TabInfo['allowReviews'] == 1){ 
								echo 'Enabled';
							} else { echo 'Disabled';
							}?>
						</a>
					</div>
					<p>Reviews are written by members of the Airsoft community and
						consist of a rating out of 5 and a free form text input.</p>
					<p>Whilst Airbana provides a terms of service agreement that users
						are obliged to follow Airbana itself is not liable for content of
						these reviews</p>
					<p>Visitors like to see recent, positive &amp; well written reviews
						so please encourage skirmishers to leave feedback via the Airbana
						Airsoft Map for your site.</p>
				</div>
				<div id="tabs-2">
					<div
						style="margin-left: auto; margin-right: auto; text-align: center;">
						<a id='contact' onclick="toggle('contact');"
							class="uibutton <?php if($TabInfo['allowContact'] == 1){ echo 'special'; } ?>"
							href="#">Contact Details <?php if($TabInfo['allowContact'] == 1){ 
								echo 'Enabled';
							} else { echo 'Disabled';
							}?>
						</a>
					</div>
					<p>Displaying your contact details allows skirmishers to get in
						touch with you without having to use facebook</p>
					<p>When enabled this will display the phone number, email address
						and Website listed on www.AirsoftMap.net</p>
					<p>Contact details can be edited at any time by visiting your entry
						at www.AirsoftMap.net and clicking the edit icon.</p>
				</div>
				<div id="tabs-3">
					<div
						style="margin-left: auto; margin-right: auto; text-align: center;">
						<a id='events' onclick="toggle('events');"
							class="uibutton <?php if($TabInfo['allowEvents'] == 1){ echo 'special'; } ?>"
							href="#">Upcoming Events <?php if($TabInfo['allowEvents'] == 1){ 
								echo 'Enabled';
							} else { echo 'Disabled';
							}?>
						</a>
					</div>
					<p>
						Displaying your upcoming events will help improve player
						attendance and when an event is added to the database it will be
						listed on the frontpage of <a href="http://www.Airsoftmap.net"
							target="_top">www.AirsoftMap.net</a> and tweeted to over 1000
						people on twitter.
					</p>
					<p>Adding events is easy and once one event has been added it's
						details can be 'cloned' to as many extra days as required with
						just one click!</p>
				</div>
			</div>
			<div
				style="margin-left: auto; margin-right: auto; text-align: center;">
				<h4>Changes are saved automatically!</h4>
			</div>
		</div>

	</div>
	<div id="poweredby"></div>
	<script type="text/javascript">
	$(document).ready(function() 
	{
		FB.Canvas.setSize();
		$( "#tabs" ).tabs();
	});
	</script>
</body>
</html>

