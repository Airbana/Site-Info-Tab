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
class infotab
{
	function AddTab($PageID, $UserID)
	{
		$PageID = infotab::CleanSQL($PageID);
		$UserID = infotab::CleanSQL($UserID);

		$Query = "select * from siteinfotab.infotabs where fbUserID = '$UserID' AND fbTabID = '$PageID'";
		$result = mysql_query($Query);
		if(mysql_num_rows($result) < 1)
		{
			$TabDetails = infotab::GetOpenGraphTabInfo($PageID);
			$TabName = $TabDetails->name;
			$Query = "insert into siteinfotab.infotabs (fbUserID, fbTabID,TabName) values ('$UserID','$PageID','$TabName')";
		}
		else
		{
			//Nothing
		}
		mysql_query($Query);
		return $TabName;
	}

	function Toggle($fbTabID,$type)
	{
		$fbTabID = infotab::CleanSQL($fbTabID);

		switch($type)
		{
			case 'contact':
				{
					$Type = 'allowContact';
				}
				break;

			case 'events':
				{
					$Type = 'allowEvents';
				}
				break;

			case 'reviews':
				{
					$Type = 'allowReviews';
				}
				break;
		}

		$Query = "UPDATE siteinfotab.infotabs SET $Type = IF($Type=1, 0, 1) where fbTabID = $fbTabID";
		return mysql_query($Query);
	}

	function GetTabInfo($PageID)
	{
		$PageID = infotab::CleanSQL($PageID);
		$Query = "select * from siteinfotab.infotabs where fbTabID = '$PageID'";
		$result = mysql_query($Query);
		return mysql_fetch_assoc($result);
	}

	function GetOpenGraphTabInfo($fbTabID)
	{
		$AirbanaRemote = curl_init();
		curl_setopt($AirbanaRemote, CURLOPT_URL, "http://graph.facebook.com/$fbTabID");
		curl_setopt($AirbanaRemote, CURLOPT_RETURNTRANSFER, true);
		$Data = curl_exec($AirbanaRemote);
		return json_decode($Data);
	}

	function GetTabs($UserID)
	{
		$UserID = infotab::CleanSQL($UserID);
		$Query = "select * from siteinfotab.infotabs where fbUserID = '$UserID'";
		$result = mysql_query($Query);
		if($result != null && mysql_num_rows($result) > 0)
		{
			while($row = mysql_fetch_assoc($result))
			{
				$Return[] = $row;
			}
		}
		else
		{
			$Return = null;
		}

		return $Return;

	}

	function AssignSiteToTab($fbTabID,$SiteID,$SiteName)
	{
		$fbTabID = infotab::CleanSQL($fbTabID);
		$SiteID = infotab::CleanSQL($SiteID);
		$SiteName = infotab::CleanSQL($SiteName);
		$TabDetails = infotab::GetOpenGraphTabInfo($fbTabID);
		$TabName = $TabDetails->name;
		$Query = "update siteinfotab.infotabs set SiteID = $SiteID, SiteName = '$SiteName',TabName = '$TabName' where fbTabID = '$fbTabID'";
		return mysql_query($Query);
	}

	function CleanSQL( $value )
	{
		if( get_magic_quotes_gpc() )
		{
			$value = stripslashes( $value );
		}
		if( function_exists( "mysql_real_escape_string" ) )
		{
			$value = mysql_real_escape_string( $value );
		}
		else
		{
			$value = addslashes( $value );
		}
		return $value;
	}
}
?>
