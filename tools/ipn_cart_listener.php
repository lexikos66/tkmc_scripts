<?php
$ipn_post_data = $_POST;
  
define('__ROOT__', dirname(dirname(__FILE__)));
if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/includes/sql_functions.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');

//Connect To Database
$db = LocalDatabase::getReadable('finance');

if(array_key_exists('test_ipn', $ipn_post_data) && 1 === (int) $ipn_post_data['test_ipn']) {
	$url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
} else {
	$url = 'https://www.paypal.com/cgi-bin/webscr';
}

if($ipn_post_data['payment_status'] == "Completed") {
	$headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' .  "\r\n";
    $headers .= 'From: TKMC Paypal Orders <orders@templarknightsmc.com>' . "\r\n";
    $headers .= 'Reply-To: orders@templarknightsmc.com' . "\r\n";

    $message = create_message_body($ipn_post_data);
    // Get Item Name
    $item_name = "";
    if ($ipn_post_data['txn_type'] == "cart") {
      for ($i = 1; $i <= $ipn_post_data['num_cart_items']; $i++) {
        if (strpos($ipn_post_data["item_number$i"], "PREREG")) {
          $item_name = $ipn_post_data["item_name$i"];
        }
      }
    } else {
      $item_name = $ipn_post_data['item_name'];
    }

    $subject = $ipn_post_data['first_name'] . ' ' . $ipn_post_data['last_name'] . ' - ' . $item_name;
    mail('orders@templarknightsmc.com', $subject , $message, $headers);
    add_record($db, $ipn_post_data);
}

function create_message_body($ipn_data) {
  $ipn_order_data = get_order_info($ipn_data);
	$messagebody = '
      <html>
      <head>
        <title>Paypal Orders for TKMC</title>
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
          <tr><td>Transaction ID: </td><td>' . $ipn_data['txn_id'] . '</td></tr>'
          . $ipn_order_data .
          '<tr><td>Memo: </td><td>' . $ipn_data['memo'] . '</td></tr>
          <tr><td>Custom Detail: </td><td>' . $ipn_data['custom'] . '</td></tr>
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

function get_order_info($ipn_data) {
  $seperator = "";
  if ($ipn_data['txn_type'] == "web_accept") {
    $ret_val .= '<tr><td>Item Name: </td><td>' . $ipn_data['item_name'] . '</td></tr>
          <tr><td>Item Number: </td><td>' . $ipn_data['item_number'] . '</td></tr>
          <tr><td>Optional Field0:' . $ipn_data['option_name0'] . '</td><td>' . $ipn_data['option_selection0'] . '</td></tr>
          <tr><td>Optional Field1:' . $ipn_data['option_name1'] . '</td><td>' . $ipn_data['option_selection1'] . '</td></tr>
          <tr><td>Optional Field2:' . $ipn_data['option_name2'] . '</td><td>' . $ipn_data['option_selection2'] . '</td></tr>
          <tr><td>Optional Field3:' . $ipn_data['option_name3'] . '</td><td>' . $ipn_data['option_selection3'] . '</td></tr>
          <tr><td>Optional Field4:' . $ipn_data['option_name4'] . '</td><td>' . $ipn_data['option_selection4'] . '</td></tr>';
  } else if ($ipn_data['txn_type'] == "cart") {
    $ret_val .= '<tr><td>CART DETAILS:</td><td>' . $ipn_data['num_cart_items'] . ' item(s) in cart</td></tr>';
    if ($ipn_data['num_cart_items'] > 1) { $seperator = '<tr><td colspan="2">-----</td></tr>'; }
    for ($i = 1; $i <= $ipn_data['num_cart_items']; $i++) {
      if (strpos($ipn_data["item_number$i"], "PREREG")) {
        $ret_val .= '<tr><td>Item Name:</td><td>' . $ipn_data["item_name$i"] . '</td></tr>
          <tr><td>Item Number:</td><td>' . $ipn_data["item_number$i"] . '</td></tr>
          <tr><td>' . $ipn_data["option_name1_$i"] . ':</td><td>' . $ipn_data["option_selection1_$i"] . '</td></tr>
          <tr><td>' . $ipn_data["option_name2_$i"] . ':</td><td>' . $ipn_data["option_selection2_$i"] . '</td></tr>
          <tr><td>' . $ipn_data["option_name3_$i"] . ':</td><td>' . $ipn_data["option_selection3_$i"] . '</td></tr>' .
          $seperator;
      } else {
        $ret_val .= '<tr><td>Item Name:</td><td>' . $ipn_data["item_name$i"] . '</td></tr>
          <tr><td>Item Number:</td><td>' . $ipn_data["item_number$i"] . '</td></tr>
          <tr><td>' . $ipn_data["option_name1_$i"] . '</td><td>' . $ipn_data["option_selection1_$i"] . '</td></tr>' .
          $seperator;
      }
    }
  }
  return $ret_val;
}

function add_record($db, $ipn_data) {
    $usertable='fin_paypal_txn';
    $ledgertable='fin_ledger_txn';
    $customertable='fin_customer_directory';

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

    $result = mysqli_query($db->connection, $query);
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
    $result = mysqli_query($db->connection, $query);
    if($result) {
      mail('hollywood@templarknightsmc.com', 'Paypal Customer ' . $ipn_data['payer_id'] . ' inserted into Table', 'Paypal Customer ' . $ipn_data['payer_id'] . ' inserted into Table');
    }
    else {
      mail('hollywood@templarknightsmc.com', 'Paypal Customer ' . $ipn_data['payer_id'] . ' FAILED insert', 'Paypal Customer ' . $ipn_data['payer_id'] . ' FAIELD insert into Table.  Possible cause could be that the Customer already exists.');
    }

    $query = 'SELECT * FROM ' . $customertable . ' WHERE alt_id = "' . $ipn_data['payer_id'] . '"';
    $result = mysqli_query($db->connection, $query);
    if($result) {
      $row = mysqli_fetch_array($result);
      $customerNum = $row['cust_num'];
    }

    switch ($ipn_data['item_number']) {
      case "FPR2015-REG":
        $purposeNum = "1";
        break;
      case "TKMC-KNIGHTING":
        $purposeNum = "6";
        break;
      case "TKMC-ANNUAL":
        $purposeNum = "4";
        break;
      case "TKMC-LKNIGHTING":
        $purposeNum = "6";
        break;
      case "TKMC-LANNUAL":
        $purposeNum = "4";
        break;
      default:
        $purposeNum = "5";
        break;
    }

    $txnAmount = $ipn_data['mc_gross'] - $ipn_data['mc_fee'];
    $query = 'INSERT INTO ' . $ledgertable . ' (date, ref_num, cust_num, payment_form, purpose, description, is_debit, txn_amount)
      VALUES ("' . $txnDate . '", "' . $ipn_data['txn_id'] . '", "' . $customerNum . '", "7", "' . $purposeNum . '", "' . $ipn_data['item_name'] .
      '", "0", "' . $txnAmount . '")';

    $result = mysqli_query($db->connection, $query);
    if($result) {
      mail('hollywood@templarknightsmc.com', 'Ledger TXN Successful', 'Ledger TXN for ' . $ipn_data['txn_id'] . ' was successful.');
    }
    else {
      mail('hollywood@templarknightsmc.com', 'Ledger TXN FAILED', 'Ledger TXN for ' . $ipn_data['txn_id'] . ' was UNSUCCESSFUL.');
    }
    mysqli_close($db->connection);
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