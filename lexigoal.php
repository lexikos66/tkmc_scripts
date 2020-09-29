<?php
  //Connect To Database
  $hostname='tkmcfinance.db.3516407.hostedresource.com';
  $username='TKMCRO';
  $password='Templar13';
  $dbname='tkmcfinance';
  $ctable='fin_paypal_txn';
  $odomurl='http://templarknightsmc.com/static/Odometer';

  mysql_connect($hostname,$username, $password) OR DIE ('Unable to connect to database! Please try again later.');
  mysql_select_db($dbname);

  $query = 'SELECT * FROM ' . $ctable . ' WHERE ITEM_NAME="LEXI"';
  $result = mysql_query($query);
  if($result) {
    $totaldollars = 0;
    while($row = mysql_fetch_array($result)){
      $totaldollars += $row[MC_GROSS];
    }
    $dollarlen = strlen($totaldollars);
    $i = 0;
    echo '<table><tr><td><font color="white" size="6">$</font>';
    while($i < $dollarlen) {
      echo '<image src="' . $odomurl . substr($totaldollars, $i, 1) . '.png" width="20" height="30" />';
      $i++;
    }
    echo '</font></td></tr></table>';
  }
  mysql_close();
?>
