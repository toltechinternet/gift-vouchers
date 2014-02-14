<?php
//Allows us to use wordpress to query DB
include_once('../../../../wp-config.php');
include_once('../../../../wp-includes/wp-db.php');
global $wpdb;

$wpdb->query('DELETE FROM '.$wpdb->prefix.'toltech_gift_vouchers WHERE ID='.$_GET['id'].'');

wp_redirect(get_option('siteurl').'/wp-admin/admin.php?page=sold-certificates');

