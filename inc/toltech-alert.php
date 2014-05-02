<?php
//ALERT TOLTECH THAT VOUCHER PURCHASED
$toltech_recipients="joe@toltech.co.uk,anthony@toltech.co.uk";
$toltech_subject="Voucher Purchase from ".$settings->company_name." Website.";
$toltech_body="Voucher Purchased from ".$settings->company_name." Website.";
mail($toltech_recipients, $toltech_subject, $toltech_body, $headers);
?>