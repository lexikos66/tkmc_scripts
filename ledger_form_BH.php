<?php
	if (!defined('SCRIPTS_DIR')) {
		define('SCRIPTS_DIR', '/home1/templark/scripts');
	}
	require_once(SCRIPTS_DIR.'/includes/defines.inc');
	require_once(SCRIPTS_DIR.'/classes/Database.class');
	//Connect To Database
	$db = LocalDatabase::getReadable('finance');
	$ledgertable='fin_ledger_txn';
	$customertable = 'fin_customer_directory';
	$paypaltable = 'fin_paypal_txn';

	$query = 'SELECT * FROM ' . $ledgertable . ' ORDER BY date DESC LIMIT 25';
	$fin_result = mysql_query($query);
	if($fin_result) {
	echo '<div><style type="text/css">
		.beta table
		{
		border-collapse:collapse;
		}
		.beta table,th,td
		{
		border:1px solid black;
		}
		.beta th
		{
		background-color:grey;
		color:black;
		text-align:center;
		}
		.beta tr
		{
		text-align:center;
		background-color:white;
		color:black;
		font-size:12px;
		}
		.beta td
		{
		padding:5px;
		color:black;
		font-size:12px;
		}
		</style>';
		echo '<table class="beta"><tr><th>TXN<br>Number</th><th>Date</th><th>Reference<br>Num</th><th>Customer</th><th>TXN<br>Source</th><th>Tax<br>Category</th><th>Purpose</th><th>TXN<br>Description</th><th>Credit</th><th>Debit</th><th>Balance</th></tr>';
		$fullTable = "";
		while($fin_row = mysql_fetch_array($fin_result)) {
			$customerName="";
			$query = 'SELECT * FROM ' . $customertable . ' WHERE cust_num = ' . $fin_row['cust_num'];
			$cust_result = mysql_query($query);
			if($cust_result) {
				while($cust_row = mysql_fetch_array($cust_result)) {
					$extrastuff = '<table><tr><td>' . $paypal_row['FIRST_NAME'] . $paypal_row['LAST_NAME'] . '</td></tr></table>';
					$customerName = $cust_row['cust_name'];
				}

				$query = 'SELECT payment_type FROM fin_payment_form WHERE payment_num = ' . $fin_row['payment_form'];
				$result = mysql_query($query);
				$paymentForm="";
				if($result) {
					$row = mysql_fetch_array($result);
					$paymentForm = $row['payment_type'];
				}	
	
				$query = 'SELECT category_name FROM fin_tax_expenses WHERE category_num = ' . $fin_row['tax_category'];
				$result = mysql_query($query);
				$taxCategory="";
				if($result) {
					$row = mysql_fetch_array($result);
					$taxCategory = $row['category_name'];
				}

				$query = 'SELECT purpose_name FROM fin_purpose WHERE purpose_num = ' . $fin_row['purpose'];
				$result = mysql_query($query);
				$purposeName="";
				if($result) {
					$row = mysql_fetch_array($result);
					$purposeName = $row['purpose_name'];
				}

				$tableRow = '<tr><td>' . $fin_row['txn_num'] . '</td><td>' . $fin_row['date'] . '</td><td>' . $fin_row['ref_num'] .
				'</td><td>' . $customerName . '</td><td>' . $paymentForm . '</td><td>' . $taxCategory .
				'</td><td>' . $purposeName . '</td><td>' . $fin_row['description'] . '</td>';

				if($fin_row['is_debit']) {
					$tableRow .= '<td></td><td align="right">' . format_money($fin_row['txn_amount']) . '</td>';
				} else {
					$tableRow .= '<td align="right">' . format_money($fin_row['txn_amount']) . '</td><td></td>';
				}

				$tableRow .= '<td></td></tr>';
				$fullTable = $tableRow . $fullTable;
			}
		}
	}
	echo $fullTable;
	echo '</table></div>';
	mysql_close($db->connection);

	function format_money($amount) {
		$newAmount = number_format($amount, 2, '.', ',');
		return $newAmount;
	}
?>
