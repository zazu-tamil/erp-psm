<?php
$mysqli = new mysqli('localhost', 'root', '', 'erp_psm_db');
$result = $mysqli->query('SHOW COLUMNS FROM vendor_purchase_invoice_info');
echo "vendor_purchase_invoice_info:\n";
if($result) {
    while($row = $result->fetch_assoc()){
        echo $row['Field'] . ' - ' . $row['Type'] . "\n";
    }
}
$result = $mysqli->query('SHOW COLUMNS FROM vendor_po_info');
echo "vendor_po_info:\n";
if($result) {
    while($row = $result->fetch_assoc()){
        echo $row['Field'] . ' - ' . $row['Type'] . "\n";
    }
}
