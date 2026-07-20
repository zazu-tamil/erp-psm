<?php
$mysqli = new mysqli('localhost', 'root', '', 'erp_psm_db');
$result = $mysqli->query("SELECT currency_id, currency_code FROM currencies_info WHERE currency_code = 'BHD'");
if($result) {
    while($row = $result->fetch_assoc()){
        echo $row['currency_code'] . ' - ' . $row['currency_id'] . "\n";
    }
}
