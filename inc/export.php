<?php
//Allows us to use wordpress to query DB
include_once('../../../../wp-config.php');
include_once('../../../../wp-includes/wp-db.php');
global $wpdb;

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=export.csv');

// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// output the column headings
fputcsv($output, array('Purchaser', 'Email', 'Address1', 'Address2', 'City', 'State', 'Postcode', 'Country', 'Telephone', 'Recipient', 'Delivery Method', 'Voucher Cost', 'Status', 'Pending Reason', 'Date Purchased', 'Email Sent', 'Voucher Code'));

// fetch the data
$rows = mysql_query('SELECT name,email,address1,address2,city,state,postal_code,country,telephone,recipient_name,delivery_method,voucher_cost,status,pending_reason,date_purchased,email_sent,voucher_code FROM  '.$wpdb->prefix.'toltech_gift_vouchers');

// loop over the rows, outputting them
while ($row = mysql_fetch_assoc($rows)) fputcsv($output, $row);

fclose($output);
