<?php

if (!defined(SCRIPTS_DIR)) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');
require_once(SCRIPTS_DIR.'/includes/form_functions.inc');

//Connect To Database
$db = LocalDatabase::getReadable('master');
$ktable='knight_directory';
$ltable='lodge_directory';
$ptable='provisional_progress';
$lodgeNum='';
$task_array = array();

if (isset($_GET["knightNum"])) {
	echo '<p><a href="http://member.templarknightsmc.com/provisional-progress/provisional-report">Back</a></p>';
	echo '<table><tr><td>Task 1: Find an event the Lodge could help promote</td><td>Task 4: Patch Symbolism</td></tr>';
	echo '<tr><td>Task 2: Volunteer time at a current event</td><td>Task 5: Growth of the Order</td></tr>';
	echo '<tr><td>Task 3: Ride 400 miles with Mentor</td><td></td></tr></table>';
	echo '<form action="http://templarknightsmc.com/scripts/patch_action.php" method="post">';
	$query = 'SELECT kd.FNAME, kd.LNAME, kd.NICKNAME, ld.LODGE_NAME, ld.EMAIL_ADDR, ld.COUNTRY FROM '.$ktable.' as kd JOIN '.$ltable.' as ld ON kd.LODGE = ld.LODGE_NUM WHERE kd.RECORD_NUM = ' . $_GET["knightNum"];
	$result = mysqli_query($db->connection, $query);
	if($result) {
		while($row = mysqli_fetch_array($result)) {
			echo 'PROVISIONAL: ' . $row['FNAME'] . " '" . $row['NICKNAME'] . "' " . $row['LNAME'] . '<br>';
			echo '<div style="display:none;"><input type="hidden" name="first_name" value="' . $row['FNAME'] . '"></div>';
			echo '<div style="display:none;"><input type="hidden" name="last_name" value="' . $row['LNAME'] . '"></div>';
			echo '<div style="display:none;"><input type="hidden" name="knight_name" value="' . $row['FNAME'] . " '" . $row['NICKNAME'] . "' " . $row['LNAME'] . '"></div>';
			echo '<div style="display:none;"><input type="hidden" name="knight_num" value="' . $_GET['knightNum'] . '"></div>';
			echo 'LODGE: ' . $row['LODGE_NAME'];
			echo '<div style="display:none;"><input type="hidden" name="lodge_name" value="' . $row['LODGE_NAME'] . '"></div>';
			echo '<div style="display:none;"><input type="hidden" name="lodge_email" value="' . $row['EMAIL_ADDR'] . '"></div>';
			echo '<div style="display:none;"><input type="hidden" name="country" value="' . $row['COUNTRY'] . '"></div>';
			echo '<table><tr><th></th><th>Completion Date<br>(yyyy-mm-dd)</th><th>Task Description</th></tr>';

			$query = 'SELECT * FROM '.$ptable.' WHERE knight_num = ' . $_GET['knightNum'];
			$tresult = mysqli_query($db->connection, $query);
			if(mysqli_num_rows($tresult) != 1) {
				echo '<div style="display:none;"><input type="hidden" name="doUpdate" value="0" /></div>';
			} else {
				echo '<div style="display:none;"><input type="hidden" name="doUpdate" value="1" /></div>';
			}
			$progress = mysqli_fetch_array($tresult);
			if ($progress['t1_date'] == "0000-00-00" || $progress['t1_date'] == "") {
				echo '<tr><td>Task #1</td><td><input type="text" name="t1_date"/></td><td><textarea name="t1_detail" rows="2" cols="50"></textarea></td></tr>';
			} else {
				// We have stuff for Task 1 - lets keep it.
				echo '<tr><td>Task #1</td><td>' . $progress['t1_date'] . '</td><td>' . $progress['t1_detail'] . '</td></tr>';
				echo '<div style="display:none;"><input type="hidden" name="t1_date" value="'.$progress['t1_date'].'"></div>';
				echo '<div style="display:none;"><input type="hidden" name="t1_detail" value="'.$progress['t1_detail'].'"></div>';
				$task_array[1] = true;
			}
			if ($progress['t2_date'] == "0000-00-00" || $progress['t2_date'] == "") {
				echo '<tr><td>Task #2</td><td><input type="text" name="t2_date"/></td><td><textarea name="t2_detail" rows="2" cols="50"></textarea></td></tr>';
			} else {
				// We have stuff for Task 2 - lets keep it.
				echo '<tr><td>Task #2</td><td>' . $progress['t2_date'] . '</td><td>' . $progress['t2_detail'] . '</td></tr>';
				echo '<div style="display:none;"><input type="hidden" name="t2_date" value="'.$progress['t2_date'].'"></div>';
				echo '<div style="display:none;"><input type="hidden" name="t2_detail" value="'.$progress['t2_detail'].'"></div>';
				$task_array[2] = true;
			}
			if ($progress['t3_date'] == "0000-00-00" || $progress['t3_date'] == "") {
				echo '<tr><td>Task #3</td><td><input type="text" name="t3_date"/></td><td><textarea name="t3_detail" rows="2" cols="50"></textarea></td></tr>';
			} else {
				// We have stuff for Task 3 - lets keep it.
				echo '<tr><td>Task #3</td><td>' . $progress['t3_date'] . '</td><td>' . $progress['t3_detail'] . '</td></tr>';
				echo '<div style="display:none;"><input type="hidden" name="t3_date" value="'.$progress['t3_date'].'"></div>';
				echo '<div style="display:none;"><input type="hidden" name="t3_detail" value="'.$progress['t3_detail'].'"></div>';
				$task_array[3] = true;
			}
			if($task_array[1] && $task_array[2] && $task_array[3]) {
				echo '<tr><td colspan="3" align="center"><font color="green">** Top Rocker can be requested. **</font><br><i>When requesting the Top Rocker, the full set of patches needs to be paid for in full, unless you are in Sweden.  Sweden Lodges pay for patches on delivery (COD).</i></td></tr>';
			} else {
				echo '<tr><td colspan="3" align="center"><font color="red">** Cannot request Top Rocker until Tasks 1-3 are completed **</font></td></tr>';
			}
			if ($progress['t4_date'] == "0000-00-00" || $progress['t4_date'] == "") {
				echo '<tr><td>Task #4</td><td><input type="text" name="t4_date"/></td><td><textarea name="t4_detail" rows="2" cols="50"></textarea></td></tr>';
			} else {
				// We have stuff for task 4 - lets keep it.
				echo '<tr><td>Task #4</td><td>' . $progress['t4_date'] . '</td><td>' . $progress['t4_detail'] . '</td></tr>';
				echo '<div style="display:none;"><input type="hidden" name="t4_date" value="'.$progress['t4_date'].'"></div>';
				echo '<div style="display:none;"><input type="hidden" name="t4_detail" value="'.$progress['t4_detail'].'"></div>';
				$task_array[4] = true;
			}
			if ($progress['t5_date'] == "0000-00-00" || $progress['t5_date'] == "") {
				echo '<tr><td>Task #5</td><td><input type="text" name="t5_date"/></td><td><textarea name="t5_detail" rows="2" cols="50"></textarea></td></tr>';
			} else {
				// We have stuff for Task 5 - lets keep it.
				echo '<tr><td>Task #5</td><td>' . $progress['t5_date'] . '</td><td>' . $progress['t5_detail'] . '</td></tr>';
				echo '<div style="display:none;"><input type="hidden" name="t5_date" value="'.$progress['t5_date'].'"></div>';
				echo '<div style="display:none;"><input type="hidden" name="t5_detail" value="'.$progress['t5_detail'].'"></div>';
				$task_array[5] = true;
			}
			echo '</table><table>';
			if ($progress['knighting_date'] == "0000-00-00" || $progress['knighting_date'] == "") {
				echo '<tr><td>Knighting Date (yyyy-mm-dd):</td><td><input type="text" name="knighting_date"/></td></tr>';
			} else {
				echo '<tr><td>Knighting Date (yyyy-mm-dd):</td><td>' . $progress['knighting_date'] . '</td></tr>';
				echo '<div style="display:none;"><input type="hidden" name="knighting_date" value="'.$progress['knighting_date'].'" /></div>';
			}
			if ($progress['needed_by'] == "0000-00-00" || $progress['needed_by'] == "") {
				echo '<tr><td>Patches Needed by (yyyy-mm-dd):</td><td><input type="text" name="needed_by"/></td></tr>';
			} else {
				echo '<tr><td>Patches Needed by (yyyy-mm-dd):</td><td>' . $progress['needed_by'] . '</td></tr>';
				echo '<div style="display:none;"><input type="hidden" name="needed_by" value="'.$progress['needed_by'].'" /></div>';
			}
			if ($progress['nickname'] == "" && $row['NICKNAME'] == "") {
				echo '<tr><td>Nickname:</td><td><input type="text" name="nickname" disabled/> *Add Nickname through the Update Knight Utility.</td></tr>';
			} else {
				echo '<tr><td>Nickname:</td><td>' . $row['NICKNAME'] . '<div style="display:none;"><input type="hidden" name="nickname" value="' . $row['NICKNAME'] . '" /></div></td></tr>';
			}
			echo '<tr><td>Patches Needed:</td><td>';
			if($task_array[1] && $task_array[2] && $task_array[3] && (!$progress['top_rocker'])) {
				echo '<input type="checkbox" name="patchesNeeded[]" value="top_rocker" />Top Rocker<br>';
			} else {
				if ($progress['top_rocker']) {
					echo '<input type="checkbox" name="tr_dontsend[]" value="top_rocker" checked disabled/><font color="gray">Top Rocker Requested on '.$progress['tr_request_date'].'</font>';
				} else {
					echo '<input type="checkbox" name="patchesNeeded[]" value="top_rocker" disabled/><font color="gray">Top Rocker</font>';
				}
				echo '<br>';
			}
			if($task_array[1] && $task_array[2] && $task_array[3] && $task_array[5] && (!$progress['bottom_rocker'])) {
				echo '<input type="checkbox" name="patchesNeeded[]" value="bottom_rocker" />Bottom Rocker<br>';
			} else {
				if ($progress['bottom_rocker']) {
					echo '<input type="checkbox" name="patchesNeeded[]" value="bottom_rocker" checked disabled/><font color="gray">Bottom Rocker Requested on '. $progress['request_date'] . '</font>';
				} else {
					echo '<input type="checkbox" name="patchesNeeded[]" value="bottom_rocker" disabled/><font color="gray">Bottom Rocker</font>';
				}
				echo '<br>';
			}
			if($task_array[1] && $task_array[2] && $task_array[3] && $task_array[5] && (!$progress['center_patch'])) {
				echo '<input type="checkbox" name="patchesNeeded[]" value="center_patch" />Center Club Patch<br>';
			} else {
				if ($progress['center_patch']) {
					echo '<input type="checkbox" name="patchesNeeded[]" value="center_patch" checked disabled/><font color="gray">Center Club Patch Requested on '. $progress['request_date'] . '</font>';
				} else {
					echo '<input type="checkbox" name="patchesNeeded[]" value="center_patch" disabled/><font color="gray">Center Club Patch</font>';
				}
				echo '<br>';
			}
			if($task_array[1] && $task_array[2] && $task_array[3] && $task_array[5] && (!$progress['rank_patch'])) {
				echo '<input type="checkbox" name="patchesNeeded[]" value="rank_patch" />Rank Patch<br>';
			} else {
				if ($progress['rank_patch']) {
					echo '<input type="checkbox" name="patchesNeeded[]" value="rank_patch" checked disabled/><font color="gray">Rank Patch Requested on '. $progress['request_date'] . '</font>';
				} else {
					echo '<input type="checkbox" name="patchesNeeded[]" value="rank_patch" disabled/><font color="gray">Rank Patch</font>';
				}
				echo '<br>';
			}
			if($task_array[1] && $task_array[2] && $task_array[3] && $task_array[5] && (!$progress['nickname_patch'])) {
				echo '<input type="checkbox" name="patchesNeeded[]" value="nickname_patch" />Nickname Patch<br>';
			} else {
				if ($progress['nickname_patch']) {
					echo '<input type="checkbox" name="patchesNeeded[]" value="nickname_patch" checked disabled/><font color="gray">Nickname Patch Requested on '. $progress['request_date'] . '</font>';
				} else {
					echo '<input type="checkbox" name="patchesNeeded[]" value="nickname_patch" disabled/><font color="gray">Nickname Patch</font>';
				}
				echo '<br>';
			}
			echo '</td></tr>';
			echo '<tr><td><input type="submit" name="patch_request" value="Update Record" /></td>';
			if (($task_array[1] && $task_array[2] && $task_array[3] && !$progress[top_rocker]) || ($task_array[1] && $task_array[2] && $task_array[3] && $task_array[5])) {
				echo '<td><input type="submit" name="patch_request" value="Request Patches" /></td></tr>';
			} else {
				echo '<td><input type="submit" name="patch_request" value="Request Patches" disabled/></td></tr>';
			}
			echo '</table>';
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
			echo '<tr><td>Application Fee Paid (yyyy-mm-dd):</td><td>' . $progress['app_fee_date'] . '</td></tr>';
			echo '<div style="display:none;"><input type="hidden" name="app_fee_date" value="'.$progress['app_fee_date'].'" /></div>';
			
			if ($row['COUNTRY'] == 'Sweden') {
				echo '<tr><td>Top Rocker Fee Paid (yyyy-mm-dd):</td><td>*Cash on Delivery*</td></tr>';
				echo '<tr><td>Top Rocker Ordered Date (yyyy-mm-dd):</td><td>' . $progress['tr_ordered_date'] . '</td></tr>';
				echo '<tr><td>Other Patches Fee Paid (yyyy-mm-dd):</td><td>*Cash on Delivery*</td></tr>';
				echo '<tr><td>Other Patches Ordered Date (yyyy-mm-dd):</td><td>' . $progress['ordered_date'] . '</td></tr>';
			} else {
				echo '<tr><td>Patches Fee Paid (yyyy-mm-dd):</td><td>'.$progress['patches_paid_date'].'</td></tr>';
				echo '<tr><td>Top Rocker Ordered Date (yyyy-mm-dd):</td><td>' . $progress['tr_ordered_date'] . '</td></tr>';
				echo '<tr><td>Top Rocker Shipped Date (yyyy-mm-dd):</td><td>' . $progress['tr_shipped_date'] . '</td></tr>';
				echo '<tr><td>Other Patches Ordered Date (yyyy-mm-dd):</td><td>' . $progress['ordered_date'] . '</td></tr>';
				echo '<tr><td>Other Patches Shipped Date (yyyy-mm-dd):</td><td>' . $progress['shipped_date'] . '</td></tr>';
			}
			echo '<tr><td>Notes:</td><td><textarea name="notes" rows="4" cols="45" disabled>' . $progress[notes] . '</textarea></td></tr>';
			echo '</table>';
		} 
	}
	echo '</form>';
} else {
	echo 'Select a <a href="http://member.templarknightsmc.com/provisional-progress/provisional-report">Provisional</a>.'; 
}

mysqli_close($db->connection);
?>
