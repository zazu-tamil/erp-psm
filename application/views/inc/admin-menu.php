<?php
// Define master menu pages
$g_master = ['company-list', 'category-list', 'brand-list', 'items-list', 'uom-list', 'gst-list', 'user-list', 'vendor-list', 'customer-list'];

// Get current page segment
$current_page = $this->uri->segment(1, 0);
?>

<!-- Dashboard -->
<li class="header">Dashboard</li>
<li class="<?= ($current_page === 'dash') ? 'active' : '' ?>">
    <a href="<?= site_url('dash') ?>">
        <i class="fa fa-line-chart"></i>
        <span>Dashboard</span>
    </a>
</li>

<!-- Tender Enquiry Section -->
<li class="header">Tender Enquiry</li>
<li class="treeview <?= in_array($current_page, ['add-tender-enquiry', 'tender-enquiry-list']) ? 'active' : '' ?>">
    <a href="#">
        <i class="fa fa-file-text-o"></i>
        <span>Tender Enquiry</span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
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
<!-- Vendor Enquiry Section -->
<li class="header">Vendor Enquiry</li>

<li
    class="treeview <?= in_array($current_page, ['vendor-rate-enquiry-list', 'vendor-rate-enquiry']) ? 'active' : '' ?>">
    <a href="#">
        <i class="fa fa-handshake-o"></i>
        <span>Vendor Enquiry</span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>

    <ul class="treeview-menu">
        <li class="<?= ($current_page === 'vendor-rate-enquiry') ? 'active' : '' ?>">
            <a href="<?= site_url('vendor-rate-enquiry') ?>">
                <i class="fa fa-file-text-o"></i> Add Vendor Enquiry
            </a>
        </li>

        <li class="<?= ($current_page === 'vendor-rate-enquiry-list') ? 'active' : '' ?>">
            <a href="<?= site_url('vendor-rate-enquiry-list') ?>">
                <i class="fa fa-list-alt"></i> Vendor Enquiry List
            </a>
        </li>
    </ul>
</li>
<!-- tender_quotation_info -->
<li class="header">Tender Quotation</li>

<li class="treeview <?= in_array($current_page, ['tender-quotation-list', 'tender-quotation-add']) ? 'active' : '' ?>">
    <a href="#">
        <i class="fa fa-file-text"></i>
        <span>Tender Quotation</span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>

    <ul class="treeview-menu">
        <li class="<?= ($current_page === 'tender-quotation-add') ? 'active' : '' ?>">
            <a href="<?= site_url('tender-quotation-add') ?>">
                <i class="fa fa-plus-circle"></i> Add Tender Quotation
            </a>
        </li>

        <li class="<?= ($current_page === 'tender-quotation-list') ? 'active' : '' ?>">
            <a href="<?= site_url('tender-quotation-list') ?>">
                <i class="fa fa-list-ul"></i> Vendor Enquiry List
            </a>
        </li>
    </ul>
</li>





<!-- Master Section -->
<li class="header">Master</li>
<li class="treeview <?= in_array($current_page, $g_master) ? 'active' : '' ?>">
    <a href="#">
        <i class="fa fa-database"></i>
        <span>Master</span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li class="<?= ($current_page === 'company-list') ? 'active' : '' ?>">
            <a href="<?= site_url('company-list') ?>">
                <i class="fa fa-building"></i> Company Info
            </a>
        </li>
        <li class="<?= ($current_page === 'category-list') ? 'active' : '' ?>">
            <a href="<?= site_url('category-list') ?>">
                <i class="fa fa-folder-open"></i> Category List
            </a>
        </li>
        <li class="<?= ($current_page === 'brand-list') ? 'active' : '' ?>">
            <a href="<?= site_url('brand-list') ?>">
                <i class="fa fa-industry"></i> Brand List
            </a>
        </li>
        <li class="<?= ($current_page === 'items-list') ? 'active' : '' ?>">
            <a href="<?= site_url('items-list') ?>">
                <i class="fa fa-cubes"></i> Items List
            </a>
        </li>
        <li class="<?= ($current_page === 'uom-list') ? 'active' : '' ?>">
            <a href="<?= site_url('uom-list') ?>">
                <i class="fa fa-balance-scale"></i> UOM List
            </a>
        </li>
        <li class="<?= ($current_page === 'gst-list') ? 'active' : '' ?>">
            <a href="<?= site_url('gst-list') ?>">
                <i class="fa fa-percent"></i> VAT List
            </a>
        </li>
        <li class="<?= ($current_page === 'user-list') ? 'active' : '' ?>">
            <a href="<?= site_url('user-list') ?>">
                <i class="fa fa-user"></i> User List
            </a>
        </li>
        <li class="<?= ($current_page === 'vendor-list') ? 'active' : '' ?>">
            <a href="<?= site_url('vendor-list') ?>">
                <i class="fa fa-users"></i> Vendor List
            </a>
        </li>
        <li class="<?= ($current_page === 'customer-list') ? 'active' : '' ?>">
            <a href="<?= site_url('customer-list') ?>">
                <i class="fa fa-users"></i> <span>Customer List</span>
            </a>
        </li>

    </ul>
</li>