<?php
$mysqli = new mysqli('localhost', 'root', '', 'erp_psm_db');
if ($mysqli->connect_error) {
    die('DB error: ' . $mysqli->connect_error);
}

// 1. Find the non-header Reports dropdown (parent_id = 0, is_header = 0)
$res = $mysqli->query("SELECT menu_id FROM menu_info WHERE parent_id = 0 AND is_header = 0 AND (menu_title = 'Reports' OR menu_icon = 'fa fa-area-chart') AND status != 'Delete' LIMIT 1");
$r = $res->fetch_assoc();
$reports_main_id = $r ? (int)$r['menu_id'] : 152;
echo "Found Reports main ID: {$reports_main_id}\n";

// 2. Reparent any Tender Report / Supplier Report under $reports_main_id
$mysqli->query("UPDATE menu_info SET parent_id = {$reports_main_id} WHERE menu_title IN ('Tender Report', 'Supplier Report') AND is_header = 0 AND status != 'Delete'");

// 3. Ensure Tender Report exists under $reports_main_id
$t_res = $mysqli->query("SELECT menu_id FROM menu_info WHERE parent_id = {$reports_main_id} AND menu_title = 'Tender Report' AND status != 'Delete' LIMIT 1");
$t_row = $t_res->fetch_assoc();
if ($t_row) {
    $tender_id = (int)$t_row['menu_id'];
    $mysqli->query("UPDATE menu_info SET menu_icon = 'fa fa-file-text-o', sort_order = 1 WHERE menu_id = {$tender_id}");
} else {
    $mysqli->query("INSERT INTO menu_info (parent_id, menu_title, menu_slug, menu_icon, is_header, sort_order, status) VALUES ({$reports_main_id}, 'Tender Report', NULL, 'fa fa-file-text-o', 0, 1, 'Active')");
    $tender_id = $mysqli->insert_id;
}
echo "Tender Report ID: {$tender_id}\n";

// Delete any old legacy "Tender Info Report" with parent_id = $reports_main_id
$mysqli->query("UPDATE menu_info SET status = 'Delete' WHERE parent_id = {$reports_main_id} AND menu_title = 'Tender Info Report'");

// 4. Ensure Supplier Report exists under $reports_main_id
$s_res = $mysqli->query("SELECT menu_id FROM menu_info WHERE parent_id = {$reports_main_id} AND menu_title = 'Supplier Report' AND status != 'Delete' LIMIT 1");
$s_row = $s_res->fetch_assoc();
if ($s_row) {
    $supplier_id = (int)$s_row['menu_id'];
    $mysqli->query("UPDATE menu_info SET menu_icon = 'fa fa-industry', sort_order = 2 WHERE menu_id = {$supplier_id}");
} else {
    $mysqli->query("INSERT INTO menu_info (parent_id, menu_title, menu_slug, menu_icon, is_header, sort_order, status) VALUES ({$reports_main_id}, 'Supplier Report', NULL, 'fa fa-industry', 0, 2, 'Active')");
    $supplier_id = $mysqli->insert_id;
}
echo "Supplier Report ID: {$supplier_id}\n";

// 5. Delete obsolete duplicate rows
$mysqli->query("UPDATE menu_info SET status = 'Delete' WHERE menu_slug IN ('customer-invoice-pending-report', 'vendor-invoice-pending-report')");

// 6. Ensure Customer Pending Report exists under Tender Report
$cp_res = $mysqli->query("SELECT menu_id FROM menu_info WHERE menu_slug = 'customer-pending-invoice-report' AND status != 'Delete' LIMIT 1");
$cp_row = $cp_res->fetch_assoc();
if ($cp_row) {
    $cp_id = (int)$cp_row['menu_id'];
    $mysqli->query("UPDATE menu_info SET parent_id = {$tender_id}, menu_title = 'Customer Pending Report', menu_icon = 'fa fa-file-text' WHERE menu_id = {$cp_id}");
} else {
    $mysqli->query("INSERT INTO menu_info (parent_id, menu_title, menu_slug, menu_icon, is_header, sort_order, status) VALUES ({$tender_id}, 'Customer Pending Report', 'customer-pending-invoice-report', 'fa fa-file-text', 0, 4, 'Active')");
    $cp_id = $mysqli->insert_id;
}

// 7. Ensure Vendor Pending Report exists under Supplier Report
$vp_res = $mysqli->query("SELECT menu_id FROM menu_info WHERE menu_slug = 'vendor-pending-invoice-report' AND status != 'Delete' LIMIT 1");
$vp_row = $vp_res->fetch_assoc();
if ($vp_row) {
    $vp_id = (int)$vp_row['menu_id'];
    $mysqli->query("UPDATE menu_info SET parent_id = {$supplier_id}, menu_title = 'Vendor Pending Report', menu_icon = 'fa fa-file-text' WHERE menu_id = {$vp_id}");
} else {
    $mysqli->query("INSERT INTO menu_info (parent_id, menu_title, menu_slug, menu_icon, is_header, sort_order, status) VALUES ({$supplier_id}, 'Vendor Pending Report', 'vendor-pending-invoice-report', 'fa fa-file-text', 0, 1, 'Active')");
    $vp_id = $mysqli->insert_id;
}

// 8. Move Tender reports under $tender_id
$tender_slugs = "'tender-enquiry-timeline', 'tender-enquiry-summary-report', 'item-rate-report', 'customer-pending-invoice-report', 'customer-statement-report', 'invoice-report', 'tender-progress-report'";
$mysqli->query("UPDATE menu_info SET parent_id = {$tender_id} WHERE menu_slug IN ({$tender_slugs}) AND status != 'Delete'");

// 9. Move Supplier reports under $supplier_id
$supplier_slugs = "'vendor-pending-invoice-report', 'vendor-statement-report', 'supplier-summary-report'";
$mysqli->query("UPDATE menu_info SET parent_id = {$supplier_id} WHERE menu_slug IN ({$supplier_slugs}) AND status != 'Delete'");

// 10. Fix sort orders under $reports_main_id
$mysqli->query("UPDATE menu_info SET sort_order = 1 WHERE menu_id = {$tender_id}");
$mysqli->query("UPDATE menu_info SET sort_order = 2 WHERE menu_id = {$supplier_id}");
$mysqli->query("UPDATE menu_info SET sort_order = 3 WHERE parent_id = {$reports_main_id} AND menu_title = 'NBR Report'");
$mysqli->query("UPDATE menu_info SET sort_order = 4 WHERE parent_id = {$reports_main_id} AND menu_slug = 'pl-report'");
$mysqli->query("UPDATE menu_info SET sort_order = 5 WHERE parent_id = {$reports_main_id} AND menu_slug = 'account-trial-balance'");

// 11. Grant permissions to all roles for tender_id and supplier_id
$roles_res = $mysqli->query("SELECT role_id FROM role_info WHERE status != 'Delete'");
while ($role = $roles_res->fetch_assoc()) {
    $rid = (int)$role['role_id'];
    foreach (array($tender_id, $supplier_id, $cp_id, $vp_id) as $mid) {
        $p_check = $mysqli->query("SELECT permission_id FROM role_permission WHERE role_id = {$rid} AND menu_id = {$mid}");
        if ($p_check->num_rows == 0) {
            $mysqli->query("INSERT INTO role_permission (role_id, menu_id, can_view, can_add, can_edit, can_delete) VALUES ({$rid}, {$mid}, 1, 1, 1, 1)");
        }
    }
}

echo "\n=== UPDATED TREE UNDER REPORTS (ID: {$reports_main_id}) ===\n";
$q = $mysqli->query("SELECT menu_id, parent_id, menu_title, menu_slug, menu_icon, is_header, sort_order, status FROM menu_info WHERE parent_id = {$reports_main_id} AND status != 'Delete' ORDER BY sort_order ASC");
while ($r = $q->fetch_assoc()) {
    echo "  [Level 2] ID: {$r['menu_id']} | Title: {$r['menu_title']} | Slug: {$r['menu_slug']} | Icon: {$r['menu_icon']}\n";
    $q_sub = $mysqli->query("SELECT menu_id, parent_id, menu_title, menu_slug, menu_icon, is_header, sort_order, status FROM menu_info WHERE parent_id = {$r['menu_id']} AND status != 'Delete' ORDER BY sort_order ASC");
    while ($r_sub = $q_sub->fetch_assoc()) {
        echo "      [Level 3] ID: {$r_sub['menu_id']} | Title: {$r_sub['menu_title']} | Slug: {$r_sub['menu_slug']}\n";
    }
}
