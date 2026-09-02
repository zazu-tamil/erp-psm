# ERP-PSM System Architecture & Comprehensive Documentation

---

## 1. Executive Summary & Project Purpose

**ERP-PSM** is an enterprise-grade web application built to streamline end-to-end commercial operations for companies engaged in **Tender-based Project Execution, Multi-Vendor Procurement, Complex Landed Costing, and Financial Accounting**. 

Traditional ERPs often struggle to connect individual supplier procurement costs (like customs, shipping, freight, and local job-work) directly to specific customer tenders. ERP-PSM solves this by providing a unified data model linking **Customer Tenders & Quotations** directly with **Vendor Procurement, Landed Cost Elements, Invoicing, and Double-Entry Accounts**.

---

## 2. Technology-Wise Architectural Breakdown

```
+-----------------------------------------------------------------------+
|                            PRESENTATION LAYER                         |
|   Bootstrap 3 / AdminLTE | jQuery & AJAX | DataTables | CSS3 Print    |
+-----------------------------------------------------------------------+
                                    |
                                HTTP / REST
                                    v
+-----------------------------------------------------------------------+
|                            APPLICATION LAYER                          |
|             PHP 7.4 / 8.x + CodeIgniter 3 MVC Framework               |
|                                                                       |
|  +--------------------+  +--------------------+  +------------------+ |
|  | Controllers        |  | Models             |  | Dynamic Security | |
|  | - Tender.php       |  | - Role_model.php   |  | - RBAC System    | |
|  | - Vendor.php       |  | - Menu_model.php   |  | - Session Auth   | |
|  | - Accounts.php     |  | - Invoice_report.. |  | - JWT (Composer) | |
|  | - Payment.php      |  | - Pl_model.php     |  +------------------+ |
|  | - General.php      |  | - Generic DB ops   |                       |
|  +--------------------+  +--------------------+                       |
+-----------------------------------------------------------------------+
                                    |
                           Active Record / SQL
                                    v
+-----------------------------------------------------------------------+
|                               DATA LAYER                              |
|                   MySQL / MariaDB Relational Database                 |
|                                                                       |
|  [Master Entities]    [Tender Lifecycle]      [Accounts & Ledgers]    |
|  - company_info       - tender_enquiry        - cb_account_head_info  |
|  - customer_info      - tender_quotation      - cb_sub_account_head   |
|  - vendor_info        - customer_tender_po    - cb_cash_inward        |
|  - items_info         - tender_delivery_c..   - cb_cash_outward       |
|  - company_bank       - tender_po_invoice     - cb_petty_cash         |
+-----------------------------------------------------------------------+
                                    |
                           Physical File Storage
                                    v
+-----------------------------------------------------------------------+
|                          DOCUMENT STORAGE LAYER                       |
|  /uploads | /tender-documents | /vendor-quotations-documents          |
|  /vendor-pur-inward-documents | /letterpad | /bill_photo              |
+-----------------------------------------------------------------------+
```

### 2.1 Backend Architecture
- **Language & Runtime:** PHP 7.4 / 8.x.
- **Framework:** CodeIgniter 3 (MVC Architecture):
  - `application/controllers/`: Encapsulates business logic, data validation, report calculations, and API response serialization.
  - `application/models/`: Encapsulates reusable queries, role hierarchies, report datasets, and complex joins.
  - `application/views/`: Decoupled layout templates partitioned into `page/`, `inc/` (headers, sidebars, footers), and `inc/inc-js/` (page-specific JS logic).
  - `application/config/routes.php`: SEO-friendly, clean REST-like URL mappings.
- **Security & Authorization:**
  - Role-Based Access Control (RBAC) via `menu_manager` and role-permission matrices.
  - Session verification (`zazu_logged_in`) guarding all internal controller methods.
  - JWT Support (`firebase/php-jwt`) integrated via Composer for tokenized service authentication.
  - SQL injection prevention using CodeIgniter query escaping (`$this->db->escape_str()`) and active record parameter binding.

### 2.2 Database Architecture
- **Database Engine:** MySQL / MariaDB (InnoDB engine for ACID compliance and transactional consistency).
- **Core Design Patterns:**
  - **Relational Integrity:** Foreign key structures linking primary transaction headers to line-item child tables (e.g., `tender_quotation` $\rightarrow$ `tender_quotation_items`).
  - **Soft Deletion & Status Flags:** Records maintain `status = 'Active'` or `'Deleted'`, preventing accidental financial data loss.
  - **Double-Entry Financial Ledger:** Separation of general ledger heads (`cb_account_head_info`) and sub-heads (`cb_sub_account_head_info`) with auto-reconciling debit/credit entries.

### 2.3 Frontend & UI Layer
- **Framework:** AdminLTE Dashboard template built on Bootstrap 3.
- **Dynamic Asynchronous Layer:** jQuery and AJAX for inline form submission, cascading dropdowns, and instant row additions without page reload.
- **Data Display:** DataTables for fast server-side and client-side sorting, instant filtering, and multi-format export (Excel/CSV/PDF).
- **Print & PDF System:** Native CSS print stylesheets (`@media print`, `@page { size: A4 portrait; }`) allowing direct-to-print quotation sheets, Delivery Challans, and commercial tax invoices.

### 2.4 Server & Directory Infrastructure
- **Web Server:** Apache 2.4+ with `mod_rewrite` enabled via `.htaccess`.
- **Upload Repositories:** Dedicated, isolated document folders:
  - `tender-documents/`: Client specification PDFs, contracts, signed customer POs.
  - `vendor-quotations-documents/`: Supplier RFQ responses and price quotes.
  - `vendor-pur-inward-documents/`: Delivery receipts and packing slips.
  - `bill_photo/` & `letterpad/`: Header logos, tax seal graphics, and expense vouchers.

---

## 3. Comprehensive Module & Feature Breakdown

### Module 1: Dynamic RBAC & Menu Manager
* **Functionality:** Eliminates hardcoded permissions by allowing administrators to create roles, define custom menus, and assign granular view/edit/delete rights.
* **Controllers:** `Menu_manager.php`
* **Models:** `Role_model.php`, `Menu_model.php`
* **Key Routes:** `/menu-management`, `/role-management`, `/role-permission`

### Module 2: Master Configuration System
* **Functionality:** Single source of truth for foundational business records.
* **Entities:**
  - **Company Profile & Multi-Bank:** Bank details, IBAN, Swift code, VAT numbers, CR numbers, invoice footer terms.
  - **Customer & Vendor Directories:** Contact persons, tax registration, GPS locations, and opening ledger balances.
  - **Item Catalog:** Items, Categories, Brands, Units of Measure (UOM), HSN codes, default VAT rates.
* **Controllers:** `Master.php`

### Module 3: Customer Tender-to-Cash Workflow
* **Lifecycle:**
  1. **Tender Enquiry:** Record customer RFQs, upload tender specs, set submission deadlines, and register item line lists.
  2. **Tender Quotation:** Price estimation with multi-currency conversion, profit margins, VAT, and custom validity terms. Generates printable Quotation PDF.
  3. **Customer PO:** Award recording, customer PO number tracking, and verification of confirmed item quantities.
  4. **Delivery Challan (DC):** Material dispatch note capturing vehicle number, driver name, and gate-pass details.
  5. **Commercial Tax Invoice:** Generates final commercial VAT invoice referencing PO & DC line items.
  6. **Customer Receipt:** Receipt voucher module allocating incoming funds (cheque/bank/cash) against pending invoices.
* **Controllers:** `Tender.php`, `Payment.php`

### Module 4: Supplier Procurement & Landed Costing
* **Lifecycle:**
  1. **Supplier Rate Enquiry (RFQ):** Send rate requests to multiple vendors for items listed in tender enquiries.
  2. **Supplier Quotation:** Record vendor bid rates, currency conversions, and delivery lead times.
  3. **Supplier Purchase Order (PO):** Official purchase order issuance with payment and delivery terms.
  4. **Goods Receipt Note (Inward/GRN):** Warehouse physical inspection against PO lines.
  5. **Direct Purchase Bills:** Match actual supplier invoice totals and VAT.
  6. **Landed Cost Components:**
     - **Local Purchase Bills:** Ad-hoc local materials, consumables, and fabrication charges.
     - **Delivery Partner Bills:** International shipping, air/sea freight, and domestic courier charges.
     - **Customs Duty Bills:** Clearing agent fees, customs duty, and import stamps.
* **Controllers:** `Vendor.php`

### Module 5: Financial Accounting, Treasury & Banking
* **Capabilities:**
  - **Cash & Bank Inward:** Record capital injections, interest income, and non-sales receipts.
  - **Cash & Bank Outward:** Operational expenditures, rent, staff compensation, and administrative expenses.
  - **Contra Entries:** Bank-to-Bank, Cash-to-Bank, and Bank-to-Cash internal transfers.
  - **Petty Cash Management:** Dedicated petty cash float tracking with voucher replenishment.
  - **Credit & Debit Notes:** Adjustments for sales returns, supplier disputes, or rate discrepancies.
  - **Bank & Cash Ledgers:** Running balance calculations across all active company bank accounts.
* **Controllers:** `Accounts.php`, `PettyCash.php`, `CreditDebitNote.php`

### Module 6: Audit, Compliance & Business Intelligence Reports
* **Reports Suite:**
  - **Tender Summary & Profitability Report:** Compares Invoiced Sales against total landed costs (Supplier POs + Local Bills + Delivery Partner Bills + Customs), displaying net project margins.
  - **Customer & Supplier Statements of Account (SOA):** Date-filtered ledger view of invoices, receipts, payments, and running balance.
  - **Tax & NBR Reports:** VAT collection and payment reports aligned with National Board of Revenue / VAT filing standards.
  - **Trial Balance & Profit & Loss:** Double-entry ledger reconciliation and organizational financial performance.
* **Controllers:** `Reports.php`, `Audit.php`

---

## 4. Developer Guide: How to Add New Features to ERP-PSM

Follow this standardized 8-step workflow whenever adding a new feature or sub-module to ensure architectural consistency and prevent regressions.

```
+------------------------------------------------------------------------------+
|                   STEP-BY-STEP FEATURE IMPLEMENTATION PIPELINE                |
+------------------------------------------------------------------------------+
| [Step 1] Database Table Design (Schema & Migration)                          |
|    |                                                                         |
|    v                                                                         |
| [Step 2] Register URL Routes in application/config/routes.php                |
|    |                                                                         |
|    v                                                                         |
| [Step 3] Implement Controller Logic in application/controllers/              |
|    |                                                                         |
|    v                                                                         |
| [Step 4] Add Model Methods in application/models/ (or query via DB builder)  |
|    |                                                                         |
|    v                                                                         |
| [Step 5] Create Views in application/views/page/<module>/                    |
|    |                                                                         |
|    v                                                                         |
| [Step 6] Implement Client-Side JS in application/views/inc/inc-js/<module>/  |
|    |                                                                         |
|    v                                                                         |
| [Step 7] Add Menu Link & Role Permissions in Menu Manager                    |
|    |                                                                         |
|    v                                                                         |
| [Step 8] Test CRUD, Validation, DataTables & Print Layout                    |
+------------------------------------------------------------------------------+
```

### Step 1: Database Table Design
1. Follow existing table naming conventions: `<entity>_info` or `<entity>_entry`.
2. Always include mandatory metadata and audit columns:
```sql
CREATE TABLE `sample_feature_info` (
  `sample_id` INT(11) NOT NULL AUTO_INCREMENT,
  `company_id` INT(11) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `amount` DECIMAL(15,2) DEFAULT '0.00',
  `status` ENUM('Active','Inactive','Deleted') DEFAULT 'Active',
  `created_by` INT(11) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`sample_id`),
  KEY `idx_company_id` (`company_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Step 2: Register Routes (`application/config/routes.php`)
Define clean, descriptive routes instead of exposing raw controller/method names:
```php
// Sample Feature Routes
$route['sample-feature-list']              = 'master/sample_feature_list';
$route['sample-feature-list/(:num)']       = 'master/sample_feature_list/$1';
$route['sample-feature-save']              = 'master/sample_feature_save';
$route['sample-feature-delete/(:num)']     = 'master/sample_feature_delete/$1';
```

### Step 3: Implement Controller Logic (`application/controllers/<Module>.php`)
1. Enforce login verification.
2. Load header, sidebar menu, target view, and specific footer script.
```php
public function sample_feature_list($id = null)
{
    if (!$this->session->userdata('zazu_logged_in')) {
        redirect('login');
    }

    $data['title'] = "Sample Feature Management";
    $data['edit_id'] = $id;

    // Load data from model or DB builder
    $data['records'] = $this->db->get_where('sample_feature_info', ['status' => 'Active'])->result_array();

    // Standard view assembly
    $this->load->view('inc/header', $data);
    $this->load->view('inc/admin-menu');
    $this->load->view('page/master/sample-feature-list', $data);
    $this->load->view('inc/footer');
    $this->load->view('inc/inc-js/master/sample-feature-list.inc');
}
```

### Step 4: Add Model Methods (Optional / When Needed)
For complex calculations, aggregations, or multi-table joins, create or update a model in `application/models/`:
```php
public function get_sample_report($from_date, $to_date)
{
    $this->db->select('s.*, c.company_name');
    $this->db->from('sample_feature_info s');
    $this->db->join('company_info c', 'c.company_id = s.company_id', 'left');
    $this->db->where('s.created_at >=', $from_date);
    $this->db->where('s.created_at <=', $to_date);
    $this->db->where('s.status', 'Active');
    return $this->db->get()->result_array();
}
```

### Step 5: Create View Template (`application/views/page/<module>/`)
Build the UI using Bootstrap 3 / AdminLTE layout standards with unique form and table IDs:
```html
<div class="content-wrapper">
    <section class="content-header">
        <h1>Sample Feature <small>Manage records</small></h1>
    </section>
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <table id="sampleFeatureTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Loop or DataTable AJAX feed -->
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
```

### Step 6: Client-Side Script & AJAX (`application/views/inc/inc-js/<module>/`)
Place client-side initialization, form validation, and AJAX calls in the dedicated `.inc` file:
```html
<script>
$(document).ready(function() {
    $('#sampleFeatureTable').DataTable({
        responsive: true,
        pageLength: 25
    });

    // Handle AJAX Save
    $('#btnSaveSample').on('click', function(e) {
        e.preventDefault();
        var formData = $('#sampleForm').serialize();
        $.ajax({
            url: "<?= base_url('sample-feature-save'); ?>",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function(res) {
                if(res.status == 'success') {
                    location.reload();
                } else {
                    alert(res.message);
                }
            }
        });
    });
});
</script>
```

### Step 7: Dynamic Menu & Permission Integration
1. Navigate to **Menu Management** (`/menu-management`) in the running application.
2. Add the new menu entry:
   - **Menu Title:** e.g., "Sample Feature"
   - **Route URL:** `sample-feature-list`
   - **Parent Menu:** Choose appropriate group (Master, Tender, Vendor, or Accounts).
   - **Icon Class:** e.g., `fa fa-cube`.
3. Open **Role Permissions** (`/role-permission`) and enable view/create/edit permissions for designated user roles.

### Step 8: Standard Quality & Regression Checklist
Before releasing the new feature:
- [ ] Ensure all input strings are sanitized with CodeIgniter's form validation or DB escape functions.
- [ ] Confirm soft-delete works without breaking historical foreign-key relationships.
- [ ] Test table sorting, pagination, and multi-currency formatting on DataTables.
- [ ] If printing is required, verify that `@media print` rules render cleanly on A4 paper.
