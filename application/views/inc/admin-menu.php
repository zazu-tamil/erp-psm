<?php
// Define master menu pages
$g_master = ['company-list', 'category-list', 'brand-list', 'items-list', 'uom-list', 'gst-list', 'user-list', 'vendor-list', 'customer-list', 'customer-contact-list', 'vendor-contact-list', 'currency-list', 'account-head-list', 'sub-account-head-list', 'account-head-for-list', 'voucher-type-list', 'opening-balance-list'];

// Get current page
$current_page = $this->uri->segment(1, 0);
?>

<!-- Dashboard -->
<li class="header">Dashboard</li>
<li class="<?= ($current_page === 'dash') ? 'active' : '' ?>">
    <a href="<?= site_url('dash') ?>">
        <i class="fa fa-dashboard"></i>
        <span>Dashboard</span>
    </a>
</li>

<!-- Tender Enquiry -->
<li class="header">Tender Enquiry</li>

<li
    class="treeview <?= in_array($current_page, ['add-tender-enquiry', 'tender-enquiry-list', 'tender-enquiry-edit']) ? 'active' : '' ?>">
    <a href="#">
        <i class="fa fa-file-text-o"></i>
        <span>Tender Enquiry</span>
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
<!-- Vendor Enquiry -->
<li class="header">Vendor Enquiry</li>

<li
    class="treeview <?= in_array($current_page, ['vendor-rate-enquiry', 'vendor-rate-enquiry-list', 'vendor-rate-enquiry-edit']) ? 'active' : '' ?>">
    <a href="#">
        <i class="fa fa-envelope-open"></i>
        <span>Vendor Enquiry</span>
        <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
    </a>
    <ul class="treeview-menu">

        <li class="<?= ($current_page === 'vendor-rate-enquiry') ? 'active' : '' ?>">
            <a href="<?= site_url('vendor-rate-enquiry') ?>">
                <i class="fa fa-plus-square"></i> Add Vendor Enquiry
            </a>
        </li>

        <li class="<?= ($current_page === 'vendor-rate-enquiry-list') ? 'active' : '' ?>">
            <a href="<?= site_url('vendor-rate-enquiry-list') ?>">
                <i class="fa fa-list"></i> Vendor Enquiry List
            </a>
        </li>

    </ul>
</li>


<!-- Vendor PO -->
<li
    class="treeview <?= in_array($current_page, ['vendor-po-add', 'vendor-po-list', 'vendor-po-view', 'vendor-po-edit']) ? 'active' : '' ?>">
    <a href="#">
        <i class="fa fa-files-o"></i>
        <span>Vendor PO</span>
        <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
    </a>
    <ul class="treeview-menu">

        <li class="<?= ($current_page === 'vendor-po-add') ? 'active' : '' ?>">
            <a href="<?= site_url('vendor-po-add') ?>">
                <i class="fa fa-plus-square"></i> Add Vendor PO
            </a>
        </li>

        <li class="<?= ($current_page === 'vendor-po-list') ? 'active' : '' ?>">
            <a href="<?= site_url('vendor-po-list') ?>">
                <i class="fa fa-list"></i> Vendor PO List
            </a>
        </li>

    </ul>
</li>
<!-- Tender Quotation -->
<li class="header">Tender Quotation</li>

<li
    class="treeview <?= in_array($current_page, ['tender-quotation-add', 'tender-quotation-list', 'tender-quotation-edit']) ? 'active' : '' ?>">
    <a href="#">
        <i class="fa fa-file-text"></i>
        <span>Tender Quotation</span>
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
        <i class="fa fa-briefcase"></i>
        <span>Customer Tender PO</span>
        <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
    </a>
    <ul class="treeview-menu">

        <li class="<?= ($current_page === 'customer-tender-po-add') ? 'active' : '' ?>">
            <a href="<?= site_url('customer-tender-po-add') ?>">
                <i class="fa fa-plus-square"></i> Add Customer Tender PO
            </a>
        </li>

        <li class="<?= ($current_page === 'customer-tender-po-list') ? 'active' : '' ?>">
            <a href="<?= site_url('customer-tender-po-list') ?>">
                <i class="fa fa-list"></i> Customer Tender PO List
            </a>
        </li>

    </ul>
</li>

<?php /*?>
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


<li class="header">Reports</li>
<li class="treeview <?= in_array($this->uri->segment(1), ['cash-ledger', 'cash-in-statement', 'cash-out-statement','inward-summary','outward-summary','na-cash-in-statement','na-cash-out-statement']) ? 'active' : '' ?>">
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
<?php */ ?>
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
            class="treeview <?= in_array($current_page, ['category-list', 'brand-list', 'items-list', 'uom-list', 'gst-list', 'currency-list']) ? 'active' : '' ?>">
            <a href="#">
                <i class="fa fa-cog"></i> General
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>

            <ul class="treeview-menu">
                <li class="<?= ($current_page === 'category-list') ? 'active' : '' ?>">
                    <a href="<?= site_url('category-list') ?>"><i class="fa fa-folder-open"></i> Category List</a>
                </li>

                <li class="<?= ($current_page === 'brand-list') ? 'active' : '' ?>">
                    <a href="<?= site_url('brand-list') ?>"><i class="fa fa-industry"></i> Brand List</a>
                </li>

                <li class="<?= ($current_page === 'items-list') ? 'active' : '' ?>">
                    <a href="<?= site_url('items-list') ?>"><i class="fa fa-cubes"></i> Items List</a>
                </li>

                <li class="<?= ($current_page === 'uom-list') ? 'active' : '' ?>">
                    <a href="<?= site_url('uom-list') ?>"><i class="fa fa-balance-scale"></i> UOM List</a>
                </li>

                <li class="<?= ($current_page === 'gst-list') ? 'active' : '' ?>">
                    <a href="<?= site_url('gst-list') ?>"><i class="fa fa-percent"></i> VAT List</a>
                </li>

                <li class="<?= ($current_page === 'currency-list') ? 'active' : '' ?>">
                    <a href="<?= site_url('currency-list') ?>"><i class="fa fa-money"></i> Currency List</a>
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
        <?php /*?>
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
<?php */ ?>
    </ul>
</li>