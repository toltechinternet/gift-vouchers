<?php

/*/
Plugin Name: Mussel Inn Gift Vouchers
Plugin URI: www.mussel-inn.com
Description: This plugin allows you to sell, using PayPal, printable gift certificates as well as manage sold gift certificates.
Version: 1.0
Author: Toltech Internet Solutions
Author URI: www.toltech.co.uk
/*/


/*/ Register Database and active hook upon installation /*/

register_activation_hook( __FILE__, 'gift_voucher_activation' );

function gift_voucher_activation() {
	global $wpdb;
	
	$table_name = $wpdb->prefix . 'toltech_gift_vouchers';
	
	$wpdb->query("CREATE TABLE " . $table_name . " (
	  id int(11) NOT NULL AUTO_INCREMENT,
	  name VARCHAR(225) NOT NULL,
	  email VARCHAR(225) NOT NULL,
	  address TEXT,
	  telephone VARCHAR(50) NOT NULL,
	  recipient_name VARCHAR(100) NOT NULL,
	  delivery_method VARCHAR(50) NOT NULL,
	  voucher_cost INT(11) NOT NULL,
	  status VARCHAR(100) NOT NULL,
	  pending_reason text,
	  PRIMARY KEY (`id`)
	)");
}

/*/ Set plugin base folder and include files /*/
$plugin_basename = plugin_basename(__FILE__);

include('inc/paypal-settings.php');
include('inc/shortcodes.php');
include('inc/functions.php');