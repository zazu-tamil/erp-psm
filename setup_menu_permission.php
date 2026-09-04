<?php
/**
 * One-time installer / seeder for the Role-based Menu & Permission system.
 *
 * Creates:
 *   - role_info          (roles, keyed by the existing user_login_info.level string)
 *   - menu_info          (dynamic menu tree)
 *   - role_permission    (per-role view/add/edit/delete flags per menu)
 *
 * Seeds the full menu tree (mirroring the previous admin-menu.php) and grants
 * the Admin role full access to every menu.
 *
 * Re-running is safe: tables are created with IF NOT EXISTS and seed data is
 * inserted only when the target table is empty (unless ?force=1 is passed).
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'erp_psm_db';

$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_error) {
    die('DB connection failed: ' . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');

$force = (isset($_GET['force']) && $_GET['force'] === '1') || (isset($argv[1]) && $argv[1] === 'force');

/* ---------------------------------------------------------------------------
 * 1. CREATE TABLES
 * ------------------------------------------------------------------------ */
$ddl = array();

$ddl[] = "CREATE TABLE IF NOT EXISTS `role_info` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_key` varchar(50) NOT NULL,
  `role_name` varchar(100) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'Active',
  `created_date` datetime DEFAULT current_timestamp(),
  `updated_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `uniq_role_key` (`role_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$ddl[] = "CREATE TABLE IF NOT EXISTS `menu_info` (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `menu_title` varchar(150) NOT NULL,
  `menu_slug` varchar(150) DEFAULT NULL,
  `menu_icon` varchar(60) DEFAULT NULL,
  `is_header` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'Active',
  `created_date` datetime DEFAULT current_timestamp(),
  `updated_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`menu_id`),
  KEY `idx_parent` (`parent_id`),
  KEY `idx_slug` (`menu_slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$ddl[] = "CREATE TABLE IF NOT EXISTS `role_permission` (
  `permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `can_view` tinyint(1) NOT NULL DEFAULT 0,
  `can_add` tinyint(1) NOT NULL DEFAULT 0,
  `can_edit` tinyint(1) NOT NULL DEFAULT 0,
  `can_delete` tinyint(1) NOT NULL DEFAULT 0,
  `created_date` datetime DEFAULT current_timestamp(),
  `updated_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`permission_id`),
  UNIQUE KEY `uniq_role_menu` (`role_id`,`menu_id`),
  KEY `idx_role` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

foreach ($ddl as $sql) {
    if (!$mysqli->query($sql)) {
        die('DDL error: ' . $mysqli->error . "\nSQL: " . $sql);
    }
}
echo "Tables ensured.\n";

/* ---------------------------------------------------------------------------
 * 2. SEED ROLES
 * ------------------------------------------------------------------------ */
$roles = array(
    array('key' => 'Admin', 'name' => 'Admin', 'default' => 1),
    array('key' => 'Staff', 'name' => 'Staff', 'default' => 0),
    array('key' => 'Supervisor', 'name' => 'Supervisor', 'default' => 0),
    array('key' => 'Agent', 'name' => 'Agent', 'default' => 0),
    array('key' => 'Labour', 'name' => 'Labour', 'default' => 0),
    array('key' => 'Vendor', 'name' => 'Vendor', 'default' => 0),
    array('key' => 'Customer', 'name' => 'Customer', 'default' => 0),
);

$count = (int) $mysqli->query("SELECT COUNT(*) c FROM role_info")->fetch_assoc()['c'];
if ($count === 0 || $force) {
    $sort = 1;
    foreach ($roles as $r) {
        $stmt = $mysqli->prepare("INSERT INTO role_info (role_key, role_name, is_default, sort_order, status) VALUES (?,?,?,?, 'Active')
                                  ON DUPLICATE KEY UPDATE role_name = VALUES(role_name), sort_order = VALUES(sort_order)");
        $stmt->bind_param('ssii', $r['key'], $r['name'], $r['default'], $sort);
        $stmt->execute();
        $stmt->close();
        $sort++;
    }
    echo "Roles seeded.\n";
} else {
    echo "Roles already present (skipped).\n";
}

/* ---------------------------------------------------------------------------
 * 3. SEED MENU TREE
 * ------------------------------------------------------------------------ */
$menu = array(
    array('h' => 'Dashboard & Help'),
    array('t' => 'Dashboard', 's' => 'dash', 'i' => 'fa fa-dashboard'),

    array('h' => 'TENDER'),
    array(
        't' => 'Tender Enquiry',
        'i' => 'fa fa-file-text-o',
        'c' => array(
            array('t' => 'Add Tender Enquiry', 's' => 'add-tender-enquiry', 'i' => 'fa fa-plus-circle'),
            array('t' => 'Tender Enquiry List', 's' => 'tender-enquiry-list', 'i' => 'fa fa-list'),
        )
    ),
    array(
        't' => 'Tender',
        'i' => 'fa fa-folder-open',
        'c' => array(
            array(
                't' => 'Tender Quotation',
                'i' => 'fa fa-file-text',
                'c' => array(
                    array('t' => 'Add Tender Quotation', 's' => 'tender-quotation-add', 'i' => 'fa fa-plus-circle'),
                    array('t' => 'Tender Quotation List', 's' => 'tender-quotation-list', 'i' => 'fa fa-list-ul'),
                )
            ),
            array(
                't' => 'Tender PO',
                'i' => 'fa fa-briefcase',
                'c' => array(
                    array('t' => 'Add Tender PO', 's' => 'customer-tender-po-add', 'i' => 'fa fa-plus-square'),
                    array('t' => 'Tender PO List', 's' => 'customer-tender-po-list', 'i' => 'fa fa-list'),
                )
            ),
            array(
                't' => 'Tender DC',
                'i' => 'fa fa-briefcase',
                'c' => array(
                    array('t' => 'Add Tender DC', 's' => 'tender-dc-add', 'i' => 'fa fa-plus-square'),
                    array('t' => 'Tender DC List', 's' => 'tender-dc-list', 'i' => 'fa fa-list'),
                )
            ),
            array(
                't' => 'Tender Invoice',
                'i' => 'fa fa-briefcase',
                'c' => array(
                    array('t' => 'Add Tender Invoice', 's' => 'tender-invoice-add', 'i' => 'fa fa-plus-square'),
                    array('t' => 'Tender Invoice List', 's' => 'tender-invoice-list', 'i' => 'fa fa-list'),
                )
            ),
            array('t' => 'Tender Receipt List', 's' => 'customer-invoice-receipt', 'i' => 'fa fa-list'),
        )
    ),

    array('h' => 'In Stock Items'),
    array(
        't' => 'In Stock Items',
        'i' => 'fa fa-file-text',
        'c' => array(
            array('t' => 'In Stock Item List', 's' => 'in-stock-item-list', 'i' => 'fa fa-plus-circle'),
            array('t' => 'In Stock Item Report', 's' => 'in-stock-item-report', 'i' => 'fa fa-list'),
        )
    ),

    array('h' => 'SUPPLIER'),
    array(
        't' => 'Supplier',
        'i' => 'fa fa-industry',
        'c' => array(
            array(
                't' => 'Supplier Enquiry',
                'i' => 'fa fa-envelope',
                'c' => array(
                    array('t' => 'Add Supplier Enquiry', 's' => 'vendor-rate-enquiry', 'i' => 'fa fa-plus-square'),
                    array('t' => 'Supplier Enquiry List', 's' => 'vendor-rate-enquiry-list', 'i' => 'fa fa-list'),
                )
            ),
            array(
                't' => 'Supplier Quotation',
                'i' => 'fa fa-file-text-o',
                'c' => array(
                    array('t' => 'Add Supplier Quotation', 's' => 'vendor-quotation-add', 'i' => 'fa fa-plus-square'),
                    array('t' => 'Supplier Quotation List', 's' => 'vendor-quotation-list', 'i' => 'fa fa-list'),
                )
            ),
            array(
                't' => 'Supplier PO',
                'i' => 'fa fa-files-o',
                'c' => array(
                    array('t' => 'Add Supplier PO', 's' => 'vendor-po-add', 'i' => 'fa fa-plus-square'),
                    array('t' => 'Supplier PO List', 's' => 'vendor-po-list', 'i' => 'fa fa-list'),
                )
            ),
            array(
                't' => 'Supplier Inward',
                'i' => 'fa fa-files-o',
                'c' => array(
                    array('t' => 'Add Supplier Inward', 's' => 'vendor-pur-inward-add', 'i' => 'fa fa-plus-square'),
                    array('t' => 'Supplier Inward List', 's' => 'vendor-pur-inward-list', 'i' => 'fa fa-list'),
                )
            ),
            array(
                't' => 'Supplier Invoice/Bill',
                'i' => 'fa fa-files-o',
                'c' => array(
                    array('t' => 'Add Supplier Bill Entry', 's' => 'vendor-purchase-bill-add', 'i' => 'fa fa-plus-square'),
                    array('t' => 'Supplier Bill List', 's' => 'vendor-purchase-bill-list', 'i' => 'fa fa-list'),
                    array('t' => 'Local Supplier Bill List', 's' => 'local-purchase-bill-list', 'i' => 'fa fa-list'),
                    array('t' => 'Delivery Partner Bill List', 's' => 'delivery-partner-bill-list', 'i' => 'fa fa-list'),
                    array('t' => 'Customs Bill List', 's' => 'customs-bill-list', 'i' => 'fa fa-list'),
                )
            ),
            array('t' => 'Supplier Advance Payment', 's' => 'vendor-adv-payment', 'i' => 'fa fa-list'),
            array('t' => 'Supplier Payment List', 's' => 'vendor-payment-list', 'i' => 'fa fa-list'),
        )
    ),

    array('h' => 'REPORTS'),
    array(
        't' => 'Reports',
        'i' => 'fa fa-area-chart',
        'c' => array(
            array(
                't' => 'Tender Report',
                'i' => 'fa fa-file-text-o',
                'c' => array(
                    array('t' => 'Tender Timeline Report', 's' => 'tender-enquiry-timeline', 'i' => 'fa fa-list'),
                    array('t' => 'Tender Info Report', 's' => 'tender-enquiry-summary-report', 'i' => 'fa fa-list'),
                    array('t' => 'Item Rate Report', 's' => 'item-rate-report', 'i' => 'fa fa-file-text-o'),
                    array('t' => 'Customer Pending Report', 's' => 'customer-pending-invoice-report', 'i' => 'fa fa-file-text'),
                    array('t' => 'Customer Statement Report', 's' => 'customer-statement-report', 'i' => 'fa fa-file-text'),
                    array('t' => 'Invoice Summary Report', 's' => 'invoice-report', 'i' => 'fa fa-file-text'),
                    array('t' => 'Tender Progress Report', 's' => 'tender-progress-report', 'i' => 'fa fa-line-chart'),
                )
            ),
            array(
                't' => 'Supplier Report',
                'i' => 'fa fa-industry',
                'c' => array(
                    array('t' => 'Vendor Pending Report', 's' => 'vendor-pending-invoice-report', 'i' => 'fa fa-file-text'),
                    array('t' => 'Vendor Statement Report', 's' => 'vendor-statement-report', 'i' => 'fa fa-file-text'),
                    array('t' => 'PO Summary Report', 's' => 'supplier-summary-report', 'i' => 'fa fa-file-text'),
                )
            ),
            array(
                't' => 'NBR Report',
                'i' => 'fa fa-envelope',
                'c' => array(
                    array('t' => 'VAT Return Form Summary', 's' => 'sales-purchase-report', 'i' => 'fa fa-list'),
                    array('t' => 'Sales NBR Report', 's' => 'sales-nbr-report', 'i' => 'fa fa-list'),
                    array('t' => 'Purchase NBR Report', 's' => 'purchase-nbr-report', 'i' => 'fa fa-list'),
                )
            ),
            array('t' => 'Profit & Loss', 's' => 'pl-report', 'i' => 'fa fa-line-chart'),
            array('t' => 'Account Trial Balance', 's' => 'account-trial-balance', 'i' => 'fa fa-balance-scale'),
        )
    ),

    array('h' => 'Accounts Book Info'),
    array(
        't' => 'Accounts Book Info',
        'i' => 'fa fa-file-text',
        'c' => array(
            array('t' => 'Inward Entry', 's' => 'inward-list', 'i' => 'fa fa-plus-circle'),
            array('t' => 'Outward Entry', 's' => 'outward-list', 'i' => 'fa fa-list-ul'),
            array('t' => 'Contra Entry', 's' => 'contra-entry', 'i' => 'fa fa-exchange'),
            array('t' => 'Bank Statement', 's' => 'cash-in-out-statement', 'i' => 'fa fa-university'),
            array('t' => 'Cash Statement', 's' => 'petty-cash-statement', 'i' => 'fa fa-file-text-o'),
            array(
                't' => 'Credit/Debit Note',
                'i' => 'fa fa-file-text-o',
                'c' => array(
                    array('t' => 'Add Credit/Debit Note', 's' => 'credit-debit-note-add', 'i' => 'fa fa-plus-circle'),
                    array('t' => 'Credit/Debit Note List', 's' => 'credit-debit-note-list', 'i' => 'fa fa-list-ul'),
                )
            ),
        )
    ),

    array('h' => 'Master'),
    array(
        't' => 'Master',
        'i' => 'fa fa-cubes',
        'c' => array(
            array(
                't' => 'General',
                'i' => 'fa fa-cog',
                'c' => array(
                    array('t' => 'Category List', 's' => 'category-list', 'i' => 'fa fa-folder-open'),
                    array('t' => 'Brand List', 's' => 'brand-list', 'i' => 'fa fa-industry'),
                    array('t' => 'Items List', 's' => 'items-list', 'i' => 'fa fa-cubes'),
                    array('t' => 'UOM List', 's' => 'uom-list', 'i' => 'fa fa-balance-scale'),
                    array('t' => 'VAT List', 's' => 'gst-list', 'i' => 'fa fa-percent'),
                    array('t' => 'VAT Filing Head List', 's' => 'vat-filing-head-list', 'i' => 'fa fa-file-text-o'),
                    array('t' => 'Currency List', 's' => 'currency-list', 'i' => 'fa fa-money'),
                    array('t' => 'Settings', 's' => 'settings', 'i' => 'fa fa-cogs'),
                    array('t' => 'Country List', 's' => 'country-list', 'i' => 'fa fa-globe'),
                    array('t' => 'Addt Charges Type List', 's' => 'addt-charges-type-list', 'i' => 'fa fa-tag'),
                )
            ),
            array(
                't' => 'Company Info',
                'i' => 'fa fa-building',
                'c' => array(
                    array('t' => 'Company Details', 's' => 'company-list', 'i' => 'fa fa-address-book'),
                    array('t' => 'Bank Details', 's' => 'company-bank-list', 'i' => 'fa fa-bank'),
                    array('t' => 'Cash Category', 's' => 'cash-category-list', 'i' => 'fa fa-tags'),
                    array('t' => 'User List', 's' => 'user-list', 'i' => 'fa fa-user'),
                )
            ),
            array(
                't' => 'Customer Info',
                'i' => 'fa fa-address-book',
                'c' => array(
                    array('t' => 'Customer List', 's' => 'customer-list', 'i' => 'fa fa-users'),
                    array('t' => 'Customer Contact Info', 's' => 'customer-contact-list', 'i' => 'fa fa-building'),
                    array('t' => 'Customer Opening Balance', 's' => 'customer-opening-balance-list', 'i' => 'fa fa-balance-scale'),
                )
            ),
            array(
                't' => 'Vendor Info',
                'i' => 'fa fa-address-card',
                'c' => array(
                    array('t' => 'Vendor List', 's' => 'vendor-list', 'i' => 'fa fa-users'),
                    array('t' => 'Vendor Contact Info', 's' => 'vendor-contact-list', 'i' => 'fa fa-building'),
                    array('t' => 'Vendor Opening Balance', 's' => 'vendor-opening-balance-list', 'i' => 'fa fa-balance-scale'),
                )
            ),
            array(
                't' => 'Accounts',
                'i' => 'fa fa-money',
                'c' => array(
                    array('t' => 'Account Head', 's' => 'account-head-list', 'i' => 'fa fa-university'),
                    array('t' => 'Sub-Account Head', 's' => 'sub-account-head-list', 'i' => 'fa fa-bank'),
                    array('t' => 'Opening Balance', 's' => 'opening-balance-list', 'i' => 'fa fa-balance-scale'),
                )
            ),
        )
    ),

    array('h' => 'Access Control'),
    array(
        't' => 'Access Control',
        'i' => 'fa fa-shield',
        'c' => array(
            array('t' => 'Menu Management', 's' => 'menu-management', 'i' => 'fa fa-list-alt'),
            array('t' => 'Role Management', 's' => 'role-management', 'i' => 'fa fa-user-secret'),
            array('t' => 'Role Permissions', 's' => 'role-permission', 'i' => 'fa fa-key'),
        )
    ),
);

$menuCount = (int) $mysqli->query("SELECT COUNT(*) c FROM menu_info")->fetch_assoc()['c'];
if ($menuCount === 0 || $force) {
    if ($force) {
        // Clear children first to satisfy no FK (we don't use FK constraints, but be tidy)
        $mysqli->query("DELETE FROM role_permission");
        $mysqli->query("DELETE FROM menu_info");
    }

    $sortCounter = 1;

    $insertNode = null;
    $insertNode = function ($node, $parentId) use (&$mysqli, &$sortCounter, &$insertNode) {
        if (isset($node['h'])) {
            // header node
            $stmt = $mysqli->prepare("INSERT INTO menu_info (parent_id, menu_title, menu_slug, menu_icon, is_header, sort_order, status) VALUES (?,?,NULL,NULL,1,?,'Active')");
            $stmt->bind_param('isi', $parentId, $node['h'], $sortCounter);
            $stmt->execute();
            $stmt->close();
            $sortCounter++;
            return;
        }

        $title = $node['t'];
        $slug = isset($node['s']) ? $node['s'] : null;
        $icon = isset($node['i']) ? $node['i'] : null;
        $hasChildren = isset($node['c']) && is_array($node['c']);

        // A node with children is a non-clickable group (slug null unless explicitly set)
        $stmt = $mysqli->prepare("INSERT INTO menu_info (parent_id, menu_title, menu_slug, menu_icon, is_header, sort_order, status) VALUES (?,?,?,?,0,?,'Active')");
        $stmt->bind_param('isssi', $parentId, $title, $slug, $icon, $sortCounter);
        $stmt->execute();
        $newId = $mysqli->insert_id;
        $stmt->close();
        $sortCounter++;

        if ($hasChildren) {
            foreach ($node['c'] as $child) {
                $insertNode($child, $newId);
            }
        }
    };

    foreach ($menu as $node) {
        $insertNode($node, 0);
    }

    echo "Menu tree seeded (" . ($sortCounter - 1) . " rows).\n";
} else {
    echo "Menu tree already present (skipped). Use ?force=1 to reseed.\n";
}

/* ---------------------------------------------------------------------------
 * 4. GRANT ADMIN FULL ACCESS; STAFF FULL ACCESS EXCEPT the Access Control section
 *    (Menu/Role management is Admin-only).
 * ------------------------------------------------------------------------ */
$adminOnlySlugs = array('menu-management', 'role-management', 'role-permission');

$roleRows = $mysqli->query("SELECT role_id, role_key FROM role_info WHERE status='Active'");
while ($roleRow = $roleRows->fetch_assoc()) {
    $roleId = (int) $roleRow['role_id'];
    $roleKey = $roleRow['role_key'];

    if ($roleKey === 'Admin') {
        $menuQuery = $mysqli->query("SELECT menu_id FROM menu_info WHERE status='Active'");
    } elseif ($roleKey === 'Staff') {
        $placeholders = implode(',', array_fill(0, count($adminOnlySlugs), '?'));
        $stmt = $mysqli->prepare("SELECT menu_id FROM menu_info WHERE status='Active' AND (menu_slug IS NULL OR menu_slug NOT IN ($placeholders))");
        $types = str_repeat('s', count($adminOnlySlugs));
        $stmt->bind_param($types, ...$adminOnlySlugs);
        $stmt->execute();
        $menuQuery = $stmt->get_result();
        $stmt->close();
    } else {
        continue; // other roles are configured via the UI
    }

    $insert = $mysqli->prepare("INSERT INTO role_permission (role_id, menu_id, can_view, can_add, can_edit, can_delete)
                                VALUES (?,?,1,1,1,1)
                                ON DUPLICATE KEY UPDATE can_view=1, can_add=1, can_edit=1, can_delete=1");
    while ($row = $menuQuery->fetch_assoc()) {
        $menuId = (int) $row['menu_id'];
        $insert->bind_param('ii', $roleId, $menuId);
        $insert->execute();
    }
    $insert->close();

    echo "Role '$roleKey' granted access.\n";
}

echo "\nDONE. You can now delete this file (optional).\n";
