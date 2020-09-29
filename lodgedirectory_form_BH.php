<?php

if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');

//Connect to Database
$db = LocalDatabase::getReadable('master');
$ktable='knight_directory';
$ltable='lodge_directory';
$lodgeTitleString='';
$lodgeCreationDate='';

if(isset($_GET["lodgeNum"])) {
	$lodgeNum = $_GET["lodgeNum"];
	$query = 'SELECT * FROM ' . $ltable . ' WHERE LODGE_NUM = ' . $lodgeNum;
	$result = mysql_query($query, $db->connection);
	if($result) {
		$row = mysql_fetch_array($result);
		$lodgeTitleString = '<p style="font-size:20px"><b>' . $row['LODGE_NAME'] . '</b></p> [ ' . $row['CITY'] . ' | ' . $row['STATE'] . ' | ' . $row['COUNTRY'] . ' ]<br>';
		$lodgeCreationDate = $row['DATE_CREATION'];
		echo '<a href="http://templarknightsmc.com/lodges">Back</a><br>';
		echo $lodgeTitleString;
		echo '<a href="mailto:' . $row['EMAIL_ADDR'] . '"><img src="http://templarknightsmc.com/static/email_trans_icon.png" alt="eMail" height="20" width="20"> eMail</a> | <a href="'
		. $row['WEB_ADDR'] . '"><img src="http://templarknightsmc.com/static/webicon.png" alt="Web" height="20" width="20"> Website</a>';
		echo '<ul>';
		if($lodgeCreationDate != "0000-00-00") {
			position_print("%Commander%", $lodgeNum, $db);
		} else {
			position_print("%Recruiting%", $lodgeNum, $db);
		}
		position_print("%Captain%", $lodgeNum, $db);
		position_print("%Master%", $lodgeNum, $db);
		position_print("%Arms%", $lodgeNum, $db);
		position_print("%Road%", $lodgeNum, $db);
	    
		$query = "SELECT * FROM " . $ktable . ' WHERE LODGE=' . $lodgeNum . ' AND (RANK="Marshal" OR RANK="Grand Marshal") AND DATE_WITHDREW ' . '= "0000-00-00"';
		$result = mysql_query($query, $db->connection);
		if($result) {
			$row = mysql_fetch_array($result);
			if($row['RANK'] == "") {
				echo '<li>Not Currently Filled.</li>';
			} else {
				echo '<li>' . $row['RANK'] . ': ' . $row['FNAME'] . ' "' . $row['NICKNAME'] . '" ' . $row['LNAME'] . '</li>';
			}
		}
		position_print("%Under%Marshal%", $lodgeNum, $db);
		echo '</ul>';
	}
} else {
	// Get United States Info First & Grand Lodge Info First
	echo '<p style="font-size:20px"><b>United States</b></p>';
	echo '&nbsp;&nbsp;<a href="http://templarknightsmc.com/lodges?lodgeNum=1">Grand Lodge</a> - Pleasant Grove, Utah<br>';
	$query = 'SELECT STATE FROM ' . $ltable . ' WHERE COUNTRY="United States" GROUP BY STATE ORDER BY STATE';
	$result = mysql_query($query, $db->connection);
	if($result) {
		while($row = mysql_fetch_array($result)) {
			$query = 'SELECT * FROM ' . $ltable . ' WHERE STATE="' . $row['STATE'] . '" ORDER BY LODGE_NAME';
			$stateRes = mysql_query($query, $db->connection);
			if($stateRes) {
				while($stateRow = mysql_fetch_array($stateRes)) {
					if($stateRow['LODGE_NAME'] != "Grand Lodge" && $stateRow['DATE_DISBANDED'] == "0000-00-00") {
						echo '&nbsp;&nbsp;<a href="http://templarknightsmc.com/lodges/?lodgeNum=' . $stateRow['LODGE_NUM'] . '">' . $stateRow['LODGE_NAME'] . '</a> - ' . $stateRow['CITY'] . ', ' . $stateRow['STATE'] . '<br>'; 
					}
				}
			}
		}
	}	
	$query = 'SELECT COUNTRY FROM ' . $ltable . ' GROUP BY COUNTRY ORDER BY COUNTRY';
	$result = mysql_query($query, $db->connection);
	if($result) {
		while($row = mysql_fetch_array($result)) {
			if($row['COUNTRY'] != "United States") {
				echo '<p style="font-size:20px"><b>' . $row['COUNTRY'] . '</b></p>';
				$query = 'SELECT STATE FROM ' . $ltable . ' WHERE COUNTRY="' . $row['COUNTRY'] . '" GROUP BY STATE ORDER BY STATE';
				$countryRes = mysql_query($query, $db->connection);
				if($countryRes) {
					while($countryRow = mysql_fetch_array($countryRes)) {
						$query = 'SELECT * FROM ' . $ltable . ' WHERE STATE="' . $countryRow['STATE'] . '" ORDER BY LODGE_NAME';
						$stateRes = mysql_query($query, $db->connection);
						if($stateRes) {
							while($stateRow = mysql_fetch_array($stateRes)) {
								if($stateRow['DATE_DISBANDED'] == "0000-00-00") {
									echo '&nbsp;&nbsp;<a href="http://templarknightsmc.com/lodges?lodgeNum=' . $stateRow['LODGE_NUM'] . '">' . $stateRow['LODGE_NAME'] . '</a> - ' . $stateRow['CITY'] . ', ' . $stateRow['STATE'] . '<br>'; 
								}
							}
						}
					}
				}
			}	
		}		
	}
}
mysql_close($db->connection);

function position_print($rank, $lodgeNum, $db) {
	$query = "SELECT * FROM knight_directory WHERE RANK LIKE '" . $rank . "' AND DATE_WITHDREW " . '= "0000-00-00" AND LODGE=' . $lodgeNum;
	$result = mysql_query($query, $db->connection);
	if($result) {
		$row = mysql_fetch_array($result);
		if($row['RANK'] == "") {
			echo '<li>Not Currently Filled.</li>';
		} else {
			echo '<li>' . $row['RANK'] . ': ' . $row['FNAME'] . ' "' . $row['NICKNAME'] . '" ' . $row['LNAME'] . '</li>';
		}
	}
}

?>