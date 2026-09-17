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
        $this->ensure_split_reports_menu();
        $this->ensure_item_inward_outward_menu();
        $this->db->where('status !=', 'Delete');
        $this->db->order_by('parent_id', 'ASC');
        $this->db->order_by('sort_order', 'ASC');
        $this->db->order_by('menu_id', 'ASC');
        return $this->db->get('menu_info')->result_array();
    }

    /**
     * Dynamically ensures that Reports menu is split into Tender Report and Supplier Report in menu_info,
     * cleans up any duplicate pending/invoice report rows, and ensures view permissions for all roles.
     */
    public function ensure_split_reports_menu()
    {
        // 1. Find main "Reports" top-level dropdown menu (parent_id = 0, is_header = 0)
        $reports_main = $this->db->where('parent_id', 0)
                                 ->where('is_header', 0)
                                 ->group_start()
                                     ->where('menu_title', 'Reports')
                                     ->or_where('menu_icon', 'fa fa-area-chart')
                                 ->group_end()
                                 ->where('status !=', 'Delete')
                                 ->get('menu_info')
                                 ->row_array();

        if (!$reports_main) {
            return;
        }
        $reports_main_id = (int)$reports_main['menu_id'];

        // 2. Reparent any orphaned or mis-parented Tender Report / Supplier Report under $reports_main_id
        $this->db->where_in('menu_title', array('Tender Report', 'Supplier Report'))
                 ->where('is_header', 0)
                 ->where('status !=', 'Delete')
                 ->update('menu_info', array('parent_id' => $reports_main_id));

        // 3. Find or create "Tender Report" parent under $reports_main_id
        $tender_parent = $this->db->where('parent_id', $reports_main_id)
                                  ->where('menu_title', 'Tender Report')
                                  ->where('status !=', 'Delete')
                                  ->get('menu_info')
                                  ->row_array();

        if ($tender_parent) {
            $tender_parent_id = (int)$tender_parent['menu_id'];
            $this->db->where('menu_id', $tender_parent_id)->update('menu_info', array(
                'parent_id'  => $reports_main_id,
                'menu_icon'  => 'fa fa-file-text-o',
                'sort_order' => 1
            ));
        } else {
            // Check if legacy Tender Info Report exists under $reports_main_id
            $legacy = $this->db->where('parent_id', $reports_main_id)
                               ->where('menu_title', 'Tender Info Report')
                               ->where('status !=', 'Delete')
                               ->get('menu_info')
                               ->row_array();
            if ($legacy) {
                $this->db->where('menu_id', $legacy['menu_id'])->update('menu_info', array(
                    'menu_title' => 'Tender Report',
                    'menu_icon'  => 'fa fa-file-text-o',
                    'sort_order' => 1
                ));
                $tender_parent_id = (int)$legacy['menu_id'];
            } else {
                $this->db->insert('menu_info', array(
                    'parent_id'   => $reports_main_id,
                    'menu_title'  => 'Tender Report',
                    'menu_slug'   => null,
                    'menu_icon'   => 'fa fa-file-text-o',
                    'is_header'   => 0,
                    'sort_order'  => 1,
                    'status'      => 'Active'
                ));
                $tender_parent_id = $this->db->insert_id();
            }
        }

        // Clean up any extra legacy "Tender Info Report" under $reports_main_id
        $this->db->where('parent_id', $reports_main_id)
                 ->where('menu_title', 'Tender Info Report')
                 ->where('menu_id !=', $tender_parent_id)
                 ->update('menu_info', array('status' => 'Delete'));

        // 4. Find or create "Supplier Report" parent under $reports_main_id
        $supplier_parent = $this->db->where('parent_id', $reports_main_id)
                                    ->where('menu_title', 'Supplier Report')
                                    ->where('status !=', 'Delete')
                                    ->get('menu_info')
                                    ->row_array();

        if ($supplier_parent) {
            $supplier_parent_id = (int)$supplier_parent['menu_id'];
            $this->db->where('menu_id', $supplier_parent_id)->update('menu_info', array(
                'parent_id'  => $reports_main_id,
                'menu_icon'  => 'fa fa-industry',
                'sort_order' => 2
            ));
        } else {
            $this->db->insert('menu_info', array(
                'parent_id'   => $reports_main_id,
                'menu_title'  => 'Supplier Report',
                'menu_slug'   => null,
                'menu_icon'   => 'fa fa-industry',
                'is_header'   => 0,
                'sort_order'  => 2,
                'status'      => 'Active'
            ));
            $supplier_parent_id = $this->db->insert_id();
        }

        // 5. Clean up duplicate customer/vendor pending/invoice report rows
        $this->db->where_in('menu_slug', array('customer-invoice-pending-report', 'vendor-invoice-pending-report'))
                 ->update('menu_info', array('status' => 'Delete'));

        // 6. Ensure customer-pending-invoice-report exists
        $c_menu = $this->db->where('menu_slug', 'customer-pending-invoice-report')
                           ->where('status !=', 'Delete')
                           ->get('menu_info')
                           ->row_array();
        if (!$c_menu) {
            $this->db->insert('menu_info', array(
                'parent_id'  => $tender_parent_id,
                'menu_title' => 'Customer Pending Report',
                'menu_slug'  => 'customer-pending-invoice-report',
                'menu_icon'  => 'fa fa-file-text',
                'is_header'  => 0,
                'sort_order' => 4,
                'status'     => 'Active'
            ));
            $c_menu_id = $this->db->insert_id();
        } else {
            $c_menu_id = (int)$c_menu['menu_id'];
            $this->db->where('menu_id', $c_menu_id)->update('menu_info', array(
                'parent_id'  => $tender_parent_id,
                'menu_title' => 'Customer Pending Report',
                'menu_icon'  => 'fa fa-file-text'
            ));
        }

        // 7. Ensure vendor-pending-invoice-report exists
        $v_menu = $this->db->where('menu_slug', 'vendor-pending-invoice-report')
                           ->where('status !=', 'Delete')
                           ->get('menu_info')
                           ->row_array();
        if (!$v_menu) {
            $this->db->insert('menu_info', array(
                'parent_id'  => $supplier_parent_id,
                'menu_title' => 'Vendor Pending Report',
                'menu_slug'  => 'vendor-pending-invoice-report',
                'menu_icon'  => 'fa fa-file-text',
                'is_header'  => 0,
                'sort_order' => 1,
                'status'     => 'Active'
            ));
            $v_menu_id = $this->db->insert_id();
        } else {
            $v_menu_id = (int)$v_menu['menu_id'];
            $this->db->where('menu_id', $v_menu_id)->update('menu_info', array(
                'parent_id'  => $supplier_parent_id,
                'menu_title' => 'Vendor Pending Report',
                'menu_icon'  => 'fa fa-file-text'
            ));
        }

        // 7b. Ensure vendor-balance-report exists
        $vb_menu = $this->db->where('menu_slug', 'vendor-balance-report')
                            ->where('status !=', 'Delete')
                            ->get('menu_info')
                            ->row_array();
        if (!$vb_menu) {
            $this->db->insert('menu_info', array(
                'parent_id'   => $supplier_parent_id,
                'menu_title'  => 'Vendor Balance Report',
                'menu_slug'   => 'vendor-balance-report',
                'menu_icon'   => 'fa fa-balance-scale',
                'is_header'   => 0,
                'sort_order'  => 3,
                'status'      => 'Active'
            ));
            $vb_menu_id = $this->db->insert_id();
        } else {
            $vb_menu_id = (int)$vb_menu['menu_id'];
            $this->db->where('menu_id', $vb_menu_id)->update('menu_info', array(
                'parent_id'  => $supplier_parent_id,
                'menu_title' => 'Vendor Balance Report',
                'menu_icon'  => 'fa fa-balance-scale',
                'sort_order' => 3,
                'status'     => 'Active'
            ));
        }

        // 8. Update parent_id for Tender Reports
        $tender_slugs = array(
            'tender-enquiry-timeline',
            'tender-enquiry-summary-report',
            'item-rate-report',
            'customer-pending-invoice-report',
            'customer-statement-report',
            'invoice-report',
            'tender-progress-report'
        );
        $this->db->where_in('menu_slug', $tender_slugs)
                 ->where('status !=', 'Delete')
                 ->update('menu_info', array('parent_id' => $tender_parent_id));

        // 9. Update parent_id for Supplier Reports
        $supplier_slugs = array(
            'vendor-pending-invoice-report',
            'vendor-statement-report',
            'vendor-balance-report',
            'supplier-summary-report'
        );
        $this->db->where_in('menu_slug', $supplier_slugs)
                 ->where('status !=', 'Delete')
                 ->update('menu_info', array('parent_id' => $supplier_parent_id));

        // 10. Fix sort orders under Reports dropdown
        $this->db->where('menu_id', $tender_parent_id)->update('menu_info', array('sort_order' => 1));
        $this->db->where('menu_id', $supplier_parent_id)->update('menu_info', array('sort_order' => 2));
        $this->db->where('parent_id', $reports_main_id)->where('menu_title', 'NBR Report')->update('menu_info', array('sort_order' => 3));
        $this->db->where('parent_id', $reports_main_id)->where('menu_slug', 'pl-report')->update('menu_info', array('sort_order' => 4));
        $this->db->where('parent_id', $reports_main_id)->where('menu_slug', 'account-trial-balance')->update('menu_info', array('sort_order' => 5));

        // Fix sort orders inside Supplier Report
        $this->db->where('menu_slug', 'vendor-pending-invoice-report')->update('menu_info', array('sort_order' => 1));
        $this->db->where('menu_slug', 'vendor-statement-report')->update('menu_info', array('sort_order' => 2));
        $this->db->where('menu_slug', 'vendor-balance-report')->update('menu_info', array('sort_order' => 3));
        $this->db->where('menu_slug', 'supplier-summary-report')->update('menu_info', array('sort_order' => 4));

        // 11. Ensure role_permission for all active roles
        $roles = $this->db->where('status !=', 'Delete')->get('role_info')->result_array();
        if (!empty($roles)) {
            $check_ids = array($tender_parent_id, $supplier_parent_id, $c_menu_id, $v_menu_id, $vb_menu_id);
            foreach ($roles as $role) {
                $role_id = (int)$role['role_id'];
                foreach ($check_ids as $mid) {
                    if ($mid > 0) {
                        $has_perm = $this->db->where('role_id', $role_id)
                                             ->where('menu_id', $mid)
                                             ->count_all_results('role_permission');
                        if ($has_perm == 0) {
                            $this->db->insert('role_permission', array(
                                'role_id'    => $role_id,
                                'menu_id'    => $mid,
                                'can_view'   => 1,
                                'can_add'    => 1,
                                'can_edit'   => 1,
                                'can_delete' => 1
                            ));
                        }
                    }
                }
            }
        }
    }

    /**
     * Dynamically ensures that "Item Inward & Outward Report" exists in menu_info
     * under "In Stock Items", with active status and view permissions granted to all active roles.
     */
    public function ensure_item_inward_outward_menu()
    {
        // 1. Find In Stock Items parent menu
        $in_stock_rep = $this->db->where('menu_slug', 'in-stock-item-report')
                                 ->where('status !=', 'Delete')
                                 ->get('menu_info')
                                 ->row_array();

        $in_stock_parent_id = 0;
        $sort_order = 3;
        if ($in_stock_rep) {
            $in_stock_parent_id = (int)$in_stock_rep['parent_id'];
            $sort_order = (int)$in_stock_rep['sort_order'] + 1;
        } else {
            $in_stock_parent = $this->db->where('menu_title', 'In Stock Items')
                                        ->where('is_header', 0)
                                        ->where('status !=', 'Delete')
                                        ->get('menu_info')
                                        ->row_array();
            if ($in_stock_parent) {
                $in_stock_parent_id = (int)$in_stock_parent['menu_id'];
            }
        }

        // 2. Check if item-inward-outward-report exists
        $menu = $this->db->where('menu_slug', 'item-inward-outward-report')
                         ->where('status !=', 'Delete')
                         ->get('menu_info')
                         ->row_array();

        $menu_id = 0;
        if (!$menu) {
            $this->db->insert('menu_info', array(
                'parent_id'   => $in_stock_parent_id,
                'menu_title'  => 'Item Inward & Outward Report',
                'menu_slug'   => 'item-inward-outward-report',
                'menu_icon'   => 'fa fa-exchange',
                'is_header'   => 0,
                'sort_order'  => $sort_order,
                'status'      => 'Active'
            ));
            $menu_id = $this->db->insert_id();
        } else {
            $menu_id = (int)$menu['menu_id'];
            $upd = array(
                'status'     => 'Active',
                'menu_title' => 'Item Inward & Outward Report',
                'menu_icon'  => 'fa fa-exchange'
            );
            if ($in_stock_parent_id > 0 && empty($menu['parent_id'])) {
                $upd['parent_id'] = $in_stock_parent_id;
            }
            $this->db->where('menu_id', $menu_id)->update('menu_info', $upd);
        }

        // 3. Ensure role_permission has view/add/edit/delete for all active roles
        if ($menu_id > 0) {
            $roles = $this->db->where('status !=', 'Delete')->get('role_info')->result_array();
            if (!empty($roles)) {
                foreach ($roles as $role) {
                    $role_id = (int)$role['role_id'];
                    $has_perm = $this->db->where('role_id', $role_id)
                                         ->where('menu_id', $menu_id)
                                         ->count_all_results('role_permission');
                    if ($has_perm == 0) {
                        $this->db->insert('role_permission', array(
                            'role_id'    => $role_id,
                            'menu_id'    => $menu_id,
                            'can_view'   => 1,
                            'can_add'    => 1,
                            'can_edit'   => 1,
                            'can_delete' => 1
                        ));
                    } else {
                        $this->db->where('role_id', $role_id)
                                 ->where('menu_id', $menu_id)
                                 ->update('role_permission', array('can_view' => 1));
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
