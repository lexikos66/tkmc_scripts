<?php

if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR','/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');
require_once(SCRIPTS_DIR.'/includes/form_functions.inc');

//Connect To Database
$db = LocalDatabase::getReadable('master');
$lodgetable='lodge_directory';
$ktable='knight_directory';
$rtable='ride_directory';
$ctable='ride_corollary';
$lodgeNum=0;
$lodge='';

$current_user = wp_get_current_user();
$roles = implode('","',$current_user->roles);
$query = 'SELECT * FROM ' . $lodgetable . ' WHERE LODGE_CODE in ("'. $roles .'")';
$result = mysqli_query($db->connection, $query);
if($result) {
  while($row = mysqli_fetch_array($result)){
    $lodgenum = $row['LODGE_NUM'];
  }
}

$query = 'SELECT * FROM ' . $ktable . ' WHERE LODGE = "' . $lodgenum . '" AND NICKNAME = "' . $current_user->display_name . '"';
$result = mysqli_query($db->connection, $query);
echo '<div style="height:709px;width:742px;overflow-x:hidden;overflow-y:scroll;"><table>';

if($result) {
	while($row = mysqli_fetch_array($result)){
		$mileage = calc_mileage($row, $rtable, $ctable, $db);
		$rides = get_rides($row, $rtable, $ctable, $db);
		display_info($row,$rides,$mileage);
	}
}
echo '</table></div>';
mysqli_close($db->connection);

function display_info($knightdata,$rides,$mileage) {
  echo '<tr><td><font color="black">';
  echo '<font size="5">' . $knightdata['FNAME'] . " '" . $knightdata['NICKNAME'] . "' " . $knightdata[LNAME] . '</font><br>';
  echo '<br>';
  echo $rides;
  echo '<br><font size="4"><b>Total Miles Logged: ' . $mileage . '</b></font><br>';
  echo '</font></td></tr>';
}

function get_rides($knightdata,$rtable,$ctable,$db) {
  $rides = "<table>";
  $cquery = 'SELECT * FROM ' . $ctable . ' WHERE KNIGHT_NUM = ' . $knightdata['RECORD_NUM'];
  $cresult = mysqli_query($db->connection, $cquery);
  if($cresult) {
    while($crow = mysqli_fetch_array($cresult)) {
      $ridequery = 'SELECT * FROM ' . $rtable . ' WHERE RIDE_NUM = ' . $crow['RIDE_NUM'] . ' ORDER BY DATE';
      $rideresult = mysqli_query($db->connection, $ridequery);
      if($rideresult) {
        $riderow = mysqli_fetch_array($rideresult);
        $rides = $rides . '<tr><td width="250">' . $riderow['NAME'] . '</td><td>' . $riderow['MILES'] . '</td></tr>';
      }
    }
  }
  $rides = $rides . '</table>';
  return $rides;
}

?>