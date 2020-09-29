<?php

if (!defined(SCRIPTS_DIR)) {
	define('SCRIPTS_DIR', '/home/content/07/3516407/html/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');
	
//Connect To Database
$hostId='tem1212107143424';
$wpdb = WpDatabase::getReadable($hostId);
$atable='wp_ngg_album';
$lodge='';
$pageId=90;
$album_array = array();

$query = 'SELECT id from '.$atable.' ORDER BY id DESC';
$result = mysql_query($query, $wpdb->connection);
$i = 1;
if($result) {
	while($row = mysql_fetch_array($result)) {
		$album_array[$i] = $row[id];
		$i++;
	}
}

echo '[<a href="http://templarknightsmc.com'.$lodge.'/?page_id='.$pageId.'#pics">pictures</a>][<a href="http://templarknightsmc.com'.$lodge.'/?page_id='.$pageId.'#videos">videos</a>]
<a name="pictures"></a><table>';

for ($i = 1; $i <= sizeof($album_array); $i++) {
	if ($i % 2 == 0) {
		echo '<td width="50%">[album id='.$album_array[$i].' template=extend]</td></tr>';
	} else {
		echo '<tr><td width="50%">[album id='.$album_array[$i].' template=extend]</td>';
	}
}
if ($i % 2 == 0) {
	echo '</tr>';
}
echo '</table>';
echo '<a name="videos"></a>';
echo '[youtubefeeder]';
?>