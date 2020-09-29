<?php
/**
 * This script just sends an email to Hollywood with the latest attendance report
 */

if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');
	
//Connect To Database
$db = LocalDatabase::getReadable('master');
$requiredId=3;
$lodgeNum=1;
$ktable='knight_directory';
$ctable='attendance_corollary';
$knightArray = array();
$errorMsg = '';
$msgBody = '<div><table><tr><th>Name</th><th>Attendance</th><th>Events Missed</th></tr>';

$todaysDate = getdate();
if (isset($_GET['year'])) {
	if ($_GET['year'] > 2010 && $_GET['year'] <= $todaysDate['year']) {
		$mtgYear = $_GET['year'];
	} else {
		$errorMsg .= 'Year argument was out of bounds.  Returning current year results.';
		$mtgYear = $todaysDate['year'];
	}
} else {
	$errorMsg .= 'Year argument was not given.  Returning current year results.';
	$mtgYear = $todaysDate['year'];
}
$nextYear = $mtgYear + 1;
$year = $todaysDate['year'];
$month = formatDate($todaysDate['mon']);
$day = formatDate($todaysDate['mday']);
if ($mtgYear != $year) {
	$year = $mtgYear;
	$month = '12';
	$day = '31';
}

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
			'" AND wp_hiqx_em_events.post_id = wp_hiqx_term_relationships.object_id ORDER BY wp_hiqx_em_events.event_start_date'; 
	$result = mysql_query($query, $wpdb->connection);
	$eventsAttended = 0;
	$totalEvents = 0;
	$missedEvents = "";
	$knightRow = '<tr><td>' . $knight['name'] . '</td>';
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
			$knightRow .= '<td>100%</td>';
		} else {
			$knightRow .= '<td>' . number_format((($eventsAttended / $totalEvents) * 100)) . '%</td>';
		}
		if($missedEvents == "") {
			$knightRow .= '<td></td>';
		} else {
			$knightRow .= '<td>' . implode("<br>", $missedEvents) . '</td>';
		}
	} else {
		$knightRow .= '<td>100%</td><td></td></tr>';
	}
	$msgBody .= $knightRow;
}
$msgBody .=  '</table></div>';
mailIt($msgBody, $mtgYear, $errorMsg);
mysql_close($wpdb->connection);
 
function formatDate($element) {
	if($element < 10) {
		return '0' . $element;
	} else {
		return $element;
	}
}

function mailIt($msgBody, $mtgYear, $errorMsg) {
	$headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' .  "\r\n";
    $headers .= 'From: TKMC Grand Council <contact@templarknightsmc.com>' . "\r\n";
    $headers .= 'Reply-To: contact@templarknightsmc.com' . "\r\n";

    $message = '
      <html>
      <body>
        Here is the ' . $mtgYear . ' Attendance Report you requested.<br>' . $errorMsg . '<br>' . $msgBody . '
		<br>
		Fraternally,<br>
		<br><br>
		The Grand Commandery 
      </body>
	  </html>';
    $subject = $mtgYear.' Attendance Report';
    mail('hollywood@templarknightsmc.com', $subject, $message, $headers);
}
?>