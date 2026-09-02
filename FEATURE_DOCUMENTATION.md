# ERP-PSM Comprehensive Feature Specification Document

---

## Document Overview
This document provides an exhaustive, granular inventory and functional specification of **all features, modules, sub-modules, workflows, calculations, data validations, and reporting tools** within the **ERP-PSM System**.

---

## Feature Matrix Summary

| Module Code | Module Name | Key Functional Scope | Primary User Roles |
| :--- | :--- | :--- | :--- |
| **MOD-01** | **Authentication & Dynamic RBAC** | User login, dynamic menu builder, role management, granular access permissions | Super Admin, Admin |
| **MOD-02** | **Master Data Management** | Company profile, multi-currency banks, customer/vendor directories, item catalog | Admin, Manager, Data Entry |
| **MOD-03** | **Customer Tender & Sales Cycle** | RFQ enquiries, cost estimation quotes, customer PO, delivery challans, tax invoices | Sales Team, Project Engineers |
| **MOD-04** | **Supplier Procurement & Landed Cost** | Vendor RFQs, quotes, purchase orders, goods inward (GRN), local & customs bills | Procurement Officers, Buyers |
| **MOD-05** | **Accounts, Cash & Bank Treasury** | Inward/outward books, contra transfers, petty cash fund, credit/debit notes | Accountants, Finance Managers |
| **MOD-06** | **Payment Processing & Settlements** | Customer invoice receipts, supplier payouts, multi-invoice allocation | Cashiers, Finance Officers |
| **MOD-07** | **Audit, Ledger & Financial Statements** | Account groups, journal vouchers, trial balance, balance sheet, P&L report | Auditors, Financial Controllers |
| **MOD-08** | **Business Intelligence & Tax Reports** | Tender profitability matrix, customer/vendor SOA, NBR/VAT compliance reports | Management, Directors, Tax Auditors |

---

## Detailed Feature Specifications

```
==================================================================================
MODULE 01: AUTHENTICATION, SECURITY & ROLE-BASED ACCESS CONTROL (RBAC)
==================================================================================
```

### Feature 1.1: Secure User Authentication & Session Control
- **Description:** Entry point into ERP-PSM protecting sensitive commercial and accounting data.
- **Key Capabilities:**
  - Standard username/email and encrypted password authentication.
  - Session verification (`zazu_logged_in`) guarding all internal controller methods.
  - Automatic redirect to login upon session expiration or unauthorized access attempt.
  - User password change module (`/change-password`) with current password verification.
- **Input Fields:** Username/Email, Password.
- **Validation Rules:** Required fields, minimum password strength, brute-force protection.

### Feature 1.2: Dynamic Menu Builder & Reordering
- **Route:** `/menu-management`
- **Description:** Allows administrators to construct and rearrange navigation hierarchies on the fly without touching PHP code.
- **Key Capabilities:**
  - Create parent menu groups (e.g., Master, Tender, Vendor, Accounts, Reports).
  - Create child submenu items linked to specific system route aliases.
  - Assign FontAwesome icon classes (e.g., `fa fa-briefcase`, `fa fa-truck`).
  - Drag-and-drop or sequence number reordering (`/menu-management/reorder`).
  - Soft-delete or deactivate obsolete menu items.

### Feature 1.3: Role Management & Granular Permissions Matrix
- **Routes:** `/role-management`, `/role-permission`
- **Description:** Complete security layer defining what each organizational role can see and perform.
- **Key Capabilities:**
  - Define custom roles (e.g., Super Admin, Procurement Officer, Project Engineer, Accountant, Auditor).
  - Matrix interface showing all menus vs. roles with four access tiers:
    1. **View Access:** View listing pages and reports.
    2. **Add Access:** Create new enquiries, quotations, POs, or vouchers.
    3. **Edit Access:** Modify draft transactions or update master records.
    4. **Delete Access:** Permission to soft-delete records (restricted to management).
  - Instant permission synchronization across active user sessions.

---

```
==================================================================================
MODULE 02: MASTER DATA MANAGEMENT (CONFIGURATION ENGINE)
==================================================================================
```

### Feature 2.1: Company Profile & Organizational Header
- **Route:** `/company-list`
- **Key Capabilities:**
  - Configure legal entity name, Commercial Registration (CR) Number, and VAT/Tax ID.
  - Upload high-resolution header logos and seal stamps used in official PDF printouts.
  - Configure default terms & conditions for Quotations, Invoices, Delivery Challans, and POs.
  - Multi-branch or multi-company configuration support.

### Feature 2.2: Company Bank Master
- **Route:** `/company-bank-list`
- **Key Capabilities:**
  - Register corporate bank accounts with Bank Name, Account Number, IBAN, Swift Code, Branch, and Currency.
  - Link each bank account to an automated general ledger account.
  - Select default remittance bank accounts for customer invoice payment instructions.

### Feature 2.3: Customer Directory & Contact Management
- **Routes:** `/customer-list`, `/customer-contact-list`
- **Key Capabilities:**
  - Register clients with Company Name, CR Number, Tax/VAT ID, and Billing/Shipping addresses.
  - Embed GPS Map coordinates for delivery location verification.
  - Multi-contact support: Store multiple contact persons (Name, Designation, Phone, Email) per customer.
  - Setup customer initial ledger opening balance (`/customer-opening-balance-list`).

### Feature 2.4: Supplier / Vendor Directory
- **Routes:** `/vendor-list`, `/vendor-contact-list`
- **Key Capabilities:**
  - Maintain international and domestic supplier profiles with Country, VAT, Payment terms, and Bank details.
  - Multi-contact person directory per vendor.
  - Setup vendor opening balances with debit/credit indicators (`/vendor-opening-balance-list`).

### Feature 2.5: Item Master, Categories & Brands
- **Routes:** `/items-list`, `/category-list`, `/brand-list`, `/uom-list`
- **Key Capabilities:**
  - Master item catalog with SKU, Item Description, Category, Brand, and Unit of Measure (UOM).
  - Default HSN / SAC code assignment for tax tracking.
  - Default VAT % rate assignment per item.
  - Attach item specification sheets or documentation photos (`Item_doc/`).

### Feature 2.6: System Parameter Masters
- **Routes:** `/currency-list`, `/gst-list`, `/country-list`, `/vat-filing-head-list`, `/addt-charges-type-list`
- **Key Capabilities:**
  - **Currencies:** Register foreign currencies with exchange rates relative to base currency.
  - **VAT/GST Heads:** Define standard tax brackets (e.g., 0%, 5%, 10%, 15%).
  - **Additional Charges:** Setup charge categories (Freight, Clearance, Handling, Inspection).

---

```
==================================================================================
MODULE 03: CUSTOMER TENDER & SALES EXECUTION LIFECYCLE
==================================================================================
```

### Feature 3.1: Tender Enquiry Registration (RFQ)
- **Routes:** `/add-tender-enquiry`, `/tender-enquiry-list`
- **Workflow Position:** Step 1 of Customer Lifecycle
- **Key Capabilities:**
  - Log incoming customer Request for Quotation (RFQ).
  - Associate Tender with Company entity and Customer.
  - Record Tender Number, Tender Name, Submission Deadline Date, and Closing Time.
  - Upload customer technical specification PDFs, drawing attachments, and tender terms.
  - Register required line items (description, required quantity, target UOM).
  - Generates unique auto-incremented Tender Enquiry Tracking ID.

### Feature 3.2: Tender Quotation & Cost Estimation
- **Routes:** `/tender-quotation-add`, `/tender-quotation-list`, `/tender-quotation-print/(:num)`
- **Workflow Position:** Step 2 of Customer Lifecycle
- **Key Capabilities:**
  - Direct import of line items from Tender Enquiry.
  - Multi-currency selection with auto exchange rate conversion.
  - Line-by-line pricing: Input Unit Cost, Profit Markup %, Discount %, and VAT %.
  - Automatic calculation of:
    $$\text{Line Net Rate} = \text{Unit Cost} \times (1 + \text{Profit Margin \%})$$
    $$\text{Line Subtotal} = \text{Quantity} \times \text{Line Net Rate}$$
    $$\text{VAT Amount} = \text{Line Subtotal} \times \frac{\text{VAT \%}}{100}$$
    $$\text{Gross Total} = \sum \text{Line Subtotal} + \sum \text{VAT Amount} + \text{Freight / Handling Charges}$$
  - Define customized validity terms (e.g., 30 Days, 60 Days, payment terms).
  - **Print Engine:** Generates official branded commercial PDF Quotation ready for client submission.

### Feature 3.3: Customer Purchase Order (Award Tracking)
- **Routes:** `/customer-tender-po-add`, `/customer-tender-po-list`
- **Workflow Position:** Step 3 of Customer Lifecycle
- **Key Capabilities:**
  - Records successful tender award from client.
  - Select Customer and target Quotation Number (auto-fetches approved items and negotiated rates).
  - Capture Customer PO Reference Number and Official Award Date.
  - Upload signed customer PO copy and contract agreement.
  - Update Tender Status to `Awarded` / `In Progress`.

### Feature 3.4: Delivery Challan (DC) & Dispatch Note
- **Routes:** `/tender-dc-add`, `/tender-dc-list`, `/tender-dc-print/(:num)`
- **Workflow Position:** Step 4 of Customer Lifecycle
- **Key Capabilities:**
  - Create physical dispatch notes for partial or complete shipments against Customer PO.
  - Capture Dispatch Date, Transporter Name, Vehicle Number, Driver Contact, and Gate Pass Reference.
  - Record dispatched item quantities with backorder calculation.
  - **Print Engine:** Generates legal Delivery Challan note with recipient signature boxes.

### Feature 3.5: Commercial Tax Invoice Generation
- **Routes:** `/tender-invoice-add`, `/tender-invoice-list`, `/tender-po-invoice-print/(:num)`
- **Workflow Position:** Step 5 of Customer Lifecycle
- **Key Capabilities:**
  - Auto-generate invoice referencing Customer PO and Delivery Challan.
  - Verification of line items, billing quantities, unit prices, and VAT amounts.
  - Select remittance company bank account for wire transfer instructions.
  - Synchronizes with Accounts Receivable ledger as a debit to Sundry Debtors.
  - **Print Engine:** Generates compliant commercial tax invoice with CR, VAT numbers, and QR/terms.

---

```
==================================================================================
MODULE 04: SUPPLIER PROCUREMENT & COMPLETE LANDED COSTING
==================================================================================
```

### Feature 4.1: Supplier Rate Enquiry (Vendor RFQ)
- **Routes:** `/vendor-rate-enquiry`, `/vendor-rate-enquiry-list`, `/vendor-rate-enquiry-print/(:num)`
- **Workflow Position:** Step 1 of Procurement Lifecycle
- **Key Capabilities:**
  - Link procurement requests directly to a specific Customer Tender Enquiry.
  - Send RFQs to multiple suppliers for price discovery.
  - Generate printable Supplier RFQ sheet without revealing customer pricing or margins.

### Feature 4.2: Supplier Quotation Logging & Comparison
- **Routes:** `/vendor-quotation-add`, `/vendor-quotation-list`
- **Workflow Position:** Step 2 of Procurement Lifecycle
- **Key Capabilities:**
  - Record incoming supplier quotations with vendor reference numbers and quote dates.
  - Multi-currency rate conversion (USD, EUR, GBP, AED, SAR, etc.).
  - Side-by-side rate evaluation to determine best cost before placing orders.

### Feature 4.3: Supplier Purchase Order (PO)
- **Routes:** `/vendor-po-add`, `/vendor-po-list`, `/vendor-po-view/(:num)`
- **Workflow Position:** Step 3 of Procurement Lifecycle
- **Key Capabilities:**
  - Issue official Purchase Orders to approved suppliers referencing Tender ID.
  - Define delivery schedules, warehouse destination, payment terms, and warranty stipulations.
  - Print and export official Supplier PO PDF with company authorization.

### Feature 4.4: Goods Receipt Note (Supplier Inward / GRN)
- **Routes:** `/vendor-pur-inward-add`, `/vendor-pur-inward-list`
- **Workflow Position:** Step 4 of Procurement Lifecycle
- **Key Capabilities:**
  - Physical warehouse verification of goods delivered against Supplier PO.
  - Record delivered quantities, rejected quantities, and serial/batch details.
  - Upload supplier delivery slips or inspection certificates (`vendor-pur-inward-documents/`).

### Feature 4.5: Direct Vendor Purchase Bills
- **Routes:** `/vendor-purchase-bill-add`, `/vendor-purchase-bill-list`
- **Workflow Position:** Step 5 of Procurement Lifecycle
- **Key Capabilities:**
  - Record final supplier tax invoice referencing GRN.
  - Reconcile PO price with final billed price and compute payable tax amounts.
  - Auto-posts credit entry to Supplier Payable Sub-Ledger.

### Feature 4.6: Multi-Element Landed Cost Tracking
- **Description:** Complete landed cost framework that attaches all ancillary expenses directly to the project tender:
  - **Local Purchase Bills (`/local-purchase-bill-list`):** Consumables, local hardware, nuts/bolts, fabrication work.
  - **Delivery Partner Bills (`/delivery-partner-bill-list`):** International air/sea freight, local truck haulage, courier fees.
  - **Customs Duty & Clearance Bills (`/customs-bill-list`):** Customs import duties, port tariffs, clearing agent documentation charges.
- **Financial Impact:** Feeds directly into the Tender Profitability Matrix to establish exact landed cost per item.

---

```
==================================================================================
MODULE 05: FINANCIAL ACCOUNTING, TREASURY & GENERAL LEDGER
==================================================================================
```

### Feature 5.1: Chart of Accounts & Sub-Ledger Structure
- **Routes:** `/account-head-list`, `/sub-account-head-list`
- **Key Capabilities:**
  - Multi-tier general ledger structure categorizing entries into:
    - **Assets** (Current Assets, Fixed Assets, Bank Accounts, Sundry Debtors)
    - **Liabilities** (Current Liabilities, Sundry Creditors, Duties & Taxes)
    - **Income** (Operating Sales, Project Invoiced Revenue, Interest Income)
    - **Expenses** (Direct Project Cost, Overheads, Administrative, Freight, Customs)
  - Sub-Account Heads for departmental, vendor, or project classification.

### Feature 5.2: Cash & Bank Inward Book (Receipts)
- **Route:** `/inward-list`
- **Key Capabilities:**
  - Record non-sales receipts, bank interest, partner capital injections, or asset liquidations.
  - Select receiving bank or cash account, reference transaction number, and account head.
  - Generates official receipt voucher (`/print-receipt/(:num)`).

### Feature 5.3: Cash & Bank Outward Book (Disbursements)
- **Route:** `/outward-list`
- **Key Capabilities:**
  - Record administrative expenses, office rent, utility bills, software subscriptions, and staff compensation.
  - Capture payee, voucher date, payment mode (Cheque, NEFT/RTGS, Wire, Cash), and bill attachments.
  - Generates official payment voucher (`/print-voucher/(:num)`).

### Feature 5.4: Contra Entry Management (Internal Fund Transfers)
- **Route:** `/contra-entry`
- **Key Capabilities:**
  - Record internal movements of money without affecting overall equity:
    - **Cash Deposit:** Physical Cash Office $\rightarrow$ Corporate Bank Account.
    - **Cash Withdrawal:** Corporate Bank Account $\rightarrow$ Cash In Hand.
    - **Inter-Bank Transfer:** Bank Account A $\rightarrow$ Bank Account B.
  - Automatic double-entry synchronization with both account ledgers.

### Feature 5.5: Petty Cash Fund Float Management
- **Routes:** `/petty-cash`, `/pettycash/add_funds`, `/pettycash/add_expense`, `/petty-cash-statement`
- **Key Capabilities:**
  - Allocate and track an imprest petty cash fund float.
  - Record small day-to-day office vouchers (tea, courier, stationery, site travel).
  - Real-time petty cash running balance and replenishment statements.

### Feature 5.6: Credit & Debit Note Adjustments
- **Routes:** `/credit-debit-note-list`, `/credit-debit-note-add`, `/credit-debit-note-save`
- **Key Capabilities:**
  - **Credit Notes:** Issued to customers for returned goods, damaged deliveries, or post-invoice commercial discounts.
  - **Debit Notes:** Issued to suppliers for rejected materials, rate discrepancies, or contractual penalties.
  - Dynamic AJAX auto-fetch of original invoice line items and balances.

---

```
==================================================================================
MODULE 06: PAYMENT SETTLEMENT & RECONCILIATION
==================================================================================
```

### Feature 6.1: Customer Payment Collections & Invoicing Receipts
- **Routes:** `/customer-invoice-receipt`, `/get-customer-balance-summary`
- **Key Capabilities:**
  - Select Customer and view real-time outstanding balance across all historical invoices.
  - Multi-invoice FIFO or manual allocation: Allocate lump-sum incoming payments against specific invoice numbers.
  - Record Cheque / Transaction Reference Number, Deposit Bank, and Clearance Date.

### Feature 6.2: Vendor Payment Disbursements
- **Routes:** `/vendor-payment-list`, `/get-vendor-balance-summary`, `/vendor-adv-payment`
- **Key Capabilities:**
  - Record advance payments against Supplier POs before shipment.
  - Settle outstanding vendor bills against specific purchase bill numbers.
  - Real-time payable summary calculation and payment voucher generation.

---

```
==================================================================================
MODULE 07: AUDIT, DOUBLE-ENTRY ACCOUNTING & FINANCIAL STATEMENTS
==================================================================================
```

### Feature 7.1: Journal Vouchers & Voucher Entries
- **Routes:** `/vouchers-list`, `/voucher-entries-list`
- **Key Capabilities:**
  - Post manual journal vouchers for depreciation, adjustments, year-end provisions, and tax liabilities.
  - Enforces strict debit = credit balance validation prior to database commit.

### Feature 7.2: Ledger Transactions & Statement of Accounts
- **Routes:** `/ledger-transactions-report`, `/cash-in-out-statement`
- **Key Capabilities:**
  - Date-filtered ledger view for any account head showing Opening Balance, Total Debits, Total Credits, and Net Closing Balance.
  - Drill-down capability from ledger entry directly to original transaction voucher.

### Feature 7.3: Trial Balance & Profit and Loss (P&L)
- **Routes:** `/trial-balance-list`, `/profit-loss-report`, `/pl-report`
- **Key Capabilities:**
  - **Trial Balance:** Aggregated balances across all Asset, Liability, Equity, Revenue, and Expense heads.
  - **Profit & Loss Report:** Calculates Gross Margin, Operating Expenses, and Net Profit across selected accounting periods.

---

```
==================================================================================
MODULE 08: BUSINESS INTELLIGENCE, TENDER PROGRESS & TAX COMPLIANCE
==================================================================================
```

### Feature 8.1: Tender Enquiry Summary & Profitability Matrix
- **Route:** `/tender-enquiry-summary-report`
- **The Core Analytical Engine of ERP-PSM:**
  - Aggregates the entire financial lifecycle for every tender into a unified row:
    $$\text{Project Net Margin} = \text{Total Invoiced Sales} - (\text{Supplier POs} + \text{Local Bills} + \text{Freight / Shipping} + \text{Customs Duty})$$
  - Color-coded gross margin percentage indicators (Profitable vs Loss-making tenders).

### Feature 8.2: Customer Statement of Account (SOA)
- **Route:** `/customer-statement-report`
- **Key Capabilities:**
  - Complete chronological statement showing starting balance, invoices issued, payment receipts credited, and current net overdue.
  - One-click PDF printout and Excel `.xls` export for monthly client reconciliation.

### Feature 8.3: Vendor Statement of Account (SOA)
- **Route:** `/vendor-statement-report`
- **Key Capabilities:**
  - Supplier ledger showing purchase bills, advance payouts, adjustments, and balance payable.
  - Exportable to Excel for supplier accounts reconciliation.

### Feature 8.4: NBR & VAT Compliance Reporting
- **Routes:** `/sales-nbr-report`, `/purchase-nbr-report`, `/vat-statement-report`
- **Key Capabilities:**
  - **Sales VAT Register:** Lists taxable sales, zero-rated exports, standard VAT collected, and invoice numbers.
  - **Purchase VAT Register:** Input tax credit (ITC) schedule detailing VAT paid on domestic purchases and import customs.
  - Pre-configured to meet National Board of Revenue (NBR) and GCC VAT audit filing requirements.

### Feature 8.5: Pending Receivables & Payables Aging
- **Routes:** `/customer-invoice-pending-report`, `/vendor-invoice-pending-report`
- **Key Capabilities:**
  - Tracks overdue customer receivables and outstanding supplier bills by aging brackets (0-30 days, 31-60 days, 61-90 days, 90+ days).
