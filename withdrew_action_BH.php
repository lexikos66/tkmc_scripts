<?php

if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');
	
//Connect To Database
$db = LocalDatabase::getReadable('master');
$ktable='knight_directory';
$ltable='lodge_directory';
$knightArray = array();

$todaysDate = getdate();
$year = $todaysDate['year'];
$month = formatDate($todaysDate['mon']);
$day = formatDate($todaysDate['mday']);
$today = $year.'-'.$month.'-'.$day;
$weekago = strtotime("-1 week");
$weekagoDate = getdate($weekago);
$wYear = $weekagoDate['year'];
$wMonth = formatDate($weekagoDate['mon']);
$wDay = formatDate($weekagoDate['mday']);
$week = $wYear.'-'.$wMonth.'-'.$wDay;

$query = 'SELECT k.FNAME AS FIRSTNAME, k.LNAME AS LASTNAME, k.NICKNAME AS NICKNAME, k.RANK AS RANK, k.DATE_KNIGHTED AS DATE_KNIGHTED, k.NOTES AS NOTES, l.lodge_name AS lodgename FROM ' . $ktable . ' AS k, '.$ltable.' AS l WHERE k.DATE_WITHDREW >= "'.$week.'" AND k.DATE_WITHDREW <= "'.$today.'" AND k.LODGE = l.lodge_num';
$result = mysql_query($query, $db->connection);
if($result)  {
	$i = 0;
	while($row = mysql_fetch_array($result)) {
		$knightArray[$i] = array("firstname" => $row['FIRSTNAME'], "nickname" => $row['NICKNAME'], "lastname" => $row['LASTNAME'], "rank" => $row['RANK'], "knightyear" => substr($row['DATE_KNIGHTED'], 0, 4), "knightmonth" => substr($row['DATE_KNIGHTED'], 5, 2), "knightday" => substr($row['DATE_KNIGHTED'], 8, 2), "lodgename" => $row['lodgename'], "notes" => $row['NOTES']);
		$i++;
	}
	if (!empty($knightArray)) {
		mailsummary($knightArray);
	}
}

function formatDate($element) {
	if($element < 10) {
		return '0' . $element;
	} else {
		return $element;
	}
}

mysql_close($db->connection);

function mailsummary($knightArray) {
	$headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' .  "\r\n";
    $headers .= 'From: TKMC Grand Council <contact@templarknightsmc.com>' . "\r\n";
    $headers .= 'Reply-To: contact@templarknightsmc.com' . "\r\n";
	$summaryTable = '<table border="1" cellpadding="10"><tr><th>Knight</th><th>Status</th><th>Lodge</th><th>Notes</th></tr>';
	foreach($knightArray as $knight) {
		$summaryTable .= '<tr><td>'.$knight["firstname"].' "'.$knight["nickname"].'" '.$knight["lastname"].'</td><td><center>'.$knight["rank"].'</center></td><td>'.$knight["lodgename"].'</td><td>'.$knight["notes"].'</tr>';
	}
	$summaryTable .= '</table>';
	$message = '
	<html>
	<body>
		The following Knights were removed from the records of the Local Lodge:<br><br>'.$summaryTable.'
		<br>
	</body>
	</table>';
	$subject = 'Member Removal Summary';
	mail('grand.council@templarknightsmc.com', $subject, $message, $headers);
}

?>