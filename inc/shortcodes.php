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

	
	
	<h1>Gift Vouchers</h1>
	
	<style>
		.voucher_image{		float: left;	}
		.voucher_container{		margin-top: 15px	}
		.voucher_information{	border: 1px solid #eeeeee; float: left; padding: 13px; background-color: white; width: 300px;	}
		.button{	margin-top: 15px;	}
		.clear{		clear: both;	}
		#formTable{
			border: 1px solid #eeeeee; 
			float: left; 
			padding: 13px; 
			background-color: white; 
			width: 400px;
			margin-top: 15px;
		}
			#formTable	input[type=text], textarea, select{
				border: 1px solid #e3e3e3;
				font-size: 14px;
				color: #333333;
				padding: 5px;
				width: 205px;
			}

			#form

		.red{	color: #eee!important;		}
		.error{padding:10px;background:pink;border:3px solid red;}
		.error h2{color:red;}
		.error ul{}
		.error ul li b{color:red;}
		.missing{color: #b94a48!important; background-color: #f2dede!important; border-color: #eed3d7!important;}
		.hide{ display: none; }
		.complete{ background-color: white!important; border: 1px solid #eeeeee!important; color: #333333!important;}
	</style>

	<script type="text/javascript">

		$(function() {

			$('.buy').click(function () {
				
				var id = $(this).attr('id');
			    	
			    	$('.form' + id).toggle(500);

			    	$('#process-'+ id).click(function () {

			    				
			    				// Monetary Validation

			    				if ($('#cost-monetary-'+ id).val() < '20.00') {
									$('#cost-monetary-'+ id).addClass('missing').removeClass('complete').focus();

									console.log($('#cost-monetary-'+ id).val());
				           			return false
								} else{
									$('#cost-monetary-'+ id).removeClass('missing').addClass('complete')
								}

								if ($('#cost-monetary-'+ id).val() > '100.00') {
									$('#cost-monetary-'+ id).addClass('missing').removeClass('complete').focus();
				           			return false
								} else{
									$('#cost-monetary-'+ id).removeClass('missing').addClass('complete')
								}


								// Form Validation

				         		if ($('#name-'+ id).val() == "") {
				           			$('#name-'+ id).addClass('missing').removeClass('complete').focus();
				           			return false

				      			} else{
				      				$('#name-'+ id).removeClass('missing').addClass('complete')
				      			}

				         		if ($('#email-'+ id).val() == "") {
				           			$('#email-'+ id).addClass('missing').removeClass('complete').focus();
				           			return false

				      			} else{
				      				$('#email-'+ id).removeClass('missing').addClass('complete')
				      			}

				      			if ($('#address-'+ id).val() == "") {
				           			$('#address-'+ id).addClass('missing').removeClass('complete').focus();
				           			return false

				      			} else{
				      				$('#address-'+ id).removeClass('missing').addClass('complete')
				      			}

				      			if ($('#telephone-'+ id).val() == "") {
				           			$('#telephone-'+ id).addClass('missing').removeClass('complete').focus();
				           			return false

				      			} else{
				      				$('#telephone-'+ id).removeClass('missing').removeClass('complete').addClass('complete')
				      			}

				      			if ($('#recipient-'+ id).val() == "") {
				           			$('#recipient-'+ id).addClass('missing').removeClass('complete').focus();
				           			return false

				      			} else{
				      				$('#recipient-'+ id).removeClass('missing').addClass('complete')
				      			}

				     		});

			     	return false;
			});


	});
	</script>
	<?php
	
	if(isset($_REQUEST['error']) && $_REQUEST['error']!=""){
		$foo = explode(":",urldecode($_REQUEST['error']));
		echo "<div class=\"error\"><h2>Ooops!</h2>Its looks like you made a mistake:<ul>";
		foreach($foo as $v){echo "<li>".$v."</li>";}
		echo "</ul></div>";
	}

	?>
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
			<strong>Amount:</strong> £<?php echo $price; ?><br />
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
				echo '<tr>
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
			        <label for="recipient">Address <span style="color: red;">*</span>:</label>
			    </td>
			    <td>
			    	<textarea cols="24" rows="8" id="address-<?php echo get_the_ID(); ?>" name="address"></textarea>
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
			    	<select name="method" id="method">
						<option value="Email">Delivery by Email</option>
						<option value="Postal">Deliver by Postal (additional £3.50)</option>
						<option value="Collection-Glasgow">Collection from Glasgow restaurant</option>
						<option value="Collection-Edinburgh">Collection from Edinburgh restaurant</option>
					</select>
			    </td>
			</tr>
			<tr>
				<td></td>
				<td>
				<a style="color: blue;" href="#">Delivery Information</a><br />
				<a style="color: blue;" href="#">Terms and Conditions</a><br /><br />
				<small>Payment is via Paypal. By clicking the Buy Voucher button below you will be taken to PayPal’s secure payment system.</small>
				<br /><br />
					<input type="submit" value="Process Voucher" id="process-<?php echo get_the_ID(); ?>" /></td>
        </table>
		</form>

	</div>
	<div class="clear"></div>

	<?php endwhile; ?>
		
		<?php else : ?>
			No Gift Vouchers!
		<?php endif; ?>
	
	<?php } ?>