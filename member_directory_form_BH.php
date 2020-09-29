<?php

if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');
require_once(SCRIPTS_DIR.'/includes/form_functions.inc');
	
//Connect To Database
$db = LocalDatabase::getReadable('master');
$ktable='knight_directory';
$ltable='lodge_directory';
$knightcount=0;
$provisionalcount=0;

if (isset($_GET["searchBy"]) || isset($_GET["lodgeNum"])) {
	if($_GET["searchBy"] == "BLANK") {
		$query = "SELECT * FROM " . $ktable . " WHERE LODGE = " . $_GET["lodgeNum"] . " ORDER BY CASE WHEN TKMC_NUM = '' THEN 1 ELSE 0 END";
	}
	else {
  		$query = 'SELECT * FROM ' . $ktable . ' WHERE ' . $_GET["searchBy"] . ' = "' . $_GET["searchName"] . '" ORDER BY RECORD_NUM';
	}
	$result = mysqli_query($db->connection, $query);
	echo '<a href="http://member.templarknightsmc.com/member-directory">Back</a><br>';
	if($result) {
  		while($row = mysqli_fetch_array($result)){
    		if($row['DATE_WITHDREW'] == "0000-00-00") {
      			$lquery = 'SELECT * FROM ' . $ltable . ' WHERE LODGE_NUM = ' . $row[LODGE];
      			$lresult = mysqli_query($db->connection, $lquery);
      			if($lresult) {
        			$lrow = mysqli_fetch_array($lresult);
        			if($row['RANK'] !== 'Honorary') {
           				display_info($row,$lrow);
           				if($row['RANK'] == 'Provisional') {
              				$provisionalcount++;
           				} else {
              				$knightcount++;
           				}	
        			}
      			}
    		}
  		}
	}
	echo 'Total: Knights [' . $knightcount . '] Provisionals [' . $provisionalcount . ']<br>';
	echo 'Records Displayed: ' . ($knightcount + $provisionalcount);
}
else {
	echo '<form action="http://member.templarknightsmc.com/member-directory" method="get">';
	echo '<p>Search by Name: ';
	echo '<select name="searchBy">';
	echo '  <option value="BLANK">Select . . . </option>';
	echo '  <option value="FNAME">First Name</option>';
	echo '  <option value="LNAME">Last Name</option>';
	echo '  <option value="NICKNAME">Nickname</option>';
	echo '</select>';
	echo '</p>';
	echo 'Name: <input type="text" name="searchName"/><br><br>';
	echo 'OR<br><br>';
	lodge_dropdown($db, $ltable);
	echo '</p>';
	echo '<p><input style="float:center; margin-right:12px;" type="submit" value="Search!" id="search-button" class="button"></p>';
	echo '</form>';
}
mysqli_close($db->connection);

function display_info($knightdata,$lodgedata) {
  echo '<div id="knight">';
  echo '<strong>' . $knightdata['FNAME'] . " '" . $knightdata['NICKNAME'] . "' " . $knightdata['LNAME'] . ' (' . $knightdata['RANK'] . ' ' . $knightdata['TKMC_NUM'] . ')</strong><br>';
  echo 'eMail: ' . $knightdata['EMAIL'] . '<br>';
  echo 'Phone: ' . format_phone($knightdata['PHONE']) . '<br>';
  echo 'City: ' . $knightdata['CITY'] . '<br>';
  echo 'Lodge: ' . $lodgedata['LODGE_NAME'] . '<br>';
  echo '-------------------- <br></div>';
}

function format_phone($phone) {
  $newphone = substr($phone,0,3) . '-' . substr($phone,3,3) . '-' . substr($phone,-4);
  return $newphone;
}

?>