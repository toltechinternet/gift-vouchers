<?php

/*/ Register Custom Post Type /*/

function gift_vouchers() {
	$labels = array(
		'name'               => _x( 'Gift Vouchers', 'post type general name' ),
		'singular_name'      => _x( 'Voucher', 'post type singular name' ),
		'add_new'            => _x( 'Create Voucher', 'create voucher' ),
		'add_new_item'       => __( 'Add New Voucher' ),
		'edit_item'          => __( 'Edit Product' ),
		'new_item'           => __( 'New Product' ),
		'all_items'          => __( 'View All' ),
		'view_item'          => __( 'View Voucher' ),
		'search_items'       => __( 'Search Gift Vouchers' ),
		'not_found'          => __( 'No Gift Vouchers found' ),
		'not_found_in_trash' => __( 'No Gift Vouchers found in the Trash' ), 
		'parent_item_colon'  => '',
		'menu_name'          => 'Gift Forms',
	);
	$args = array(
		'labels'        => $labels,
		'description'   => 'Holds our products and product specific data',
		'public'        => true,
		'menu_position' => 25,
		'supports'      => array( 'title' ),
		'show_in_menu' => 'theme-options',
		'has_archive'   => true,
		'menu_icon' => get_bloginfo("url") . "/wp-content/plugins/gift-vouchers/images/menu-icon.png",
		'register_meta_box_cb' => 'add_voucher_metaboxes'
	);

	register_post_type( 'gift-vouchers', $args );

}
add_action( 'init', 'gift_vouchers' );


/*/ Plugin Pages for Creating Voucher, Settings and Sold Certificates /*/
function theme_options_panel(){
  add_menu_page('Gift Vouchers', 'Gift Vouchers', 'manage_options', 'theme-options', 'gift_vouchers', get_bloginfo("url") . "/wp-content/plugins/gift-vouchers/images/menu-icon.png");
  add_submenu_page( 'theme-options', 'Create Voucher', 'Create Voucher', 'manage_options', 'post-new.php?post_type=gift-vouchers');
  add_submenu_page( 'theme-options', 'Settings', 'Settings', 'manage_options', 'settings', 'voucher_settings');
  add_submenu_page( 'theme-options', 'Sold Certificates', 'Sold Certificates', 'manage_options', 'sold-certificates', 'voucher_sold');
}

add_action('admin_menu', 'theme_options_panel');

function voucher_settings(){
    
global $wpdb;
$table_name = $wpdb->prefix . 'toltech_gift_vouchers_settings';
$settings = $wpdb->get_row("SELECT * FROM ".$table_name,OBJECT);
    
    
    
                $output .= '<div class="wrap"><div id="icon-options-general" class="icon32"><br></div>';
                
	                 $output .= '<h2>Settings</h2>';
	                
	                 $output .= '<h3>General Settings</h3>';
                     $output .= '<form action="'.get_admin_url().'admin-post.php" method="post">';
                     $output .= '<input type="hidden" name="action" value="save_settings" />';
	                 $output .= 'Company Name<br><input type="text" id="company_name" name="company_name" value="'.$settings->company_name.'"><br><br>';
	                 $output .= 'Company Info<br><textarea rows="7" cols="60" id="company_info" name="company_info">'.$settings->company_info.'</textarea><br><br>';
	                 $output .= 'Terms &amp; Conditions<br><textarea rows="7" cols="60" id="terms_conditions" name="terms_conditions">'.$settings->terms_conditions.'</textarea><br><br>';
	                 
	                 $output .= '<hr>';

	                 $output .= '<h3>Paypal Settings</h3>';
	                 $output .= 'Live Paypal Account<br><input type="text" id="pp_live_account" name="pp_live_account" value="'.$settings->pp_live_account.'"><br><br>';
	                 $output .= 'Test Paypal Account<br><input type="text" id="pp_test_account" name="pp_test_account" value="'.$settings->pp_test_account.'"><br><br>';
	                 if($settings->pp_mode=="Test Mode"){$a='selected="selected"';}else{$b='selected="selected"';}
					 $output .= 'Mode<br><select name="pp_mode"><option '.$a.'>Test Mode</option><option '.$b.'>Live Mode</option></select><br><br>';
	                 $output .= 'Return URL<br><input type="text" id="pp_return_url" name="pp_return_url" value="'.$settings->pp_return_url.'"><br><br>';
	                 $output .= 'Cancel URL<br><input type="text" id="pp_cancel_url" name="pp_cancel_url" value="'.$settings->pp_cancel_url.'"><br><br>';
	                 $output .= 'Notify URL<br><input type="text" id="pp_notify_url" name="pp_notify_url" value="'.$settings->pp_notify_url.'"><br><br>';
	                 $output .= '<hr>';
                     $output .= '<input type="submit" id="submit">';
                     $output .= '</form>';
	                
	            $output .= '</div>';

                 echo $output;
}
    
add_action('admin_post_save_settings', 'save_settings');
function save_settings(){
    //**************************************************************************//
    // Update settings table or insert new record if first time adding settings //
    //**************************************************************************//
    global $wpdb;
    
    $company_name=$_REQUEST['company_name'];
    $company_info=$_REQUEST['company_info'];
    $terms_conditions=$_REQUEST['terms_conditions'];
    $pp_live_account=$_REQUEST['pp_live_account'];
    $pp_test_account=$_REQUEST['pp_test_account'];
    $pp_mode=$_REQUEST['pp_mode'];
    $pp_return_url=$_REQUEST['pp_return_url'];
    $pp_cancel_url=$_REQUEST['pp_cancel_url'];
    $pp_notify_url=$_REQUEST['pp_notify_url'];
    
    $table_name = $wpdb->prefix . 'toltech_gift_vouchers_settings';
    //If installing plugin for first time, add a test record
    $voucher_count = $wpdb->query("SELECT * FROM ".$table_name);
    if($voucher_count==0){
        //INSERT
        $wpdb->query("INSERT INTO ".$table_name."(company_name,company_info,terms_conditions,pp_live_account,pp_test_account,pp_mode,pp_return_url,pp_cancel_url,pp_notify_url) VALUES ('".$company_name."','".$company_info."','".$terms_conditions."','".$pp_live_account."','".$pp_test_account."','".$pp_mode."','".$pp_return_url."','".$pp_cancel_url."','".$pp_notify_url."')");
    }else{
        //UPDATE
        $wpdb->query("UPDATE ".$table_name." SET company_name='".$company_name."',company_info='".$company_info."',terms_conditions='".$terms_conditions."',pp_live_account='".$pp_live_account."',pp_test_account='".$pp_test_account."',pp_mode='".$pp_mode."',pp_return_url='".$pp_return_url."',pp_cancel_url='".$pp_cancel_url."',pp_notify_url='".$pp_notify_url."'");
    }
    //return to settings page
    wp_redirect(get_option('siteurl').'/wp-admin/admin.php?page=settings');
}
    

function voucher_sold(){
	global $wpdb;
    
    $output .= '<div class="wrap">';
    $output .= '<div id="icon-options-general" class="icon32"><br></div>';
    	$output .= '<h2>Sold Certificates</h2>';
					$output .= '<table class="wp-list-table widefat fixed">';
					$output .= '<tr>';
						$output .= '<th style="color: white; font-weight:bold;background:#0074a2;width:20px;">ID</th>';
						$output .= '<th style="color: white; font-weight:bold;background:#0074a2;">Name</th>';
						$output .= '<th style="color: white; font-weight:bold;background:#0074a2;">Email</th>';
						$output .= '<th style="color: white; font-weight:bold;background:#0074a2;">Address</th>';
						$output .= '<th style="color: white; font-weight:bold;background:#0074a2;">Telephone</th>';
						$output .= '<th style="color: white; font-weight:bold;background:#0074a2;">Recipient Name</th>';
						$output .= '<th style="color: white; font-weight:bold;background:#0074a2;">Delivery Method</th>';
						$output .= '<th style="color: white; font-weight:bold;background:#0074a2;">Cost</th>';
						$output .= '<th style="color: white; font-weight:bold;background:#0074a2;">Status</th>';
						$output .= '<th style="color: white; font-weight:bold;background:#0074a2;">Pending Reason</th>';
						$output .= '<th style="background:#0074a2;"></th>';
					$output .= '</tr>';
	
		$rows= $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."toltech_gift_vouchers" );
	
				foreach($rows as $row){
					$output .= '<tr >';
						$output .= '<td>'.$row->id.'</td>';
						$output .= '<td>'.$row->name.'</td>';
						$output .= '<td><a href=\"mailto:'.$row->email.'>'.$row->email.'</a></td>';
						$output .= '<td>'.$row->address.'</td>';
						$output .= '<td>'.$row->telephone.'</td>';
						$output .= '<td>'.$row->recipient_name.'</td>';
						$output .= '<td>'.$row->delivery_method.'</td>';
						$output .= '<td>'.$row->voucher_cost.'</td>';
						$output .= '<td>'.$row->status.'</td>';
						$output .= '<td>'.$row->pending_reason.'</td>';
						$output .= '<td><a href="#">Edit</a> - <a href="#"">Resend</a></td>';
					$output .= '</tr>';
				}
					$output .= '</table></div>';
	
			   echo $output;
}


/*/ Meta boxes for creating voucher form /*/

add_action( 'add_meta_boxes', 'add_voucher_metaboxes' );
function add_voucher_metaboxes() {
	add_meta_box("gift_voucher_details", "Gift Voucher Details", "gift_voucher_details_ui", "gift-vouchers");
}

function gift_voucher_details_ui() {
	global $post;
	$custom = get_post_custom($post->ID);

	$price = $custom['_price'][0];
	$description = $custom['_description'][0];

	$output .= '<p><label for="_price">Price (£):</label><br />';
	$output .= '<input type="text" name="_price" id="_price" size="40" value="'. $price .'" /></p>';
	
	$output .= '<p><label for="_description">Description:</label><br />';
	$output .= '<textarea type="text" name="_description" id="_description" cols="60" rows="5" />'. $description .'</textarea></p>';

	echo $output;  

}	

/*/ Saving Meta Boxes /*/
add_action('save_post', 'gift_voucher_save_post');
function gift_voucher_save_post($post_id) {
	$post = get_post($post_id);
	
	if ($post->post_type != 'gift-vouchers' || !isset($_POST['_price']))
			return;
	
	update_post_meta($post->ID, "_price", $_POST["_price"]);
	update_post_meta($post->ID, "_description", $_POST["_description"]);
}

?>
