<?php

$begin_date = '0000-00-00';
$end_date = '0000-00-00';
$todaysDate = getdate();
$year = $todaysDate[year];

switch ($_GET['month']){
	case '1':
		$begin_date = $year.'-01-01';
		$end_date = $year.'-01-31';
		break;
	case '2':
		$begin_date = $year.'-02-01';
		if ($year % 4 == 0) {
			$feb_end = '29';
		} else {
			$feb_end = '28';
		}
		$end_date = $year.'-02-'.$feb_end;
		break;
	case '3':
		$begin_date = $year.'-03-01';
		$end_date = $year.'-03-31';
		break;
	case '4':
		$begin_date = $year.'-04-01';
		$end_date = $year.'-04-30';
		break;
	case '5':
		$begin_date = $year.'-05-01';
		$end_date = $year.'-05-31';
		break;
	case '6':
		$begin_date = $year.'-06-01';
		$end_date = $year.'-06-30';
		break;
	case '7':
		$begin_date = $year.'-07-01';
		$end_date = $year.'-07-31';
		break;
	case '8':
		$begin_date = $year.'-08-01';
		$end_date = $year.'-08-31';
		break;
	case '9':
		$begin_date = $year.'-09-01';
		$end_date = $year.'-09-30';
		break;
	case '10':
		$begin_date = $year.'-10-01';
		$end_date = $year.'-10-31';
		break;
	case '11':
		$begin_date = $year.'-11-01';
		$end_date = $year.'-11-30';
		break;
	case '12':
		$begin_date = $year.'-12-01';
		$end_date = $year.'-12-31';
		break;
}
$_POST["scope"] = array($begin_date,$end_date);
header("Location: http://templarknightsmc.com/letsride_wp/?page_id=11");

	