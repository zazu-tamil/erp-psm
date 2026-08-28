<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?php echo base_url() ?>/asset/images/user.jpg" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <a href="#">
                    <p><?php echo strtoupper($this->session->userdata(SESS_HD . 'staff_name')); ?></p>
                </a>
                <a href="#"><i class="fa fa-circle text-success"></i> Online </a>

            </div>
        </div>
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
            <?php
            // Dynamic, role-aware menu. Built from menu_info + role_permission.
            $current_page = $this->uri->segment(1, 0);
            $menu_tree = get_menu_tree_for_current_role();
            render_sidebar_menu($menu_tree, $current_page);
            ?>
            <li>
                <a href="<?php echo site_url('logout') ?>">
                    <i class="fa fa-sign-out"></i> <span>Logout</span>
                </a>
            </li>

        </ul>
    </section>
    <!-- /.sidebar -->
</aside>