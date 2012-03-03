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
	$app_secret = '****APP SECRET****';
	$api_key = '****api key****';
	$CurrentTab = null;
	$canvas_page = "https://apps.facebook.com/airsoftsiteinfotab/";
	
	$infotab = new infotab();
	$facebook = new Facebook(array('appId'  => $app_id,'secret' => $app_secret,));
	
	$auth_url = "http://www.facebook.com/dialog/oauth?client_id=" . $app_id . "&redirect_uri=" . urlencode($canvas_page);
	$signed_request = $_REQUEST["signed_request"];
	list($encoded_sig, $payload) = explode('.', $signed_request, 2);
	$data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
	
	if(isset($_GET['current_tab']) && !empty($_GET['current_tab']))
	$CurrentTab = $_GET['current_tab'];
	
	if (empty($data["user_id"]))
	{
		echo("<script> top.location.href='" . $auth_url . "'</script>");
	}
	else
	{
		$Authed = 'complete';
	}
	
	if(!empty($_GET['tabs_added']))
	{
		//print_r($data);
		while ($tabSuccess = current($_GET['tabs_added']))
		{
			if ($tabSuccess == 1)
			{
				$TabID = key($_GET['tabs_added']);
				$Tab = 'complete';
			}
			next($_GET['tabs_added']);
		}
		$EnableAutoComplete = true;
		$CurrentTab = $infotab->AddTab($TabID, $data["user_id"]);
		$UserTabs = $infotab->GetTabs($data["user_id"]);
		$AirbanaRemote = curl_init();
		curl_setopt($AirbanaRemote, CURLOPT_URL, "http://api.airbana.net/2.0/sites.php?apikey=$api_key");
		curl_setopt($AirbanaRemote, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($AirbanaRemote, CURLOPT_POST, 1);
		$Data = curl_exec($AirbanaRemote);
		$Data = unserialize($Data);
	}
	else
	{
		//Check if this user has any other tabs
		$UserTabs = $infotab->GetTabs($data["user_id"]);
		if(!empty($UserTabs) && !empty($UserTabs[0]))
		{
			$Tab = 'complete';
			if(isset($_GET['current_tab']))
			{
				$EnableAutoComplete = true;
				$AirbanaRemote = curl_init();
				curl_setopt($AirbanaRemote, CURLOPT_URL, "http://api.airbana.net/2.0/sites.php?apikey=$api_key");
				curl_setopt($AirbanaRemote, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($AirbanaRemote, CURLOPT_POST, 1);
				$Data = curl_exec($AirbanaRemote);
				$Data = unserialize($Data);
			}
		}
		else
		{
			$Tab = 'incomplete';
			$EnableAutoComplete = false;
		}
	}

?>
<!DOCTYPE html>
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


<script src="http://connect.facebook.net/en_US/all.js"></script>
<script>
FB.init({
appId : '<?php echo $app_id; ?>',
status : true, // check login status
cookie : true, // enable cookies to allow the server to access the session
xfbml : true // parse XFBML
});
</script>

</head>
<body>
	<div id="progress" class="progress"
		style="position: absolute; top: 2px; right: 2px;">
		<span style="padding-left: 8px; padding-right: 18px;" id="auth"><img
			src="images/<?php echo $Authed; ?>.png"> Authorized</span> <span
			id="tab"><img src="images/<?php echo $Tab; ?>.png"> Added to a page</span>
	</div>

	<div class="container">

		<div class="element title-element">
			<img src="images/imageover.png" class="element-overimage" />
			<h1>Airbana Site Info Tab</h1>
			<p>
				This Facebook app allows you to display information from the Airbana
				Airsoft Map such as a Google Map <em>(including Street View etc)</em>,
				<br />your sites reviews, contact details &amp; upcoming events on
				your sites Fan Page. Click <a
					href="http://www.youtube.com/watch?v=beopkOY8-MI" target="_blank">here</a>
				to see a YouTube video with more details.
			</p>
			<a class="uibutton icon add" style="float: right;" target="_top"
				href="http://www.facebook.com/dialog/pagetab?app_id=<?php echo $app_id; ?>&next=<?php echo $canvas_page; ?>">Add
				Tab to another Page</a>
			<div class="fb-like"
				data-href="http://apps.facebook.com/airsoftsiteinfotab/"
				data-send="true" data-layout="box_count" data-width="55"
				data-show-faces="false"
				style="top: 4px; right: 4px; position: absolute;"></div>
		</div>
		
<?php if(isset($UserTabs) && !empty($UserTabs)): ?>
<div class="element">
<h3>Manage Existing Pages</h3>
<p>From here you change the configuration of any of your existing pages. Select the name of one your pages that have had the Site Info tab installed to configure the settings. Configurable settings include the display of Reviews, contact details etc.</p>
<?php foreach($UserTabs as $UserTab)
	{
		if(isset($UserTab['TabName']))
		{
			$Name = $UserTab['TabName'];
		}
		else
		{
			$Name = $UserTab['fbTabID'];
		}
		print('<a class="uibutton icon edit" href="managetab.php?fbTabID='.$UserTab['fbTabID'].'&SiteName='.urlencode($Name).'">' . $Name .'</a>');
	}
?>
</div>
<?php endif ?>


	<?php if($Tab == 'incomplete'): ?>
		<div class="element">
		<h3>Outstanding Actions - Add to a Page</h3>
		<p><img src="images/information.png"/> Before you can choose which site to manage and what details to show you must add this add to page. This is so we know which site details to display
on which page tab.</p>
		<a class="uibutton icon add" target="_top" href="http://www.facebook.com/dialog/pagetab?app_id=<?php echo $app_id; ?>&next=<?php echo $canvas_page; ?>">Add Tab to a Page</a>
		</div>
	<?php endif ?>

<?php if($EnableAutoComplete == true): ?>
<script src="js/functions.js"></script> 
<div class="element">
	<h3>Choose which Site Details to Display on the <?php echo $CurrentTab; ?> Page</h3>
	<p>Now that you have selected a page on which to display your site's details you need to choose which site to use.</p>
	<p>Start typing the name of your Skirmish or Retailer site into the text box below and it will automatically attempt to match it to an entry in the Airbana database.</p>
	<p>Once you have selected your site click on the 'Use this site' button.</p>
	<form action="managetab.php" method="POST" name="managetab">
	<select id="combobox" name="SiteID">
		<option value="">Select one...</option>
		<?php
			foreach($Data['Sites'] as $SiteDetail)
			{
				echo '<option value="'.$SiteDetail['SiteID'].'">'.$SiteDetail['SiteName'].'</option>';
			}
		?>
	</select>

	<input type="hidden" name="fbTabID" value="<?php echo $TabID; ?>">
	<input type="hidden" name="firstAdd" value="true">
	<a style="margin-left:20px; margin-bottom:8px;" class="uibutton large confirm" href="#" onclick="document.managetab.submit();">Use this Site</a>	
	</form>
</div>
<?php else: ?>
<!-- Nothing to see here yet -->
<?php endif ?>

</div>
	<div id="fb-root"></div>
	<div id="poweredby"></div>
	<script type="text/javascript">
	$(document).ready(function() 
	{
	FB.Canvas.setSize();
	(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1&appId=<?php echo $app_id; ?>";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
	});
</script>
</body>
</html>
