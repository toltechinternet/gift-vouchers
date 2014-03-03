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
  add_submenu_page( null, 'Edit Certificate', 'Edit Certificate', 'manage_options', 'edit-certificates', 'edit_certificate');
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
                     $output .= '<table>
								 	<tr>
								 		<td>
								 			<input type="hidden" name="action" value="save_settings" />';
					 			$output .= 'Company Name<br><input type="text" id="company_name" name="company_name" value="'.$settings->company_name.'"><br><br>';
	                 			$output .= 'Admin Email<br><input type="text" id="company_email" name="company_email" value="'.$settings->company_email.'"><br><br></td></tr>';
	                 $output .= '<tr>
	                 			 	<td width="350px">Company Info<br><textarea rows="7" cols="60" id="company_info" name="company_info">'.$settings->company_info.'</textarea><br><br></td>';
	                 	$output .= '<td width="350px">Terms &amp; Conditions<br><textarea rows="7" cols="60" id="terms_conditions" name="terms_conditions">'.$settings->terms_conditions.'</textarea><br><br></td>';
	                 	$output .= '<td width="350px">Delivery Information<br><textarea rows="7" cols="60" id="delivery_information" name="delivery_information">'.$settings->delivery_information.'</textarea><br><br></td>
	                 			</tr>
	                 		</table>';
					 
	                 $output .= '<hr>';

	                 $output .= '<table style="float: left;" width="870px">
	                 				<tr valign="top">
	                 					<td><table>
									<tr><h3>Paypal Settings</h3>
										<td valign="top" width="200px">Live Paypal Account<br><input type="text" id="pp_live_account" name="pp_live_account" value="'.$settings->pp_live_account.'"><br><br>';
	                 		$output .= 'Test Paypal Account<br><input type="text" id="pp_test_account" name="pp_test_account" value="'.$settings->pp_test_account.'"><br><br></td>';
	                			 if($settings->pp_mode=="Test Mode"){$a='selected="selected"';}else{$b='selected="selected"';}
					 $output .= '<td>Mode<br><select name="pp_mode"><option '.$a.'>Test Mode</option><option '.$b.'>Live Mode</option></select><br><br>';
	                 $output .= 'Return URL<br><input type="text" id="pp_return_url" name="pp_return_url" value="'.$settings->pp_return_url.'"><br><br>';
	                 $output .= 'Cancel URL<br><input type="text" id="pp_cancel_url" name="pp_cancel_url" value="'.$settings->pp_cancel_url.'"><br><br></td></tr></table>';
	                		$output .= '</td><td>';
	                
	                 $output .= '<table>';
	                 	 $output .= '<tr><h3>Google Analytics (Not yet Working)</h3>';
	                 	 	 $output .= '<td>Tracking Code <br><input style="width: 260px;" type="text" name="tracking_code" value="UA-xxxxxxxx-x" /></td>';
	                 	 $output .= '</tr>';
	                 	  $output .= '<tr>';
	                 	 	 $output .= '<td>Google Goal<br><input style="width: 260px;" type="text" name="tracking_goal" value="/Goal/Purchased-Gift-Voucher" /></td>';
	                 	 $output .= '</tr>';
	                 $output .= '</table>';
	                 $output .= '</td></tr></table><div style="clear: both;"></div>';
	                
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
	$company_email=$_REQUEST['company_email'];
    $company_info=$_REQUEST['company_info'];
    $terms_conditions=$_REQUEST['terms_conditions'];
	$delivery_information=$_REQUEST['delivery_information'];
    $pp_live_account=$_REQUEST['pp_live_account'];
    $pp_test_account=$_REQUEST['pp_test_account'];
    $pp_mode=$_REQUEST['pp_mode'];
    $pp_return_url=$_REQUEST['pp_return_url'];
    $pp_cancel_url=$_REQUEST['pp_cancel_url'];
    
    $table_name = $wpdb->prefix . 'toltech_gift_vouchers_settings';
    //If installing plugin for first time, add a test record
    $voucher_count = $wpdb->query("SELECT * FROM ".$table_name);
    if($voucher_count==0){
        //INSERT
        $wpdb->query(
			$wpdb->prepare(
			"INSERT INTO ".$table_name."(company_name,company_email,company_info,terms_conditions,delivery_information,pp_live_account,pp_test_account,pp_mode,pp_return_url,pp_cancel_url) VALUES (%s,%s,%s,%s,$s,%s,%s,%s,%s,%s)",
			$company_name,
			$company_email,
			$company_info,
			$terms_conditions,
			$delivery_information,
			$pp_live_account,
			$pp_test_account,
			$pp_mode,
			$pp_return_url,
			$pp_cancel_url
			)
		);
    }else{
        //UPDATE
        $wpdb->query(
			$wpdb->prepare(
			"UPDATE ".$table_name." SET company_name=%s,company_email=%s,company_info=%s,terms_conditions=%s,delivery_information=%s,pp_live_account=%s,pp_test_account=%s,pp_mode=%s,pp_return_url=%s,pp_cancel_url=%s",
			$company_name,
			$company_email,
			$company_info,
			$terms_conditions,
			$delivery_information,
			$pp_live_account,
			$pp_test_account,
			$pp_mode,
			$pp_return_url,
			$pp_cancel_url
			)
		);
    }
    //return to settings page
    wp_redirect(get_option('siteurl').'/wp-admin/admin.php?page=settings');
}

add_action('admin_post_edit_voucher', 'edit_voucher');
function edit_voucher(){
	//***********************************
	// Update voucher details
	//***********************************
	echo "here";
	global $wpdb;
	$table_name = $wpdb->prefix .'toltech_gift_vouchers';
	$id=$_REQUEST['id'];
	$name=$_REQUEST['name'];
	$email=$_REQUEST['email'];
	$telephone=$_REQUEST['telephone'];
	$recipient=$_REQUEST['recipient'];
	
	 $wpdb->query(
	 	$wpdb->prepare(
			"UPDATE ".$table_name." SET name=%s, email=%s, telephone=%s, recipient_name=%s WHERE id=%d",
			 $name,$email,$telephone,$recipient,$id
		)
	);
	//return to sold vouchers page
    wp_redirect(get_option('siteurl').'/wp-admin/admin.php?page=sold-certificates');
}

function voucher_sold(){
	global $wpdb;
    
    $output .= '<div class="wrap">';
    $output .= '<div id="icon-options-general" class="icon32"><br></div>';
    	$output .= '<h2>Sold Certificates</h2>';
    		$output .= '<div class="legend"><img src="'. get_bloginfo("url") .'/wp-content/plugins/gift-vouchers/images/info.png" style="float: left; margin-right: 10px; margin-top: -4px;" /> Certificates highlighted are new within a 5 day period.</div>';
					$output .= '<table class="wp-list-table widefat fixed">';
					$output .= '<tr>';
						$output .= '<th class="certificate_id"></th>';
						$output .= '<th style="width: 120px" class="certificate_th">Purchaser Name</th>';
						$output .= '<th class="certificate_th">Email</th>';
						$output .= '<th class="certificate_th">Telephone</th>';
						$output .= '<th class="certificate_th">Recipient Name</th>';
						$output .= '<th class="certificate_th">Delivery Method</th>';
						$output .= '<th class="certificate_th">Cost</th>';
						$output .= '<th class="certificate_th">Status</th>';
						$output .= '<th class="certificate_th">Pending Reason</th>';
						$output .= '<th class="certificate_th"></th>';
					$output .= '</tr>';
	
		$rows= $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."toltech_gift_vouchers ORDER BY id desc" );
	
				foreach($rows as $row){
					if( strtotime($row->date_purchased) > strtotime('-5 day') ) {
    					$output .= '<tr class="new">';
						} else { $output .= '<tr>'; }
					
						$output .= '<td><a id="id-'.$row->id.'" href="#"><img width="20" height="20" src="'. get_bloginfo("url") .'/wp-content/plugins/gift-vouchers/images/info.png" /></a></td>';
						$output .= '<td>'.$row->name.'</td>';
						$output .= '<td><a href="mailto:'.$row->email.'"">'.$row->email.'</a></td>';
						//$output .= '<td>'.$row->address1.' '.$row->address2.'<br>'.$row->.'</td>';
						$output .= '<td>'.$row->telephone.'</td>';
						$output .= '<td>'.$row->recipient_name.'</td>';
						$output .= '<td>'.$row->delivery_method.'</td>';
						$output .= '<td>'.$row->voucher_cost.'</td>';
						$output .= '<td>'.$row->status.'</td>';
						$output .= '<td>'.$row->pending_reason.'</td>';
						$output .= '<td>
										<a id="button-'.$row->id.'" href="#">Edit</a> - 
										<a href="'. get_bloginfo("url") .'/wp-content/plugins/gift-vouchers/inc/resend-certificate.php?id='.$row->id.'&name='.$row->name.'&email='.$row->email.'&recipient='.$row->recipient_name.'&address1='.$row->address1.'&address2='.$row->address2.'&city='.$row->city.'&postalcode='.$row->postalcode.'&state='.$row->state.'&country='.$row->country.'&telephone='.$row->telephone.'&cost='.$row->voucher_cost.'">Resend</a> - 
										<a class="del" href="'. get_bloginfo("url") .'/wp-content/plugins/gift-vouchers/inc/delete-certificate.php?id='.$row->id.'">Delete</a></td>';
					$output .= '</tr>';
					$output .= '<tr>';
						$output .= '<td colspan="10">';

							$output .= '<div id="payment-div-'.$row->id.'" class="hidedetails">
											<div class="moredetails">
												<img src="'. get_bloginfo("url") .'/wp-content/plugins/gift-vouchers/images/paypal.png" style="float: left; margin-right: 15px;" />
													<strong>Payment ID:</strong> '.$row->id.' - <strong>Payment Time: </strong> '.$row->date_purchased.' - <strong>Email Sent: </strong> '.$row->email_sent.'
											</div>
										</div>';
							$output .= '<div id="payment-div-'.$row->id.'" class="hidedetails">
											<div class="moredetails">
												<img src="'. get_bloginfo("url") .'/wp-content/plugins/gift-vouchers/images/address.png" style="float: left; margin-right: 15px;" />'.$row->address1.', '.$row->address2.', '.$row->city.', '.$row->state.', '.$row->postal_code.', '.$row->country.'
											</div>
										</div>';

							$output .= '<div id="toggle-div-'.$row->id.'" style="display: none;">
										<form action="'.get_admin_url().'admin-post.php" method="post">
										<input type="hidden" name="action" value="edit_voucher" />
											<table style="width: 100%!important;" class="update_certificate">
											<tr>
												<th class="white">Purchaser Name</th>
												<th class="white">Email</th>
												<th class="white">Telephone</th>
												<th class="white">Recipient Name</th>
												<th></th>
											</tr>
											<tr>
												<td><input type="text" name="name" value="'.$row->name.'" class="update_width" /></td>
												<td><input type="text" name="email" value="'.$row->email.'" class="update_width" /></td>
												<td><input type="text" name="telephone" value="'.$row->telephone.'" class="update_width" /></td>
												<td><input type="text" name="recipient" value="'.$row->recipient_name.'" class="update_width" /></td>
												<td><input type="submit" value="Update" /><input type="hidden" name="id" value="'.$row->id.'" /></td>
											</tr>
											</table>
										</form>
										</div>';
						$output .= '</td>';
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
