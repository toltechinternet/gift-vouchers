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
		$id=$_REQUEST['id'];
		$data['name'] = $_REQUEST['name'];
		$data['email'] = $_REQUEST['email'];
		$data['address1'] = $_REQUEST['address1'];
		$data['address2'] = $_REQUEST['address2'];
		$data['city'] = $_REQUEST['city'];
		$data['state'] = $_REQUEST['state'];
		$data['postalcode'] = $_REQUEST['postalcode'];
		$data['country'] = $_REQUEST['country'];
		$data['telephone'] = $_REQUEST['telephone'];
		$data['recipient_name'] = $_REQUEST['recipient'];
		$data['delivery_method'] = $_REQUEST['method'];
			if($data['delivery_method']=="Postal"){$shipping=3.50;}
		$data['status'] = "Pending";
		$cost = $_REQUEST['cost'];
		if(isset($_REQUEST['cost-monetary']) && $_REQUEST['cost-monetary']!=""){
			$cost = $_REQUEST['cost-monetary'];
		}
			//SET SOME MORE VARIABLE IF 'SEND DIRECTLY' CHOSEN
			if(isset($_REQUEST['send_to_recipient_address']) && $_REQUEST['send_to_recipient_address']="Yes"){
				$data['Raddress1'] = $_REQUEST['Raddress1'];
				$data['Raddress2'] = $_REQUEST['Raddress2'];
				$data['Rcity'] = $_REQUEST['Rcity'];
				$data['Rstate'] = $_REQUEST['Rstate'];
				$data['Rpostalcode'] = $_REQUEST['Rpostalcode'];
				$data['Rcountry'] = $_REQUEST['Rcountry'];
			}
		
//VALIDATE INPUT
	$kick_back=0;
	$error_msg="";
		if($data['name']==""){$error_msg.="Please supply a <b>NAME</b>:";$kick_back=1;}
		if($data['email']==""){$error_msg="Please supply an <b>EMAIL</b>:";$kick_back=1;}
		if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)){	$error_msg.="Please supply a <b>VALID EMAIL ADDRESS</b>:";$kick_back=1;}
		if($data['address1']==""){$error_msg.="Please supply <b>ADDRESS LINE 1</b>:";$kick_back=1;}
		if($data['city']==""){$error_msg.="Please supply a <b>CITY</b>:";$kick_back=1;}
		if($data['postalcode']==""){$error_msg.="Please supply a <b>POSTAL CODE</b>:";$kick_back=1;}
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
	$voucher_code=uniqid();
	$wpdb->query($wpdb->prepare(
						"INSERT INTO ".$wpdb->prefix."toltech_gift_vouchers (
						voucher_code,
						name,
						email,
						address1,
						address2,
						city,
						state,
						postal_code,
						country,
						telephone,
						recipient_name,
						delivery_method,
						voucher_cost,
						status,
						pending_reason,
						date_purchased
						) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%f,%s,%s,%s)",
						$voucher_code,
						$data['name'],
						$data['email'],
						$data['address1'],
						$data['address2'],
						$data['city'],
						$data['state'],
						$data['postalcode'],
						$data['country'],
						$data['telephone'],
						$data['recipient_name'],
						$data['delivery_method'],
						$cost,
						$data['status'],
						'Payment Incomplete',
						current_time('mysql', 1)
						)
				);
				
	$ID=$wpdb->insert_id;
	
	//IF DELIVERY METHOD = POSTAL && POST DIRECT TO RECIPIENT CHECKED
	//THEN ADD THE DETAILS TO toltech_gift_vouchers_recipient_address TABLE
	if(isset($_REQUEST['send_to_recipient_address']) && $_REQUEST['send_to_recipient_address']="Yes"){
	
		$wpdb->query($wpdb->prepare(
						"INSERT INTO ".$wpdb->prefix."toltech_gift_vouchers_recipient_address (
						voucher_id,
						voucher_code,
						recipient_name,
						address1,
						address2,
						city,
						state,
						postal_code,
						country
						) VALUES (%d,%s,%s,%s,%s,%s,%s,%s,%s)",
						$ID,
						$voucher_code,
						$data['recipient_name'],
						$data['Raddress1'],
						$data['Raddress2'],
						$data['Rcity'],
						$data['Rstate'],
						$data['Rpostalcode'],
						$data['Rcountry']
						)
				);
	
	}
	

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
	
	//Delivery Cost if applicable
	if($data['delivery_method']=="Postal"){$querystring .= "shipping=".urlencode($shipping)."&";} //if postal incur delivery fee
	
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
	$querystring .= "notify_url=".urlencode(plugins_url().'/gift-vouchers/inc/process.php');
	
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
						$message.= "VALIDATED & VERIFIED\n\n";
						
						//QUERY DB TO FIND OUT WHAT TYPE OF DELIVERY METHOD CHOSEN
						$voucher_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."toltech_gift_vouchers WHERE ID=%d",$data['custom']),OBJECT);
						//QUERY DB FOR SETTINGS DATA
						$settings = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."toltech_gift_vouchers_settings",OBJECT);
						
						//IF VOUCHER NEEDS EMAILS TO BE SENT
						//This will stop the multiple issue of emails as multiple ipn responses come in
						if($voucher_data->email_sent=="No"){
							$message.="DELIVERY METHOD = ".$voucher_data->delivery_method;
							$message.= "\n\n";		
							$message.= "---------> UPDATE DB\n\n";
							$message.= "UPDATE ".$wpdb->prefix."toltech_gift_vouchers SET status='".$data['payment_status']."', pending_reason='".$data['pending_reason']."' WHERE ID='".$data['custom']."'\n\n";		
							$wpdb->query($wpdb->prepare(
								"UPDATE ".$wpdb->prefix."toltech_gift_vouchers SET status=%s, pending_reason=%s, date_purchased=%s WHERE ID=%d",
								$data['payment_status'],
								$data['pending_reason'],
								current_time('mysql', 1),
								$data['custom']
								)
							);
						
							//DELIVERY METHODS			
							if($voucher_data->delivery_method=="Email"){
							//EMAIL
								//SEND VOUCHER TO CUSTOMER
								$message.= "ATTEMPT TO SEND VOUCHER TO CUSTOMER (".$voucher_data->email.") VIA EMAIL\n\n";
								$message.= "INCLUDE send-certificate.php\n\n";
								$recipients = $voucher_data->email; //Email to Customer
								include('send-certificate.php');
								
								//SEND EMAIL TO ADMIN
								$message.= "ALERT ADMIN(".$settings->company_email.") OF VOUCHER PURCHASE\n\n";
								$recipients=$settings->company_email; //Email admin
								$subject="Administrator Notification - Gift Voucher Purchased";
								$body='<html><head></head><body style="font-family:helvetica">
								<h1>Notification!</h1>
								<h2>Gift Voucher Purchased</h2>
								You are recieving this email because a Gift Voucher has recently been purchased.
								<br>The customer decided on <strong>EMAIL</strong> as their delivery method so this is just an alert and requires no action on your part.
								<br /><br />
								<table rules="all" style="border: solid 1px #000; width: 600px;">
								 <tr>
								  <td style="width: 120px;"><strong>Purchased By:</strong></td>
								  <td>'.$voucher_data->name.'( <a href="mailto:'.$voucher_data->email.'">'.$voucher_data->email.'</a> )</td>
								 </tr>
								 <tr>
								  <td><strong>Address:</strong></td>
								  <td>'.$voucher_data->address1.' '.$voucher_data->address2.', '.$voucher_data->city.', '.$voucher_data->postal_code.', '.$voucher_data->state.'</td>
								 </tr>
								 <tr>
								  <td><strong>Telephone:</strong></td>
								  <td>'.$voucher_data->telephone.'</td>
								 </tr>
								 <tr>
								  <td><strong>Delivery Method:</strong></td>
								  <td>'.$voucher_data->delivery_method.'</td>
								 </tr>
								 <tr>
								  <td><strong>Purchased For:</strong></td>
								  <td>'.$voucher_data->recipient_name.'</td>
								 </tr>
								 <tr>
								  <td><strong>Voucher Code:</strong></td>
								  <td>'.$voucher_data->voucher_code.'</td>
								 </tr>
								</table>

								</body></html>';
								$headers="From: vouchers@mussel-inn.com\r\n";
								$headers.="Reply-To: vouchers@mussel-inn.com\r\n";
								$headers.="MIME-Version: 1.0\r\n";
								$headers.="Content-Type: text/html; charset=ISO-8859-1\r\n";
								mail($recipients, $subject, $body, $headers);
								
								$message.= "END\n\n";
							}
							else if($voucher_data->delivery_method=="Collection-Glasgow" || $voucher_data->delivery_method=="Collection-Edinburgh" || $voucher_data->delivery_method=="Postal"){
							//PICKUP & POSTAL
								//SEND VOUCHER TO ADMIN FOR PICKUP OR POSTAL PURPOSES
								$message.= "ATTEMPT TO SEND VOUCHER TO ADMIN (".$settings->company_email.") VIA EMAIL\n\n";
								$message.= "INCLUDE send-certificate.php\n\n";
								$recipients = $settings->company_email;//Email to Matt
								include('send-certificate.php');
								
								//SEND EMAIL TO CUSTOMER
								$message.= "ALERT CUSTOMER (".$voucher_data->email.") OF VOUCHER PURCHASE\n\n";
								$recipients=$voucher_data->email;
								$subject="Gift Voucher Purchase";
								$body='<html><head></head><body style="font-family:helvetica;"><h1>Thank You!</h1>
								You are recieving this email because you have recently purchased a Gift Voucher.<br><br>
								<table rules="all" style="border: solid 1px #000; width: 600px;">
								 <tr>
								  <td style="width: 120px;"><strong>Purchased By:</strong></td>
								  <td>'.$voucher_data->name.'( <a href="mailto:'.$voucher_data->email.'">'.$voucher_data->email.'</a> )</td>
								 </tr>
								 <tr>
								  <td><strong>Address:</strong></td>
								  <td>'.$voucher_data->address1.' '.$voucher_data->address2.', '.$voucher_data->city.', '.$voucher_data->postal_code.', '.$voucher_data->state.'</td>
								 </tr>
								 <tr>
								  <td><strong>Telephone:</strong></td>
								  <td>'.$voucher_data->telephone.'</td>
								 </tr>
								 <tr>
								  <td><strong>Delivery Method:</strong></td>
								  <td>';

				  				 if($voucher_data->delivery_method=="Collection-Edinburgh"){
				                  		 $body .= 'Pickup at Edinburgh Mussel Inn Restaurant';
				                  	}
								 else if($voucher_data->delivery_method=="Collection-Glasgow"){
										 $body .= 'Pickup at Glasgow Mussel Inn Restaurant';
									}
								 else if($voucher_data->delivery_method=="Postal"){
										$body .= 'Delivery by Postal';
									}

						$body .= '</td>
								 </tr>
								 <tr>
								  <td><strong>Purchased For:</strong></td>
								  <td>'.$voucher_data->recipient_name.'</td>
								 </tr>
								 <tr>
								  <td><strong>Voucher Code:</strong></td>
								  <td>'.$voucher_data->voucher_code.'</td>
								 </tr>
								</table><br />
								<h2>What Do I Do Next?</h2>';
								 if($voucher_data->delivery_method=="Collection-Edinburgh"){
									$body.='<h3>Instructions to collect your voucher</h3>
									<ol>
						            	<li>Visit Mussel Inn Edinburgh (<a href="http://www.mussel-inn.com/seafood-restaurant-edinburgh/mussel-inn-edinburgh-opening-hours/">Please check opening hours</a>)</li>
						            	<li>Bring this email with you</li>
						           		<li>Collect your voucher (<a href="http://www.mussel-inn.com/seafood-restaurant-edinburgh/mussel-inn-edinburgh-opening-hours/">Directions to Mussel Inn Edinburgh</a>)</li>
						            </ol>';
								}
								else if($voucher_data->delivery_method=="Collection-Glasgow"){
									$body.='<h3>Instructions to collect your voucher</h3>
									<ol>
						            	<li>Visit Mussel Inn Glasgow (<a href="http://www.mussel-inn.com/seafood-restaurant-glasgow/mussel-inn-glasgow-opening-hours/">Please check opening hours</a>)</li>
						            	<li>Bring this email with you</li>
						           		<li>Collect your voucher (<a href="http://www.mussel-inn.com/seafood-restaurant-glasgow/restaurant-glasgow-city-centre/">Directions to Mussel Inn Glasgow</a>)</li>
						            </ol>';
								}
								else if($voucher_data->delivery_method=="Postal"){
									$body.='Sit back and relax, you will recieve your voucher in the mail in a few days time.';
								}
								 
								$body.='</ul>
								<h3>Thank you from everyone at '.$settings->company_name.'</h3>
								</body></html>';
								$headers="From: vouchers@mussel-inn.com\r\n";
								$headers.="Reply-To: vouchers@mussel-inn.com\r\n";
								$headers.="MIME-Version: 1.0\r\n";
								$headers.="Content-Type: text/html; charset=ISO-8859-1\r\n";
								mail($recipients, $subject, $body, $headers);
								$message.= "END\n\n";
							}
							else{
							//PROBLEM WITH DELIVERY METHOD, EMAIL ADMIN
								$message.= "SEND EMAIL TO ADMIN TO ALERT OF VOUCHER PURCHASE WITHOUT DELIVERY METHOD SPECIFIED\n\n";
							}
							
							//UPDATE VOUCHER RECORD TO SAY THAT EMAIL HAS NOW BEEN SENT
							$wpdb->query($wpdb->prepare(
								"UPDATE ".$wpdb->prefix."toltech_gift_vouchers SET email_sent='Yes' WHERE ID=%d",
								$data['custom']
								)
							);
						}
						
						

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
	//mail($to,$subject,$message,$headers);
}
