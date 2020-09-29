<?php

if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');
	
//Connect To Database
$db = LocalDatabase::getReadable('master');
$ltable='lodge_directory';
$lodgekey =  get_post_custom_values("lodge_num");
$lodgeNum = $lodgekey[0];
$fb_addr = 'http://www.facebook.com/templarknightsmc';
$twitter_addr = 'http://www.twitter.com/templarknightmc';
$youtube_addr = 'http://www.youtube.com/user/TemplarKnightsMc';

if($lodgeNum == NULL || $lodgeNum < 1) {
	$query = 'SELECT * FROM '.$ltable.' WHERE LODGE_NUM = 1 LIMIT 1';
} else {
	$query = 'SELECT * FROM ' . $ltable . ' WHERE LODGE_NUM = '.$lodgeNum.' LIMIT 1';
}
$result = mysql_query($query, $db->connection);

if($result) {
	$row = mysql_fetch_array($result);
	if (strpos($row['FB_ADDR'], 'facebook')) {
		$fb_addr = $row['FB_ADDR'];
	}
	if (strpos($row['TWITTER_ADDR'], 'twitter')) {
		$twitter_addr = $row['TWITTER_ADDR'];
	}
	if (strpos($row['YOUTUBE_ADDR'], 'youtube')) {
		$youtube_addr = $row['YOUTUBE_ADDR'];
	}
}
echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="'.$fb_addr.'"><img src="http://www.templarknightsmc.com/wp-content/uploads/2014/08/FB-f-Logo__blue_1024.png" height="32" width="32" alt="Facebook"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="'.$twitter_addr.'"><img src="http://www.templarknightsmc.com/wp-content/uploads/2014/08/Twitter_logo_blue.png" height="37" width="37" alt="Twitter"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="'.$youtube_addr.'"><img src="http://www.templarknightsmc.com/wp-content/uploads/2014/08/YouTube-logo-full_color-e1409195245507.png" height="42" width="72" alt="YouTube"></a>';

mysql_close($db->connection);

?>