<?php
// Define master menu pages
$g_master = ['company-list', 'category-list', 'brand-list', 'items-list', 'uom-list', 'gst-list', 'user-list', 'vendor-list', 'customer-list', 'customer-contact-list', 'vendor-contact-list', 'currency-list', 'account-head-list', 'country-list', 'addt-charges-type-list', 'sub-account-head-list', 'account-head-for-list', 'voucher-type-list', 'opening-balance-list', 'settings'];

// Get current page
$current_page = $this->uri->segment(1, 0);

echo "<br> dsfsdfsdf" . $current_page;
?>

<!-- Dashboard -->
<li class="header">Dashboard</li>
<li class="<?= ($current_page === 'dash') ? 'active' : '' ?>">
    <a href="<?= site_url('dash') ?>">
        <i class="fa fa-dashboard"></i>
        <span>Dashboard</span>
    </a>
</li>
<!-- Tender Main Menu -->
<li class="header">TENDER</li>
<!-- Tender Enquiry -->
<li
    class="treeview <?= in_array($current_page, ['add-tender-enquiry', 'tender-enquiry-list', 'tender-enquiry-edit']) ? 'active' : '' ?>">
    <a href="#">
        <i class="fa fa-file-text-o"></i> Tender Enquiry
        <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
    </a>

    <ul class="treeview-menu">
        <li class="<?= ($current_page === 'add-tender-enquiry') ? 'active' : '' ?>">
            <a href="<?= site_url('add-tender-enquiry') ?>">
                <i class="fa fa-plus-circle"></i> Add Tender Enquiry
            </a>
        </li>

        <li class="<?= ($current_page === 'tender-enquiry-list') ? 'active' : '' ?>">
            <a href="<?= site_url('tender-enquiry-list') ?>">
                <i class="fa fa-list"></i> Tender Enquiry List
            </a>
        </li>
    </ul>
</li>

<li class="treeview 
    <?= in_array($current_page, [
        'tender-quotation-add',
        'tender-quotation-list',
        'tender-quotation-edit',
        'customer-tender-po-add',
        'customer-tender-po-list',
        'customer-tender-po-edit',
        'tender-invoice-add',
        'tender-invoice-list',
        'tender-po-invoice-edit',
        'tender-dc-add',
        'tender-dc-list',
        'tender-dc-edit'
    ]) ? 'active' : '' ?>">

    <a href="#">
        <i class="fa fa-folder-open"></i>
        <span>Tender</span>
        <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
    </a>

    <ul class="treeview-menu">

        <!-- Tender Quotation -->
        <li
            class="treeview <?= in_array($current_page, ['tender-quotation-add', 'tender-quotation-list', 'tender-quotation-edit']) ? 'active' : '' ?>">
            <a href="#">
                <i class="fa fa-file-text"></i> Tender Quotation
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>

            <ul class="treeview-menu">
                <li class="<?= ($current_page === 'tender-quotation-add') ? 'active' : '' ?>">
                    <a href="<?= site_url('tender-quotation-add') ?>">
                        <i class="fa fa-plus-circle"></i> Add Tender Quotation
                    </a>
                </li>

                <li class="<?= ($current_page === 'tender-quotation-list') ? 'active' : '' ?>">
                    <a href="<?= site_url('tender-quotation-list') ?>">
                        <i class="fa fa-list-ul"></i> Tender Quotation List
                    </a>
                </li>
            </ul>
        </li>

        <!-- Customer Tender PO -->
        <li
            class="treeview <?= in_array($current_page, ['customer-tender-po-add', 'customer-tender-po-list', 'customer-tender-po-edit']) ? 'active' : '' ?>">
            <a href="#">
                <i class="fa fa-briefcase"></i> Tender PO
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>

            <ul class="treeview-menu">
                <li class="<?= ($current_page === 'customer-tender-po-add') ? 'active' : '' ?>">
                    <a href="<?= site_url('customer-tender-po-add') ?>">
                        <i class="fa fa-plus-square"></i> Tender PO
                    </a>
                </li>

                <li class="<?= ($current_page === 'customer-tender-po-list') ? 'active' : '' ?>">
                    <a href="<?= site_url('customer-tender-po-list') ?>">
                        <i class="fa fa-list"></i> Tender PO List
                    </a>
                </li>
            </ul>
        </li>
        <li
            class="treeview <?= in_array($current_page, ['tender-dc-add', 'tender-dc-list', 'tender-dc-edit']) ? 'active' : '' ?>">
            <a href="#">
                <i class="fa fa-briefcase"></i> Tender DC
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>

            <ul class="treeview-menu">
                <li class="<?= ($current_page === 'tender-dc-add') ? 'active' : '' ?>">
                    <a href="<?= site_url('tender-dc-add') ?>">
                        <i class="fa fa-plus-square"></i> Tender DC
                    </a>
                </li>

                <li class="<?= ($current_page === 'tender-dc-list') ? 'active' : '' ?>">
                    <a href="<?= site_url('tender-dc-list') ?>">
                        <i class="fa fa-list"></i> Tender DC List
                    </a>
                </li>
            </ul>
        </li>
        <li
            class="treeview <?= in_array($current_page, ['tender-invoice-add', 'tender-invoice-list', 'tender-po-invoice-edit']) ? 'active' : '' ?>">
            <a href="#">
                <i class="fa fa-briefcase"></i> Tender Invoice
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>

            <ul class="treeview-menu">
                <li class="<?= ($current_page === 'tender-invoice-add') ? 'active' : '' ?>">
                    <a href="<?= site_url('tender-invoice-add') ?>">
                        <i class="fa fa-plus-square"></i> Tender Invoice
                    </a>
                </li>

                <li class="<?= ($current_page === 'tender-invoice-list') ? 'active' : '' ?>">
                    <a href="<?= site_url('tender-invoice-list') ?>">
                        <i class="fa fa-list"></i> Tender Invoice List
                    </a>
                </li>
            </ul>
        </li>



    </ul>
</li>
<li class="<?= ($current_page === 'tender-enquiry-timeline') ? 'active' : '' ?>">
    <a href="<?= site_url('tender-enquiry-timeline') ?>">
        <i class="fa fa-clock-o"></i>
        <span>Tender Timeline</span>
    </a>
</li>
<li class="<?= ($current_page === 'tender-enquiry-summary-report') ? 'active' : '' ?>">
    <a href="<?= site_url('tender-enquiry-summary-report') ?>">
        <i class="fa fa-file-text-o"></i>
        <span>Tender Summary Report</span>
    </a>
</li>

<?php
$vendor_m_grp = [
    'vendor-rate-enquiry',
    'vendor-rate-enquiry-list',
    'vendor-rate-enquiry-edit',
    'vendor-quotation-add',
    'vendor-quotation-list',
    'vendor-quotation-edit',
    'vendor-po-add',
    'vendor-po-list',
    'vendor-po-view',
    'vendor-po-edit',
    'vendor-pur-inward-add',
    'vendor-pur-inward-list',
    'vendor-pur-inward-edit',
    'vendor-purchase-bill-add',
    'vendor-purchase-bill-list',
    'vendor-purchase-bill-edit'
];
?>

<!-- Vendor -->
<li class="header">VENDOR</li>

<li class="treeview 
    <?= in_array($current_page, $vendor_m_grp) ? 'active' : '' ?>">

    <a href="#">
        <i class="fa fa-industry"></i>
        <span>Vendor</span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>

    <ul class="treeview-menu">

        <!-- Vendor Enquiry -->
        <li
            class="treeview <?= in_array($current_page, ['vendor-rate-enquiry', 'vendor-rate-enquiry-list', 'vendor-rate-enquiry-edit']) ? 'active' : '' ?>">
            <a href="#">
                <i class="fa fa-envelope"></i> Vendor Enquiry
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>
            <ul class="treeview-menu">
                <li class="<?= ($current_page === 'vendor-rate-enquiry') ? 'active' : '' ?>">
                    <a href="<?= site_url('vendor-rate-enquiry') ?>"><i class="fa fa-plus-square"></i> Add Vendor
                        Enquiry</a>
                </li>
                <li class="<?= ($current_page === 'vendor-rate-enquiry-list') ? 'active' : '' ?>">
                    <a href="<?= site_url('vendor-rate-enquiry-list') ?>"><i class="fa fa-list"></i> Vendor Enquiry
                        List</a>
                </li>
            </ul>
        </li>

        <!-- Vendor Quotation -->
        <li
            class="treeview <?= in_array($current_page, ['vendor-quotation-add', 'vendor-quotation-list', 'vendor-quotation-edit']) ? 'active' : '' ?>">
            <a href="#">
                <i class="fa fa-file-text-o"></i> Vendor Quotation
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>
            <ul class="treeview-menu">
                <li class="<?= ($current_page === 'vendor-quotation-add') ? 'active' : '' ?>">
                    <a href="<?= site_url('vendor-quotation-add') ?>"><i class="fa fa-plus-square"></i> Add Vendor
                        Quotation</a>
                </li>
                <li class="<?= ($current_page === 'vendor-quotation-list') ? 'active' : '' ?>">
                    <a href="<?= site_url('vendor-quotation-list') ?>"><i class="fa fa-list"></i> Vendor Quotation
                        List</a>
                </li>
            </ul>
        </li>

        <!-- Vendor PO -->
        <li
            class="treeview <?= in_array($current_page, ['vendor-po-add', 'vendor-po-list', 'vendor-po-view', 'vendor-po-edit']) ? 'active' : '' ?>">
            <a href="#">
                <i class="fa fa-files-o"></i> Vendor PO
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>
            <ul class="treeview-menu">
                <li class="<?= ($current_page === 'vendor-po-add') ? 'active' : '' ?>">
                    <a href="<?= site_url('vendor-po-add') ?>"><i class="fa fa-plus-square"></i> Add Vendor PO</a>
                </li>
                <li class="<?= ($current_page === 'vendor-po-list') ? 'active' : '' ?>">
                    <a href="<?= site_url('vendor-po-list') ?>"><i class="fa fa-list"></i> Vendor PO List</a>
                </li>
            </ul>
        </li>
        <!-- Vendor PO -->
        <li
            class="treeview <?= in_array($current_page, ['vendor-pur-inward-add', 'vendor-pur-inward-list', 'vendor-pur-inward-edit']) ? 'active' : '' ?>">
            <a href="#">
                <i class="fa fa-files-o"></i> Vendor Inward
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>
            <ul class="treeview-menu">
                <li class="<?= ($current_page === 'vendor-pur-inward-add') ? 'active' : '' ?>">
                    <a href="<?= site_url('vendor-pur-inward-add') ?>"><i class="fa fa-plus-square"></i> Add Vendor
                        Inward</a>
                </li>
                <li class="<?= ($current_page === 'vendor-pur-inward-list') ? 'active' : '' ?>">
                    <a href="<?= site_url('vendor-pur-inward-list') ?>"><i class="fa fa-list"></i> Vendor Inward
                        List</a>
                </li>
            </ul>
        </li>

        <li
            class="treeview <?= in_array($current_page, ['vendor-purchase-bill-add', 'vendor-purchase-bill-list', 'vendor-purchase-bill-edit']) ? 'active' : '' ?>">
            <a href="#">
                <i class="fa fa-files-o"></i> Vendor Invoice/Bill
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>
            <ul class="treeview-menu">
                <li class="<?= ($current_page === 'vendor-purchase-bill-add') ? 'active' : '' ?>">
                    <a href="<?= site_url('vendor-purchase-bill-add') ?>"><i class="fa fa-plus-square"></i> Add Vendor
                        Bill Entry</a>
                </li>
                <li class="<?= ($current_page === 'vendor-purchase-bill-list') ? 'active' : '' ?>">
                    <a href="<?= site_url('vendor-purchase-bill-list') ?>"><i class="fa fa-list"></i> Vendor Bill
                        List</a>
                </li>
            </ul>
        </li>

    </ul>
</li>

<!-- Report -->
<?php
$report_m_grp = [
    'sales-nbr-report',
    'purchase-nbr-report'
]; 
?>
<li class="header">REPORTS</li>

<li class="treeview 
    <?= in_array($current_page, $report_m_grp) ? 'active' : '' ?>">

    <a href="#">
        <i class="fa fa-area-chart"></i>
        <span>Reports</span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>

    <ul class="treeview-menu">

        <!-- Vendor Enquiry -->
        <li
            class="treeview <?= in_array($current_page, ['sales-nbr-report', 'purchase-nbr-report']) ? 'active' : '' ?>">
            <a href="#">
                <i class="fa fa-envelope"></i> NBR Report
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>
            <ul class="treeview-menu">
                <li class="<?= ($current_page === 'sales-nbr-report') ? 'active' : '' ?>">
                    <a href="<?= site_url('sales-nbr-report') ?>"><i class="fa fa-list"></i> Sales NBR Report</a>
                </li>
                <li class="<?= ($current_page === 'purchase-nbr-report') ? 'active' : '' ?>">
                    <a href="<?= site_url('purchase-nbr-report') ?>"><i class="fa fa-list"></i> Purchase NBR Report</a>
                </li>
            </ul>
        </li>
         </ul> 
    </li>


<?php /*
<li class="header">Acounts Book Info</li>

<li class="treeview <?= in_array($current_page, ['inward-list', 'outward-list']) ? 'active' : '' ?>">
<a href="#">
<i class="fa fa-file-text"></i>
<span>Accounts Book Info</span>
<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
</a>
<ul class="treeview-menu">

<li class="<?= ($current_page === 'inward-list') ? 'active' : '' ?>">
  <a href="<?= site_url('inward-list') ?>">
      <i class="fa fa-plus-circle"></i> Inward Entery
  </a>
</li>

<li class="<?= ($current_page === 'outward-list') ? 'active' : '' ?>">
  <a href="<?= site_url('outward-list') ?>">
      <i class="fa fa-list-ul"></i> Outward Entery
  </a>
</li>

</ul>
</li>
*/ ?>
<li class="header">AUDIT</li>

<li class="treeview <?= in_array($this->uri->segment(1), [
    'account-group-list',
    'ledger-accounts-list',
    'vouchers-list',
    'voucher-entries-list',
    'trial-balance-list',
    'profit-loss-report'
]) ? 'active' : '' ?>">


    <a href="#">
        <i class="fa fa-database"></i>
        <span>AUDIT</span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>

    <ul class="treeview-menu">

        <!-- Account Groups -->
        <li class="<?= ($this->uri->segment(1) == 'account-group-list') ? 'active' : '' ?>">
            <a href="<?= site_url('account-group-list') ?>">
                <i class="fa fa-sitemap"></i> Account Groups
            </a>
        </li>

        <!-- Ledger Accounts -->
        <li class="<?= ($this->uri->segment(1) == 'ledger-accounts-list') ? 'active' : '' ?>">
            <a href="<?= site_url('ledger-accounts-list') ?>">
                <i class="fa fa-book"></i> Ledger Accounts
            </a>
        </li>

        <!-- Vouchers -->
        <li class="<?= ($this->uri->segment(1) == 'vouchers-list') ? 'active' : '' ?>">
            <a href="<?= site_url('vouchers-list') ?>">
                <i class="fa fa-file-text"></i> Vouchers
            </a>
        </li>

        <!-- Voucher Entries -->
        <li class="<?= ($this->uri->segment(1) == 'voucher-entries-list') ? 'active' : '' ?>">
            <a href="<?= site_url('voucher-entries-list') ?>">
                <i class="fa fa-pencil-square-o"></i> Voucher Entries
            </a>
        </li>

        <!-- Trial Balance -->
        <li class="<?= ($this->uri->segment(1) == 'trial-balance-list') ? 'active' : '' ?>">
            <a href="<?= site_url('trial-balance-list') ?>">
                <i class="fa fa-balance-scale"></i> Trial Balance
            </a>
        </li>

        <!-- Profit & Loss -->
        <li class="<?= ($this->uri->segment(1) == 'profit-loss-report') ? 'active' : '' ?>">
            <a href="<?= site_url('profit-loss-report') ?>">
                <i class="fa fa-line-chart"></i> Profit &amp; Loss
            </a>
        </li>

    </ul>
</li>

<?php /*
<li class="header">Reports</li>
<li
class="treeview <?= in_array($this->uri->segment(1), ['cash-ledger', 'cash-in-statement', 'cash-out-statement', 'inward-summary', 'outward-summary', 'na-cash-in-statement', 'na-cash-out-statement']) ? 'active' : '' ?>">
<a href="#">
<i class="fa fa-file-text"></i>
<span>Reports</span>
<span class="pull-right-container">
  <i class="fa fa-angle-left pull-right"></i>
</span>
</a>

<ul class="treeview-menu">

<li class="<?= ($this->uri->segment(1) == 'cash-ledger') ? 'active' : '' ?>">
  <a href="<?= site_url('cash-ledger') ?>">
      <i class="fa fa-book"></i> Ledger Reports
  </a>
</li>
<li class="<?= ($this->uri->segment(1) == 'cash-in-statement') ? 'active' : '' ?>">
  <a href="<?= site_url('cash-in-statement') ?>">
      <i class="fa fa-arrow-circle-down"></i> Inward Statement
  </a>
</li>

<li class="<?= ($this->uri->segment(1) == 'cash-out-statement') ? 'active' : '' ?>">
  <a href="<?= site_url('cash-out-statement') ?>">
      <i class="fa fa-arrow-circle-up"></i> Outward Statement
  </a>
</li>

<li class="<?= ($this->uri->segment(1) == 'inward-summary') ? 'active' : '' ?>">
  <a href="<?= site_url('inward-summary') ?>">
      <i class="fa fa-file-text-o"></i> Inward Summary
  </a>
</li>

<li class="<?= ($this->uri->segment(1) == 'outward-summary') ? 'active' : '' ?>">
  <a href="<?= site_url('outward-summary') ?>">
      <i class="fa fa-file-text-o"></i> Outward Summary
  </a>
</li>

<li class="text-fuchsia">Non-Accountable</li>

<li class="<?= ($this->uri->segment(1) == 'na-cash-in-statement') ? 'active' : '' ?>">
  <a href="<?= site_url('na-cash-in-statement') ?>">
      <i class="fa fa-file-text-o"></i> NA-Inward Statement
  </a>
</li>

<li class="<?= ($this->uri->segment(1) == 'na-cash-out-statement') ? 'active' : '' ?>">
  <a href="<?= site_url('na-cash-out-statement') ?>">
      <i class="fa fa-file-text-o"></i> NA-Outward Statement
  </a>
</li>

</ul>
</li>
*/ ?>
<!-- Master Section -->
<li class="header">Master</li>

<li class="treeview <?= in_array($current_page, $g_master) ? 'active' : '' ?>">
    <a href="#">
        <i class="fa fa-cubes"></i>
        <span>Master</span>
        <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
    </a>

    <ul class="treeview-menu">

        <!-- General -->
        <li
            class="treeview <?= in_array($current_page, ['category-list', 'brand-list', 'items-list', 'uom-list', 'gst-list', 'currency-list', 'settings', 'country-list', 'addt-charges-type-list']) ? 'active' : '' ?>">
            <a href="#">
                <i class="fa fa-cog"></i> General
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>

            <ul class="treeview-menu">
                <!-- <li class="<?= ($current_page === 'category-list') ? 'active' : '' ?>">
                    <a href="<?= site_url('category-list') ?>"><i class="fa fa-folder-open"></i> Category List</a>
                </li>

                <li class="<?= ($current_page === 'brand-list') ? 'active' : '' ?>">
                    <a href="<?= site_url('brand-list') ?>"><i class="fa fa-industry"></i> Brand List</a>
                </li> -->

                <!-- <li class="<?= ($current_page === 'items-list') ? 'active' : '' ?>">
                    <a href="<?= site_url('items-list') ?>"><i class="fa fa-cubes"></i> Items List</a>
                </li>

                <li class="<?= ($current_page === 'uom-list') ? 'active' : '' ?>">
                    <a href="<?= site_url('uom-list') ?>"><i class="fa fa-balance-scale"></i> UOM List</a>
                </li>

                <li class="<?= ($current_page === 'gst-list') ? 'active' : '' ?>">
                    <a href="<?= site_url('gst-list') ?>"><i class="fa fa-percent"></i> VAT List</a>
                </li> -->
                <li class="<?= ($this->uri->segment(1) == 'vat-filing-head-list') ? 'active' : '' ?>">
                    <a href="<?= site_url('vat-filing-head-list') ?>">
                        <i class="fa fa-file-text-o"></i> VAT Filing Head List
                    </a>
                </li>
                <li class="<?= ($current_page === 'currency-list') ? 'active' : '' ?>">
                    <a href="<?= site_url('currency-list') ?>"><i class="fa fa-money"></i> Currency List</a>
                </li>
                <li class="<?= ($current_page === 'settings') ? 'active' : '' ?>">
                    <a href="<?= site_url('settings') ?>"><i class="fa fa-cogs"></i> Settings</a>
                </li>
                <li class="<?= ($this->uri->segment(1) == 'country-list') ? 'active' : '' ?>">
                    <a href="<?= site_url('country-list') ?>">
                        <i class="fa fa-globe"></i> Country List
                    </a>
                </li>
                <li class="<?= ($this->uri->segment(1) == 'addt-charges-type-list') ? 'active' : '' ?>">
                    <a href="<?= site_url('addt-charges-type-list') ?>">
                        <i class="fa fa-tag"></i> Addt Charges Type List
                    </a>
                </li>


            </ul>
        </li>

        <!-- Company Info -->
        <li class="treeview <?= in_array($current_page, ['company-list', 'user-list']) ? 'active' : '' ?>">
            <a href="#">
                <i class="fa fa-building"></i> Company Info
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>

            <ul class="treeview-menu">
                <li class="<?= ($current_page === 'company-list') ? 'active' : '' ?>">
                    <a href="<?= site_url('company-list') ?>"><i class="fa fa-address-book"></i> Company Details</a>
                </li>

                <li class="<?= ($current_page === 'user-list') ? 'active' : '' ?>">
                    <a href="<?= site_url('user-list') ?>"><i class="fa fa-user"></i> User List</a>
                </li>
            </ul>
        </li>

        <!-- Customer Info -->
        <li class="treeview <?= in_array($current_page, ['customer-list', 'customer-contact-list']) ? 'active' : '' ?>">
            <a href="#">
                <i class="fa fa-address-book"></i> Customer Info
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>

            <ul class="treeview-menu">
                <li class="<?= ($current_page === 'customer-list') ? 'active' : '' ?>">
                    <a href="<?= site_url('customer-list') ?>"><i class="fa fa-users"></i> Customer List</a>
                </li>

                <li class="<?= ($current_page === 'customer-contact-list') ? 'active' : '' ?>">
                    <a href="<?= site_url('customer-contact-list') ?>"><i class="fa fa-building"></i> Customer Contact
                        Info</a>
                </li>
            </ul>
        </li>

        <!-- Vendor Info -->
        <li class="treeview <?= in_array($current_page, ['vendor-list', 'vendor-contact-list']) ? 'active' : '' ?>">
            <a href="#">
                <i class="fa fa-address-card"></i> Vendor Info
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>

            <ul class="treeview-menu">
                <li class="<?= ($current_page === 'vendor-list') ? 'active' : '' ?>">
                    <a href="<?= site_url('vendor-list') ?>"><i class="fa fa-users"></i> Vendor List</a>
                </li>

                <li class="<?= ($current_page === 'vendor-contact-list') ? 'active' : '' ?>">
                    <a href="<?= site_url('vendor-contact-list') ?>"><i class="fa fa-building"></i> Vendor Contact
                        Info</a>
                </li>
            </ul>
        </li>
        <?php /*
<li
  class="treeview <?= in_array($current_page, ['account-head-list', 'sub-account-head-list', 'account-head-for-list', 'voucher-type-list', 'opening-balance-list']) ? 'active' : '' ?>">
  <a href="#">
      <i class="fa fa-money"></i> Accounts
      <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
  </a>

  <ul class="treeview-menu">
      <li class="<?= ($current_page === 'account-head-list') ? 'active' : '' ?>">
          <a href="<?= site_url('account-head-list') ?>"><i class="fa fa-university"></i> Account Head</a>
      </li>

      <li class="<?= ($current_page === 'sub-account-head-list') ? 'active' : '' ?>">
          <a href="<?= site_url('sub-account-head-list') ?>"><i class="fa fa-bank"></i> Sub-Account Head</a>
      </li>

      <li class="<?= ($current_page === 'account-head-for-list') ? 'active' : '' ?>">
          <a href="<?= site_url('account-head-for-list') ?>"><i class="fa fa-inbox"></i> A/c In From / Out
              For</a>
      </li>

      <li class="<?= ($current_page === 'voucher-type-list') ? 'active' : '' ?>">
          <a href="<?= site_url('voucher-type-list') ?>"><i class="fa fa-file-text"></i> Voucher Type</a>
      </li>

      <li class="<?= ($current_page === 'opening-balance-list') ? 'active' : '' ?>">
          <a href="<?= site_url('opening-balance-list') ?>"><i class="fa fa-balance-scale"></i> Opening
              Balance</a>
      </li>
  </ul>
</li>
*/ ?>
    </ul>
</li>