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
		.form{
			border: 1px solid #eeeeee; 
			float: left; 
			padding: 13px; 
			background-color: white; 
			width: 380px;
			margin-top: 15px;
		}
	</style>
	
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
			<a href="#"><img class="button button-<?php echo get_the_ID(); ?>" alt="Buy Voucher" src="<?php echo get_bloginfo("url"); ?>/wp-content/plugins/gift-vouchers/images/button.png" /></a>
		</div>
	</div>
	<div class="clear"></div>

	<div class="form">
		
		Hidden Paypal Form

	</div>
	<div class="clear"></div>

	<?php endwhile; ?>
		
		<?php else : ?>
			No Gift Vouchers!
		<?php endif; ?>
	
	<?php } ?>