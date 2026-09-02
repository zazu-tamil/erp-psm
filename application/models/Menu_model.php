<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Menu_model
 *
 * Dynamic, tree-based menu store. Menus are stored in `menu_info` as a
 * parent/child tree. `is_header` marks a section label (non-clickable).
 */
class Menu_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * All active menus, ordered by parent + sort order.
     */
    public function get_all()
    {
        $this->ensure_pending_reports_menu();
        $this->db->where('status !=', 'Delete');
        $this->db->order_by('parent_id', 'ASC');
        $this->db->order_by('sort_order', 'ASC');
        $this->db->order_by('menu_id', 'ASC');
        return $this->db->get('menu_info')->result_array();
    }

    /**
     * Dynamically ensures that Customer and Vendor Pending Reports exist in menu_info
     * and that all active roles have view permissions.
     */
    public function ensure_pending_reports_menu()
    {
        // 1. Find parent report menu (Tender Info Report, or Reports)
        $parent_id = 0;
        $parent = $this->db->where('menu_slug', 'tender-enquiry-timeline')->where('status !=', 'Delete')->get('menu_info')->row_array();
        if (!empty($parent['parent_id'])) {
            $parent_id = (int)$parent['parent_id'];
        } else {
            $parent = $this->db->where('menu_title', 'Tender Info Report')->where('parent_id >', 0)->where('status !=', 'Delete')->get('menu_info')->row_array();
            if (!empty($parent['menu_id'])) {
                $parent_id = (int)$parent['menu_id'];
            }
        }

        if ($parent_id <= 0) {
            return;
        }

        // 2. Ensure Customer Pending Report menu
        $c_menu = $this->db->where('menu_slug', 'customer-pending-invoice-report')->where('status !=', 'Delete')->get('menu_info')->row_array();
        if (!$c_menu) {
            $this->db->insert('menu_info', array(
                'parent_id' => $parent_id,
                'menu_title' => 'Customer Pending Report',
                'menu_slug' => 'customer-pending-invoice-report',
                'menu_icon' => 'fa fa-file-text',
                'is_header' => 0,
                'sort_order' => 53,
                'status' => 'Active'
            ));
            $c_menu_id = $this->db->insert_id();
        } else {
            $c_menu_id = $c_menu['menu_id'];
        }

        // 3. Ensure Vendor Pending Report menu
        $v_menu = $this->db->where('menu_slug', 'vendor-pending-invoice-report')->where('status !=', 'Delete')->get('menu_info')->row_array();
        if (!$v_menu) {
            $this->db->insert('menu_info', array(
                'parent_id' => $parent_id,
                'menu_title' => 'Vendor Pending Report',
                'menu_slug' => 'vendor-pending-invoice-report',
                'menu_icon' => 'fa fa-file-text',
                'is_header' => 0,
                'sort_order' => 54,
                'status' => 'Active'
            ));
            $v_menu_id = $this->db->insert_id();
        } else {
            $v_menu_id = $v_menu['menu_id'];
        }

        // 4. Ensure permissions in role_permission for all active roles
        $roles = $this->db->where('status !=', 'Delete')->get('role_info')->result_array();
        if (!empty($roles)) {
            foreach ($roles as $role) {
                $role_id = (int)$role['role_id'];
                foreach (array($c_menu_id, $v_menu_id) as $mid) {
                    if ($mid > 0) {
                        $has_perm = $this->db->where('role_id', $role_id)->where('menu_id', $mid)->count_all_results('role_permission');
                        if ($has_perm == 0) {
                            $this->db->insert('role_permission', array(
                                'role_id' => $role_id,
                                'menu_id' => $mid,
                                'can_view' => 1,
                                'can_add' => 1,
                                'can_edit' => 1,
                                'can_delete' => 1
                            ));
                        }
                    }
                }
            }
        }
    }

    /**
     * Full active menu tree (all roles / super admin).
     */
    public function get_tree()
    {
        return $this->build_tree($this->get_all(), 0);
    }

    /**
     * Menu tree filtered to what a role may view, specified by role_key or numeric role_id.
     */
    public function get_tree_for_role($role_key)
    {
        $role_id = 0;
        if (is_numeric($role_key)) {
            $role_id = (int)$role_key;
        } else {
            $this->db->select('role_id');
            $this->db->group_start();
            $this->db->where('role_key', $role_key);
            $this->db->or_where('LOWER(role_key)', strtolower($role_key));
            $this->db->or_where('role_name', $role_key);
            $this->db->group_end();
            $this->db->where('status !=', 'Delete');
            $role = $this->db->get('role_info')->row();
            if ($role) {
                $role_id = (int)$role->role_id;
            }
        }

        if ($role_id <= 0) {
            return array();
        }

        return $this->get_tree_for_role_id($role_id);
    }

    /**
     * Menu tree filtered to what a role may view by role_id.
     *
     * Menus granted can_view=1 (or any action permission) plus their ancestors are kept.
     * Section headers (is_header=1) are retained when at least one top-level item in that
     * section is visible.
     */
    public function get_tree_for_role_id($role_id)
    {
        $role_id = (int)$role_id;
        if ($role_id <= 0) {
            return array();
        }

        $sql = "SELECT m.menu_id
                FROM menu_info m
                JOIN role_permission p ON p.menu_id = m.menu_id
                WHERE p.role_id = ? AND (p.can_view = 1 OR p.can_add = 1 OR p.can_edit = 1 OR p.can_delete = 1)
                  AND (m.status IS NULL OR m.status != 'Delete')";
        $visible = $this->db->query($sql, array($role_id))->result_array();
        $visible_ids = array_column($visible, 'menu_id');

        $all = $this->get_all();
        $by_id = array();
        foreach ($all as $row) {
            $by_id[$row['menu_id']] = $row;
        }

        // Keep visible ids + their ancestor chain.
        $keep = array();
        foreach ($visible_ids as $id) {
            $cur = (int)$id;
            while ($cur && isset($by_id[$cur])) {
                $keep[$cur] = true;
                $parent = (int)$by_id[$cur]['parent_id'];
                if ($parent === 0) {
                    break;
                }
                $cur = $parent;
            }
        }

        // Keep a section header if any following top-level sibling (until the
        // next header) is visible.
        $top = array();
        foreach ($all as $row) {
            if ((int)$row['parent_id'] === 0) {
                $top[] = $row;
            }
        }
        $count = count($top);
        for ($i = 0; $i < $count; $i++) {
            if ((int)$top[$i]['is_header'] !== 1) {
                continue;
            }
            $show = false;
            for ($j = $i + 1; $j < $count; $j++) {
                if ((int)$top[$j]['is_header'] === 1) {
                    break;
                }
                if (isset($keep[$top[$j]['menu_id']])) {
                    $show = true;
                    break;
                }
            }
            if ($show) {
                $keep[$top[$i]['menu_id']] = true;
            }
        }

        $filtered = array();
        foreach ($all as $row) {
            if (isset($keep[$row['menu_id']])) {
                $filtered[] = $row;
            }
        }

        return $this->build_tree($filtered, 0);
    }

    /**
     * Build a nested tree from a flat list.
     */
    private function build_tree($rows, $parent_id)
    {
        $tree = array();
        foreach ($rows as $row) {
            if ((int)$row['parent_id'] === (int)$parent_id) {
                $row['children'] = $this->build_tree($rows, (int)$row['menu_id']);
                $tree[] = $row;
            }
        }
        return $tree;
    }

    public function get($menu_id)
    {
        return $this->db->get_where('menu_info', array('menu_id' => $menu_id))->row_array();
    }

    public function insert($data)
    {
        if (!isset($data['created_date'])) {
            $data['created_date'] = date('Y-m-d H:i:s');
        }
        $this->db->insert('menu_info', $data);
        return $this->db->insert_id();
    }

    public function update($menu_id, $data)
    {
        $data['updated_date'] = date('Y-m-d H:i:s');
        $this->db->where('menu_id', $menu_id);
        return $this->db->update('menu_info', $data);
    }

    /**
     * Soft-delete a menu and its descendants.
     */
    public function soft_delete($menu_id)
    {
        $this->db->where('menu_id', $menu_id);
        $this->db->or_where('parent_id', $menu_id);
        return $this->db->update('menu_info', array('status' => 'Delete'));
    }

    /**
     * Persist a reorder payload.
     * $items is a list of arrays: ['id'=>, 'parent_id'=>, 'sort_order'=>].
     */
    public function reorder($items)
    {
        foreach ($items as $it) {
            $this->db->where('menu_id', (int)$it['id']);
            $this->db->update('menu_info', array(
                'parent_id'   => (int)$it['parent_id'],
                'sort_order'  => (int)$it['sort_order'],
                'updated_date'=> date('Y-m-d H:i:s'),
            ));
        }
        return true;
    }

    /**
     * All menus that have a slug (leaf/pages) — used for the permission matrix
     * and for enforcing view access.
     */
    public function get_slugs()
    {
        $this->db->where('status !=', 'Delete');
        $this->db->where('menu_slug IS NOT NULL');
        $this->db->where("menu_slug != ''");
        $this->db->order_by('menu_id', 'ASC');
        return $this->db->get('menu_info')->result_array();
    }
}
