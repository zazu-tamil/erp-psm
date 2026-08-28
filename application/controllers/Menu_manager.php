<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Menu_manager
 *
 * Role-based menu & permission administration (Admin only):
 *   - Menu CRUD + drag-and-drop reorder
 *   - Role CRUD
 *   - Per-role view/add/edit/delete permission matrix
 */
class Menu_manager extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Menu_model');
        $this->load->model('Role_model');
    }

    /* ------------------------------------------------------------------ */
    /*  Guards & helpers                                                  */
    /* ------------------------------------------------------------------ */

    private function _guard()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect('login');
        }
        if (!is_super_admin()) {
            echo '<h3 style="color:red;">Permission Denied</h3>';
            exit;
        }
    }

    private function _json($data, $status = 200)
    {
        $this->output
            ->set_content_type('application/json')
            ->set_status_header($status)
            ->set_output(json_encode($data));
    }

    /**
     * Flat list of slug menus (leaf pages) with breadcrumb paths — used by the
     * permission matrix.
     */
    private function _slug_menu_list()
    {
        $tree = $this->Menu_model->get_tree();
        $list = array();
        $this->_flatten_slugs($tree, '', $list);
        return $list;
    }

    private function _flatten_slugs($items, $path, &$list)
    {
        foreach ($items as $item) {
            $label = ($path === '') ? $item['menu_title'] : $path . ' » ' . $item['menu_title'];
            if (!empty($item['menu_slug'])) {
                $list[] = array(
                    'menu_id'    => $item['menu_id'],
                    'menu_title' => $item['menu_title'],
                    'menu_slug'  => $item['menu_slug'],
                    'path'       => $label,
                );
            }
            if (!empty($item['children'])) {
                $this->_flatten_slugs($item['children'], $label, $list);
            }
        }
    }

    /* ------------------------------------------------------------------ */
    /*  Menu management                                                   */
    /* ------------------------------------------------------------------ */

    public function index()
    {
        $this->_guard();
        $data['js'] = 'menu-list.inc';
        $data['menu_tree'] = $this->Menu_model->get_tree();
        $data['icons'] = $this->_icon_options();
        $this->load->view('page/menu/menu-list', $data);
    }

    public function save_menu()
    {
        $this->_guard();

        $mode       = $this->input->post('mode');
        $parent_id  = (int)$this->input->post('parent_id');
        $menu_title = trim($this->input->post('menu_title'));
        $is_header  = $this->input->post('is_header') ? 1 : 0;
        $menu_slug  = trim($this->input->post('menu_slug'));
        $menu_icon  = trim($this->input->post('menu_icon'));
        $status     = $this->input->post('status') ?: 'Active';

        if ($menu_title === '') {
            $this->_json(array('success' => false, 'msg' => 'Menu title is required.'));
            return;
        }

        $data = array(
            'parent_id'   => $parent_id,
            'menu_title'  => $menu_title,
            'menu_slug'   => ($menu_slug !== '' ? $menu_slug : null),
            'menu_icon'   => ($menu_icon !== '' ? $menu_icon : null),
            'is_header'   => $is_header,
            'status'      => $status,
        );

        if ($mode === 'Edit') {
            $menu_id = (int)$this->input->post('menu_id');
            $this->Menu_model->update($menu_id, $data);
            $this->_json(array('success' => true, 'id' => $menu_id));
        } else {
            // place at end of its siblings
            $this->db->select_max('sort_order', 'mx');
            $this->db->where('parent_id', $parent_id);
            $row = $this->db->get('menu_info')->row();
            $data['sort_order'] = ($row && $row->mx !== null) ? (int)$row->mx + 1 : 1;

            $id = $this->Menu_model->insert($data);
            $this->_json(array('success' => true, 'id' => $id));
        }
    }

    public function delete_menu($menu_id = null)
    {
        $this->_guard();
        $menu_id = (int)$menu_id;
        if ($menu_id <= 0) {
            $this->_json(array('success' => false, 'msg' => 'Invalid menu id.'));
            return;
        }
        $this->Menu_model->soft_delete($menu_id);
        $this->_json(array('success' => true));
    }

    public function reorder_menu()
    {
        $this->_guard();

        $raw = $this->input->post('items');
        $items = json_decode($raw, true);

        if (!is_array($items)) {
            $this->_json(array('success' => false, 'msg' => 'Invalid payload.'));
            return;
        }

        $normalized = array();
        foreach ($items as $it) {
            if (!isset($it['id'])) {
                continue;
            }
            $normalized[] = array(
                'id'         => (int)$it['id'],
                'parent_id'  => (int)(isset($it['parent_id']) ? $it['parent_id'] : 0),
                'sort_order' => (int)(isset($it['sort_order']) ? $it['sort_order'] : 0),
            );
        }

        $this->Menu_model->reorder($normalized);
        $this->_json(array('success' => true));
    }

    /* ------------------------------------------------------------------ */
    /*  Role management                                                   */
    /* ------------------------------------------------------------------ */

    public function role_list()
    {
        $this->_guard();
        $data['js'] = 'role-list.inc';
        $data['roles'] = $this->Role_model->get_all();
        $this->load->view('page/menu/role-list', $data);
    }

    public function save_role()
    {
        $this->_guard();

        $mode       = $this->input->post('mode');
        $role_key   = trim($this->input->post('role_key'));
        $role_name  = trim($this->input->post('role_name'));
        $is_default = $this->input->post('is_default') ? 1 : 0;
        $status     = $this->input->post('status') ?: 'Active';

        if ($role_key === '' || $role_name === '') {
            $this->_json(array('success' => false, 'msg' => 'Role key and name are required.'));
            return;
        }

        // Unique role_key check (excluding self on edit)
        $existing = $this->Role_model->get_by_key($role_key);
        $role_id = ($mode === 'Edit') ? (int)$this->input->post('role_id') : 0;
        if ($existing && (int)$existing['role_id'] !== $role_id) {
            $this->_json(array('success' => false, 'msg' => 'Role key already exists.'));
            return;
        }

        // Clear other defaults if this one becomes default
        if ($is_default) {
            $this->db->update('role_info', array('is_default' => 0));
        }

        $data = array(
            'role_key'   => $role_key,
            'role_name'  => $role_name,
            'is_default' => $is_default,
            'status'     => $status,
        );

        if ($mode === 'Edit') {
            $this->Role_model->update($role_id, $data);
            $this->_json(array('success' => true, 'id' => $role_id));
        } else {
            $this->db->select_max('sort_order', 'mx');
            $row = $this->db->get('role_info')->row();
            $data['sort_order'] = ($row && $row->mx !== null) ? (int)$row->mx + 1 : 1;
            $id = $this->Role_model->insert($data);
            $this->_json(array('success' => true, 'id' => $id));
        }
    }

    public function delete_role($role_id = null)
    {
        $this->_guard();
        $role_id = (int)$role_id;
        if ($role_id <= 0) {
            $this->_json(array('success' => false, 'msg' => 'Invalid role id.'));
            return;
        }

        $role = $this->Role_model->get($role_id);
        if (!$role) {
            $this->_json(array('success' => false, 'msg' => 'Role not found.'));
            return;
        }
        if ($role['role_key'] === 'Admin') {
            $this->_json(array('success' => false, 'msg' => 'The Admin role cannot be deleted.'));
            return;
        }

        $this->Role_model->soft_delete($role_id);
        $this->_json(array('success' => true));
    }

    /* ------------------------------------------------------------------ */
    /*  Role permission matrix                                            */
    /* ------------------------------------------------------------------ */

    public function role_permissions($role_id = null)
    {
        $this->_guard();

        $roles = $this->Role_model->get_all();

        $selected = null;
        if ($role_id !== null && $role_id !== '') {
            $selected = $this->Role_model->get((int)$role_id);
            if (!$selected) {
                $selected = $this->Role_model->get_by_key($role_id);
            }
        }
        if (!$selected) {
            $selected = $this->Role_model->get_by_key('Admin');
            if (!$selected && !empty($roles)) {
                $selected = $roles[0];
            }
        }

        $selected_id = $selected ? (int)$selected['role_id'] : 0;
        $perms = $selected_id ? $this->Role_model->get_permissions($selected_id) : array();

        $data['js']            = 'role-permission.inc';
        $data['roles']         = $roles;
        $data['selected_role'] = $selected;
        $data['perms']         = $perms;
        $data['slug_menus']    = $this->_slug_menu_list();

        $this->load->view('page/menu/role-permission', $data);
    }

    public function save_role_permissions()
    {
        $this->_guard();

        $role_id = (int)$this->input->post('role_id');
        if ($role_id <= 0) {
            $this->_json(array('success' => false, 'msg' => 'Invalid role.'));
            return;
        }

        $role = $this->Role_model->get($role_id);
        if (!$role) {
            $this->_json(array('success' => false, 'msg' => 'Role not found.'));
            return;
        }

        // Prevent a non-admin from stripping Admin access.
        if (strcasecmp($role['role_key'], 'Admin') === 0 || (int)$role['role_id'] === 1) {
            $this->_json(array('success' => false, 'msg' => 'Admin permissions are managed automatically (full access).'));
            return;
        }

        $input = $this->input->post('perm');
        $slug_menus = $this->_slug_menu_list();

        $perms = array();
        foreach ($slug_menus as $menu) {
            $mid = (int)$menu['menu_id'];
            $p = isset($input[$mid]) && is_array($input[$mid]) ? $input[$mid] : array();
            $perms[$mid] = array(
                'view'   => !empty($p['view']),
                'add'    => !empty($p['add']),
                'edit'   => !empty($p['edit']),
                'delete' => !empty($p['delete']),
            );
        }

        $this->Role_model->save_permissions($role_id, $perms);
        $this->_json(array('success' => true, 'msg' => 'Permissions saved successfully.'));
    }

    /* ------------------------------------------------------------------ */
    /*  Icon options for the menu editor                                  */
    /* ------------------------------------------------------------------ */

    private function _icon_options()
    {
        return array(
            'fa fa-dashboard', 'fa fa-file-text-o', 'fa fa-file-text', 'fa fa-folder-open',
            'fa fa-briefcase', 'fa fa-industry', 'fa fa-envelope', 'fa fa-files-o',
            'fa fa-area-chart', 'fa fa-line-chart', 'fa fa-balance-scale', 'fa fa-cubes',
            'fa fa-cog', 'fa fa-cogs', 'fa fa-building', 'fa fa-address-book', 'fa fa-address-card',
            'fa fa-money', 'fa fa-university', 'fa fa-bank', 'fa fa-users', 'fa fa-user',
            'fa fa-user-secret', 'fa fa-key', 'fa fa-shield', 'fa fa-list-alt', 'fa fa-list',
            'fa fa-list-ul', 'fa fa-plus-circle', 'fa fa-plus-square', 'fa fa-exchange',
            'fa fa-tags', 'fa fa-tag', 'fa fa-globe', 'fa fa-clock-o', 'fa fa-sign-out',
        );
    }
}
