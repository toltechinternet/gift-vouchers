<?php
add_shortcode('gift-voucher', 'gift_voucher');
function gift_voucher() {
	$voucher_args = array(
		'post_type' => 'gift-vouchers',
		'post_status' => 'publish',
		'posts_per_page' => 5
	);
	$voucher = new WP_Query($voucher_args);
	?>

	<?php
	
	if(isset($_REQUEST['error']) && $_REQUEST['error']!=""){
		$foo = explode(":",urldecode($_REQUEST['error']));
		echo "<div class=\"error\"><h2>Ooops!</h2>Its looks like you made a mistake:<ul>";
		foreach($foo as $v){echo "<li>".$v."</li>";}
		echo "</ul></div>";
	}

	?>
	<h1>Mussel Inn Gift Vouchers</h1>
	<p style="font-weight: bold;">Mussel Inn Gift Vouchers for our Edinburgh and Glasgow restaurants make the ideal present or treat for special occasions such as birthdays, anniversaries or celebrations and occasions. Perfect for seafood lovers and restaurant goers for any time of year.</p>

	<p>You can collect your voucher at the restaurant of your choice (Edinburgh or Glasgow), we can email it to you for printing out or we can also deliver a printed version of the voucher for a small cost.</p>
	
	<div class="line"></div>

	<?php if($voucher->have_posts()) : ?>
	<?php while($voucher->have_posts()) : ?>
	<?php $voucher->the_post(); ?>

	<?php 
		$custom = get_post_custom(get_the_ID());
		$price = $custom['_price'][0];
		$description = $custom['_description'][0];  
	?>


	<div class="voucher_container">
	<img class="voucher_image" src="<?php echo get_bloginfo("url"); ?>/wp-content/plugins/gift-vouchers/images/voucher.jpg">
		<div class="voucher_information">
		<?php
		if($price == '0.00'){ ?>
			<strong>Amount:</strong> £20.00 to £100.00<br />
		<?php } else { ?>
			<strong>Amount:</strong> £<?php echo $price; ?><br />
		<?php } ?>
			<strong>Description:</strong> <?php echo $description; ?><br />
			<a class="buy" id="<?php echo get_the_ID(); ?>" href="#"><img class="button button-<?php echo get_the_ID(); ?>" alt="Buy Voucher" src="<?php echo get_bloginfo("url"); ?>/wp-content/plugins/gift-vouchers/images/button.png" /></a>
		</div>
	</div>
	<div class="clear"></div>

	<div style="display: none;" id="formTable" class="form<?php echo get_the_ID(); ?>">
		
		<form id="<?php echo get_the_ID(); ?>" method="post" action="<?php echo get_bloginfo("url"); ?>/wp-content/plugins/gift-vouchers/inc/process.php">
		<input type="hidden" id="id" name="id" value="<?php echo get_the_ID(); ?>" />
		
		<div style="margin-bottom: 15px;"><small><span style="color: red;">*</span> = Required Field</small></div>

		<table>
		<?php
			if($price == '0.00'){
				echo '
					<tr>
						<th colspan="2"><div class="formtitle">Choose your voucher value</div></th>
					</tr>
					<tr>
						<td width="130px;">
							<label for="name">Voucher Cost (£) <span style="color: red;">*</span>:</label>
			   		 	</td>
			   		 	<td>
			   		 		<input type="text" id="cost-monetary-'. get_the_ID() .'" name="cost-monetary" value="0.00" /><br />
			   		 		<small><div style="color: red; margin-bottom: 10px;">Amount Specified must be between £20 and £100</div></small>
			   		 	</td>
			   		 </tr>';
			} else{
				echo '<input type="hidden" id="cost" name="cost" value="'.$price.'" />';
			}
		?>

			<tr>
				<th colspan="2">
					<div class="formtitle">Purchaser Information</div>
				</th>
			</tr>
			<tr>
				<td width="130px;">
					<label for="name">Your Name <span style="color: red;">*</span>:</label>
			    </td>
			    <td>
			    	<input type="text" id="name-<?php echo get_the_ID(); ?>" name="name" />
			    	<span class="missing-name"></span>
			    </td>
			</tr>
			<tr>
				<td>
			        <label for="email">Email <span style="color: red;">*</span>:</label>
			    </td>
			    <td>
			    	<input type="text" id="email-<?php echo get_the_ID(); ?>" name="email" /><br />
			    	<div style="margin-bottom: 10px;">
			    		<small><strong>Case sensitive.</strong> 
			    		Please ensure your email address is entered correctly. If it isn’t you may not receive your voucher if you opt for email delivery.</small>
			    	</div>
			    </td>
			</tr>
			<tr>
				<td>
			        <label for="address1">Address 1 <span style="color: red;">*</span>:</label>
			    </td>
			    <td>
			    	<input type="text" id="address1-<?php echo get_the_ID(); ?>" name="address1" />
			    </td>
			</tr>
			<tr>
				<td>
			        <label for="address2">Address 2:</label>
			    </td>
			    <td>
			    	<input type="text" id="address2-<?php echo get_the_ID(); ?>" name="address2" />
			    </td>
			</tr>
			<tr>
				<td>
			        <label for="city">Town <span style="color: red;">*</span>:</label>
			    </td>
			    <td>
			    	<input type="text" id="city-<?php echo get_the_ID(); ?>" name="city" />
			    </td>
			</tr>
			<tr>
				<td>
			        <label for="state">County / State:</label>
			    </td>
			    <td>
			    	<input type="text" id="state-<?php echo get_the_ID(); ?>" name="state" />
			    </td>
			</tr>
			<tr>
				<td>
			        <label for="postalcode">Post Code <span style="color: red;">*</span>:</label>
			    </td>
			    <td>
			    	<input type="text" id="postalcode-<?php echo get_the_ID(); ?>" name="postalcode" />
			    </td>
			</tr>
			<tr>
				<td>
			        <label for="country">Country <span style="color: red;">*</span>:</label>
			    </td>
			    <td>
			    	<select name="country" id="country-<?php echo get_the_ID(); ?>">
						<option value="Australia">Australia</option>
						<option value="Canada">Canada</option>
						<option value="France">France</option>
						<option value="Germany">Germany</option>
						<option value="Ireland">Ireland</option>
						<option value="Italy">Italy</option>
						<option value="Netherlands">Netherlands</option>
						<option value="Spain">Spain</option>
						<option value="United Kingdom" selected="selected">United Kingdom</option>
						<option value="United States">United States</option>
					</select>
			    </td>
			</tr>
			<tr>
				<td>
			        <label for="telephone">Telephone <span style="color: red;">*</span>:</label>
			    </td>
			    <td>
			    	<input type="text" id="telephone-<?php echo get_the_ID(); ?>" name="telephone" />
			    </td>
			</tr>
			<tr>
				<th colspan="2">
					<div class="formtitle">Voucher, Delivery and Payment</div>
				</th>
			</tr>
			<tr>
				<td>
			        <label for="recipient">Recipient Name <span style="color: red;">*</span>:</label>
			    </td>
			    <td>
			    	<input type="text" id="recipient-<?php echo get_the_ID(); ?>" name="recipient" />
			    </td>
			</tr>
			<tr>
				<td>
			        <label for="method">Delivery Method <span style="color: red;">*</span>:</label>
			    </td>
			    <td>
			    	<select name="method" id="method-<?php echo get_the_ID(); ?>">
						<option value="Email">Delivery by Email</option>
						<option value="Postal">Deliver by Post (additional £3.50)</option>
						<option value="Collection-Glasgow">Collection from Glasgow restaurant</option>
						<option value="Collection-Edinburgh">Collection from Edinburgh restaurant</option>
					</select>
			    </td>
			</tr>
			
			<!-- HIDDEN -->			
			<tr id="postal-delivery-selected-<?php echo get_the_ID(); ?>" class="postal-delivery-selected">
				<td><label for="send-to-recipient-address">Post directly to Recipient? :</label></td>
				<td><input type="checkbox" name="send_to_recipient_address" class="send_to_recipient_address" id="send_to_recipient_address-<?php echo get_the_ID(); ?>" value="Yes"></td>
			</tr>
				<tr class="box-ticked" id="box-ticked-<?php echo get_the_ID(); ?>-1">
					<td>
						<label for="Raddress1">Recipient Address 1 <span style="color: red;">*</span>:</label>
					</td>
					<td>
						<input type="text" id="Raddress1-<?php echo get_the_ID(); ?>" name="Raddress1" />
					</td>
				</tr>
				<tr class="box-ticked" id="box-ticked-<?php echo get_the_ID(); ?>-2">
					<td>
						<label for="Raddress2">Recipient Address 2:</label>
					</td>
					<td>
						<input type="text" id="Raddress2-<?php echo get_the_ID(); ?>" name="Raddress2" />
					</td>
				</tr>
				<tr class="box-ticked" id="box-ticked-<?php echo get_the_ID(); ?>-3">
					<td>
						<label for="city">Recipient Town <span style="color: red;">*</span>:</label>
					</td>
					<td>
						<input type="text" id="Rcity-<?php echo get_the_ID(); ?>" name="Rcity" />
					</td>
				</tr>
				<tr class="box-ticked" id="box-ticked-<?php echo get_the_ID(); ?>-4">
					<td>
						<label for="Rstate">Recipient County / State:</label>
					</td>
					<td>
						<input type="text" id="Rstate-<?php echo get_the_ID(); ?>" name="Rstate" />
					</td>
				</tr>
				<tr class="box-ticked" id="box-ticked-<?php echo get_the_ID(); ?>-5">
					<td>
						<label for="Rpostalcode">Recipient Post Code <span style="color: red;">*</span>:</label>
					</td>
					<td>
						<input type="text" id="Rpostalcode-<?php echo get_the_ID(); ?>" name="Rpostalcode" />
					</td>
				</tr>
				<tr class="box-ticked" id="box-ticked-<?php echo get_the_ID(); ?>-6">
					<td>
						<label for="Rcountry">Recipient Country <span style="color: red;">*</span>:</label>
					</td>
					<td>
						<select name="Rcountry" id="country-<?php echo get_the_ID(); ?>">
							<option value="Australia">Australia</option>
							<option value="Canada">Canada</option>
							<option value="France">France</option>
							<option value="Germany">Germany</option>
							<option value="Ireland">Ireland</option>
							<option value="Italy">Italy</option>
							<option value="Netherlands">Netherlands</option>
							<option value="Spain">Spain</option>
							<option value="United Kingdom" selected="selected">United Kingdom</option>
							<option value="United States">United States</option>
						</select>
					</td>
				</tr>
<!-- END -->	
						
			<tr>
				<td></td>
				<td>
				<ul class="information">
					<li><a class="show_delivery" id="show_delivery-<?php echo get_the_ID(); ?>">Delivery Information</a></li>
				</ul>
					<div id="delivery_content-<?php echo get_the_ID(); ?>" class="delivery_content">
						<h1>Delivery Information</h1>
						
						<?php
							global $wpdb;
							$table_name = $wpdb->prefix . 'toltech_gift_vouchers_settings';
							$settings = $wpdb->get_row("SELECT * FROM ".$table_name. " LIMIT 1");
							
							$output = $settings->delivery_information;

							echo nl2br($output);
						?>

					</div>
				<ul class="information" style="margin-top: 0px!important; margin-bottom: 0px!important;">
					<li><a class="show_terms" id="show_terms-<?php echo get_the_ID(); ?>">Terms and Conditions</a></li>
				</ul>
					<div id="terms_content-<?php echo get_the_ID(); ?>" class="terms_content">
						<h1>Terms and Conditions</h1>

						<?php
							global $wpdb;
							$table_name = $wpdb->prefix . 'toltech_gift_vouchers_settings';
							$settings = $wpdb->get_row("SELECT * FROM ".$table_name. " LIMIT 1");

							$output = $settings->terms_conditions;

							echo nl2br($output);
						?>
						
					</div>
				
				<br />
				<small>Payment is via Paypal. By clicking the Buy Voucher button below you will be taken to PayPal’s secure payment system.</small>
				<br /><br />
					<input type="submit" value="Buy Voucher" id="process-<?php echo get_the_ID(); ?>" /></td>
        </table>
		</form>

	</div>
	<div class="clear"></div>

	<?php endwhile; ?>
		
		<?php else : ?>
			No Gift Vouchers!
		<?php endif; ?>
	
	<?php } ?>