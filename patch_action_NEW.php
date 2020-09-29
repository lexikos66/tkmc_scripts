<?php

if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/classes/Database.class');
require_once(SCRIPTS_DIR.'/includes/defines.inc');

//Connect To Database
$db = LocalDatabase::getWritable('master'); 
$table_name='provisional_progress';
$query="";
$nickname = $_POST['nickname'];
$requestNotify='grand.council@templarknightsmc.com, '.$_POST['lodge_email'];

if ($_POST['doUpdate'] == 1) {
	$query = "UPDATE " . $table_name . " SET t1_date='" . $_POST['t1_date'] . "', t1_detail='" . $_POST['t1_detail'] . "', t2_date='" . $_POST['t2_date'] . "', t2_detail='" . $_POST['t2_detail'] . "', t3_date='" . $_POST['t3_date'] . "', t3_detail='" . $_POST['t3_detail'] . "', t4_date='" . $_POST['t4_date'] . "', t4_detail='" . $_POST['t4_detail'] . "', t5_date='" . $_POST['t5_date'] . "', t5_detail='" . $_POST['t5_detail'] . "', knighting_date='" . $_POST['knighting_date'] . "', needed_by='" . $_POST['needed_by'] . "', nickname='". $nickname . "' WHERE knight_num=" . $_POST['knight_num'];
	$result = mysqli_query($db->connection, $query);
	if(!$result) {
		exit("Failed to update record for " . $_POST['knight_name'] . "-" . mysqli_error());
	}
} else {
	$query = "INSERT into " . $table_name . " (knight_num, t1_date, t1_detail, t2_date, t2_detail, t3_date, t3_detail, t4_date, t4_detail, t5_date, t5_detail, knighting_date, needed_by, nickname) VALUES (" . $_POST['knight_num'] . ", '" . $_POST['t1_date'] . "', '" . $_POST['t1_detail'] . "', '" . $_POST['t2_date'] . "', '" . $_POST['t2_detail'] . "', '" . $_POST['t3_date'] . "', '" . $_POST['t3_detail'] . "', '" . $_POST['t4_date'] . "', '" . $_POST['t4_detail'] . "', '" . $_POST['t5_date'] . "', '" . $_POST['t5_detail'] . "', '" . $_POST['knighting_date'] . "', '" . $_POST['needed_by'] . "', '". $nickname . "')";
	$result = mysqli_query($db->connection, $query);
	if (!$result) {
		exit("Failed to insert record for " . $_POST['knight_name'] . "-" . mysqli_error());
	}
}

$patch_html = '';
if (isset($_POST['patchesNeeded']) && $_POST['patch_request'] == 'Request Patches') {
	$tr_ordered = false;
	$patchesNeeded = implode(",",$_POST['patchesNeeded']);
	foreach(explode(",",$patchesNeeded) as $patch) {
		$query = 'UPDATE ' . $table_name . ' SET ' . $patch . '=1 WHERE knight_num=' . $_POST['knight_num'];
		$result = mysqli_query($db->connection, $query);
		if(!$result) {
			exit("Failed to update record or " .  $_POST['knight_name'] . "-" . mysqli_error());
		}
		if ($patch == "top_rocker") {
			$query = 'UPDATE ' . $table_name . ' SET tr_request_date="'.date("Y-m-d").'" WHERE knight_num=' . $_POST['knight_num'];
			$result = mysqli_query($db->connection, $query);
			if(!$result) {
				exit("Failed to update record or " .  $_POST['knight_name'] . "-" . mysqli_error());
			}
		}
		if ($patch == "center_patch") {
			$query = 'UPDATE ' . $table_name . ' SET request_date="'.date("Y-m-d").'" WHERE knight_num=' . $_POST['knight_num'];
			$result = mysqli_query($db->connection, $query);
			if(!$result) {
				exit("Failed to update record or " .  $_POST['knight_name'] . "-" . mysqli_error());
			}
		}

		$patch = ucwords(str_replace('_', ' ', $patch));
		if ($patch == 'Top Rocker') {
			$patch_html .= $patch . '<br>';
			$tr_ordered = true;
		} else if ($patch == 'Nickname Patch') {
			$patch_html .= $patch . ' - ' . $_POST['nickname'] . '<br>';
		} else {
			$patch_html .= $patch . '<br>';
		}
	}
	$query = 'SELECT * from '.$table_name.' WHERE knight_num='.$_POST['knight_num'];
	$result = mysqli_query($db->connection, $query);
	if ($result) {
		$row = mysqli_fetch_array($result);
		if (!$tr_ordered) { $patch_html = 'Top Rocker - ' . $row['tr_ordered_date'] . '<br>' . $patch_html; }
		mailit($requestNotify, $patch_html, $row, $nickname);
	}
}

header("Location: http://member.templarknightsmc.com/provisional-progress/update-provisional?knightNum=".$_POST['knight_num']);
mysqli_close($db->connection);


function mailit($mail_to, $patches, $row, $nickname) {
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' .  "\r\n";
	$headers .= 'From: TKMC Patch Request <contact@templarknightsmc.com>' . "\r\n";
    $headers .= 'Reply-To: contact@templarknightsmc.com' . "\r\n";

    $message = '
      <html>
      <head>
        <title>Patch Order Request</title>
      </head>
      <body>
        <p>Patches are being requested for:</p><br>
        <table>
          <tr><td colspan="2" valign="center"><b>LODGE: ' . $_POST['lodge_name'] . '</b></td></tr>
          <tr><td>First Name: </td><td>' . $_POST['first_name'] . '</td></tr>
          <tr><td>Last Name: </td><td>' . $_POST['last_name'] . '</td></tr>
		  <tr><td>Nickname: </td><td>' . $nickname . '</td></tr>
		  <tr><td colspan="2"><br></td></tr>
		  <tr><td><b>PATCHES NEEDED:</b></td><td>' . $patches . '</td></tr>
		  <tr><td colspan="2"><br></td></tr>
		  <tr><td>Patches Needed By: </td><td>' . $_POST['needed_by'] . '</td></tr>
		  <tr><td>Knighting Date: </td><td>' . $_POST['knighting_date'] . '</td></tr>
		  <tr><td colspan="2"><br></td></tr>
          <tr><td>Application Fee Paid: </td><td>' . $row['app_fee_date'] . '</td></tr>
          <tr><td>Top Rocker Fee Paid: </td><td>' . $row['tr_paid_date'] . '</td></tr>
          <tr><td>Top Rocker Ordered: </td><td>' . $row['tr_ordered_date'] . '</td></tr>
          <tr><td>Top Rocker Shipped: </td><td>' . $row['tr_shipped_date'] . '</td></tr>
          <tr><td>Date Patches Paid: </td><td>' . $row['patches_paid_date'] . '</td></tr>
          <tr><td>Date Patches Ordered: </td><td>' . $row['ordered_date'] . '</td></tr>
          <tr><td>Date Patches Shipped: </td><td>' . $row['shipped_date'] . '</td></tr>
          <tr><td>Notes: </td><td>' . $row['notes'] . '</td></tr>
		  <tr><td colspan="2"></td></tr>
		  <tr><td>Task #1 Detail</td><td>'. $_POST['t1_detail'] . '</td></tr>
		  <tr><td>Task #2 Detail</td><td>'. $_POST['t2_detail'] . '</td></tr>
		  <tr><td>Task #3 Detail</td><td>'. $_POST['t3_detail'] . '</td></tr>
		  <tr><td>Task #5 Detail</td><td>'. $_POST['t5_detail'] . '</td></tr>
		</table><br>
      </body>
      </html>';
    $subject = $_POST['first_name'] . ' ' . $_POST['last_name'];
	$subject .= ' - REQUEST Patches';
	mail($mail_to, $subject, $message, $headers);
}

?>