<?php

if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');
require_once(SCRIPTS_DIR.'/includes/form_functions.inc');
require_once(SCRIPTS_DIR.'/includes/form_tools.inc');
	
//Connect To Database
$db = LocalDatabase::getReadable('master');
$ktable='knight_directory';
$ptable='provisional_progress';
$ltable='lodge_directory';
$rtable='ride_directory';
$ctable='ride_corollary';
$lodgeNum='';

$current_user = wp_get_current_user();
$roles = implode('","',$current_user->roles);
$query = 'SELECT * FROM ' . $ltable . ' WHERE LODGE_CODE in ("'. $roles .'")';
$result = mysqli_query($db->connection, $query);
if($result) {
    while($row = mysqli_fetch_array($result)){
        $lodgeNum = $row['LODGE_NUM'];
    }
}

echo '<p><a href="http://member.templarknightsmc.com/provisional-progress/provisional-report">Back</a><b>';
echo '<table><tr><td>Task 1: Find an event the Lodge could help promote</td><td>Task 4: Patch Symbolism <i>*Completed at Ceremony*</i></td></tr>';
echo '<tr><td>Task 2: Volunteer time at a current event</td><td>Task 5: Growth of the Order</td></tr>';
echo '<tr><td>Task 3: Ride 400 miles with Mentor</td><td></td></tr></table>';
echo '<hr><hr><table>';
$query = 'SELECT kd.FNAME, kd.LNAME, kd.NICKNAME, kd.RECORD_NUM, ld.LODGE_NAME FROM knight_directory as kd JOIN lodge_directory as ld ON kd.LODGE = ld.LODGE_NUM WHERE kd.LODGE = ' . $lodgeNum . ' AND (kd.RANK = 3 OR kd.RANK = 12) AND kd.DATE_WITHDREW = 0000-00-00 ORDER BY kd.FNAME';
$result = mysqli_query($db->connection, $query);
if($result) {
	while ($row = mysqli_fetch_array($result)) {
		echo '<tr><td colspan="2"><b><a href="http://member.templarknightsmc.com/provisional-progress/update-provisional?knightNum='. $row['RECORD_NUM'] . '&lodgeNum=' . $lodgeNum . '">' . $row['FNAME'] . ' "'. $row['NICKNAME'] . '" ' . $row['LNAME'] . '</a></b></td><td>Mentor: ' . get_mentor($row['RECORD_NUM'], $ktable, $db) . '</td><td>Lodge: ' . $row['LODGE_NAME'] . '</td></tr>';
		$query = 'SELECT * FROM ' . $ptable . ' WHERE knight_num = ' . $row['RECORD_NUM'];
		$presult = mysqli_query($db->connection, $query);
		if(mysqli_num_rows($presult) != 1) {
			echo '<tr><td colspan="4"><i>No Progress Recorded</i></td></tr>';
		} else {
			$progress = mysqli_fetch_array($presult);
			echo '<tr><td>Task 1:</td><td>' . $progress['t1_date'] . '</td><td colspan="2">' . $progress['t1_detail'] . '</td></tr>';
			echo '<tr><td>Task 2:</td><td>' . $progress['t2_date'] . '</td><td colspan="2">' . $progress['t2_detail'] . '</td></tr>';
			echo '<tr><td>Task 3:</td><td>' . $progress['t3_date'] . '</td><td colspan="2">' . calc_mileage($row, $rtable, $ctable, $db) . ' miles recorded</td></tr>';
			echo '<tr><td>Task 5:</td><td>' . $progress['t5_date'] . '</td><td colspan="2">' . $progress['t5_detail'] . '</td></tr>';
			echo '<tr><td>App Fee Paid:</td><td>' . $progress['app_fee_date'] . '</td><td>Top Rocker Fee:</td><td>' . $progress['tr_paid_date'] . '</td></tr>';
			echo '<tr><td>Patches Fee Paid:</td><td>' . $progress['patches_paid_date'] . '</td><td>Top Rocker Ordered:</td><td>' . $progress['tr_ordered_date'] . '</td></tr>';
			echo '<tr><td>Patches Ordered:</td><td>' . $progress['ordered_date'] . '</td><td></td><td></td></tr>';
		}
		echo '<tr><td colspan="4"><hr></td></tr>';
	}
}
echo '</table>';

mysqli_close($db->connection);
?>