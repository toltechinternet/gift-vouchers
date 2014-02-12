<?php

/* TESTING VALUES
echo 'Voucher Form ID: ' . $_POST['id'] . '<br />';
echo 'Name: ' . $_POST['name'] . '<br />';
echo 'Email: '  . $_POST['email'] . '<br />';
echo 'Address: ' . $_POST['address'] . '<br />';
echo 'Telephone: ' . $_POST['telephone'] . '<br />';
echo 'Recipient: ' . $_POST['recipient'] . '<br />';
echo 'Delivery: ' . $_POST['method'] . '<br />';
echo 'Cost: ' . $_POST['cost'] . '<br />';
*/


// Database variables
$MYSQL_USER='root';
$MYSQL_PWD='relay3r';
$MYSQL_HOST='TIS-SERVER';
$MYSQL_DB="toltech1";
$MYSQL_PORT="3306";

//Connect to DB
$connection=mysqli_connect($MYSQL_HOST,$MYSQL_USER,$MYSQL_PWD,$MYSQL_DB,$MYSQL_PORT);
$connection->set_charset('utf8');
// Check connection
if (mysqli_connect_errno($connection)){
	echo "Failed to connect to MySQL: " . mysqli_connect_error() ."<br>";
}else{
	echo "Successfully connected to remote DB!<br>";
}


if (!isset($_POST["txn_id"]) && !isset($_POST["txn_type"])){
	//THE REQUEST
	
	$flag=0;
	
	//Get details send from the form
	$data=array();
		$data['name'] = $_REQUEST['name'];
		$data['email'] = $_REQUEST['email'];
		$data['address'] = $_REQUEST['address'];
		$data['telephone'] = $_REQUEST['telephone'];
		$data['recipient_name'] = $_REQUEST['recipient'];
		$data['delivery_method'] = $_REQUEST['method'];
		$data['voucher_cost'] = $_REQUEST['cost'];
		$data['status'] = "Pending";
		$cost = $_REQUEST['cost'];
		
		//check email is valid
		echo "Check Email Address Valid:<br>";
		if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)){	exit("E-mail is not valid"); }else{echo "VALID";}
		
		print_r($data);
	
	//Add to db, so even if the payment fails we still have a record of the attempt.
	$query="INSERT INTO musselinn_toltech_gift_vouchers (name,email,address,telephone,recipient_name,delivery_method,voucher_cost,status) VALUES (?,?,?,?,?,?,?,?)";
	if ($stmt = $connection->prepare($query) or $stmt->error) {
		$stmt->bind_param('ssssssis',$data['name'],$data['email'],$data['address'],$data['telephone'],$data['recipient_name'],$data['delivery_method'],$data['voucher_cost'],$data['status']);
		$stmt->execute();	//execute query
		$ID = $stmt->insert_id;
		$stmt->close();//close statement
		//echo "boo-".$ID;
	}


	//build query string to send onto paypal
	//PAYPAL SETTINGS
	$paypal_email = 'anthony-facilitator@toltech.co.uk';
	$return_url = 'http://www.mussel-inn.com/giftvoucher/thankyou.php';
	$cancel_url = 'http://www.mussel-inn.com/giftvoucher/cancelled.php';
	$notify_url = 'http://www.mussel-inn.com/giftvoucher/process.php';
	
	// Firstly Append paypal account to querystring
	$querystring = "?business=".urlencode($paypal_email)."&";

	// Append amount
	$querystring .= "amount=".urlencode($cost)."&";
	
	//Append currency code
	$querystring .= "currency_code=".urlencode('GBP')."&";
	
	//The item name
	$querystring .= "item_name=".urlencode('Voucher')."&";
	
	//needs and image apparently
	$querystring .= "submit=".urlencode('http://www.paypal.com/en_US/i/btn/x-click-but01.gif')."&";
	
	//cmd
	$querystring .= "cmd=".urlencode('_xclick')."&";
	
	//custom - pass id from db table so that we can update later on when response comes back
	$querystring .= "custom=".urlencode($ID)."&";
	
	//loop for posted values and append to querystring
	foreach($data as $key => $value){
		$value = urlencode(stripslashes($value));
		$querystring .= "$key=$value&";
	}

	// Append paypal return addresses
	$querystring .= "return=".urlencode(stripslashes($return_url))."&";
	$querystring .= "cancel_return=".urlencode(stripslashes($cancel_url))."&";
	$querystring .= "notify_url=".urlencode($notify_url);

	// Redirect to paypal IPN
	echo 'https://www.sandbox.paypal.com/cgi-bin/webscr'.$querystring;
	header('location:https://www.sandbox.paypal.com/cgi-bin/webscr'.$querystring);
	exit();
	

}else{
	//THE RESPONSE
		// Response from Paypal
	$message = "THE RESPONSE\n";
	
	// read the post from PayPal system and add 'cmd'
	$req = 'cmd=_notify-validate';
	foreach ($_POST as $key => $value) {
		$value = urlencode(stripslashes($value));
		$value = preg_replace('/(.*[^%^0^D])(%0A)(.*)/i','${1}%0D%0A${3}',$value);// IPN fix
		$req .= "&$key=$value";
	}

	// assign posted variables to local variables
	$data['item_name']			= $_POST['item_name'];
	$data['item_number'] 		= $_POST['item_number'];
	$data['pending_reason']		= $_POST['pending_reason'];
	$data['payment_status'] 	= $_POST['payment_status'];
	$data['payment_amount'] 	= $_POST['mc_gross'];
	$data['payment_currency']	= $_POST['mc_currency'];
	$data['txn_id']				= $_POST['txn_id'];
	$data['receiver_email'] 	= $_POST['receiver_email'];
	$data['payer_email'] 		= $_POST['payer_email'];
	$data['custom'] 			= $_POST['custom']; // should be ID in DB table
	
	$message.="DATA\n";
	foreach($data as $key => $v){$message.=$key." = ".$v."\n";}

	// post back to PayPal system to validate
	/*$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";*/
	
	$header = "POST /cgi-bin/webscr HTTP/1.1\r\n";
	$header .= "Host: www.sandbox.paypal.com\r\n";  // www.paypal.com for a live site
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$header .= "Content-Length: " . strlen($req) . "\r\n";
	$header .= "Connection: close\r\n\r\n";

	$fp = fsockopen ('ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30);

	if (!$fp) {
		// HTTP ERROR - can't connect in order to VERIFY
		$message.= "HTTP ERROR cant connect in order to VERIFY\n";
	} else {
		$message.= "FILE OPEN\n";
		$message.= "ATTEMPT TO VERIFY\n";	
		fputs ($fp, $header . $req);
		$x=1;
			while (!feof($fp)) {
				$res = fgets ($fp, 1024);
				$message.= "READING LINE ".$x." \n >> ".$res."\n";
				$res=trim($res);
				if (strcmp ($res, "VERIFIED") == 0) {
				$message.= "VERIFIED!\n";
				// Validate payment 
				//Check unique txnid
					function check_txnid($tnxid){
						/*global $link;
						return true;
						$valid_txnid = true;
						//get result set
						$sql = mysql_query("SELECT * FROM `payments` WHERE txnid = '$tnxid'", $link);
						if($row = mysql_fetch_array($sql)) {
							$valid_txnid = false;
						}
						return $valid_txnid;*/
						return true;
					}
					$valid_txnid = check_txnid($data['txn_id']);
				//Check correct price paid
					function check_price($price, $id){
						/*$valid_price = false;
						//Check whether the correct price has been paid for the product
						$sql = mysql_query("SELECT amount FROM `products` WHERE id = '$id'");
						if (mysql_numrows($sql) != 0) {
							while ($row = mysql_fetch_array($sql)) {
								$num = (float)$row['amount'];
								if($num == $price){
									$valid_price = true;
								}
							}
						}
						return $valid_price;*/
						return true;
					}
					$valid_price = check_price($data['payment_amount'], $data['item_number']);
					
					// PAYMENT VALIDATED & VERIFIED!
					if($valid_txnid && $valid_price){
					$message.= "VALIDATED & VERIFIED\n";
								$query="UPDATE musselinn_toltech_gift_vouchers SET status=?, pending_reason=? WHERE ID=?";
								if ($stmt = $connection->prepare($query) or $stmt->error) {
									$stmt->bind_param('ssi',$data['payment_status'],$data['pending_reason'],$data['custom']);
									$stmt->execute();	//execute query
									$stmt->close();//close statement
									$message.= "SUCCESS - UPDATE DB status=".$data['payment_status']." WHERE ID=".$data['custom']."\n";
									
								}else{
									// Error inserting into DB
									// E-mail admin or alert user
									$message.= "ERROR INSERTING INTO DB!\n";
								}
					}else{
						// Payment made but data has been changed
						// E-mail admin or alert user
						$message.= "PAYMENT MADE BUT DATA CHANGED\n";
					}
	
				}else if (strcmp ($res, "INVALID") == 0) {
					// PAYMENT INVALID & INVESTIGATE MANUALY!
					$message.= "PAYMENT INVALID\n";
					$query="UPDATE musselinn_toltech_gift_vouchers SET status=?, pending_reason=? WHERE ID=?";
					if ($stmt = $connection->prepare($query) or $stmt->error) {
						$stmt->bind_param('ssi',$data['payment_status'],$data['pending_reason'],$data['custom']);
						$stmt->execute();	//execute query
						$stmt->close();//close statement
						$message.= "DATABASE UPDATED\n";
						}
					// E-mail admin
					
					
				}
			$x++;
			}
		fclose ($fp);
		$message.= "FILE CLOSED\n";
		
		$to = "anthony@toltech.co.uk";
		$subject = "Process.php Debug";
		$from = "IPN@example.com";
		$headers = "From:" . $from;
		mail($to,$subject,$message,$headers);
		
	}

}
