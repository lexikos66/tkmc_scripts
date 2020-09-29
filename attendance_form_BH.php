<?php

if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');
	
//Connect To Database
$wpdb = LocalDatabase::getReadable('wor7320');
$knighttable='knight_directory';
$requiredId=3;
$lodgeNum=1;
$ohio172_users = 'CrowTKMCOhio172';

// If Council from other Lodge - get UserID and set Event Required ID and Lodge Number
$current_user = wp_get_current_user();
// Set if they are in Ohio172
if (strpos($ohio172_users, $current_user->user_login)) {
	$requiredId=26;
	$lodgeNum=9;
}

echo '<form action="http://templarknightsmc.com/scripts/attendance_action.php" method="post">';
$todaysDate = getdate();
$year = $todaysDate['year'];
$month = formatDate($todaysDate['mon']);
$day = formatDate($todaysDate['mday']);
echo '<p>Select Event:&nbsp;&nbsp;';
$query = 'SELECT wp_hiqx_em_events.event_id AS event_id, wp_hiqx_em_events.post_id AS post_id, wp_hiqx_em_events.event_name AS event_name, wp_hiqx_em_events.event_start_date AS start_date FROM wp_hiqx_em_events, wp_hiqx_term_relationships WHERE wp_hiqx_term_relationships.term_taxonomy_id = '.$requiredId.' AND wp_hiqx_em_events.event_start_date > "' . $year . '-01-01" AND wp_hiqx_em_events.event_start_date <= "' . $year . '-' . $month . '-' . $day . '" AND wp_hiqx_em_events.event_spaces = 0 AND wp_hiqx_em_events.post_id =  wp_hiqx_term_relationships.object_id AND wp_hiqx_em_events.recurrence_id != "" ORDER BY wp_hiqx_em_events.event_start_date'; 
$result = mysql_query($query, $wpdb->connection);
if($result) {
	echo '<select name="eventId">';
	while($row = mysql_fetch_array($result)) {
		echo '<option value="' . $row['event_id'] . '">' . $row['event_name'] . ':' . $row['start_date'] . '</option>';
	}
	echo '</select></p>';
}
mysql_close($wpdb->connection);

$db = LocalDatabase::getReadable('master');
echo '<br><b>Attendees:</b>';
$query = 'SELECT * FROM ' . $knighttable . ' WHERE LODGE = '.$lodgeNum.' ORDER BY NICKNAME';
$result = mysql_query($query, $db->connection);
if($result) {
	echo '<input type="checkbox" name="mtgParticipants[]" value="NONE" style="display:none;" />';
	echo '<table border="0"><tr>';
	$i=1;
	while($row = mysql_fetch_array($result)){
		if($row['DATE_WITHDREW'] == '0000-00-00') {
			if($row['NICKNAME'] == "") {
				echo '<td><input type="checkbox" name="mtgParticipants[]" value="' . $row['RECORD_NUM'] . '" /> ' . $row['FNAME'] . ' ' . $row['LNAME'] . '&nbsp;&nbsp;</td>';
			} else {
			echo '<td><input type="checkbox" name="mtgParticipants[]" value="' . $row['RECORD_NUM'] . '" /> ' . $row['NICKNAME'] . '&nbsp;&nbsp;</td>';
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
echo '<input type="hidden" name="lodgeNum" value="'.$lodgeNum.'" />';
echo '<p><input type="submit" value="Record Attendance"></p>';

mysql_close($db->connection);

	
function formatDate($element) {
	if($element < 10) {
		return '0' . $element;
	} else {
		return $element;
	}
}

?>
