<?php

if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');
	
//Connect To Database
$requiredId=3;
$lodgeNum=1;
$ktable='knight_directory';
$ctable='attendance_corollary';
$knightArray = "";
$ohio172_users = 'CrowTKMCOhio172';

// If Council from other Lodge - get UserID and set Event Required ID and Lodge Number
$current_user = wp_get_current_user();
// Set if they are in Ohio172
if (strpos($ohio172_users, $current_user->user_login)) {
	$requiredId=26;
	$lodgeNum=9;
}

if(isset($_GET["mtgYear"])) {
	$mtgYear = $_GET["mtgYear"];
	$nextYear = $mtgYear + 1;
	$todaysDate = getdate();
	$year = $todaysDate['year'];
	$month = formatDate($todaysDate['mon']);
	$day = formatDate($todaysDate['mday']);
	if($mtgYear != $year) {
		$year = $mtgYear;
		$month = "12";
		$day = "31";
	}
	$db = LocalDatabase::getReadable('master');

	echo '<a href="http://templarknightsmc.com/council-area/grand-council/attendance-report">Back</a>';

	echo '<div style="height:709px;width:820px;overflow-x:hidden;overflow-y:scroll;">';
	echo '<style type="text/css">
		table
		{

		}
		table,th,td
		{
		border:1px solid white;
		}
		th
		{
		text-align:center;
		padding:5px;
		background-color:white;
		color:black;
		font-size:16px;
		}
		td
		{
		color:black;
		padding:3px;
		font-size:14px;
		}
		</style>';
	echo '<table><tr><th>Name</th><th>Attendance</th><th>Events Missed</th></tr>';
	$query = "SELECT * FROM " . $ktable . " WHERE LODGE = ".$lodgeNum." AND DATE_JOINED < '" . $nextYear . "-01-01' ORDER BY RECORD_NUM";
	$result = mysql_query($query, $db->connection);
	if($result)  {
		$i = 0;
		while($row = mysql_fetch_array($result)) {
			$eventArray = "";
			if($row['DATE_WITHDREW'] == '0000-00-00' && $row['RANK'] != "Honorary") {
				if($row['NICKNAME'] == "") {
					$knightArray[$i]['name'] = $row['FNAME'] . ' ' . $row['LNAME'];
				}
				else {
					$knightArray[$i]['name'] = $row['NICKNAME'];
				}
				$knightArray[$i]['id'] = $row['RECORD_NUM'];
				$knightArray[$i]['join'] = $row['DATE_JOINED'];
				$mquery = 'SELECT * FROM ' . $ctable . ' WHERE knight_num = ' . $row['RECORD_NUM'];
				$mresult = mysql_query($mquery, $db->connection);
				if($mresult) {
					while($mrow = mysql_fetch_array($mresult)) {
						$eventArray[$mrow['event_id']] = $mrow['event_id'];
					}
				}
				if($eventArray == "") {
					$knightArray[$i]['events'] = "";
				} else {
					$knightArray[$i]['events'] = implode(",",$eventArray);
				}
				$i++;
			}
		}
	}
	mysql_close($db->connection);
	$wpdb = LocalDatabase::getReadable('wor7320');
	foreach($knightArray as $knight) {
		
		$query = 'SELECT wp_hiqx_em_events.event_id AS event_id, wp_hiqx_em_events.event_name AS event_name,
			wp_hiqx_em_events.event_start_date AS start_date FROM wp_hiqx_em_events, wp_hiqx_term_relationships
			WHERE wp_hiqx_term_relationships.term_taxonomy_id = '.$requiredId.' AND wp_hiqx_em_events.event_start_date > "' . $knight['join'] . 
			'" AND wp_hiqx_em_events.event_start_date > "' . $mtgYear . '-01-01" AND wp_hiqx_em_events.event_start_date <= "' . $year . '-' . $month . '-' . $day . 
			'" AND wp_hiqx_em_events.post_id = wp_hiqx_term_relationships.object_id AND wp_hiqx_em_events.recurrence_id != "" ORDER BY wp_hiqx_em_events.event_start_date'; 
		$result = mysql_query($query, $wpdb->connection);
		$eventsAttended = 0;
		$totalEvents = 0;
		$missedEvents = "";
		echo '<tr><td>' . $knight['name'] . '</td>';
		if($result) {
			$eventArray = explode(",", $knight['events']);
			while($row = mysql_fetch_array($result)) {
				$eventFound = 0;
				foreach($eventArray as $event) {
					if($row['event_id'] == $event) {
						$eventsAttended++;
						$eventFound = 1;
					}
				}
				if($eventFound == 0) {
					$missedEvents[$row['event_id']] = $row['event_name'] . ':' . $row['start_date'];
				}
				$totalEvents++;
			}
			if($totalEvents == 0) {
				echo '<td>100%</td>';
			} else {
				echo '<td>' . number_format((($eventsAttended / $totalEvents) * 100)) . '%</td>';
			}
			if($missedEvents == "") {
				echo '<td></td>';
			} else {
				echo '<td>' . implode("\n", $missedEvents) . '</td>';
			}
		} else {
			echo '<td>100%</td><td></td></tr>';
		}
	}
	echo '</table></div>';
	mysql_close($wpdb->connection);
} else {
	  echo '<form action="http://templarknightsmc.com/council-area/grand-council/attendance-report" method="get">';
	  $todaysDate = getDate();
	  $numYears = $todaysDate['year'] - 2010;
	  echo '<p>Select Year:&nbsp;&nbsp;';
	  echo '<select name="mtgYear">';
	  for($i = $numYears; $i > 0; $i--) {
		echo '<option value="' . ($todaysDate['year'] - $i) . '">' . ($todaysDate['year'] - $i) . '</option>';
	  }
	  echo '<option selected="yes" value="' . $todaysDate['year'] . '">' . $todaysDate['year'] . '</option>';
	  echo '</select></p>';
	  echo '<p><input type="submit" value="Generate Report"></p>';
      echo '</form>';
  }
  
function formatDate($element) {
	if($element < 10) {
		return '0' . $element;
	} else {
		return $element;
	}
}
?>
