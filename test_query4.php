<?php
$db = new mysqli('localhost', 'root', '', 'erp_psm_db');
if ($db->connect_error) die("Connection failed: " . $db->connect_error);

$sql = "SELECT vendor_quote_item_id, item_code, qty, rate, gst, amount FROM vendor_quote_item_info WHERE vendor_quote_id = 1 AND status = 'Active'";
$result = $db->query($sql);
echo "Items:\n";
print_r($result->fetch_all(MYSQLI_ASSOC));

$sql = "SELECT addt_charges_amt, addt_charges_vat, addt_charges_vat_amt, addt_charges_tot_amt FROM vendor_quote_addtchrg_info WHERE vendor_quote_id = 1 AND status = 'Active'";
$result = $db->query($sql);
echo "\nAddt charges:\n";
print_r($result->fetch_all(MYSQLI_ASSOC));
