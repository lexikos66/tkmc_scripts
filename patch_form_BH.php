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
$lodgeNum='';
$task_array = array();

if (isset($_GET["knightNum"])) {
	echo '<p><a href="http://templarknightsmc.com/council-area/patch-requestprovisional-progress?lodgeNum='.$_GET['lodgeNum'].'">Back</a></p>';
	echo '<table><tr><td>Task 1: Find an event the Lodge could help promote</td><td>Task 4: Patch Symbolism</td></tr>';
	echo '<tr><td>Task 2: Volunteer time at a current event</td><td>Task 5: Growth of the Order</td></tr>';
	echo '<tr><td>Task 3: Ride 400 miles with Mentor</td><td></td></tr></table>';
	echo '<form action="http://templarknightsmc.com/scripts/patch_action.php" method="post">';
	$query = 'SELECT kd.FNAME, kd.LNAME, kd.NICKNAME, ld.LODGE_NAME FROM knight_directory as kd JOIN lodge_directory as ld ON kd.LODGE = ld.LODGE_NUM WHERE kd.RECORD_NUM = ' . $_GET["knightNum"];
	$result = mysql_query($query, $db->connection);
	if($result) {
		while($row = mysql_fetch_array($result)) {
			echo 'PROVISIONAL: ' . $row['FNAME'] . " '" . $row['NICKNAME'] . "' " . $row['LNAME'] . '<br>';
			echo '<div style="display:none;"><input type="hidden" name="first_name" value="' . $row['FNAME'] . '"></div>';
			echo '<div style="display:none;"><input type="hidden" name="last_name" value="' . $row['LNAME'] . '"></div>';
			echo '<div style="display:none;"><input type="hidden" name="knight_name" value="' . $row['FNAME'] . " '" . $row['NICKNAME'] . "' " . $row['LNAME'] . '"></div>';
			echo '<div style="display:none;"><input type="hidden" name="knight_num" value="' . $_GET["knightNum"] . '"></div>';
			echo '<div style="display:none;"><input type="hidden" name="pageId" value="' . $pageId . '"></div>';
			echo '<div style="display:none;"><input type="hidden" name="lodge" value="' . $lodge . '"></div>';
			echo 'LODGE: ' . $row['LODGE_NAME'];
			echo '<div style="display:none;"><input type="hidden" name="lodge_name" value="' . $row['LODGE_NAME'] . '"></div>';
			echo '<table><tr><th></th><th>Completion Date<br>(yyyy-mm-dd)</th><th>Task Description</th></tr>';

			$query = 'SELECT * FROM provisional_progress WHERE knight_num = ' . $_GET["knightNum"];
			$tresult = mysql_query($query, $db->connection);
			if(mysql_num_rows($tresult) != 1) {
				echo '<div style="display:none;"><input type="hidden" name="doUpdate" value="0" /></div>';
			} else {
				echo '<div style="display:none;"><input type="hidden" name="doUpdate" value="1" /></div>';
			}
			$progress = mysql_fetch_array($tresult);
			if ($progress['t1_date'] == "0000-00-00" || $progress['t1_date'] == "") {
				echo '<tr><td>Task #1</td><td><input type="text" name="t1_date"/></td><td><textarea name="t1_detail" rows="2" cols="50"></textarea></td></tr>';
				} else {
				echo '<tr><td>Task #1</td><td>' . $progress['t1_date'] . '</td><td>' . $progress['t1_detail'] . '</td></tr>';
				echo '<div style="display:none;"><input type="hidden" name="t1_date" value="'.$progress['t1_date'].'"></div>';
				echo '<div style="display:none;"><input type="hidden" name="t1_detail" value="'.$progress['t1_detail'].'"></div>';
				$task_array[1] = true;
			}
			if ($progress['t2_date'] == "0000-00-00" || $progress['t2_date'] == "") {
				echo '<tr><td>Task #2</td><td><input type="text" name="t2_date"/></td><td><textarea name="t2_detail" rows="2" cols="50"></textarea></td></tr>';
			} else {
				echo '<tr><td>Task #2</td><td>' . $progress['t2_date'] . '</td><td>' . $progress['t2_detail'] . '</td></tr>';
				echo '<div style="display:none;"><input type="hidden" name="t2_date" value="'.$progress['t2_date'].'"></div>';
				echo '<div style="display:none;"><input type="hidden" name="t2_detail" value="'.$progress['t2_detail'].'"></div>';
				$task_array[2] = true;
			}
			if ($progress['t3_date'] == "0000-00-00" || $progress['t3_date'] == "") {
				echo '<tr><td>Task #3</td><td><input type="text" name="t3_date"/></td><td><textarea name="t3_detail" rows="2" cols="50"></textarea></td></tr>';
			} else {
				echo '<tr><td>Task #3</td><td>' . $progress['t3_date'] . '</td><td>' . $progress['t3_detail'] . '</td></tr>';
				echo '<div style="display:none;"><input type="hidden" name="t3_date" value="'.$progress['t3_date'].'"></div>';
				echo '<div style="display:none;"><input type="hidden" name="t3_detail" value="'.$progress['t3_detail'].'"></div>';
				$task_array[3] = true;
			}
			if($task_array[1] && $task_array[2] && $task_array[3]) {
				echo '<tr><td colspan="3" align="center"><font color="green">** Top Rocker/Patches can be requested. **</font></td></tr>';
			} else {
				echo '<tr><td colspan="3" align="center"><font color="red">** Cannot request Top Rocker/Patches until Tasks 1-3 are completed **</font></td></tr>';
			}
			if ($progress['t4_date'] == "0000-00-00" || $progress['t4_date'] == "") {
				echo '<tr><td>Task #4</td><td><input type="text" name="t4_date"/></td><td><textarea name="t4_detail" rows="2" cols="50"></textarea></td></tr>';
			} else {
				echo '<tr><td>Task #4</td><td>' . $progress['t4_date'] . '</td><td>' . $progress['t4_detail'] . '</td></tr>';
				echo '<div style="display:none;"><input type="hidden" name="t4_date" value="'.$progress['t4_date'].'"></div>';
				echo '<div style="display:none;"><input type="hidden" name="t4_detail" value="'.$progress['t4_detail'].'"></div>';
				$task_array[4] = true;
			}
			if ($progress['t5_date'] == "0000-00-00" || $progress['t5_date'] == "") {
				echo '<tr><td>Task #5</td><td><input type="text" name="t5_date"/></td><td><textarea name="t5_detail" rows="2" cols="50"></textarea></td></tr>';
			} else {
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
				echo '<tr><td>Nickname:</td><td><input type="text" name="nickname" disabled/> *Add Nickname through Update Knight Utility.</td></tr>';
			} else {
				echo '<tr><td>Nickname:</td><td>' . $row['NICKNAME'] . '<div style="display:none;"><input type="hidden" name="nickname" value="' . $row['NICKNAME'] . '" /></div></td></tr>';
			}
			echo '<tr><td>Patches Needed:</td><td>';
			if($task_array[1] && $task_array[2] && $task_array[3]) {
				if ($progress['top_rocker']) {
					echo '<input type="checkbox" name="patchesNeeded[]" value="all_patches" disabled checked/>All Patches<br>';
					echo '<font color="gray">Top Rocker</font> - *Requested/Ordered on '. $progress['tr_ordered_date'] . '*<br>';
					echo '<font color="gray">Bottom Rocker</font> - *Requested/Ordered on '. $progress['ordered_date'] . '*<br>';
					echo '<font color="gray">Center Club Patch</font> - *Requested/Ordered on '. $progress['ordered_date'] . '*<br>';
					echo '<font color="gray">Rank Patch</font> - *Requested/Ordered on '. $progress['ordered_date'] . '*<br>';
					echo '<font color="gray">Nickname Patch</font> - *Requested/Ordered on '. $progress['ordered_date'] . '*<br>';
				} else {
					echo '<input type="checkbox" name="patchesNeeded[]" value="all_patches" />All Patches<br>';
					echo '<font color="gray">Top Rocker</font><br>';
					echo '<font color="gray">Bottom Rocker</font><br>';
					echo '<font color="gray">Center Club Patch</font><br>';
					echo '<font color="gray">Rank Patch</font><br>';
					echo '<font color="gray">Nickname Patch</font><br>';
				}
			} else {
					echo '<input type="checkbox" name="patchesNeeded[]" value="all_patches" disabled/>All Patches<br>';
					echo '<font color="gray">Top Rocker</font><br>';
					echo '<font color="gray">Bottom Rocker</font><br>';
					echo '<font color="gray">Center Club Patch</font><br>';
					echo '<font color="gray">Rank Patch</font><br>';
					echo '<font color="gray">Nickname Patch</font><br>';
			}
			echo '</td></tr>';
			echo '<tr><td><input type="submit" name="update_record" value="Update Record" /></td>';
			if (($task_array[1] && $task_array[2] && $task_array[3] && !$progress['top_rocker'])) {
				echo '<td><input type="submit" name="request_patches" value="Request Patches" /></td></tr>';
			} else {
				echo '<td><input type="submit" name="request_patches" value="Request Patches" disabled/></td></tr>';
			}
			echo '</table>';
			echo '<br><hr>';
			echo '<p style="text-align:center;font-size:20px">--- Grand Master Sergeant Only ---- Grand Master Sergeant Only ----</p>';
			echo '<hr><br><table>';
			$current_user = wp_get_current_user();
			/**
			 * @example Safe usage: $current_user = wp_get_current_user();
			 * if ( !($current_user instanceof WP_User) )
			 *     return;
			 */
			$approved_users = 'Hollywood Pepperman reaper';
			$is_approved = strpos($approved_users, $current_user->user_login);
			if ($progress['app_fee_date'] == "0000-00-00" || $progress['app_fee_date'] == "") {
				if ($is_approved === false) {
					echo '<tr><td>Application Fee Paid (yyyy-mm-dd):</td><td><input type="text" disabled name="app_fee_date"/></td></tr>';
				} else {
					echo '<tr><td>Application Fee Paid (yyyy-mm-dd):</td><td><input type="text" name="app_fee_date"/></td></tr>';
				}
			} else {
				echo '<tr><td>Application Fee Paid (yyyy-mm-dd):</td><td>' . $progress['app_fee_date'] . '</td></tr>';
				echo '<div style="display:none;"><input type="hidden" name="app_fee_date" value="'.$progress['app_fee_date'].'" /></div>';
			}
			// We don't let them order just the Top Rocker anymore so hide this instead of mucking with code and database inserts.
			echo '<div style="display:none;"><input type="hidden" name="tr_paid_date" value="'.$progress['tr_paid_date'].'" /></div>';
			echo '<div style="display:none;"><input type="hidden" name="tr_ordered_date" value="'.$progress['tr_ordered_date'].'" /></div>';

			if ($progress['patches_paid_date'] == "0000-00-00" || $progress['patches_paid_date'] == "") {
				if ($is_approved === false) {
					echo '<tr><td>Date Patches Paid (yyyy-mm-dd):</td><td><input type="text" disabled name="patches_paid_date"/></td></tr>';
				} else {
					echo '<tr><td>Date Patches Paid (yyyy-mm-dd):</td><td><input type="text" name="patches_paid_date"/></td></tr>';
				}
			} else {
				echo '<tr><td>Date Patches Paid (yyyy-mm-dd):</td><td>' . $progress['patches_paid_date'] . '</td></tr>';
				echo '<div style="display:none;"><input type="hidden" name="patches_paid_date" value="'.$progress['patches_paid_date'].'" /></div>';
			}
			echo '<tr><td>Date Ordered (yyyy-mm-dd):</td><td>' . $progress['ordered_date'] . '</td></tr>';
			echo '<div style="display:none;"><input type="hidden" name="ordered_date" value="'.$progress['ordered_date'].'" /></div>';

			if ($progress['tr_shipped_date'] == "0000-00-00" || $progress['tr_shipped_date'] == "") {
				if ($is_approved === false) {
					echo '<tr><td>Top Rocker Shipped Date (yyyy-mm-dd):</td><td><input type="text" disabled name="tr_shipped_date"/></td></tr>';
				} else {
					echo '<tr><td>Top Rocker Shipped Date (yyyy-mm-dd):</td><td><input type="text" name="tr_shipped_date"/></td></tr>';
				}
			} else {
				echo '<tr><td>Top Rocker Shipped Date (yyyy-mm-dd):</td><td>' . $progress['tr_shipped_date'] . '</td></tr>';
				echo '<div style="display:none;"><input type="hidden" name="tr_shipped_date" value="'.$progress['tr_shipped_date'].'" /></div>';
			}

			if ($progress['shipped_date'] == "0000-00-00" || $progress['shipped_date'] == "") {
				if ($is_approved === false) {
					echo '<tr><td>Shipped Date (yyyy-mm-dd):</td><td><input type="text" disabled name="shipped_date"/></td></tr>';
				} else {
					echo '<tr><td>Shipped Date (yyyy-mm-dd):</td><td><input type="text" name="shipped_date"/></td></tr>';
				}
			} else {
				echo '<tr><td>Shipped Date (yyyy-mm-dd):</td><td>' . $progress['shipped_date'] . '</td></tr>';
				echo '<div style="display:none;"><input type="hidden" name="shipped_date" value="'.$progress['shipped_date'].'" /></div>';
			}
			echo '<tr><td>Notes:</td><td><textarea name="notes" rows="4" cols="45">' . $progress['notes'] . '</textarea></td></tr>';
			if ($progress['ordered_date'] == '0000-00-00' && $progress['patches_paid_date'] != '0000-00-00') {
				echo '<tr><td><input type="submit" name="update_record" value="Update Record" /></td><td><input type="submit" name="order_patches" value="Order Patches" /></td></tr>';
			} else {
				echo '<tr><td><input type="submit" name="update_record" value="Update Record" /></td><td><input type="submit" name="order_patches" value="Order Patches" disabled /></td></tr>';
			}
			echo '</table>';
		} 
	}
	echo '</p>';
	echo '<div style="display:none;"><input type="hidden" name="page_id" value="1080" /></div>';
	echo '</form>';
} else if (isset ($_GET["lodgeNum"])) {
	echo '<p><a href="http://templarknightsmc.com/council-area/patch-requestprovisional-progress">Back</a></p>';
	echo '<form action="http://templarknightsmc.com/council-area/patch-requestprovisional-progress" method="get">';
	nonpatched_dropdown($db, $ktable, $_GET["lodgeNum"]);
  	mysql_close($db->connection);
	echo '</p>';
	echo '<div style="display:none;"><input type="hidden" name="lodgeNum" value="'.$_GET['lodgeNum'].'"></div>';
	echo '<p><input type="submit" value="Get Provisional Record"></p>';
	echo '</form>';
} else {
	echo '<form action="http://templarknightsmc.com/council-area/patch-requestprovisional-progress" method="get">';
	lodge_dropdown($db, $ltable);
	echo '</p>';
	echo '<div style="display:none;"><input type="hidden" name="page_id" value="1080" /></div>';
	echo '<p><input type="submit" value="Get Provisionals"></p>';
	echo '</form>';
}

mysql_close($db->connection);
?>