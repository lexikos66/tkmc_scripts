<?php
//Connect To Database
$hostname='tkmcgrandlodge.db.3516407.hostedresource.com';
$username='TKMCREAD';
$password='Templar13';
$dbname='tkmcgrandlodge';
$ktable='knight_directory';

mysql_connect($hostname,$username, $password) OR DIE ('Unable to connect to database! Please try again later.');
mysql_select_db($dbname);

$query = 'SELECT * FROM ' . $ktable . ' WHERE LODGE = 1 ORDER BY RECORD_NUM';
$result = mysql_query($query);
if($result) {
	$count = 1;
	echo '<table border="0"><tr>';
	while($row = mysql_fetch_array($result)){
		if($row[DATE_WITHDREW] == "0000-00-00" && $row[RANK] != "Honorary") {
			display_info($row);
			if($count/4 == 1) {
				echo '</tr><tr>';
				$count = 0;
			}
			$count++;
		}
	}
	echo '</tr></table>';
}
mysql_close();

/*
 * function display_info($knightdata)
 *
 * Displays the Knight information in the indivdual cells of the table
 */

function display_info($knightdata) {
	$url='http://templarknightsmc.com/static/grandlodge/';
	$filepath='./../static/grandlodge/';

	if($knightdata[NICKNAME] == "") {
		$imgname=strtolower(substr($knightdata[FNAME],0,1) . substr($knightdata[LNAME],0,1));
	}
	else {
		$imgname=strtolower($knightdata[NICKNAME]);
	}
	if(file_exists($filepath . $imgname . '.jpg')) {
		echo '<td><a href="' . $url . $imgname . '.jpg" rel="lightbox" title="'. ucfirst($imgname) . '&lt;br&gt;' . $knightdata[RANK] . '">' . ucfirst($imgname) . '<br />' . $knightdata[RANK] . '</a></td>';
	}
	else {
		echo '<td><a href="' . $url . 'nopic.jpg" rel="lightbox" title="'. ucfirst($imgname) . '&lt;br&gt;' . $knightdata[RANK] . '">' . ucfirst($imgname) . '<br />' . $knightdata[RANK] . '</a></td>';
	}
}

?>
