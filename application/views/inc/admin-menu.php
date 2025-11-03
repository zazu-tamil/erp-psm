<?php
// Define master menu pages
$g_master = ['company-list', 'category-list', 'brand-list', 'items-list', 'uom-list', 'gst-list', 'user-list', 'vendor-list'];

// Get current page segment
$current_page = $this->uri->segment(1, 0);
?>

<!-- Dashboard -->
<li class="header">Dashboard</li>
<li class="<?= ($current_page === 'dash') ? 'active' : '' ?>">
    <a href="<?= site_url('dash') ?>">
        <i class="fa fa-chart-line"></i> 
        <span>Dashboard</span>
    </a>
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
                <i class="fa fa-tags"></i> Category List
            </a>
        </li>
        <li class="<?= ($current_page === 'brand-list') ? 'active' : '' ?>">
            <a href="<?= site_url('brand-list') ?>">
                <i class="fa fa-tags"></i> Brand List
            </a>
        </li>
        <li class="<?= ($current_page === 'items-list') ? 'active' : '' ?>">
            <a href="<?= site_url('items-list') ?>">
                <i class="fa fa-tags"></i> items List
            </a>
        </li>
        <li class="<?= ($current_page === 'uom-list') ? 'active' : '' ?>">
            <a href="<?= site_url('uom-list') ?>">
                <i class="fa fa-tags"></i> UOM List
            </a>
        </li>
        <li class="<?= ($current_page === 'gst-list') ? 'active' : '' ?>">
            <a href="<?= site_url('gst-list') ?>">
                <i class="fa fa-tags"></i> VAT List
            </a>
        </li>
        <li class="<?= ($current_page === 'user-list') ? 'active' : '' ?>">
            <a href="<?= site_url('user-list') ?>">
                <i class="fa fa-tags"></i> user List
            </a>
        </li>
        <li class="<?= ($current_page === 'vendor-list') ? 'active' : '' ?>">
            <a href="<?= site_url('vendor-list') ?>">
                <i class="fa fa-tags"></i> Vendor List
            </a>
        </li>
       
    </ul>
</li>
