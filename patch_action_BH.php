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
$requestNotify='hollywood@templarknightsmc.com, reaper@templarknightsmc.com, johnny@templarknightsmc.com, bruce@templarknightsmc.com, swan@templarknightsmc.com';

if ($_POST['doUpdate'] == 1) {
	$query = "UPDATE " . $table_name . " SET t1_date='" . $_POST['t1_date'] . "', t1_detail='" . $_POST['t1_detail'] . "', t2_date='" . $_POST['t2_date'] . "', t2_detail='" . $_POST['t2_detail'] . "', t3_date='" . $_POST['t3_date'] . "', t3_detail='" . $_POST['t3_detail'] . "', t4_date='" . $_POST['t4_date'] . "', t4_detail='" . $_POST['t4_detail'] . "', t5_date='" . $_POST['t5_date'] . "', t5_detail='" . $_POST['t5_detail'] . "', top_rocker_date='" . $_POST['patches_paid_date'] . "', knighting_date='" . $_POST['knighting_date'] . "', needed_by='" . $_POST['needed_by'] . "', app_fee_date='" . $_POST['app_fee_date'] . "', patches_paid_date='" . $_POST['patches_paid_date'] . "', ordered_date='" . $_POST['ordered_date'] . "', shipped_date='" . $_POST['shipped_date'] . "', tr_paid_date='" . $_POST['patches_paid_date'] . "', tr_ordered_date='" . $_POST['ordered_date'] . "', tr_shipped_date='" . $_POST['tr_shipped_date'] . "', notes='" . $_POST['notes'] . "', nickname='". $_POST['nickname'] . "' WHERE knight_num=" . $_POST['knight_num'];
	$result = mysql_query($query, $db->connection);
	if(!$result) {
		exit("Failed to update record for " . $_POST['knight_name'] . "-" . mysql_error());
	}
} else {
	$query = "INSERT into " . $table_name . " (knight_num, t1_date, t1_detail, t2_date, t2_detail, t3_date, t3_detail, t4_date, t4_detail, t5_date, t5_detail, top_rocker_date, knighting_date, needed_by, app_fee_date, patches_paid_date, ordered_date, shipped_date, tr_paid_date, tr_ordered_date, tr_shipped_date, notes, nickname) VALUES (" . $_POST['knight_num'] . ", '" . $_POST['t1_date'] . "', '" . $_POST['t1_detail'] . "', '" . $_POST['t2_date'] . "', '" . $_POST['t2_detail'] . "', '" . $_POST['t3_date'] . "', '" . $_POST['t3_detail'] . "', '" . $_POST['t4_date'] . "', '" . $_POST['t4_detail'] . "', '" . $_POST['t5_date'] . "', '" . $_POST['t5_detail'] . "', '" . $_POST['patches_paid_date'] . "', '" . $_POST['knighting_date'] . "', '" . $_POST['needed_by'] . "', '" . $_POST['app_fee_date'] . "', '" . $_POST['patches_paid_date'] . "', '" . $_POST['ordered_date'] . "', '" . $_POST['shipped_date'] . "', '" . $_POST['patches_paid_date'] . "', '" . $_POST['ordered_date'] . "', '" . $_POST['tr_shipped_date'] . "', '" . $_POST['notes'] . "', '" . $_POST['nickname'] . "')";
	$result = mysql_query($query, $db->connection);
	if (!$result) {
		exit("Failed to insert record for " . $_POST['knight_name'] . "-" . mysql_error());
	}
}

$patch_html = '';
if (isset($_POST['patchesNeeded'])) {
	$tr_ordered = false;
	$patchesNeeded = implode(",",$_POST['patchesNeeded']);
	foreach(explode(",",$patchesNeeded) as $patch) {
		$query = 'UPDATE ' . $table_name . ' SET top_rocker=1, bottom_rocker=1, center_patch=1, rank_patch=1, nickname_patch=1 WHERE knight_num=' . $_POST['knight_num'];
		$result = mysql_query($query);
		if(!$result) {
			exit("Failed to insert record or " .  $_POST['knight_name'] . "-" . mysql_error());
		}
		$tr_ordered = true;
		$patch_html = 'Top Rocker<br>Bottom Rocker<br>Center Patch<br>Rank Patch<br>Nickname Patch - ' . $_POST['nickname'] . '<br>';
	}
	mailit($requestNotify, $patch_html);
}

if (isset($_POST['order_patches'])) {
	$patch_html='';
	$order_tr = false;
	$order_patches = false;
	// query for patches
	$query = "SELECT * from " . $table_name . " WHERE knight_num='" . $_POST['knight_num'] . "'";
	$result = mysql_query($query, $db->connection);
	if ($result) {
		$row = mysql_fetch_array($result);
		if ($row['top_rocker'] == '1' && $row['tr_ordered_date'] == '0000-00-00') {
			$patch_html .= 'Top Rocker<br>';
			$order_tr = true;
		} else {
			$patch_html .= 'Top Rocker<br>';
		}
		if ($row['center_patch'] == '1') {
			$patch_html .= 'Bottom Rocker<br>
							Center Patch<br>
							Rank Patch<br>
							Nickname Patch - ' . $_POST['nickname'];
			$order_patches = true;
		}
		
		if ($order_tr) {
			$query = "UPDATE " . $table_name . " SET tr_ordered_date='" . date("Y-m-d") . "' WHERE knight_num=" . $_POST['knight_num'];
			$result = mysql_query($query, $db->connection);
			if(!$result) {
				exit("Failed to update ordered date for " . $_POST['knight_name'] . "-" . mysql_error());
			}
		}
			
		if($order_patches) {
			$query = "UPDATE " . $table_name . " SET ordered_date='" . date("Y-m-d") . "' WHERE knight_num=" . $_POST['knight_num'];
			$result = mysql_query($query, $db->connection);
			if(!$result) {
				exit("Failed to update ordered date for " . $_POST['knight_name'] . "-" . mysql_error());
			}
		}
		
		mailit($requestNotify, $patch_html);
	}
}


function mailit($mail_to, $patches) {
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
		  <tr><td>Nickname: </td><td>' . $_POST['nickname'] . '</td></tr>
		  <tr><td colspan="2"><br></td></tr>
		  <tr><td><b>PATCHES NEEDED:</b></td><td>' . $patches . '</td></tr>
		  <tr><td colspan="2"><br></td></tr>
		  <tr><td>Patches Needed By: </td><td>' . $_POST['needed_by'] . '</td></tr>
		  <tr><td>Knighting Date: </td><td>' . $_POST['knighting_date'] . '</td></tr>
		  <tr><td colspan="2"><br></td></tr>
          <tr><td>Application Fee Paid: </td><td>' . $_POST['app_fee_date'] . '</td></tr>
          <tr><td>Top Rocker Shipped: </td><td>' . $_POST['tr_shipped_date'] . '</td></tr>
          <tr><td>Date Patches Paid: </td><td>' . $_POST['patches_paid_date'] . '</td></tr>
          <tr><td>Date Patches Ordered: </td><td>' . $_POST['ordered_date'] . '</td></tr>
          <tr><td>Top Rocker Shipped: </td><td>' . $_POST['tr_shipped_date'] . '</td></tr>
          <tr><td>Date Patches Shipped: </td><td>' . $_POST['shipped_date'] . '</td></tr>
          <tr><td>Notes: </td><td>' . $_POST['notes'] . '</td></tr>
		</table><br>
      </body>
      </html>';
    $subject = $_POST['first_name'] . ' ' . $_POST['last_name'];
	if (isset($_POST['request_patches'])) {
		$subject .= ' - REQUEST Patches';
	} else if (isset($_POST['order_patches'])) {
		$subject .= ' - ORDER Patches Request';
	} else {
		$subject .= ' - SOMETHING WENT WRONG';
	}
	
	mail($mail_to, $subject, $message, $headers);
}

header("Location: http://templarknightsmc.com/council-area/patch-requestprovisional-progress"."?knightNum=".$_POST['knight_num']);
mysql_close($db->connection);

?>