<?php
$mysqli = new mysqli('localhost', 'root', '', 'erp_psm_db');
$result = $mysqli->query('SHOW COLUMNS FROM tender_enq_invoice_item_info');
echo "tender_enq_invoice_item_info:\n";
while($row = $result->fetch_assoc()){
    echo $row['Field'] . ' - ' . $row['Type'] . "\n";
}
$result = $mysqli->query('SHOW COLUMNS FROM tender_enq_invoice_info');
echo "\ntender_enq_invoice_info:\n";
while($row = $result->fetch_assoc()){
    echo $row['Field'] . ' - ' . $row['Type'] . "\n";
}
$result = $mysqli->query('SHOW COLUMNS FROM vendor_purchase_invoice_item_info');
echo "\nvendor_purchase_invoice_item_info:\n";
if($result) {
    while($row = $result->fetch_assoc()){
        echo $row['Field'] . ' - ' . $row['Type'] . "\n";
    }
} else {
    echo "table not found\n";
    $result = $mysqli->query('SHOW COLUMNS FROM vendor_purchase_bill_item_info');
    if($result) {
        while($row = $result->fetch_assoc()){
            echo $row['Field'] . ' - ' . $row['Type'] . "\n";
        }
    } else {
        echo "vendor_purchase_bill_item_info not found\n";
    }
}
$result = $mysqli->query('SHOW COLUMNS FROM tender_enquiry_info');
echo "\ntender_enquiry_info:\n";
while($row = $result->fetch_assoc()){
    echo $row['Field'] . ' - ' . $row['Type'] . "\n";
}
