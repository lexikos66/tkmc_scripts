<?php

if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');
require_once(SCRIPTS_DIR.'/includes/form_functions.inc');

//Connect to Database
$db = LocalDatabase::getReadable('master');
$knighttable='knight_directory';
$lodgetable='lodge_directory';
$lodge_specific=true;
$lodge_num=0;
$lodge='';

if(isset($_GET["success"])) {
	echo 'Ride successfully added.<br>';
}

$current_user = wp_get_current_user();
$roles = implode('","',$current_user->roles);
$query = 'SELECT * FROM ' . $lodgetable . ' WHERE LODGE_CODE in ("'. $roles .'"")';
$result = mysqli_query($db->connection, $query);
if($result) {
	while($row = mysqli_fetch_array($result)){
		$lodgeName = $row['LODGE_NAME'];
		$lodge_num = $row['LODGE_NUM'];
	}
}

echo '<p><a href="http://member.templarknightsmc.com/knights/ride-tracker/">Back</a></p>';
echo '<p><i>A ride is only sanctioned for mileage when the following criteria are met; 1) More than 3 Knights are in attendance for the ride OR 2) Any number of Knights are riding with a Provisional, with the mentors approval.</i></p>';	
	
echo '<form action="http://templarknightsmc.com/scripts/ridetracker_action.php" method="post"><span style="font-size: small;">Items marked with a </span><span style="color: red; font-size: small;">*</span><span style="font-size: small;"> are required fields.</span><br /><br />
	<p>Sponsoring Lodge <font color="red" size="2">*</font>: ' . $lodgeName .'<p>
	Name the Ride (Where did you go) <span style="color: red; font-size: small;">*</span>: <input type="text" name="rideName" />';

echo '<input type="hidden" name="lodgeNum" value="' . $lodge_num . '"><br><br>';

echo 'Date of Ride <strong>(yyyy-mm-dd)</strong> <span style="color: red; font-size: small;">*</span>: <input type="text" name="rideDate" />

	Miles Ridden <span style="color: red; font-size: small;">*</span>: <input type="text" name="rideMiles" />

	Hours Ridden: <input type="text" name="rideHours" /><p>';
members_dropdown($db, $knighttable, false, $lodge_num, 'Ride Organizer <span style="color: red; font-size: small;">*</span>:&nbsp;&nbsp;');
echo '<br>Ride Participants <span style="color: red; font-size: small;">*</span>:';
$query = 'SELECT * FROM ' . $knighttable . ' WHERE LODGE = ' . $lodge_num . ' ORDER BY NICKNAME';
$result = mysqli_query($db->connection, $query);
if($result) {
	echo '<input type="checkbox" name="rideParticipants[]" value="NONE" style="display:none;" />';
	echo '<table border="0"><tr>';
	$i=1;
	while($row = mysqli_fetch_array($result)){
		if($row['DATE_WITHDREW'] == '0000-00-00') {
			if($row['NICKNAME'] == "") {
				echo '<td><input type="checkbox" name="rideParticipants[]" value="' . $row['RECORD_NUM'] . '" /> ' . $row['FNAME'] . ' ' . $row['LNAME'] . '</td>';
			} else {
				echo '<td><input type="checkbox" name="rideParticipants[]" value="' . $row['RECORD_NUM'] . '" /> ' . $row['NICKNAME'] . '</td>';  
			}
			if($i/5 == 1) {
				echo '</tr><tr>';
				$i=0;
			}
			$i++;
		}
	}
	echo '</table> ';
}

echo 'Please provide a description or a Googlemap URL of the Route <span style="color: red; font-size: small;">*</span>:
	<textarea name="routeDetails" rows="2" cols="60"></textarea>

	Road and Weather Conditions:
	<textarea name="conditions" rows="2" cols="60"></textarea>

	Please provide a detailed description of Safety Hazard or Issues Noticed <span style="color: red; font-size: small;">*</span>:
	<textarea name="safetyIssues" rows="5" cols="60"></textarea>

	Please provide a summary of the ride details <span style="color: red; font-size: small;">*</span>:
	<textarea name="rideSummary" rows="5" cols="60"></textarea>

	If you interacted with the Public or any other Club, how were you recieved?
	<textarea name="perception" rows="2" cols="60"></textarea>

	Was this a Charity Ride?
	<input type="radio" name="isCharity" value="1" /> Yes
	<input type="radio" name="isCharity" value="0" checked="checked" /> No

	<br><br><input type="submit" value="Record Ride" />

	</form>';

mysqli_close($db->connection);

?>