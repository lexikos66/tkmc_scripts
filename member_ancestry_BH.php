<?php

if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/includes/form_functions.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');
	
//Connect To Database
$db = LocalDatabase::getReadable('master');
$ktable='knight_directory';
$ltable='lodge_directory';
$lodgeName='';
$ancestry = array();
$ancestry_keys = array();
$mentors = array();

if(isset($_GET["lodgeNum"])) {
	// Get the Lodge Name
	$lodgeNum = $_GET['lodgeNum'];
	$query = "SELECT LODGE_NAME FROM " . $ltable . " WHERE LODGE_NUM = " . $lodgeNum;
	$result = mysqli_query($db->connection, $query);
	if($result) {
  		$row = mysqli_fetch_array($result);
		$lodgeName = $row['LODGE_NAME'];
	}
	
	// Get Member Info for desired Lodge
	$query = "SELECT FNAME, LNAME, NICKNAME, RECORD_NUM, MENTOR, RANK FROM " . $ktable . " WHERE LODGE = " . $lodgeNum;
	$result = mysqli_query($db->connection, $query);
	while($row = mysqli_fetch_array($result)) {
		// Stuff entries in member array to pull out name/nickname
		if($row['RANK'] !== 'Honorary') {
			if($row['RECORD_NUM'] != $row['MENTOR']) {
				if($row['RANK'] == 'Provisional') {
					$ancestry[$row['MENTOR']][$row['RECORD_NUM']] = $row['FNAME'] . ' ' . $row['LNAME'];
				} else {
					$ancestry[$row['MENTOR']][$row['RECORD_NUM']] = $row['NICKNAME'];
				}
			} else {
				$ancestry[0][$row['RECORD_NUM']] = $row['NICKNAME']; // account for Commanders of Lodge
			}
		}
	}

	// Get mentors names
	$ancestry_keys = array_keys($ancestry);
	$mentorsCsv = implode(",", $ancestry_keys);
	$query = "SELECT RECORD_NUM, NICKNAME from " . $ktable . " WHERE RECORD_NUM IN (".$mentorsCsv.")";
	$result = mysqli_query($db->connection, $query);
	while($row = mysqli_fetch_array($result)) {
		$mentors[$row['RECORD_NUM']] = $row['NICKNAME'];
	}

	echo '<html>
			<head>
				<script type="text/javascript" src="https://www.google.com/jsapi"></script>
				<script type="text/javascript">
					google.load("visualization", "1", {packages:["orgchart"]});
					google.setOnLoadCallback(drawChart);
					function drawChart() {
						var data = new google.visualization.DataTable();
						data.addColumn("string", "Name");
						data.addColumn("string", "Mentor");
						data.addColumn("string", "ToolTip");
						
						data.addRows(['.getRowData($ancestry, $ancestry_keys, $mentors).']);
						var chart = new google.visualization.OrgChart(document.getElementById("chart_div"));
						chart.draw(data, {allowHtml:true color:["#ffffff"]});
					}
				</script>
			</head>
			<body>
				<div id="chart_div"></div>
			</body>
		</html>';
} else {
	echo '<form action="http://member.templarknightsmc.com/knights/ancestry" method="get">';
	lodge_dropdown($db, $ltable);
	echo '</p>';
	echo '<p><input style="float:center; margin-right:12px;" type="submit" value="Submit!" id="submit-button" class="button"></p>';
	echo '</form>';
}

mysqli_close($db->connection);

function getRowData($ancestry, $ancestry_keys, $mentors) {
	$retval = '';
	// Put Smiles in here, because he moved from Lodge to Lodge
	$retval .= '["Smiles","Mayhem",'.'""],';
	foreach($ancestry_keys as $mentor) {
		$provisionals = $ancestry[$mentor];
		foreach($provisionals as $provisional) {
			if($mentor == 0) {
				$retval .= '["'.$provisional.'","Founders",'.'""],';
			} else {
				$retval .= '["'.$provisional.'","'.$mentors[$mentor].'",'.'""],';
			}
		}
	}
	return $retval;
}
	

?>