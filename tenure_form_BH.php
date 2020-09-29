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
$query = 'SELECT k.NICKNAME AS NICKNAME, k.DATE_KNIGHTED AS DATE_KNIGHTED, k.RECORD_NUM AS RECORD_NUM, l.lodge_name AS lodgename FROM ' . $ktable . ' AS k, '.$ltable.' AS l WHERE k.RANK != 3 AND k.RANK != 4 AND k.DATE_WITHDREW = "0000-00-00" AND k.DATE_KNIGHTED != "0000-00-00" AND k.LODGE = l.lodge_num ORDER BY k.DATE_KNIGHTED, k.RECORD_NUM';
$result = mysqli_query($db->connection, $query);
if($result)  {
	$i = 0;
	while($row = mysqli_fetch_array($result)) {
		$knightyear = substr($row['DATE_KNIGHTED'], 0, 4);
		$knightmonth = substr($row['DATE_KNIGHTED'], 5, 2);
		$knightday = substr($row['DATE_KNIGHTED'], 8, 2);
		$years = $year - $knightyear;
		$months = $month - $knightmonth;
		if ($months <= 0) {
			$months += 12;
			$years--;
		}
		if ($months == 12) {
			$months -= 12;
			$years++;
		}
		$monthsGone = reinstated($db, $row['RECORD_NUM']);
		if ($monthsGone != 0) {
			while ($monthsGone > 11) {
				$years--;
				$monthsGone -= 12;
			}
			$months -= $monthsGone;
			if ($months < 0) {
				$months = 12 + $months; 
				$years--;
			}
		}
		$knightArray[$years][$i] = array("nickname" => $row['NICKNAME'], "years" => $years, "months" => $months, "lodgename" => $row['lodgename']);
		$i++;
	}
	krsort($knightArray);
	$arrayKeys = array_keys($knightArray);
	$ak = 0;
	echo '<table>';
	foreach($knightArray as $yearGroup) {
		echo '<tr><th colspan="4"><center><b>'.$arrayKeys[$ak].'+</b> years of service</center></th></tr>
		<tr><th>Knight</th><th>Years</th><th>Months</th><th>Lodge</th></tr>';
		foreach($yearGroup as $knight) {
			echo '<tr><td>'.$knight["nickname"].'</td><td>'.$knight["years"].'</td><td>'.$knight["months"].'</td><td>'.$knight["lodgename"].'</td></tr>';
		}
		$ak++;
	}
	echo '</table>';
}

mysqli_close($db->connection);

function formatDate($element) {
	if($element < 10) {
		return '0' . $element;
	} else {
		return $element;
	}
}

function reinstated($db, $record_num) {
	$query = 'SELECT * FROM reinstatement_corollary WHERE knight_num = '.$record_num;
	$result = mysqli_query($db->connection, $query);
	if($result)  {
		$rrow = mysqli_fetch_array($result);
		$wyear = substr($rrow['date_withdrew'], 0, 4);
		$wmonth = substr($rrow['date_withdrew'], 5, 2);
		$wday = substr($rrow['date_withdrew'], 8, 2);
		$ryear = substr($rrow['date_reinstated'], 0, 4);
		$rmonth = substr($rrow['date_reinstated'], 5, 2);
		$rmonth = substr($rrow['date_reinstated'], 5, 2);
	
		$years = $ryear - $wyear;
		$months = $rmonth - $wmonth;
		if ($months <= 0) {
			$months += 12;
			$years--;
		}
		if ($months == 12) {
			$months -= 12;
			$years++;
		}
		return $months + ($years * 12);
	} else {
		return 0;
	}
}
?>