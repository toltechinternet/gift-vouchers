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
                echo '<div class="wrap"><div id="icon-options-general" class="icon32"><br></div>
                <h2>Settings</h2></div>';
}

function voucher_sold(){
				global $wpdb;
                $html='<div class="wrap"><div id="icon-options-general" class="icon32"><br></div>
                <h2>Sold Certificates</h2>
				<table class="wp-list-table widefat fixed">
				<tr>
				<th style="color: white; font-weight:bold;background:#0074a2;width:20px;">ID</th>
				<th style="color: white; font-weight:bold;background:#0074a2;">Name</th>
				<th style="color: white; font-weight:bold;background:#0074a2;">Email</th>
				<th style="color: white; font-weight:bold;background:#0074a2;">Address</th>
				<th style="color: white; font-weight:bold;background:#0074a2;">Telephone</th>
				<th style="color: white; font-weight:bold;background:#0074a2;">Recipient Name</th>
				<th style="color: white; font-weight:bold;background:#0074a2;">Delivery Method</th>
				<th style="color: white; font-weight:bold;background:#0074a2;">Cost</th>
				<th style="color: white; font-weight:bold;background:#0074a2;">Status</th>
				<th style="color: white; font-weight:bold;background:#0074a2;">Pending Reason</th>
				<th style="background:#0074a2;"></th>
				</tr>';
				
				$rows= $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."toltech_gift_vouchers" );
				
				foreach($rows as $row){
					$html.="<tr >";
					$html.="<td>".$row->id."</td>";
					$html.="<td>".$row->name."</td>";
					$html.="<td><a href=\"mailto:".$row->email."\">".$row->email."</a></td>";
					$html.="<td>".$row->address."</td>";
					$html.="<td>".$row->telephone."</td>";
					$html.="<td>".$row->recipient_name."</td>";
					$html.="<td>".$row->delivery_method."</td>";
					$html.="<td>".$row->voucher_cost."</td>";
					$html.="<td>".$row->status."</td>";
					$html.="<td>".$row->pending_reason."</td>";
					$html.="<td>
						[<a href=\"#\">EDIT</a>]
						[<a href=\"#\">RESEND</a>]
						</td>";
					$html.="</tr>";
				}
				
				
				$html.='</table>
				</div>';
				echo $html;
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
?>

<p><label for="_price">Price (£):</label><br />
<input type="text" name="_price" id="_price" size="40" value="<?php echo $price;?>" /></p>

<p><label for="_description">Description:</label><br />
<textarea type="text" name="_description" id="_description" cols="60" rows="5" /><?php echo $description;?></textarea></p>

<?php	}	

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
