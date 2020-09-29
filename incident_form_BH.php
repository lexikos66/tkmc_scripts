<?php

if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');
require_once(SCRIPTS_DIR.'/includes/form_functions.inc');

//Connect To Database
$db = LocalDatabase::getReadable('master');
$ltable='lodge_directory';
$ktable='knight_directory';
$dtable='discipline_reports';

if (isset($_GET["lodgeNum"])) {
	$lodgeNum = $_GET["lodgeNum"];
		echo '<p><a href="http://templarknightsmc.com/council-area/incident-entry">Back</a></p>';
		echo '<form action="http://templarknightsmc.com/scripts/incident_action.php" method="post">';
		echo '<table><tr><td>Date Reported (yyyy-mm-dd): </td><td><input type="text" name="incidentDate" /> </td>';
		echo '<td>Time Reported (hh:mm): </td><td><input type="text" name="incidentTime" /></td></tr></table>';
		echo '<hr>';
		echo '<b>Persons Involved:</b>';
		$query = 'SELECT * FROM ' . $ktable . ' WHERE LODGE = ' . $lodgeNum . ' AND DATE_WITHDREW = "0000-00-00" ORDER BY NICKNAME';
		$result = mysql_query($query, $db->connection);
		if($result) {	
			echo '<input type="checkbox" name="involvedPersons[]" value="NONE" style="display:none;" />';
			echo '<table border="0"><tr>';
			$i=1;
			while($row = mysql_fetch_array($result)){
				if($row['NICKNAME'] == "") {
				echo '<td><input type="checkbox" name="involvedPersons[]" value="' . $row['RECORD_NUM'] . '" /> ' . $row['FNAME'] . ' ' . $row['LNAME'] . '&nbsp;&nbsp;</td>';
				} else {
				echo '<td><input type="checkbox" name="involvedPersons[]" value="' . $row['RECORD_NUM'] . '" /> ' . $row['NICKNAME'] . '&nbsp;&nbsp;</td>';
				}
				if($i/5 == 1) {
				echo '</tr><tr>';
				$i=0;
				}
				$i++;
			}
			echo '</table></font><br>';
			echo 'Guests Involved: <input type="text" name="guests" />';
			echo '<hr>';
			echo '<b>Reporting Persons:</b>';
			$query = 'SELECT * FROM ' . $ktable . ' WHERE LODGE = ' . $lodgeNum . ' ORDER BY NICKNAME';
			$result = mysql_query($query, $db->connection);
			if($result) {	
				echo '<input type="checkbox" name="reportingPersons[]" value="NONE" style="display:none;" />';
				echo '<table border="0"><tr>';
				$i=1;
				while($row = mysql_fetch_array($result)){
					if($row['DATE_WITHDREW'] == "0000-00-00") {
						if($row['NICKNAME'] == "") {
							echo '<td><input type="checkbox" name="reportingPersons[]" value="' . $row['RECORD_NUM'] . '" /> ' . $row['FNAME'] . ' ' . $row['LNAME'] . '&nbsp;&nbsp;</td>';
						} else {
							echo '<td><input type="checkbox" name="reportingPersons[]" value="' . $row['RECORD_NUM'] . '" /> ' . $row['NICKNAME'] . '&nbsp;&nbsp;</td>';
						}
						if($i/5 == 1) {
							echo '</tr><tr>';
							$i=0;
						}
						$i++;
					}
				}
				echo '</table></font><br>';
			}
			echo '<hr>';
			echo '<b>Type of Incident:</b>';
			$query = "SHOW COLUMNS FROM " . $dtable . " LIKE 'incident_type'";
			$result = mysql_query($query, $db->connection);
			if($result) {
				//echo '<input type="checkbox" name="incidentType[]" value="NONE" style="display:none;" />';
				echo '<table border="0"><tr>';
				$row = mysql_fetch_array($result);
				#Extract the values
				#The values are enclosed in single quotes and seperated by commas
				$regex = "/'(.*?)'/";
				preg_match_all($regex, $row[1], $enum_array);
				$i = 0;
				foreach($enum_array[1] as $value) {
					echo '<td><input type="checkbox" name="incidentType" value="' . $i . '" />' . $value . '&nbsp;&nbsp;</td>';
					$i++;
				}
				echo '</tr></table>';
			}
			echo '<b>Incident Details:</b><br>';
			echo '<textarea name="incidentDetails" rows="10" cols="70"></textarea></p>';
			echo '<b>Action Taken:</b><br>';
			echo '<textarea name="incidentAction" rows="10" cols="70"></textarea></p>';
			echo '<b>Other Notes:</b><br>';
			echo '<textarea name="incidentNotes" rows="10" cols="70"></textarea></p>';
			echo '<hr>';
			echo 'Attending Council: <input type="text" name="councilPresent" size="50" /><br>';
			echo '<p><input type="submit" value="Create Incident"></p>';
			echo '<input type="hidden" name="lodgeNum" value="'.$_GET['lodgeNum'].'" />';
			echo '</form>';
		}
	} else {
		echo '<form action="http://templarknightsmc.com/council-area/incident-entry" method="get">';
		lodge_dropdown($db, $ltable);
		echo '<p><input type="submit" value="Select Lodge"></p>';
		echo '</form>';
	}
	mysql_close($db->connection);
?>