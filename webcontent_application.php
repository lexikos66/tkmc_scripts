<?php

if (!defined(SCRIPTS_DIR)) {
	define('SCRIPTS_DIR', '/home/content/07/3516407/html/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');
require_once(SCRIPTS_DIR.'/includes/form_functions.inc');

//Connect to Database
$db = LodgeDatabase::getReadable();
$table='web_content';
$page_name='application';
$page_num=5;

$query = 'SELECT content FROM ' . $table . ' WHERE page_num = ' . $page_num;
$result = mysql_query($query, $db->connection);
if($result) {
	while($row = mysql_fetch_array($result)) {
		echo $row[content];
	}
}
mysql_close($db->connection);
?>