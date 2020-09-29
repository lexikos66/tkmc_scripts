<?php
  $ipn_post_data = $_POST;
  $lodge_name='Ohio 171';
  $lodge_email='ohio171.paypal';

  if(array_key_exists('test_ipn', $ipn_post_data) && 1 === (int) $ipn_post_data['test_ipn'])
  {
    $url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
  }
  else
  {
    $url = 'https://www.paypal.com/cgi-bin/webscr';
  }

  if($ipn_post_data['payment_status'] != NULL)
  {
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' .  "\r\n";
    $headers .= 'From: TKMC '.$lodge_name.' Paypal Orders <'.$lodge_email.'@templarknightsmc.com>' . "\r\n";
    $headers .= 'Reply-To: '.$lodge_email.'@templarknightsmc.com' . "\r\n";
	$headers .= 'Bcc: hollywood@templarknightsmc.com'. "\r\n";

    $message = create_message_body($ipn_post_data);
    $subject = $ipn_post_data['first_name'] . ' ' . $ipn_post_data['last_name'] . ' - ' . $ipn_post_data['item_name'];
    mail($lodge_email.'@templarknightsmc.com', $subject , $message, $headers);
    add_record($ipn_post_data);
  }

  function create_message_body($ipn_data) {
    $messagebody = '
      <html>
      <head>
        <title>Paypal Orders for TKMC '.$lodge_name.'</title>
      </head>
      <body>
        <p>You have received an order from Paypal:</p><br>
        <table>
          <tr><td colspan="2" valign="center"><b>BUYER INFORMATION</b></td></tr>
          <tr><td>Payment Type: </td><td>' . $ipn_data['payment_type'] . '</td></tr>
          <tr><td>Payment Date: </td><td>' . $ipn_data['payment_date'] . '</td></tr>
          <tr><td>Payment Status: </td><td>' . $ipn_data['payment_status'] . '</td></tr>
        </table><br>
        <table>
          <tr><td>First Name: </td><td>' . $ipn_data['first_name'] . '</td></tr>
          <tr><td>Last Name: </td><td>' . $ipn_data['last_name'] . '</td></tr>
          <tr><td>Street Address: </td><td>' . $ipn_data['address_street'] . '</td></tr>
          <tr><td>City: </td><td>' . $ipn_data['address_city'] . '</td></tr>
          <tr><td>State: </td><td>' . $ipn_data['address_state'] . '</td></tr>
          <tr><td>Zip Code: </td><td>' . $ipn_data['address_zip'] . '</td></tr>
          <tr><td>Country: </td><td>' . $ipn_data['address_country'] . '</td></tr>
          <tr><td>eMail Address: </td><td>' . $ipn_data['payer_email'] . '</td></tr>
          <tr><td>Payer ID: </td><td>' . $ipn_data['payer_id'] . '</td></tr>
        </table><br>
        <table>
          <tr><td colspan="2" valign="center"><b>PURCHASE INFORMATION</b></td></tr>
          <tr><td>Transaction Type: </td><td>' . $ipn_data['txn_type'] . '</td></tr>
          <tr><td>Transaction ID: </td><td>' . $ipn_data['txn_id'] . '</td></tr>
          <tr><td>Item Name: </td><td>' . $ipn_data['item_name'] . '</td></tr>
          <tr><td>Item Number: </td><td>' . $ipn_data['item_number'] . '</td></tr>
          <tr><td>Optional Field0:' . $ipn_data['option_name0'] . '</td><td>' . $ipn_data['option_selection0'] . '</td></tr>
          <tr><td>Optional Field1:' . $ipn_data['option_name1'] . '</td><td>' . $ipn_data['option_selection1'] . '</td></tr>
          <tr><td>Optional Field2:' . $ipn_data['option_name2'] . '</td><td>' . $ipn_data['option_selection2'] . '</td></tr>
          <tr><td>Optional Field3:' . $ipn_data['option_name3'] . '</td><td>' . $ipn_data['option_selection3'] . '</td></tr>
          <tr><td>Optional Field4:' . $ipn_data['option_name4'] . '</td><td>' . $ipn_data['option_selection4'] . '</td></tr>
          <tr><td>Custom Detail: </td><td>' . $lodge_name. ': '.$ipn_data['custom'] . '</td></tr>
          <tr><td>Invoice Detail: </td><td>' . $ipn_data['invoice'] . '</td></tr>
        </table><br>
        <table>
          <tr><td colspan="2" valign="center"><b>FINANCE INFORMATION</b></td></tr>
          <tr><td>Currency: </td><td>' . $ipn_data['mc_currency'] . '</td></tr>
          <tr><td>Paypal Fee: </td><td>' . $ipn_data['mc_fee'] . '</td></tr>
          <tr><td>Gross: </td><td>' . $ipn_data['mc_gross'] . '</td></tr>
          <tr><td>Tax: </td><td>' . $ipn_data['tax'] . '</td></tr>
          <tr><td>Shipping: </td><td>' . $ipn_data['shipping'] . '</td></tr>
        </table>
      </body>
      </html>
      ';
    return $messagebody;
  }

  function add_record($ipn_data) {
    $hostname='localhost';
    $username='templark_rw';
    $password='Deamon66!';
    $dbname='templark_tkmcfinance';
    $usertable='fin_paypal_txn';
    $ledgertable='fin_ledger_txn';
    $customertable='fin_customer_directory';

    mysql_connect($hostname,$username, $password) OR DIE ('Unable to connect to database! Please try again later.');
    mysql_select_db($dbname);

    $txnDate = format_date($ipn_data['payment_date']);

    $query = 'INSERT INTO ' . $usertable . ' (PAYMENT_TYPE, PAYMENT_DATE, PAYMENT_STATUS, ADDRESS_STATUS, PAYER_STATUS, FIRST_NAME, LAST_NAME, PAYER_EMAIL, PAYER_ID, ADDRESS_NAME,
    ADDRESS_COUNTRY, ADDRESS_COUNTRY_CODE, ADDRESS_ZIP, ADDRESS_STATE, ADDRESS_CITY, ADDRESS_STREET, ITEM_NAME, ITEM_NUMBER, QUANTITY, SHIPPING, TAX, MC_CURRENCY, MC_FEE, MC_GROSS,
    TXN_TYPE, TXN_ID, CUSTOM, INVOICE)
    VALUES ("' . $ipn_data['payment_type'] . '", "' . $txnDate . '", "' . $ipn_data['payment_status'] . '", "' . $ipn_data['address_status'] . '", "' . $ipn_data['payer_status'] .
    '", "' . $ipn_data['first_name'] . '", "' . $ipn_data['last_name'] . '", "' . $ipn_data['payer_email'] . '", "' . $ipn_data['payer_id'] . '", "' . $ipn_data['address_name'] .
    '", "' . $ipn_data['address_country'] . '", "' . $ipn_data['address_country_code'] . '", "' . $ipn_data['address_zip'] . '", "' . $ipn_data['address_state'] . '", "' . $ipn_data['address_city'] .
    '", "' . $ipn_data['address_street'] . '", "' . $ipn_data['item_name'] . '", "' . $ipn_data['item_number'] . '", "' . $ipn_data['quantity'] . '", "' . $ipn_data['shipping'] .
    '", "' . $ipn_data['tax'] . '", "' . $ipn_data['mc_currency'] . '", "' . $ipn_data['mc_fee'] . '", "' . $ipn_data['mc_gross'] . '", "' . $ipn_data['txn_type'] . '", "' . $ipn_data['txn_id'] .
    '", "' . $ipn_data['custom'] . '", "' . $ipn_data['invoice'] . '")';

    $result = mysql_query($query);
    if($result) {
       mail('hollywood@templarknightsmc.com', 'TKMC Order Successfully Recorded' , 'The transaction for ID#: ' . $ipn_data['txn_id'] . ' was recorded successfully!');
    }
    else {
      mail('hollywood@templarknightsmc.com', 'TKMC Order Failed to Record' , 'The transaction for ID#: ' . $ipn_data['txn_id'] . ' failed to record: ' . $result);
    }

    $customerNum="";
    $query = 'INSERT INTO ' . $customertable . ' (cust_name, street, city, state, zip_code, country, email_addr, is_paypal, alt_id)
      VALUES ("' . $ipn_data['first_name'] . ' ' . $ipn_data['last_name'] . '", "' . $ipn_data['address_street'] . '", "' . $ipn_data['address_city'] .
      '", "' . $ipn_data['address_state'] . '", "' . $ipn_data['address_zip'] . '", "' . $ipn_data['address_country'] . '", "' . $ipn_data['payer_email'] .
      '", "1", "' . $ipn_data['payer_id'] . '")';
    $result = mysql_query($query);
    if($result) {
      mail('hollywood@templarknightsmc.com', 'Paypal Customer ' . $ipn_data['payer_id'] . ' inserted into Table', 'Paypal Customer ' . $ipn_data['payer_id'] . ' inserted into Table');
    }
    else {
      mail('hollywood@templarknightsmc.com', 'Paypal Customer ' . $ipn_data['payer_id'] . ' FAILED insert', 'Paypal Customer ' . $ipn_data['payer_id'] . ' FAIELD insert into Table.  Possible cause could be that the Customer already exists.');
    }

    mysql_close();
  }

  function format_date($paymentDate) {
    $year = substr($paymentDate, -8, 4);
    $day = substr($paymentDate, -12, 2);
    $monthTxt = substr($paymentDate, -16, 3);

    switch ($monthTxt) {
	  case "Jan":
	    $month = "01";
	    break;
	  case "Feb":
	    $month = "02";
	    break;
	  case "Mar":
	    $month = "03";
	    break;
	  case "Apr":
	    $month = "04";
	    break;
	  case "May":
	    $month = "05";
	    break;
	  case "Jun":
	    $month = "06";
	    break;
	  case "Jul":
	    $month = "07";
	    break;
	  case "Aug":
	    $month = "08";
	    break;
	  case "Sep":
	    $month = "09";
	    break;
	  case "Oct":
	    $month = "10";
	    break;
	  case "Nov":
	    $month = "11";
	    break;
	  case "Dec":
	    $month = "12";
	    break;
	}

	return $year . '-' . $month . '-' . $day;
  }

?>