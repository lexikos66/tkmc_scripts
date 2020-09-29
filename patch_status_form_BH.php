<?php

if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/includes/form_functions.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');

//Connect To Database
$db = LocalDatabase::getReadable('master');
$ktable = 'knight_directory';
$ltable = 'lodge_directory';
$ptable='provisional_progress';
$lodgeNum = '';

// If provisional_ID is not given, then show all active provisionals
if (!isset($_GET['provisional_id'])) {

	if (isset($_GET['updated'])) {
		echo '<p style="font-weight:bold;font-size:16px;">The Provisional record has been updated successfully.</p>';
	}

	$query = 'SELECT p.*, k.FNAME, k.LNAME, k.NICKNAME, l.LODGE_NAME, l.EMAIL_ADDR, l.COUNTRY FROM '.$ptable.' as p JOIN '.$ktable.' as k ON k.RECORD_NUM = p.knight_num JOIN '.$ltable.' as l ON k.LODGE = l.LODGE_NUM WHERE (p.tr_request_date != "0000-00-00" AND p.tr_shipped_date = "0000-00-00") OR (p.request_date != "0000-00-00" AND p.shipped_date = "0000-00-00") ORDER BY l.LODGE_NAME, k.FNAME';
	
	
	$result = mysql_query($query, $db->connection);
	if ($result) {
		while($row = mysql_fetch_array($result)){
			$tr_approved = false;
			$app_fee_paid = false;
			$patches_fee_paid = false;
			$tr_can_order = false;
			$tr_ordered = false;
			$tr_shipped = false;
			$patches_can_order = false;
			$patches_ordered = false;
			$patches_shipped = false;
			$status_str = '';

			echo '<p><a href="templarknightsmc.com/council-area/grand-council/patch-status?provisional_id='.$row['knight_num'].'">'.$row['FNAME'].' '.$row['LNAME'].'</a> ['.$row['LODGE_NAME'].'] - ';
			if ($row['t1_approved'] && $row['t2_approved'] && $row['t3_approved']) { $tr_approved = true; }
			if ($row['app_fee_date'] == "0000-00-00") { $status_str .= 'App Fee Needs Paid/Recorded | '; } else { $app_fee_paid = true; }
			if ($row['COUNTRY'] != 'Sweden') { 
				if ($row['patches_paid_date'] == "0000-00-00") { $status_str .= 'Patches Fee Needs Paid/Recorded | '; } else { $patches_fee_paid = true; }
			} else { $patches_fee_paid = true; }
			if (!$tr_approved) { $status_str .= 'Top Rocker Needs Approved | '; }
			if ($tr_approved && $app_fee_paid && $patches_fee_paid) { $tr_can_order = true; }
			if ($row['tr_ordered_date'] != "0000-00-00") { $tr_ordered = true; }
			if ($tr_can_order && !$tr_ordered) { $status_str .= 'Top Rocker Needs Ordered | '; }
			if ($row['tr_shipped_date'] != "0000-00-00") { $tr_shipped = true; }
			if ($tr_ordered && !$tr_shipped) { $status_str .= 'Top Rocker Needs Shipped | '; }
			if ($row['tr_shipped_date'] != "0000-00-00") { $tr_shipped = true; }
			if ($tr_shipped && !$row['t5_approved']) { $status_str .= 'Center Patch Needs Approved | '; }
			if ($row['ordered_date'] != "0000-00-00") { $patches_ordered = true; }
			if ($row['t5_approved'] && $tr_shipped) { $patches_can_order = true; }
			if ($patches_can_order && $row['request_date'] != "0000-00-00" && !$patches_ordered) { $status_str .= 'Center Patch Needs Ordered | '; }
			if ($row['shipped_date'] != "0000-00-00") { $patches_shipped = true; }
			if ($patches_ordered && !$patches_shipped) { $status_str .= 'Center Patch Needs Shipped | '; }			
			echo substr($status_str, 0, strlen($status_str) - 2);
			echo '</p>';
		}
	}
} else {
	echo '<p><a href="http://templarknightsmc.com/council-area/grand-council/patch-status">Back</a></p>';
	echo '<table><tr><td>Task 1: Find an event the Lodge could help promote</td><td>Task 4: Patch Symbolism</td></tr>';
	echo '<tr><td>Task 2: Volunteer time at a current event</td><td>Task 5: Growth of the Order</td></tr>';
	echo '<tr><td>Task 3: Ride 400 miles with Mentor</td><td></td></tr></table>';
	echo '<form action="http://templarknightsmc.com/scripts/patch_status_action.php" method="post">';
	$query = 'SELECT kd.FNAME, kd.LNAME, kd.NICKNAME, ld.LODGE_NAME, ld.EMAIL_ADDR, ld.COUNTRY FROM '.$ktable.' as kd JOIN '.$ltable.' as ld ON kd.LODGE = ld.LODGE_NUM WHERE kd.RECORD_NUM = ' . $_GET["provisional_id"];
	$result = mysql_query($query, $db->connection);
	if($result) {
		while($row = mysql_fetch_array($result)) {
			echo 'PROVISIONAL: ' . $row['FNAME'] . " '" . $row['NICKNAME'] . "' " . $row['LNAME'] . '<br>';
			echo '<div style="display:none;"><input type="hidden" name="first_name" value="' . $row['FNAME'] . '"></div>';
			echo '<div style="display:none;"><input type="hidden" name="last_name" value="' . $row['LNAME'] . '"></div>';
			echo '<div style="display:none;"><input type="hidden" name="knight_name" value="' . $row['FNAME'] . " '" . $row['NICKNAME'] . "' " . $row['LNAME'] . '"></div>';
			echo '<div style="display:none;"><input type="hidden" name="knight_num" value="' . $_GET['provisional_id'] . '"></div>';
			echo 'LODGE: ' . $row['LODGE_NAME'];
			echo '<div style="display:none;"><input type="hidden" name="lodge_name" value="' . $row['LODGE_NAME'] . '"></div>';
			echo '<div style="display:none;"><input type="hidden" name="lodge_email" value="' . $row['EMAIL_ADDR'] . '"></div>';
			echo '<div style="display:none;"><input type="hidden" name="country" value="' . $row['COUNTRY'] . '"></div>';
			echo '<div style="display:none;"><input type="hidden" name="nickname" value="' . $row['NICKNAME'] . '"></div>';

			echo '<table><tr><th></th><th>Approved</th><th>Completion Date<br>(yyyy-mm-dd)</th><th>Task Description</th></tr>';

			$query = 'SELECT * FROM '.$ptable.' WHERE knight_num = ' . $_GET['provisional_id'];
			$tresult = mysql_query($query, $db->connection);
			if ($tresult) {
				$progress = mysql_fetch_array($tresult);
				// Show stuff for Task 1
				if ($progress['t1_approved']) {
					echo '<tr><td>Task #1</td><td><input type="checkbox" name="tasksApproved[]" value="task1" checked/></td><td>' . $progress['t1_date'] . '</td><td>' . $progress['t1_detail'] . '</td></tr>';
				} else {
					echo '<tr><td>Task #1</td><td><input type="checkbox" name="tasksApproved[]" value="task1" /></td><td>' . $progress['t1_date'] . '</td><td>' . $progress['t1_detail'] . '</td></tr>';
				}
				// Show stuff for Task 2
				if ($progress['t2_approved']) {
					echo '<tr><td>Task #2</td><td><input type="checkbox" name="tasksApproved[]" value="task2" checked/></td><td>' . $progress['t2_date'] . '</td><td>' . $progress['t2_detail'] . '</td></tr>';
				} else {
					echo '<tr><td>Task #2</td><td><input type="checkbox" name="tasksApproved[]" value="task2" /></td><td>' . $progress['t2_date'] . '</td><td>' . $progress['t2_detail'] . '</td></tr>';
				}
				// Show stuff for Task 3
				if ($progress['t3_approved']) {
					echo '<tr><td>Task #3</td><td><input type="checkbox" name="tasksApproved[]" value="task3" checked/></td><td>' . $progress['t3_date'] . '</td><td>' . $progress['t3_detail'] . '</td></tr>';
				} else {
					echo '<tr><td>Task #3</td><td><input type="checkbox" name="tasksApproved[]" value="task3" /></td><td>' . $progress['t3_date'] . '</td><td>' . $progress['t3_detail'] . '</td></tr>';
				}
				// Show stuff for task 4
				echo '<tr><td>Task #4</td><td><input type="checkbox" name="tasksNotApproved[]" value="task4" checked disabled/></td><td>' . $progress['t4_date'] . '</td><td>' . $progress['t4_detail'] . '</td></tr>';
				// Show stuff for Task 5
				if ($progress['t5_approved']) {
					echo '<tr><td>Task #5</td><td><input type="checkbox" name="tasksApproved[]" value="task5" checked/></td><td>' . $progress['t5_date'] . '</td><td>' . $progress['t5_detail'] . '</td></tr>';
				} else {
					echo '<tr><td>Task #5</td><td><input type="checkbox" name="tasksApproved[]" value="task5" /></td><td>' . $progress['t5_date'] . '</td><td>' . $progress['t5_detail'] . '</td></tr>';
				}
				echo '</table><table>';
				echo '<tr><td>Knighting Date (yyyy-mm-dd):</td><td>' . $progress['knighting_date'] . '</td></tr>';
				echo '<tr><td>Patches Needed by (yyyy-mm-dd):</td><td>' . $progress['needed_by'] . '</td></tr>';
				echo '<tr><td>Nickname:</td><td>' . $row['NICKNAME'] . '</td></tr>';
				echo '<tr><td>Patches Needed:</td><td>';
				if ($progress['top_rocker']) {
					echo '<input type="checkbox" name="tr_dontsend[]" value="top_rocker" checked disabled/><font color="gray">Top Rocker Requested on '.$progress['tr_request_date'].'</font>';
				} else {
					echo '<input type="checkbox" name="patchesNeeded[]" value="top_rocker" disabled/><font color="gray">Top Rocker</font>';
				}
				echo '<br>';
				if ($progress['bottom_rocker']) {
					echo '<input type="checkbox" name="patchesNeeded[]" value="bottom_rocker" checked disabled/><font color="gray">Bottom Rocker Requested on '. $progress['request_date'] . '</font>';
				} else {
					echo '<input type="checkbox" name="patchesNeeded[]" value="bottom_rocker" disabled/><font color="gray">Bottom Rocker</font>';
				}
				echo '<br>';
				if ($progress['center_patch']) {
					echo '<input type="checkbox" name="patchesNeeded[]" value="center_patch" checked disabled/><font color="gray">Center Club Patch Requested on '. $progress['request_date'] . '</font>';
				} else {
					echo '<input type="checkbox" name="patchesNeeded[]" value="center_patch" disabled/><font color="gray">Center Club Patch</font>';
				}
				echo '<br>';
				if ($progress['rank_patch']) {
					echo '<input type="checkbox" name="patchesNeeded[]" value="rank_patch" checked disabled/><font color="gray">Rank Patch Requested on '. $progress['request_date'] . '</font>';
				} else {
					echo '<input type="checkbox" name="patchesNeeded[]" value="rank_patch" disabled/><font color="gray">Rank Patch</font>';
				}
				echo '<br>';
				if ($progress['nickname_patch']) {
					echo '<input type="checkbox" name="patchesNeeded[]" value="nickname_patch" checked disabled/><font color="gray">Nickname Patch Requested on '. $progress['request_date'] . '</font>';
				} else {
					echo '<input type="checkbox" name="patchesNeeded[]" value="nickname_patch" disabled/><font color="gray">Nickname Patch</font>';
				}
				echo '<br>';
				echo '</td></tr>';
				echo '</table>';
				echo '<tr><td><input type="submit" name="patch_status" value="Update Record" /></td>';
				echo '<br><hr>';
				echo '<p style="text-align:center;font-size:20px">--- Grand Master Sergeant Only ---- Grand Master Sergeant Only ----</p>';
				echo '<hr><br><table>';
				//$current_user = wp_get_current_user();
				/**
				 * @example Safe usage: $current_user = wp_get_current_user();
				 * if ( !($current_user instanceof WP_User) )
				 *     return;
				 */
				//$approved_users = 'Hollywood Pepperman reaper';
				//$is_approved = strpos($approved_users, $current_user->user_login);
				echo '<tr><td>Application Fee Paid (yyyy-mm-dd):</td><td><input type="text" name="app_fee_date" value="'.$progress['app_fee_date'].'" /></td></tr>';
				
				if ($row['COUNTRY'] == 'Sweden') {
					echo '<tr><td>Top Rocker Fee Paid (yyyy-mm-dd):</td><td>*This will be set automagically to the Application Fee Paid date.*</td></tr>';
					echo '<tr><td>Top Rocker Ordered Date (yyyy-mm-dd):</td><td><input type="text" name="tr_ordered_date" value="'.$progress['tr_ordered_date'].'" /></td></tr>';
					echo '<tr><td>Other Patches Fee Paid (yyyy-mm-dd):</td><td>*This will be set automagically to the Top Rocker Ordered date.*</td></tr>';
					echo '<tr><td>Other Patches Ordered Date (yyyy-mm-dd):</td><td><input type="text" name="ordered_date" value="'.$progress['ordered_date'].'" /></td></tr>';
				} else {
					echo '<tr><td>Patches Fee Paid (yyyy-mm-dd):</td><td><input type="text" name="patches_paid_date" value="'.$progress['patches_paid_date'].'" /></td></tr>';
					echo '<tr><td>Top Rocker Ordered Date (yyyy-mm-dd):</td><td><input type="text" name="tr_ordered_date" value="'.$progress['tr_ordered_date'].'" /></td></tr>';
					echo '<tr><td>Top Rocker Shipped Date (yyyy-mm-dd):</td><td><input type="text" name="tr_shipped_date" value="'.$progress['tr_shipped_date'].'" /></td></tr>';
					echo '<tr><td>Other Patches Ordered Date (yyyy-mm-dd):</td><td><input type="text" name="ordered_date" value="'.$progress['ordered_date'].'" /></td></tr>';
					echo '<tr><td>Other Patches Shipped Date (yyyy-mm-dd):</td><td><input type="text" name="shipped_date" value="'.$progress['shipped_date'].'" /></td></tr>';
				}
				echo '<tr><td>Notes:</td><td><textarea name="notes" rows="4" cols="45">' . $progress[notes] . '</textarea></td></tr>';
				echo '</table>';
				echo '<tr><td><input type="submit" name="patch_status" value="Update Record" /></td>';

			} else {
				echo '</table>';
			}
		}
	}
	echo '</form>';
}

mysql_close($db->connection);
	
function formatDate($element) {
	if($element < 10) {
		return '0' . $element;
	} else {
		return $element;
	}
}

function getState($state_num, $db) {
	$query = 'SELECT STATE_NAME FROM state_directory WHERE STATE_NUM = '.$state_num;
	$result = mysql_query($query, $db->connection);
	$row = mysql_fetch_array($result);
	return $row['STATE_NAME'];
}

function getCountry($country_num, $db) {
	$query = 'SELECT COUNTRY_NAME FROM country_directory WHERE COUNTRY_NUM = '.$country_num;
	$result = mysql_query($query, $db->connection);
	$row = mysql_fetch_array($result);
	return $row['COUNTRY_NAME'];
}

?>