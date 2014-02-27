<?php

/*/
Plugin Name: Toltech Gift Vouchers
Plugin URI: www.mussel-inn.com
Description: This plugin allows you to sell, using PayPal, printable gift certificates as well as manage sold gift certificates.
Version: 1.0
Author: Toltech Internet Solutions
Author URI: www.toltech.co.uk
/*/


/*/ Register Database and active hook upon installation /*/

add_action('init','scripts_common');

function scripts_common() {
    wp_register_style( 'gift-vouchers', plugins_url( 'gift-vouchers/css/plugin.css' ) );
	wp_enqueue_style( 'gift-vouchers' );
    wp_enqueue_script( 'common_js', plugins_url( '/js/common.js', __FILE__ ));
}   

register_activation_hook( __FILE__, 'gift_voucher_activation' );

function gift_voucher_activation() {
	global $wpdb;
	
	$table_name = $wpdb->prefix . 'toltech_gift_vouchers'; 
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name){
		/*****************************************************/
		// Create table to store purchased vouchers settings //
		/*****************************************************/
		$wpdb->query("CREATE TABLE " . $table_name . " (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  name VARCHAR(225) NOT NULL,
		  email VARCHAR(225) NOT NULL,
		  address1 VARCHAR(225) NOT NULL,
		  address2 VARCHAR(225),
		  city VARCHAR(225),
		  state VARCHAR(225),
		  postal_code VARCHAR(225) NOT NULL,
		  country varchar(225) NOT NULL,
		  telephone VARCHAR(50) NOT NULL,
		  recipient_name VARCHAR(100) NOT NULL,
		  delivery_method VARCHAR(50) NOT NULL,
		  voucher_cost DECIMAL(10,2) NOT NULL,
		  status VARCHAR(100) NOT NULL,
		  pending_reason text,
		  date_purchased DATETIME,
		  email_sent CHAR(1) DEFAULT 'N',
		  PRIMARY KEY (`id`)
		)");
    
			//If installing plugin for first time, add a test record
			$voucher_count = $wpdb->query("SELECT * FROM ".$table_name);
			if($voucher_count==0){
				$wpdb->query("INSERT INTO ".$table_name."(
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
					pending_reason
				) VALUES (
					'John Doe',
					'j.doe@test.com',
					'123',
					'Fake Street',
					'Glasgow',
					'Greater Glasgow',
					'G24FD',
					'United Kingdom',
					'123456789',
					'Jane Doe',
					'Email',
					'20.00',
					'Pending',
					'Payment Incomplete'
				)");
			}
    }

	$table_name = $wpdb->prefix . 'toltech_gift_vouchers_settings'; 
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name){
		/*****************************************/
		// Create table to store plugin settings //
		/*****************************************/
		$table_name = $wpdb->prefix . 'toltech_gift_vouchers_settings';
		$wpdb->query("CREATE TABLE " . $table_name . " (
					 id int(11) NOT NULL AUTO_INCREMENT,
					 company_name VARCHAR(225),
					 company_email VARCHAR(225),
					 company_info TEXT,
					 terms_conditions TEXT,
					 delivery_information TEXT,
					 pp_live_account VARCHAR(225),
					 pp_test_account VARCHAR(225),
					 pp_mode VARCHAR(50),
					 pp_return_url VARCHAR(225),
					 pp_cancel_url VARCHAR(225),
					 PRIMARY KEY (`id`)
					 )");
			//If installing plugin for first time, add test settings
			$settings_count = $wpdb->query("SELECT * FROM ".$table_name);
			if($settings_count==0){
				$wpdb->query("INSERT INTO ".$table_name."(
					company_email,
					pp_test_account,
					pp_mode
				) VALUES (
					'info@toltech.co.uk',
					'anthony-facilitator@toltech.co.uk',
					'Test Mode'
				)");
			}
	}
}

/*/ Set plugin base folder and include files /*/
$plugin_basename = plugin_basename(__FILE__);

include('inc/shortcodes.php');
include('inc/functions.php');
?>