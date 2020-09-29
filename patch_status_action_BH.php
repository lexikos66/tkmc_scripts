<?php

if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/classes/Database.class');
require_once(SCRIPTS_DIR.'/includes/defines.inc');

//Connect To Database
$db = LocalDatabase::getWritable('master'); 
$table='provisional_progress';
$returnURL='http://templarknightsmc.com/council-area/grand-council/patch-status/?provisional_id='.$_POST['knight_num'];
$query='';
$subject='';
$next_steps='';
$patch_html = '';
$requestNotify='hollywood@templarknightsmc.com, reaper@templarknightsmc.com, johnny@templarknightsmc.com, bruce@templarknightsmc.com, swan@templarknightsmc.com';

$noTasks = false;
$query = 'SELECT t1_approved, t2_approved, t3_approved FROM '.$table.' WHERE knight_num='.$_POST['knight_num'];
$txresult = mysql_query($query, $db->connection);
if($txresult) {	
	$row = mysql_fetch_array($txresult);
	if ($row['t1_approved'] && $row['t2_approved'] && $row['t3_approved']) { $noTasks = true; }
}

if ($_POST['country'] == 'Sweden') {
	$query = "UPDATE " . $table . " SET app_fee_date='" . $_POST['app_fee_date'] . "', patches_paid_date='" . $_POST['tr_ordered_date'] . "', ordered_date='" . $_POST['ordered_date'] . "', shipped_date='" . $_POST['ordered_date'] . "', tr_paid_date='" . $_POST['app_fee_date'] . "', tr_ordered_date='" . $_POST['tr_ordered_date'] . "', tr_shipped_date='" . $_POST['tr_ordered_date'] . "', nickname='" . $_POST['nickname'] . "', notes='" . $_POST['notes'] . "' WHERE knight_num=" . $_POST['knight_num'];
} else {
	$query = "UPDATE " . $table . " SET app_fee_date='" . $_POST['app_fee_date'] . "', patches_paid_date='" . $_POST['patches_paid_date'] . "', ordered_date='" . $_POST['ordered_date'] . "', shipped_date='" . $_POST['shipped_date'] . "', tr_paid_date='" . $_POST['patches_paid_date'] . "', tr_ordered_date='" . $_POST['tr_ordered_date'] . "', tr_shipped_date='" . $_POST['tr_shipped_date'] . "', nickname='" . $_POST['nickname'] . "', notes='" . $_POST['notes'] . "' WHERE knight_num=" . $_POST['knight_num'];
}
$result = mysql_query($query, $db->connection);
if(!$result) {
	exit("Failed to update record for " . $_POST['knight_name'] . "-" . mysql_error());
}

$tasksApproved = implode(",",$_POST['tasksApproved']);
$updateStr = '';
foreach(explode(",", $tasksApproved) as $task) {
	switch ($task) {
		case 'task1': $updateStr .= 't1_approved=1,'; break;
		case 'task2': $updateStr .= 't2_approved=1,'; break;
		case 'task3': $updateStr .= 't3_approved=1,'; break;
		case 'task5': $updateStr .= 't5_approved=1,'; break;
	}
}
if ($updateStr != '') {
	$updateStr = substr($updateStr, 0, strlen($updateStr) - 1);
	$query = 'UPDATE '.$table.' SET '.$updateStr.' WHERE knight_num='.$_POST['knight_num'];
	$tresult = mysql_query($query, $db->connection);
	if(!$result) {	
		exit("Failed to update approvals for " . $_POST['knight_name'] . "-" . mysql_error());
	}
}

$query = 'SELECT * from '.$table.' WHERE knight_num='.$_POST['knight_num'];
$presult = mysql_query($query, $db->connection);
if ($presult) {
	$row = mysql_fetch_array($presult);
	$tr_approved = false;
	if ($row['t1_approved'] && $row['t2_approved'] && $row['t3_approved']) { $tr_approved = true; }
	if ($row['app_fee_date'] != "0000-00-00") { $subject = 'Application Fee Paid'; } else { $next_steps .= 'Application Fee needs to be paid.'; }
	if ($tr_approved && !$noTasks) { $subject = 'Top Rocker Approved'; } else if (!$noTasks) { $next_steps .= '<br>Top Rocker needs approved.'; }
	if ($row['tr_paid_date'] != "0000-00-00") { if ($_POST['country'] == 'Sweden') { $subject = $subject; } else { $subject = 'Patches Paid'; }} else { $next_steps .= '<br>Patch Fee needs to be paid.'; }
	if ($row['tr_ordered_date'] != "0000-00-00") { $subject = 'Top Rocker Ordered'; } else { $next_steps .= '<br>Top Rocker needs ordered.'; }
	if ($row['tr_shipped_date'] != "0000-00-00") { $subject = 'Top Rocker Shipped'; } else { $next_steps .= '<br>Top Rocker needs shipped.'; }
	if ($row['t5_approved']) { $subject = 'Task 5 Approved'; } else { $next_steps .= '<br>Task 5 needs approved.'; }
	if ($row['ordered_date'] != "0000-00-00") { $subject = 'Patches Ordered'; } else { $next_steps .= '<br>Patches need ordered.'; }
	if ($row['shipped_date'] != "0000-00-00") { $subject = 'Patches Shipped'; } else { $next_steps .= '<br>Patches need shipped.'; }

	if ($subject != '') {
		mailit($requestNotify, $subject, $next_steps, $row);
	}
}

function mailit($mail_to, $subject, $next_steps, $row) {
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' .  "\r\n";
	$headers .= 'From: TKMC Patch Status <contact@templarknightsmc.com>' . "\r\n";
    $headers .= 'Reply-To: contact@templarknightsmc.com' . "\r\n";

    $message = '
      <html>
      <head>
        <title>Patch Status Update</title>
      </head>
      <body>
        <p>Patches are being requested for:</p><br>
        <table>
          <tr><td colspan="2" valign="center"><b>LODGE: ' . $_POST['lodge_name'] . '</b></td></tr>
          <tr><td>First Name: </td><td>' . $_POST['first_name'] . '</td></tr>
          <tr><td>Last Name: </td><td>' . $_POST['last_name'] . '</td></tr>
		  <tr><td>Nickname: </td><td>' . $_POST['nickname'] . '</td></tr>
		  <tr><td colspan="2"><br></td></tr>
		  <tr><td><b>PATCH STATUS:</b></td><td>' . $next_steps . '</td></tr>
		  <tr><td colspan="2"><br></td></tr>
		  <tr><td>Patches Needed By: </td><td>' . $row['needed_by'] . '</td></tr>
		  <tr><td>Knighting Date: </td><td>' . $row['knighting_date'] . '</td></tr>
		  <tr><td colspan="2"><br></td></tr>
          <tr><td>Application Fee Paid: </td><td>' . $row['app_fee_date'] . '</td></tr>
          <tr><td>Date Patches Paid: </td><td>' . $row['patches_paid_date'] . '</td></tr>
          <tr><td>Top Rocker Ordered: </td><td>' . $row['tr_ordered_date'] . '</td></tr>
          <tr><td>Top Rocker Shipped: </td><td>' . $row['tr_shipped_date'] . '</td></tr>
          <tr><td>Date Patches Ordered: </td><td>' . $row['ordered_date'] . '</td></tr>
          <tr><td>Date Patches Shipped: </td><td>' . $row['shipped_date'] . '</td></tr>
          <tr><td>Notes: </td><td>' . $row['notes'] . '</td></tr>
		</table><br>
      </body>
      </html>';
    $topic = $_POST['first_name'] . ' ' . $_POST['last_name'] . ' - ' . $subject;
	mail($mail_to, $topic, $message, $headers);
}

header("Location: http://templarknightsmc.com/council-area/grand-council/patch-status?provisional_id=".$_POST['knight_num']);
mysql_close($db->connection);

?>