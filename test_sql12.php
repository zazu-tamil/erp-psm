<?php
$mysqli = new mysqli("localhost", "root", "", "erp_psm_db");

echo "vendor_rate_enquiry_info columns:\n";
$res = $mysqli->query("SHOW COLUMNS FROM vendor_rate_enquiry_info");
while($row = $res->fetch_assoc()) { echo $row['Field'] . "\n"; }

echo "\ntender_quotation_info columns:\n";
$res = $mysqli->query("SHOW COLUMNS FROM tender_quotation_info");
while($row = $res->fetch_assoc()) { echo $row['Field'] . "\n"; }
?>
