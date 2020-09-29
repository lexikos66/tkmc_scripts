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
	echo '<p><a href="http://templarknightsmc.com/council-area/grand-council/apppatch-fees?lodgeNum='.$_GET['lodgeNum'].'">Back</a></p>';
	echo '<p>Use this area to record Applicaton Fees and Patch Fees that have been received.</p>';
	echo '<form action="http://templarknightsmc.com/scripts/appfee_action.php" method="post">';
	$query = 'SELECT kd.FNAME, kd.LNAME, kd.NICKNAME, ld.LODGE_NAME FROM knight_directory as kd JOIN lodge_directory as ld ON kd.LODGE = ld.LODGE_NUM WHERE kd.RECORD_NUM = ' . $_GET["knightNum"];
	$result = mysql_query($query, $db->connection);
	if($result) {
		while($row = mysql_fetch_array($result)) {
			echo 'PROVISIONAL: ' . $row['FNAME'] . " '" . $row['NICKNAME'] . "' " . $row['LNAME'] . '<br>';
			echo '<div style="display:none;"><input type="hidden" name="first_name" value="' . $row['FNAME'] . '"></div>';
			echo '<div style="display:none;"><input type="hidden" name="last_name" value="' . $row['LNAME'] . '"></div>';
			echo '<div style="display:none;"><input type="hidden" name="knight_name" value="' . $row['FNAME'] . " '" . $row['NICKNAME'] . "' " . $row['LNAME'] . '"></div>';
			echo '<div style="display:none;"><input type="hidden" name="nickname" value="'.$row['NICKNAME'].'"></div>';
			echo '<div style="display:none;"><input type="hidden" name="knight_num" value="' . $_GET["knightNum"] . '"></div>';
			echo '<div style="display:none;"><input type="hidden" name="pageId" value="' . $pageId . '"></div>';
			echo '<div style="display:none;"><input type="hidden" name="lodge" value="' . $lodge . '"></div>';
			echo 'LODGE: ' . $row['LODGE_NAME'];
			echo '<div style="display:none;"><input type="hidden" name="lodge_name" value="' . $row['LODGE_NAME'] . '"></div>';
			echo '<div style="display:none;"><input type="hidden" name="lodgeNum" value="'.$_GET['lodgeNum'].'"></div>';
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

			$query = 'SELECT * FROM provisional_progress WHERE knight_num = ' . $_GET["knightNum"];
			$tresult = mysql_query($query, $db->connection);
			if(mysql_num_rows($tresult) != 1) {
				echo '<div style="display:none;"><input type="hidden" name="doUpdate" value="0" /></div>';
			} else {
				echo '<div style="display:none;"><input type="hidden" name="doUpdate" value="1" /></div>';
			}
			$progress = mysql_fetch_array($tresult);

			if ($progress['app_fee_date'] == "0000-00-00" || $progress['app_fee_date'] == "") {
				if ($is_approved === false) {
					echo '<tr><td>Application Fee Paid (yyyy-mm-dd):</td><td><input type="text" disabled name="app_fee_date"/></td></tr>';
				} else {
					echo '<tr><td>Application Fee Paid (yyyy-mm-dd):</td><td><input type="text" name="app_fee_date"/></td></tr>';
				}
			} else {
				echo '<tr><td>Application Fee Paid (yyyy-mm-dd):</td><td><input type="text" name="app_fee_date" value="'.$progress['app_fee_date'].'" /></td></tr>';
			}
			if ($progress['patches_paid_date'] == "0000-00-00" || $progress['patches_paid_date'] == "") {
				if ($is_approved === false) {
					echo '<tr><td>Date Patches Paid (yyyy-mm-dd):</td><td><input type="text" disabled name="patches_paid_date"/></td></tr>';
				} else {
					echo '<tr><td>Date Patches Paid (yyyy-mm-dd):</td><td><input type="text" name="patches_paid_date"/></td></tr>';
				}
			} else {
				echo '<tr><td>Date Patches Paid (yyyy-mm-dd):</td><td><input type="text" name="patches_paid_date" value="'.$progress['patches_paid_date'].'" /></td></tr>';
			}
			echo '<tr><td>Notes:</td><td><textarea name="notes" rows="4" cols="45">' . $progress['notes'] . '</textarea></td></tr>';
			echo '</table>';
			echo '<input type="submit" name="update_record" value="Update Record" />';
		} 
	}
	echo '</p>';
	echo '</form>';
} else if (isset ($_GET["lodgeNum"])) {
	echo '<p><a href="http://templarknightsmc.com/council-area/grand-council/apppatch-fees">Back</a></p>';
	echo '<form action="http://templarknightsmc.com/council-area/grand-council/apppatch-fees" method="get">';
	nonpatched_dropdown($db, $ktable, $_GET["lodgeNum"]);
  	mysql_close($db->connection);
	echo '</p>';
	echo '<div style="display:none;"><input type="hidden" name="lodgeNum" value="'.$_GET['lodgeNum'].'"></div>';
	echo '<p><input type="submit" value="Get Provisional Record"></p>';
	echo '</form>';
} else {
	echo '<form action="http://templarknightsmc.com/council-area/grand-council/apppatch-fees" method="get">';
	lodge_dropdown($db, $ltable);
	echo '</p>';
	echo '<p><input type="submit" value="Get Provisionals"></p>';
	echo '</form>';
}

mysql_close($db->connection);
?>