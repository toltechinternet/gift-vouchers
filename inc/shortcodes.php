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
		.voucher_information{	border: 1px solid #eeeeee; float: left; padding: 13px; background-color: white; width: 280px;	}
		.button{	margin-top: 15px;	}
		.clear{		clear: both;	}
		#formTable{
			border: 1px solid #eeeeee; 
			float: left; 
			padding: 13px; 
			background-color: white; 
			width: 380px;
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

		.red{	color: red;		}
	</style>

		<script type="text/javascript">
		$(function() {
			
			$('.buy').click(function () {
				
				var id = $(this).attr('id');
			    $('.form' + id).toggle(500);
			     return false;
				});

		});
	</script>
	
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
			<strong>Amount:</strong> <?php echo $price; ?><br />
			<strong>Description:</strong> <?php echo $description; ?><br />
			<a class="buy" id="<?php echo get_the_ID(); ?>" href="#"><img class="button button-<?php echo get_the_ID(); ?>" alt="Buy Voucher" src="<?php echo get_bloginfo("url"); ?>/wp-content/plugins/gift-vouchers/images/button.png" /></a>
		</div>
	</div>
	<div class="clear"></div>

	<div style="display: none;" id="formTable" class="form<?php echo get_the_ID(); ?>">
		
		<form id="<?php echo get_the_ID(); ?>" method="post" action="<?php echo get_bloginfo("url"); ?>/wp-content/plugins/gift-vouchers/inc/process.php">
		<input type="hidden" id="id" name="id" value="<?php echo get_the_ID(); ?>" />
		<table>
			<tr>
				<td width="130px;">
					<label for="name">Your Name <span class="red">*</span>:</label>
			    </td>
			    <td>
			    	<input type="text" id="name" name="name" />
			    </td>
			</tr>
			<tr>
				<td>
			        <label for="email">Email <span class="red">*</span>:</label>
			    </td>
			    <td>
			    	<input type="text" id="email" name="email" />
			    </td>
			</tr>
			<tr>
				<td>
			        <label for="recipient">Address <span class="red">*</span>:</label>
			    </td>
			    <td>
			    	<textarea cols="24" rows="8" name="address"></textarea>
			    </td>
			</tr>
			<tr>
				<td>
			        <label for="telephone">Telephone <span class="red">*</span>:</label>
			    </td>
			    <td>
			    	<input type="text" id="telephone" name="telephone" />
			    </td>
			</tr>
			<tr>
				<td>
			        <label for="recipient">Recipient Name <span class="red">*</span>:</label>
			    </td>
			    <td>
			    	<input type="text" id="recipient" name="recipient" />
			    </td>
			</tr>
			<tr>
				<td>
			        <label for="method">Delivery Method <span class="red">*</span>:</label>
			    </td>
			    <td>
			    	<select name="method">
						<option value="Email">Email</option>
						<option value="Postal">Postal</option>
						<option value="Pickup">Pickup</option>
					</select>
			    </td>
			</tr>
			<tr>
				<td>
			        <label for="cost">Cost <span class="red">*</span>:</label>
			    </td>
			    <td>
			    	<select name="cost">
		                <option value="20.00">£20</option>
		                <option value="30.00">£30</option>
		                <option value="50.00">£50</option>
		                <option value="Other">Other</option>
           			</select>
			    </td>
			</tr>
			<tr>
				<td></td>
				<td><input type="submit" value="Process Voucher" id="process" /></td>
        </table>
		</form>

	</div>
	<div class="clear"></div>

	<?php endwhile; ?>
		
		<?php else : ?>
			No Gift Vouchers!
		<?php endif; ?>
	
	<?php } ?>