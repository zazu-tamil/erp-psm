-- ============================================================
-- Role-based Menu & Permission system (erp-psm)
-- Creates + seeds: role_info, menu_info, role_permission
-- WARNING: drops and recreates these 3 tables.
-- ============================================================
SET NAMES utf8mb4;

DROP TABLE IF EXISTS `role_permission`;
DROP TABLE IF EXISTS `menu_info`;
DROP TABLE IF EXISTS `role_info`;

CREATE TABLE `role_info` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `menu_info` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `role_permission` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `role_info` (`role_id`,`role_key`,`role_name`,`is_default`,`sort_order`,`status`) VALUES
  (1, 'Admin', 'Admin', 1, 1, 'Active'),
  (2, 'Staff', 'Staff', 0, 2, 'Active'),
  (3, 'Supervisor', 'Supervisor', 0, 3, 'Active'),
  (4, 'Agent', 'Agent', 0, 4, 'Active'),
  (5, 'Labour', 'Labour', 0, 5, 'Active'),
  (6, 'Vendor', 'Vendor', 0, 6, 'Active'),
  (7, 'Customer', 'Customer', 0, 7, 'Active');

INSERT INTO `menu_info` (`menu_id`,`parent_id`,`menu_title`,`menu_slug`,`menu_icon`,`is_header`,`sort_order`,`status`) VALUES
  (1, 0, 'Dashboard & Help', NULL, NULL, 1, 1, 'Active'),
  (2, 0, 'Dashboard', 'dash', 'fa fa-dashboard', 0, 2, 'Active'),
  (3, 0, 'TENDER', NULL, NULL, 1, 3, 'Active'),
  (4, 0, 'Tender Enquiry', NULL, 'fa fa-file-text-o', 0, 4, 'Active'),
  (5, 4, 'Add Tender Enquiry', 'add-tender-enquiry', 'fa fa-plus-circle', 0, 5, 'Active'),
  (6, 4, 'Tender Enquiry List', 'tender-enquiry-list', 'fa fa-list', 0, 6, 'Active'),
  (7, 0, 'Tender', NULL, 'fa fa-folder-open', 0, 7, 'Active'),
  (8, 7, 'Tender Quotation', NULL, 'fa fa-file-text', 0, 8, 'Active'),
  (9, 8, 'Add Tender Quotation', 'tender-quotation-add', 'fa fa-plus-circle', 0, 9, 'Active'),
  (10, 8, 'Tender Quotation List', 'tender-quotation-list', 'fa fa-list-ul', 0, 10, 'Active'),
  (11, 7, 'Tender PO', NULL, 'fa fa-briefcase', 0, 11, 'Active'),
  (12, 11, 'Add Tender PO', 'customer-tender-po-add', 'fa fa-plus-square', 0, 12, 'Active'),
  (13, 11, 'Tender PO List', 'customer-tender-po-list', 'fa fa-list', 0, 13, 'Active'),
  (14, 7, 'Tender DC', NULL, 'fa fa-briefcase', 0, 14, 'Active'),
  (15, 14, 'Add Tender DC', 'tender-dc-add', 'fa fa-plus-square', 0, 15, 'Active'),
  (16, 14, 'Tender DC List', 'tender-dc-list', 'fa fa-list', 0, 16, 'Active'),
  (17, 7, 'Tender Invoice', NULL, 'fa fa-briefcase', 0, 17, 'Active'),
  (18, 17, 'Add Tender Invoice', 'tender-invoice-add', 'fa fa-plus-square', 0, 18, 'Active'),
  (19, 17, 'Tender Invoice List', 'tender-invoice-list', 'fa fa-list', 0, 19, 'Active'),
  (20, 7, 'Tender Receipt List', 'customer-invoice-receipt', 'fa fa-list', 0, 20, 'Active'),
  (21, 0, 'In Stock Items', NULL, NULL, 1, 21, 'Active'),
  (22, 0, 'In Stock Items', NULL, 'fa fa-file-text', 0, 22, 'Active'),
  (23, 22, 'In Stock Item List', 'in-stock-item-list', 'fa fa-plus-circle', 0, 23, 'Active'),
  (24, 22, 'In Stock Item Report', 'in-stock-item-report', 'fa fa-list', 0, 24, 'Active'),
  (25, 0, 'SUPPLIER', NULL, NULL, 1, 25, 'Active'),
  (26, 0, 'Supplier', NULL, 'fa fa-industry', 0, 26, 'Active'),
  (27, 26, 'Supplier Enquiry', NULL, 'fa fa-envelope', 0, 27, 'Active'),
  (28, 27, 'Add Supplier Enquiry', 'vendor-rate-enquiry', 'fa fa-plus-square', 0, 28, 'Active'),
  (29, 27, 'Supplier Enquiry List', 'vendor-rate-enquiry-list', 'fa fa-list', 0, 29, 'Active'),
  (30, 26, 'Supplier Quotation', NULL, 'fa fa-file-text-o', 0, 30, 'Active'),
  (31, 30, 'Add Supplier Quotation', 'vendor-quotation-add', 'fa fa-plus-square', 0, 31, 'Active'),
  (32, 30, 'Supplier Quotation List', 'vendor-quotation-list', 'fa fa-list', 0, 32, 'Active'),
  (33, 26, 'Supplier PO', NULL, 'fa fa-files-o', 0, 33, 'Active'),
  (34, 33, 'Add Supplier PO', 'vendor-po-add', 'fa fa-plus-square', 0, 34, 'Active'),
  (35, 33, 'Supplier PO List', 'vendor-po-list', 'fa fa-list', 0, 35, 'Active'),
  (36, 26, 'Supplier Inward', NULL, 'fa fa-files-o', 0, 36, 'Active'),
  (37, 36, 'Add Supplier Inward', 'vendor-pur-inward-add', 'fa fa-plus-square', 0, 37, 'Active'),
  (38, 36, 'Supplier Inward List', 'vendor-pur-inward-list', 'fa fa-list', 0, 38, 'Active'),
  (39, 26, 'Supplier Invoice/Bill', NULL, 'fa fa-files-o', 0, 39, 'Active'),
  (40, 39, 'Add Supplier Bill Entry', 'vendor-purchase-bill-add', 'fa fa-plus-square', 0, 40, 'Active'),
  (41, 39, 'Supplier Bill List', 'vendor-purchase-bill-list', 'fa fa-list', 0, 41, 'Active'),
  (42, 39, 'Local Supplier Bill List', 'local-purchase-bill-list', 'fa fa-list', 0, 42, 'Active'),
  (43, 39, 'Delivery Partner Bill List', 'delivery-partner-bill-list', 'fa fa-list', 0, 43, 'Active'),
  (44, 39, 'Customs Bill List', 'customs-bill-list', 'fa fa-list', 0, 44, 'Active'),
  (45, 26, 'Supplier Advance Payment', 'vendor-adv-payment', 'fa fa-list', 0, 45, 'Active'),
  (46, 26, 'Supplier Payment List', 'vendor-payment-list', 'fa fa-list', 0, 46, 'Active'),
  (47, 0, 'REPORTS', NULL, NULL, 1, 47, 'Active'),
  (48, 0, 'Reports', NULL, 'fa fa-area-chart', 0, 48, 'Active'),
  (49, 48, 'Tender Info Report', NULL, 'fa fa-envelope', 0, 49, 'Active'),
  (50, 49, 'Tender Timeline Report', 'tender-enquiry-timeline', 'fa fa-list', 0, 50, 'Active'),
  (51, 49, 'Tender Info Report', 'tender-enquiry-summary-report', 'fa fa-list', 0, 51, 'Active'),
  (52, 49, 'Item Rate Report', 'item-rate-report', 'fa fa-file-text-o', 0, 52, 'Active'),
  (53, 49, 'Customer Pending Report', 'customer-pending-invoice-report', 'fa fa-file-text', 0, 53, 'Active'),
  (54, 49, 'Vendor Pending Report', 'vendor-pending-invoice-report', 'fa fa-file-text', 0, 54, 'Active'),
  (55, 49, 'Customer Statement Report', 'customer-statement-report', 'fa fa-file-text', 0, 55, 'Active'),
  (56, 49, 'Vendor Statement Report', 'vendor-statement-report', 'fa fa-file-text', 0, 56, 'Active'),
  (57, 49, 'PO Summary Report', 'supplier-summary-report', 'fa fa-file-text', 0, 57, 'Active'),
  (58, 49, 'Invoice Summary Report', 'invoice-report', 'fa fa-file-text', 0, 58, 'Active'),
  (59, 49, 'Tender Progress Report', 'tender-progress-report', 'fa fa-line-chart', 0, 59, 'Active'),
  (60, 48, 'NBR Report', NULL, 'fa fa-envelope', 0, 60, 'Active'),
  (61, 60, 'VAT Return Form Summary', 'sales-purchase-report', 'fa fa-list', 0, 61, 'Active'),
  (62, 60, 'Sales NBR Report', 'sales-nbr-report', 'fa fa-list', 0, 62, 'Active'),
  (63, 60, 'Purchase NBR Report', 'purchase-nbr-report', 'fa fa-list', 0, 63, 'Active'),
  (64, 48, 'Profit & Loss', 'pl-report', 'fa fa-line-chart', 0, 64, 'Active'),
  (65, 48, 'Account Trial Balance', 'account-trial-balance', 'fa fa-balance-scale', 0, 65, 'Active'),
  (66, 0, 'Accounts Book Info', NULL, NULL, 1, 66, 'Active'),
  (67, 0, 'Accounts Book Info', NULL, 'fa fa-file-text', 0, 67, 'Active'),
  (68, 67, 'Inward Entry', 'inward-list', 'fa fa-plus-circle', 0, 68, 'Active'),
  (69, 67, 'Outward Entry', 'outward-list', 'fa fa-list-ul', 0, 69, 'Active'),
  (70, 67, 'Contra Entry', 'contra-entry', 'fa fa-exchange', 0, 70, 'Active'),
  (71, 67, 'Bank Statement', 'cash-in-out-statement', 'fa fa-university', 0, 71, 'Active'),
  (72, 67, 'Cash Statement', 'petty-cash-statement', 'fa fa-file-text-o', 0, 72, 'Active'),
  (73, 67, 'Credit/Debit Note', NULL, 'fa fa-file-text-o', 0, 73, 'Active'),
  (74, 73, 'Add Credit/Debit Note', 'credit-debit-note-add', 'fa fa-plus-circle', 0, 74, 'Active'),
  (75, 73, 'Credit/Debit Note List', 'credit-debit-note-list', 'fa fa-list-ul', 0, 75, 'Active'),
  (76, 0, 'Master', NULL, NULL, 1, 76, 'Active'),
  (77, 0, 'Master', NULL, 'fa fa-cubes', 0, 77, 'Active'),
  (78, 77, 'General', NULL, 'fa fa-cog', 0, 78, 'Active'),
  (79, 78, 'Category List', 'category-list', 'fa fa-folder-open', 0, 79, 'Active'),
  (80, 78, 'Brand List', 'brand-list', 'fa fa-industry', 0, 80, 'Active'),
  (81, 78, 'Items List', 'items-list', 'fa fa-cubes', 0, 81, 'Active'),
  (82, 78, 'UOM List', 'uom-list', 'fa fa-balance-scale', 0, 82, 'Active'),
  (83, 78, 'VAT List', 'gst-list', 'fa fa-percent', 0, 83, 'Active'),
  (84, 78, 'VAT Filing Head List', 'vat-filing-head-list', 'fa fa-file-text-o', 0, 84, 'Active'),
  (85, 78, 'Currency List', 'currency-list', 'fa fa-money', 0, 85, 'Active'),
  (86, 78, 'Settings', 'settings', 'fa fa-cogs', 0, 86, 'Active'),
  (87, 78, 'Country List', 'country-list', 'fa fa-globe', 0, 87, 'Active'),
  (88, 78, 'Addt Charges Type List', 'addt-charges-type-list', 'fa fa-tag', 0, 88, 'Active'),
  (89, 77, 'Company Info', NULL, 'fa fa-building', 0, 89, 'Active'),
  (90, 89, 'Company Details', 'company-list', 'fa fa-address-book', 0, 90, 'Active'),
  (91, 89, 'Bank Details', 'company-bank-list', 'fa fa-bank', 0, 91, 'Active'),
  (92, 89, 'Cash Category', 'cash-category-list', 'fa fa-tags', 0, 92, 'Active'),
  (93, 89, 'User List', 'user-list', 'fa fa-user', 0, 93, 'Active'),
  (94, 77, 'Customer Info', NULL, 'fa fa-address-book', 0, 94, 'Active'),
  (95, 94, 'Customer List', 'customer-list', 'fa fa-users', 0, 95, 'Active'),
  (96, 94, 'Customer Contact Info', 'customer-contact-list', 'fa fa-building', 0, 96, 'Active'),
  (97, 94, 'Customer Opening Balance', 'customer-opening-balance-list', 'fa fa-balance-scale', 0, 97, 'Active'),
  (98, 77, 'Vendor Info', NULL, 'fa fa-address-card', 0, 98, 'Active'),
  (99, 98, 'Vendor List', 'vendor-list', 'fa fa-users', 0, 99, 'Active'),
  (100, 98, 'Vendor Contact Info', 'vendor-contact-list', 'fa fa-building', 0, 100, 'Active'),
  (101, 98, 'Vendor Opening Balance', 'vendor-opening-balance-list', 'fa fa-balance-scale', 0, 101, 'Active'),
  (102, 77, 'Accounts', NULL, 'fa fa-money', 0, 102, 'Active'),
  (103, 102, 'Account Head', 'account-head-list', 'fa fa-university', 0, 103, 'Active'),
  (104, 102, 'Sub-Account Head', 'sub-account-head-list', 'fa fa-bank', 0, 104, 'Active'),
  (105, 102, 'Opening Balance', 'opening-balance-list', 'fa fa-balance-scale', 0, 105, 'Active'),
  (106, 0, 'Access Control', NULL, NULL, 1, 106, 'Active'),
  (107, 0, 'Access Control', NULL, 'fa fa-shield', 0, 107, 'Active'),
  (108, 107, 'Menu Management', 'menu-management', 'fa fa-list-alt', 0, 108, 'Active'),
  (109, 107, 'Role Management', 'role-management', 'fa fa-user-secret', 0, 109, 'Active'),
  (110, 107, 'Role Permissions', 'role-permission', 'fa fa-key', 0, 110, 'Active');

-- Admin: full access to every menu
INSERT INTO `role_permission` (`role_id`,`menu_id`,`can_view`,`can_add`,`can_edit`,`can_delete`)
SELECT r.role_id, m.menu_id, 1, 1, 1, 1
FROM role_info r JOIN menu_info m
WHERE r.role_key = 'Admin';

-- Staff: full access except the Access Control section (Admin-only)
INSERT INTO `role_permission` (`role_id`,`menu_id`,`can_view`,`can_add`,`can_edit`,`can_delete`)
SELECT r.role_id, m.menu_id, 1, 1, 1, 1
FROM role_info r JOIN menu_info m
WHERE r.role_key = 'Staff'
  AND (m.menu_slug IS NULL OR m.menu_slug NOT IN ('menu-management','role-management','role-permission'));
