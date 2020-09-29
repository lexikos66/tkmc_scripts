<?php

if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');

//Connect to Database
$db = LocalDatabase::getReadable('master');
$qtable='quotes';
$ktable='knight_directory';
$ltable='lodge_directory';
$displayrow=1;

$query = 'SELECT q.quote AS quote, k.nickname AS nickname, k.fname AS fname, l.lodge_name AS lodgename FROM quotes AS q, knight_directory AS k, lodge_directory AS l WHERE q.knight_num = k.record_num AND k.lodge = l.lodge_num AND q.record_num > 0';
$result = mysqli_query($db->connection, $query);
if($result) {
	while($row = mysqli_fetch_array($result)) {
            echo $row['quote'];
            if($row['nickname'] == "") {
		echo ' - ' . $row['fname'];
	    }
	    else {
		echo ' - ' . $row['nickname'];
	    }
            echo ' [' . $row['lodgename'] . ']<br />';
	}
}
mysqli_close($db->connection);
?>