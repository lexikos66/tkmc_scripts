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
$query = 'SELECT k.NICKNAME AS NICKNAME, k.RANK AS RANK, k.DATE_KNIGHTED AS DATE_KNIGHTED, k.EMAIL AS EMAIL, l.lodge_name AS lodgename FROM ' . $ktable . ' AS k, '.$ltable.' AS l WHERE k.RANK != 3 AND k.RANK != 4 AND k.DATE_WITHDREW = "0000-00-00" AND k.DATE_KNIGHTED != "0000-00-00" AND k.LODGE = l.lodge_num';
$result = mysql_query($query, $db->connection);
if($result)  {
	$i = 0;
	while($row = mysql_fetch_array($result)) {
		$knightArray[$i] = array("nickname" => $row['NICKNAME'], "rank" => $row['RANK'], "email" => $row['EMAIL'], "knightyear" => substr($row['DATE_KNIGHTED'], 0, 4), "knightmonth" => substr($row['DATE_KNIGHTED'], 5, 2), "knightday" => substr($row['DATE_KNIGHTED'], 8, 2), "lodgename" => $row['lodgename']);
		$i++;
	}
	
	$knightSummaryArray = array();
	$i=0;
	foreach($knightArray as $knight) {
		if ($day == $knight["knightday"] && $month == $knight["knightmonth"]) {
			mailit($knight, $year - $knight["knightyear"]);
			$knightSummaryArray[$i] = array("nickname" => $knight["nickname"], "years" => $year - $knight["knightyear"], "lodge" => $knight["lodgename"]);
			$i++;
		}
	}
	if (sizeof($knightSummaryArray) > 0) {
		mailsummary($knightSummaryArray, $month, $day, $year);
	}
}

mysql_close($db->connection);

function formatDate($element) {
	if($element < 10) {
		return '0' . $element;
	} else {
		return $element;
	}
}

function mailit($knight, $numYears) {
	$years = $numYears;
	switch (substr($years, -1)) {
		case '1':
			$years .= 'st';
			break;
		case '2':
			$years .= 'nd';
			break;
		case '3':
			$years .= 'rd';
			break;
		default:
			$years .= 'th';
	}
	$headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' .  "\r\n";
    $headers .= 'From: TKMC Grand Council <contact@templarknightsmc.com>' . "\r\n";
    $headers .= 'Reply-To: contact@templarknightsmc.com' . "\r\n";
	$headers .= 'BCC: hollywood@templarknightsmc.com' . "\r\n";

    $message = '
      <html>
      <body>
        Hey '.$knight["nickname"].", <br>
		<br>
		Congratulations on your ".$years.' year as a Knight in the Templar Knights Motorcycle Club. We look forward to the continued growth and strength of the Club and express our thanks and respect to you, our Brother, for your contributions.<br>
		<br>
		Fraternally,<br>
		<br><br>
		The Grand Commandery 
      </body>
	  </html>';
    $subject = $numYears.' Year Commemoration';
    mail($knight["email"], $subject, $message, $headers);
}

function mailsummary($knightArray, $month, $day, $year) {
	$headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' .  "\r\n";
    $headers .= 'From: TKMC Grand Council <contact@templarknightsmc.com>' . "\r\n";
    $headers .= 'Reply-To: contact@templarknightsmc.com' . "\r\n";
	$summaryTable = '<table><tr><th>Knight</th><th>Years with TKMC</th><th>Lodge</th></tr>';
	foreach($knightArray as $knight) {
		$summaryTable .= '<tr><td>'.$knight["nickname"].'</td><td><center>'.$knight["years"].'</center></td><td>'.$knight["lodge"].'</td></tr>';
	}
	$summaryTable .= '</table>';
	$message = '
	<html>
	<body>
		The following Knights are commemorating their anniversaries with the TKMC today:<br><br>'.$summaryTable.'
		<br>
	</body>
	</table>';
	$subject = 'Commemoration Summary for '.$month.'/'.$day.'/'.$year;
	mail('contact@templarknightsmc.com', $subject, $message, $headers);
}

?>