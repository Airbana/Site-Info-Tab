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
require './libs/db.php';
require './libs/infotab.class.php';

$app_id = '****APP ID****';
$app_secret = '****APP SECRET****';
$api_key = '****api key****';

$infotab = new infotab();
$facebook = new Facebook(array('appId'  => $app_id,'secret' => $app_secret,));

$signed_request = $_REQUEST["signed_request"];
list($encoded_sig, $payload) = explode('.', $signed_request, 2);
$data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);

$Failure = false;

if(!empty($data['page']['id']))
{
	$InfoTabDetails = $infotab->GetTabInfo($data['page']['id']);

	if($InfoTabDetails != null)
	{
		$URL = "http://api.airbana.net/2.0/site.php?siteid=".$InfoTabDetails['SiteID'].'&apikey=' . $api_key;
		$AirbanaRemote = curl_init();
		curl_setopt($AirbanaRemote, CURLOPT_URL, $URL);
		curl_setopt($AirbanaRemote, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($AirbanaRemote, CURLOPT_POST, 1);
		$Data = curl_exec($AirbanaRemote);
		$Data = unserialize($Data);
	}
	else
	{
		$Failure = true;
	}
}
else
{
	$Failure = true;
}
?>
<!DOCTYPE html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
<title>php-sdk</title>
<link type="text/css" rel="stylesheet" href="css/tab.css" />
<link type="text/css" rel="stylesheet" href="css/fb-buttons.css" />
<link type="text/css" rel="stylesheet"
	href="css/redmond/jquery-ui-1.8.18.custom.css" />
<script
	src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="js/jquery-ui-1.8.18.custom.min.js"></script>
<script type="text/javascript"
	src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript" src="js/map_functions.js"></script>
</head>
<body
	onload="initialize(<?php print($Data['Site']['Latitude'] .','.$Data['Site']['Longitude']); ?>)">
	<div id="fb-root"></div>
	<script src="http://connect.facebook.net/en_US/all.js"></script>
	<script type="text/javascript">
$(document).ready(function()
{
FB.init({appId : '<?php echo $app_id; ?>',
status : true, // check login status
cookie : true, // enable cookies to allow the server to access the session
xfbml : true // parse XFBML
});
        FB.Canvas.setSize();
});
</script>

	<div class="container">

		<div class="element" id="map_canvas" style="height: 300px"></div>

		<!-- Start Contact Details (if enabled -->
		
		
		
		
		
		
<?php if($InfoTabDetails['allowContact'] == 1): ?>
<div class="element">
<h3>Contact &amp; Location Details</h3>
	<div style="float:left; min-width:100px; margin-right:20px;">
		<?php echo $Data['Site']['Address1']; ?><br/>
                <?php echo $Data['Site']['Address2']; ?><br/>
                <?php echo $Data['Site']['PostCode']; ?><br/>
        </div>

        <div style="float:right; padding-right:10px; min-width:100px;">
		<img src="images/email.png"/>&nbsp;<?php echo $Data['Site']['ContactEmail']; ?><br/>
		<img src="images/web.png"/>&nbsp;<a target="_blank" href="http://<?php echo $Data['Site']['Website']; ?>"><?php 
		if(strlen($Data['Site']['Website']) > 30)
		{
			echo substr($Data['Site']['Website'],0,30);
			echo '...';
		}
		else
		{
			echo $Data['Site']['Website'];
		} 
		?></a><br/>
		<img src="images/phone.png"/>&nbsp;<?php echo $Data['Site']['ContactPhone']; ?><br/>
		<img src="images/comment.png"/>&nbsp;<a target="_blank" href="http://<?php echo $Data['Site']['ForumLink']; ?>">Forum</a><br/>
        </div>

<div class="clear"></div>
</div>
<?php endif ?>
<!-- END Contact Details -->

<!-- Start Events (if enabled -->
<?php if($InfoTabDetails['allowEvents'] == 1): ?>
<div class="element">
        <h3>Upcoming Events</h3> <a style="position:absolute; right:2px; top:2px;" href="http://apps.facebook.com/airsoftmap/addevent.php?site=<?php echo $Data['Site']['SiteID']; ?>" target="_top" class="uibutton icon add">Add Event</a>
	<?php
                        $AirbanaRemote = curl_init();
                        curl_setopt($AirbanaRemote, CURLOPT_URL, "http://api.airbana.net/3.0/events.php?siteid=".$InfoTabDetails['SiteID'].'&apikey='.$api_key);
                        curl_setopt($AirbanaRemote, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($AirbanaRemote, CURLOPT_POST, 1);
                        $Data = curl_exec($AirbanaRemote);
                        $Data = unserialize($Data);
        if(count($Data['Events']) != 0)
        {
                $Count = 0;
                foreach($Data['Events'] as $Event)
                {
                        print('<div class="review_block">');
                        print('<p><strong>'.$Event['EventStartDate'] . ' - ' . $Event['EventStartTime'] . ': ' . $Event['EventName'].'</strong><br/>');
			print($Event['EventSynopsis'] . '</p>');
                        print('<p class="author"><a class="uibutton icon next" href="http://www.AirsoftMap.net/Event/'.urlencode($Event['Eventname']).'/'.$Event['EventID'].'">View more details</a></p>');
                        print('</div>');
                        $Count++;

                        if($Count > 7)
                        {
                                print("<p><strong>Only showing the latest 8 of " . count($Data['Reviews']) . ' reviews.</strong><br/><br/>To see the rest visit <a href="http://www.AirsoftMap.net" target="_blank">The Airbana Airsoft Map</a></p>');
                                break;
                        }
                }
        }
        else
        {
                print("This site doesn't have any upcoming Events.<br/>");
		print('If you know of any upcoming events please use the Add Event Button above.');
        }
?>

<div class="clear"></div>
</div>
<?php endif ?>
<!-- End Events -->



<?php if($InfoTabDetails['allowReviews'] == 1): ?>
<div class="element">
<h3>Reviews</h3> <a style="position:absolute; right:2px; top:2px;" href="http://apps.facebook.com/airsoftmap/addreview.php?site=<?php echo $Data['Site']['SiteID']; ?>" target="_top" class="uibutton icon add">Add Review</a>
<?php
                        $AirbanaRemote = curl_init();
                        curl_setopt($AirbanaRemote, CURLOPT_URL, "http://api.airbana.net/3.0/review.php?siteid=".$InfoTabDetails['SiteID'].'&apikey=' . $api_key);
                        curl_setopt($AirbanaRemote, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($AirbanaRemote, CURLOPT_POST, 1);
                        $Data = curl_exec($AirbanaRemote);
                        $Data = unserialize($Data);

	if(count($Data['Reviews']) != 0)
	{
		$Count = 0;
		foreach($Data['Reviews'] as $Review)
		{
			print('<div class="review_block">');
			print('<p><img src="images/rating'.$Review['Rating'] .'.png"/>'.$Review['Review'].'</p>');
			print('<p class="author">Written by '.$Review['ReviewerName'] .' on ' . $Review['ReviewDate'].'</p>');
			print('</div>');
			$Count++;

			if($Count > 7)
			{
				print("<p><strong>Only showing the latest 8 of " . count($Data['Reviews']) . ' reviews.</strong><br/><br/>To see the rest visit <a href="http://www.AirsoftMap.net" target="_blank">The Airbana Airsoft Map</a></p>');
				break;
			}
		}
	}
	else
	{
		print("This site doesn't have any reviews yet.<br/><br/>Please click here to add a review.");
	}
?>
<div class="clear"></div>
</div>
<?php endif ?>
<a target="_top" href="http://apps.facebook.com/airsoftsiteinfotab/" class="uibutton large special icon next">Create a Tab</a><br/>
<a href="http://www.AirsoftMap.net/" target="_blank"><img style="right:8px;" src="images/powered_by.png"/></a>
</div>
</body>
</html>
