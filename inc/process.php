<?php
//Allows us to use wordpress to query DB
include_once('../../../../wp-config.php');
include_once('../../../../wp-includes/wp-db.php');
global $wpdb;

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
		$data['cost-monetary'] = $_REQUEST['cost-monetary'];

//VALIDATE INPUT
	//check email is valid
	//echo "Check Email Address Valid:<br>";
	$kick_back=0;
	$error_msg="";
		if($data['name']==""){$error_msg.="Please supply a <b>NAME</b>:";$kick_back=1;}
		if($data['email']==""){$error_msg="Please supply an <b>EMAIL</b>:";$kick_back=1;}
		if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)){	$error_msg.="Please supply a <b>VALID EMAIL ADDRESS</b>:";$kick_back=1;}
		if($data['address']==""){$error_msg.="Please supply an <b>ADDRESS</b>:";$kick_back=1;}
		if($data['telephone']==""){$error_msg.="Please supply a <b>TELEPHONE NUMBER</b>:";$kick_back=1;}
		if($data['recipient_name']==""){$error_msg.="Please supply a <b>RECIPIENT NAME</b>:";$kick_back=1;}	

	
	if($kick_back==1){
		$redirect="";
		$error_msg=substr($error_msg,0,-1);
		$new_url = preg_replace('/&?error=[^&]*/', '', wp_get_referer(),-1,$count);
		if (!$count){
			$redirect=$new_url."?error=".urlencode($error_msg);	
		}else{
			$redirect=$new_url."error=".urlencode($error_msg);	
		}	
		wp_safe_redirect( $redirect );
		exit();
	}
	
	//print_r($data);
	
	//RECORD PURCHASE REQUEST
	//Add to db, so even if the payment fails we still have a record of the attempt.
	
	$wpdb->query($wpdb->prepare(
						"INSERT INTO ".$wpdb->prefix."toltech_gift_vouchers (name,email,address,telephone,recipient_name,delivery_method,voucher_cost,status,pending_reason) VALUES (%s,%s,%s,%s,%s,%s,%f,%s,%s)",
						$data['name'],
						$data['email'],
						$data['address'],
						$data['telephone'],
						$data['recipient_name'],
						$data['delivery_method'],
						$cost,
						$data['status'],
						'Never completed payment'
						)
				);
				
	$ID=$wpdb->insert_id;

	//build query string to send onto paypal
	//PAYPAL SETTINGS FROM DB
	$settings = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."toltech_gift_vouchers_settings",OBJECT);
	
	// Firstly Append paypal account to querystring
	if($settings->pp_mode=="Test Mode"){ //TEST MODE
		$querystring = "?business=".urlencode($settings->pp_test_account)."&";
	}else if($settings->pp_mode=="Live Mode"){ //LIVE MODE
		$querystring = "?business=".urlencode($settings->pp_live_account)."&";
	}
	
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
	$querystring .= "return=".urlencode(stripslashes($settings->pp_return_url))."&";
	$querystring .= "cancel_return=".urlencode(stripslashes($settings->pp_cancel_url))."&";
	$querystring .= "notify_url=".urlencode($settings->pp_notify_url);
	
	// Redirect to paypal IPN
	if($settings->pp_mode=="Test Mode"){ //TEST MODE
		echo 'https://www.sandbox.paypal.com/cgi-bin/webscr'.$querystring;
		header('location:https://www.sandbox.paypal.com/cgi-bin/webscr'.$querystring);
	}else if($settings->pp_mode=="Live Mode"){ //LIVE MODE
		echo 'https://www.paypal.com/cgi-bin/webscr'.$querystring;
		header('location:https://www.paypal.com/cgi-bin/webscr'.$querystring);
	}
	
	exit();
	

}else{
	//THE RESPONSE
	
	//PAYPAL SETTINGS FROM DB
	$settings = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."toltech_gift_vouchers_settings",OBJECT);
	
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

	$header = "POST /cgi-bin/webscr HTTP/1.1\r\n";
		if($settings->pp_mode=="Test Mode"){ //TEST MODE
			$header .= "Host: www.sandbox.paypal.com\r\n";
		}else if($settings->pp_mode=="Live Mode"){ //LIVE MODE
			$header .= "Host: www.paypal.com\r\n"; 
		}
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$header .= "Content-Length: " . strlen($req) . "\r\n";
	$header .= "Connection: close\r\n\r\n";
	
		if($settings->pp_mode=="Test Mode"){ //TEST MODE
			$fp = fsockopen ('ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30);
		}else if($settings->pp_mode=="Live Mode"){ //LIVE MODE
			$fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);
		}
		
	if (!$fp) {
		// HTTP ERROR - can't connect in order to VERIFY
		$message.= "HTTP ERROR cant connect in order to VERIFY\n\nError No. (".$errno.")\n\n".$errstr;
	} else {	
		$message.= "FILE OPEN\n";
		$message.= "ATTEMPT TO VERIFY\n";	
		fputs ($fp, $header . $req);
		$x=1;
			while (!feof($fp)) {
				$res = fgets ($fp, 1024);
				$message.= "READING LINE ".$x." \n >> ".$res."\n";
				$res=trim($res);
				
				//READ FILE LINE BY LINE LOOKING FOR 'VERIFIED'
				//IF FOUND THEN THE PAYMENT WAS SUCCESSFUL!
				
				if (strcmp ($res, "VERIFIED") == 0) {
				$message.= ">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>:: VERIFIED!\n";
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
					
						//PAYMENT VERIFIED, UPDATE DATABASE
						$message.= "VALIDATED & VERIFIED\n";
						
						$message.= "\n\n";		
						$message.= "---------> UPDATE DB\n";
						$message.= "UPDATE ".$wpdb->prefix."toltech_gift_vouchers SET status='".$data['payment_status']."', pending_reason='".$data['pending_reason']."' WHERE ID='".$data['custom']."'\n\n";		
						$wpdb->query($wpdb->prepare(
							"UPDATE ".$wpdb->prefix."toltech_gift_vouchers SET status=%s, pending_reason=%s WHERE ID=%i",
							$data['payment_status'],
							$data['pending_reason'],
							$data['custom']
							)
						);
						
						/*	$query="UPDATE ".$wpdb->prefix."toltech_gift_vouchers SET status=?, pending_reason=? WHERE ID=?";
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
						*/
					}else{
						// Payment made but data has been changed
						// E-mail admin or alert user
						$message.= "PAYMENT MADE BUT DATA CHANGED\n";
					}
	
				}else if (strcmp ($res, "INVALID") == 0) {
					// PAYMENT INVALID & INVESTIGATE MANUALY!
					$message.= "PAYMENT INVALID\n";
					$query="UPDATE ".$wpdb->prefix."toltech_gift_vouchers SET status=?, pending_reason=? WHERE ID=?";
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
	}
		
	$to = "joe@toltech.co.uk";
	$subject = "Process.php Debug";
	$from = "IPN@example.com";
	$headers = "From:" . $from;
	mail($to,$subject,$message,$headers);
}
