<?php

/**
 * Calculate mileage for Knight
 */
function calc_mileage($knightdata, $rtable, $ctable, $db) {
	$mileage = 0;
	$cquery = 'SELECT * FROM ' . $ctable . ' WHERE KNIGHT_NUM = ' . $knightdata[RECORD_NUM];
	$cresult = mysqli_query($db->connection, $cquery);
	if($cresult) {
		while($crow = mysqli_fetch_array($cresult)) {
			$ridequery = 'SELECT * FROM ' . $rtable . ' WHERE RIDE_NUM = ' . $crow[RIDE_NUM];
			$rideresult = mysqli_query($db->connection, $ridequery);
			if($rideresult) {
				$riderow = mysqli_fetch_array($rideresult);
				$mileage = $mileage + $riderow[MILES];
			}
		}
	}
	return $mileage;
}

/**
 * Get Mentor Nickname
 */
function get_mentor($knightNum, $ktable, $db) {
	$query = 'SELECT MENTOR FROM ' . $ktable . ' WHERE RECORD_NUM = ' . $knightNum;
	$result = mysqli_query($db->connection, $query);
	if($result) {
		$row = mysqli_fetch_array($result);
		$query = 'SELECT NICKNAME FROM ' . $ktable . ' WHERE RECORD_NUM = ' . $row[MENTOR];
		$result = mysqli_query($db->connection, $query);
		if($result) {
			$row = mysqli_fetch_array($result);
			return $row[NICKNAME];
		}
	}
}

/**
 * Get Lodge Array
*/
function get_lodge_array($db) {
	$lodgeArray = "";
	$query = 'SELECT * FROM lodge_directory';
	$result = mysqli_query($db->connection, $query);
	if($result) {
		while($row = mysqli_fetch_array($result)){
			$lodgeArray[$row[LODGE_NUM]] = $row[LODGE_NAME];
		}
	}
	return $lodgeArray;
} 