<?php

if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');
require_once(SCRIPTS_DIR.'/includes/form_functions.inc');

//Connect to Database
$db = LocalDatabase::getReadable('master');
$qtable='quotes';
$ktable='knight_directory';
$ltable='lodge_directory';
$displayrow=1;

$query = 'SELECT COUNT(*) FROM ' . $qtable;
$result = mysqli_query($db->connection, $query);
if($result) {
	while($row = mysqli_fetch_array($result)) {
		$displayrow = rand(1, $row[0]);
		$query = 'SELECT q.quote AS quote, k.nickname AS nickname, k.fname AS fname, l.lodge_name AS lodgename FROM quotes AS q, knight_directory AS k, lodge_directory AS l WHERE q.knight_num = k.record_num AND k.lodge = l.lodge_num AND q.record_num = '. $displayrow;
		$result = mysqli_query($db->connection, $query);
		if($result) {
			while($row = mysqli_fetch_array($result)) {
				echo '<table><tr><td><p style="font-size:16px"><font color="white">' . $row['quote'] . ' - ';
				if($row['nickname'] == "") {
					echo $row['fname'];
				}
				else {
					echo $row['nickname'];
				}
				echo ' [' . $row['lodgename'] . ']</font></p></td></tr></table>';
			}
		}
	}
}
mysqli_close($db->connection);
?>