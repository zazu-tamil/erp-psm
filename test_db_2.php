<?php
$mysqli = new mysqli('localhost', 'root', '', 'erp_psm_db');
$result = $mysqli->query('SHOW COLUMNS FROM tender_po_info');
echo "tender_po_info:\n";
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
