<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * permission_helper.php
 *
 * Role-based access helpers used across views and controllers.
 *
 *   has_perm('user-list', 'add')          -> bool (is the "Add" action allowed?)
 *   get_menu_tree_for_current_role()      -> nested menu tree for the left sidebar
 *   require_perm('user-list','view')      -> die with "Permission Denied" if not
 *   authorize_page()                      -> central gate for page-level access
 *
 * The "Admin" role always has full access (super-admin bypass).
 */

if (!function_exists('get_current_role_info')) {
    function get_current_role_info()
    {
        static $cached_role = null;
        if ($cached_role !== null) {
            return $cached_role;
        }

        $CI =& get_instance();
        $level = $CI->session->userdata(SESS_HD . 'level');
        if ($level === null || $level === '') {
            $cached_role = false;
            return false;
        }

        $CI->load->database();

        // 1. Try matching role_key directly (e.g. 'Staff', 'Admin', 'Supervisor')
        $row = $CI->db->where('role_key', $level)
                      ->where('status !=', 'Delete')
                      ->get('role_info')
                      ->row_array();

        // 2. If not found and level is numeric, try matching role_id (e.g. 2)
        if (!$row && is_numeric($level)) {
            $row = $CI->db->where('role_id', (int)$level)
                          ->where('status !=', 'Delete')
                          ->get('role_info')
                          ->row_array();
        }

        // 3. Case-insensitive role_key or role_name match
        if (!$row) {
            $row = $CI->db->group_start()
                          ->where('LOWER(role_key)', strtolower($level))
                          ->or_where('LOWER(role_name)', strtolower($level))
                          ->group_end()
                          ->where('status !=', 'Delete')
                          ->get('role_info')
                          ->row_array();
        }

        if ($row) {
            $cached_role = $row;
            return $cached_role;
        }

        // Fallback placeholder if no database row matches
        $cached_role = array(
            'role_id'    => is_numeric($level) ? (int)$level : 0,
            'role_key'   => $level,
            'role_name'  => $level,
            'is_default' => 0,
            'status'     => 'Active'
        );
        return $cached_role;
    }
}

if (!function_exists('current_role_key')) {
    function current_role_key()
    {
        $role = get_current_role_info();
        if ($role && !empty($role['role_key'])) {
            return $role['role_key'];
        }
        $CI =& get_instance();
        return $CI->session->userdata(SESS_HD . 'level');
    }
}

if (!function_exists('current_role_id')) {
    function current_role_id()
    {
        $role = get_current_role_info();
        return ($role && !empty($role['role_id'])) ? (int)$role['role_id'] : 0;
    }
}

if (!function_exists('is_super_admin')) {
    function is_super_admin()
    {
        $role = get_current_role_info();
        if (!$role) {
            $CI =& get_instance();
            $level = $CI->session->userdata(SESS_HD . 'level');
            return ($level === 'Admin' || $level === '1' || $level === 1);
        }
        return (strcasecmp($role['role_key'], 'Admin') === 0 || (int)$role['role_id'] === 1);
    }
}

if (!function_exists('get_role_permission_map')) {
    function get_role_permission_map()
    {
        static $map = null;
        if ($map !== null) {
            return $map;
        }

        $map = array();
        $role = get_current_role_info();
        if (!$role || empty($role['role_id'])) {
            return $map;
        }

        $role_id = (int)$role['role_id'];
        $CI =& get_instance();
        $CI->load->database();

        $sql = "SELECT m.menu_slug, p.can_view, p.can_add, p.can_edit, p.can_delete
                FROM role_permission p
                JOIN menu_info m ON m.menu_id = p.menu_id
                WHERE p.role_id = ? AND (m.status IS NULL OR m.status != 'Delete')";
        $query = $CI->db->query($sql, array($role_id));

        foreach ($query->result_array() as $row) {
            $slug = $row['menu_slug'];
            if ($slug === null || $slug === '') {
                continue;
            }
            $map[$slug] = array(
                'view'   => (int)$row['can_view'],
                'add'    => (int)$row['can_add'],
                'edit'   => (int)$row['can_edit'],
                'delete' => (int)$row['can_delete'],
            );
        }

        return $map;
    }
}

if (!function_exists('has_perm')) {
    function has_perm($slug, $action = 'view')
    {
        if (is_super_admin()) {
            return true;
        }

        // Always allow base routes
        if ($slug === 'dash' || $slug === 'dashboard' || $slug === 'change-password' || $slug === 'logout' || $slug === 'user-guide') {
            return true;
        }

        $map = get_role_permission_map();

        // Direct slug match
        if (isset($map[$slug])) {
            return !empty($map[$slug][$action]);
        }

        // Slug aliases / sub-actions mapping
        $aliases = array(
            'add-tender-enquiry'             => array('slug' => 'tender-enquiry-list', 'action' => 'add'),
            'tender-enquiry-edit'            => array('slug' => 'tender-enquiry-list', 'action' => 'edit'),
            'tender-quotation-add'           => array('slug' => 'tender-quotation-list', 'action' => 'add'),
            'tender-quotation-edit'          => array('slug' => 'tender-quotation-list', 'action' => 'edit'),
            'customer-tender-po-add'         => array('slug' => 'customer-tender-po-list', 'action' => 'add'),
            'customer-tender-po-edit'        => array('slug' => 'customer-tender-po-list', 'action' => 'edit'),
            'tender-dc-add'                  => array('slug' => 'tender-dc-list', 'action' => 'add'),
            'tender-dc-edit'                 => array('slug' => 'tender-dc-list', 'action' => 'edit'),
            'tender-invoice-add'             => array('slug' => 'tender-invoice-list', 'action' => 'add'),
            'tender-po-invoice-edit'         => array('slug' => 'tender-invoice-list', 'action' => 'edit'),
            'vendor-rate-enquiry'            => array('slug' => 'vendor-rate-enquiry-list', 'action' => 'add'),
            'vendor-rate-enquiry-edit'       => array('slug' => 'vendor-rate-enquiry-list', 'action' => 'edit'),
            'vendor-quotation-add'           => array('slug' => 'vendor-quotation-list', 'action' => 'add'),
            'vendor-quotation-edit'          => array('slug' => 'vendor-quotation-list', 'action' => 'edit'),
            'vendor-po-add'                  => array('slug' => 'vendor-po-list', 'action' => 'add'),
            'vendor-po-edit'                 => array('slug' => 'vendor-po-list', 'action' => 'edit'),
            'vendor-pur-inward-add'          => array('slug' => 'vendor-pur-inward-list', 'action' => 'add'),
            'vendor-pur-inward-edit'         => array('slug' => 'vendor-pur-inward-list', 'action' => 'edit'),
            'vendor-purchase-bill-add'       => array('slug' => 'vendor-purchase-bill-list', 'action' => 'add'),
            'vendor-purchase-bill-edit'      => array('slug' => 'vendor-purchase-bill-list', 'action' => 'edit'),
            'credit-debit-note-add'          => array('slug' => 'credit-debit-note-list', 'action' => 'add'),
            'credit-debit-note-edit'         => array('slug' => 'credit-debit-note-list', 'action' => 'edit'),
            'customer-invoice-pending-report' => array('slug' => 'customer-pending-invoice-report', 'action' => 'view'),
            'vendor-invoice-pending-report'  => array('slug' => 'vendor-pending-invoice-report', 'action' => 'view'),
        );

        if (isset($aliases[$slug])) {
            $mapped_slug = $aliases[$slug]['slug'];
            $req_action = ($action === 'view') ? $aliases[$slug]['action'] : $action;
            if (isset($map[$mapped_slug])) {
                if ($action === 'view') {
                    return !empty($map[$mapped_slug]['view']) || !empty($map[$mapped_slug][$req_action]);
                }
                return !empty($map[$mapped_slug][$req_action]);
            }
        }

        return false;
    }
}

if (!function_exists('require_perm')) {
    function require_perm($slug, $action = 'view', $msg = 'Permission Denied')
    {
        if (!has_perm($slug, $action)) {
            echo '<h3 style="color:red;">' . htmlspecialchars($msg) . '</h3>';
            exit;
        }
        return true;
    }
}

if (!function_exists('registered_menu_slugs')) {
    function registered_menu_slugs()
    {
        static $slugs = null;
        if ($slugs !== null) {
            return $slugs;
        }
        $CI =& get_instance();
        $CI->load->database();
        $CI->db->select('menu_slug');
        $CI->db->where('status !=', 'Delete');
        $CI->db->where('menu_slug IS NOT NULL');
        $CI->db->where("menu_slug != ''");
        $slugs = array_column($CI->db->get('menu_info')->result_array(), 'menu_slug');
        return $slugs;
    }
}

/**
 * Central page-access gate.
 *
 * Returns true when the current request is allowed for the logged-in role:
 *   - super admin always allowed
 *   - a request whose first URI segment is NOT a registered menu page is
 *     treated as internal plumbing (AJAX/sub-page) and allowed
 *   - otherwise the role must have can_view for that menu slug
 */
if (!function_exists('authorize_page')) {
    function authorize_page()
    {
        if (is_super_admin()) {
            return true;
        }
        $CI =& get_instance();
        $slug = $CI->uri->segment(1, 0);
        if (!$slug) {
            return true;
        }

        // Basic exempt routes
        if ($slug === 'dash' || $slug === 'dashboard' || $slug === 'change-password' || $slug === 'logout' || $slug === 'user-guide') {
            return true;
        }

        if (!in_array($slug, registered_menu_slugs(), true)) {
            return true;
        }
        return has_perm($slug, 'view');
    }
}

if (!function_exists('get_menu_tree_for_current_role')) {
    function get_menu_tree_for_current_role()
    {
        $CI =& get_instance();
        $CI->load->model('Menu_model');

        if (is_super_admin()) {
            return $CI->Menu_model->get_tree();
        }

        $role = get_current_role_info();
        if (!$role || empty($role['role_id'])) {
            return array();
        }

        return $CI->Menu_model->get_tree_for_role_id((int)$role['role_id']);
    }
}

/**
 * Render the sidebar menu tree for AdminLTE (Bootstrap 3 / AdminLTE treeview).
 * Called from views/inc/left-menu.php.
 */
if (!function_exists('render_sidebar_menu')) {
    function render_sidebar_menu($items, $current_page)
    {
        foreach ($items as $item) {

            // Section header label
            if (!empty($item['is_header'])) {
                echo '<li class="header">' . htmlspecialchars($item['menu_title']) . '</li>' . "\n";
                continue;
            }

            $has_children = !empty($item['children']);

            // Collect slugs in this subtree for the active state.
            $slugs = collect_menu_slugs($item);

            if ($has_children) {
                $active = in_array($current_page, $slugs) ? 'active' : '';
                echo '<li class="treeview ' . $active . '">' . "\n";
                echo '  <a href="#"><i class="' . htmlspecialchars($item['menu_icon']) . '"></i>'
                    . ' <span>' . htmlspecialchars($item['menu_title']) . '</span>'
                    . ' <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>' . "\n";
                echo '  <ul class="treeview-menu">' . "\n";
                render_sidebar_menu($item['children'], $current_page);
                echo '  </ul>' . "\n";
                echo '</li>' . "\n";
            } else {
                $active = ($current_page === $item['menu_slug']) ? 'active' : '';
                $url = $item['menu_slug'] ? site_url($item['menu_slug']) : '#';
                echo '<li class="' . $active . '">' . "\n";
                echo '  <a href="' . $url . '"><i class="' . htmlspecialchars($item['menu_icon']) . '"></i>'
                    . ' <span>' . htmlspecialchars($item['menu_title']) . '</span></a>' . "\n";
                echo '</li>' . "\n";
            }
        }
    }
}

if (!function_exists('collect_menu_slugs')) {
    function collect_menu_slugs($item)
    {
        $slugs = array();
        if (!empty($item['menu_slug'])) {
            $slugs[] = $item['menu_slug'];
        }
        if (!empty($item['children'])) {
            foreach ($item['children'] as $child) {
                $slugs = array_merge($slugs, collect_menu_slugs($child));
            }
        }
        return $slugs;
    }
}
