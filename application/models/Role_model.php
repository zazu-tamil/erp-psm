<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Role_model
 *
 * Roles are keyed by `role_key`, which maps 1:1 onto the existing
 * `user_login_info.level` column. Permissions are stored per (role, menu).
 */
class Role_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all()
    {
        $this->db->where('status !=', 'Delete');
        $this->db->order_by('sort_order', 'ASC');
        $this->db->order_by('role_id', 'ASC');
        return $this->db->get('role_info')->result_array();
    }

    public function get($role_id)
    {
        $this->db->where('role_id', (int)$role_id);
        $this->db->where('status !=', 'Delete');
        return $this->db->get('role_info')->row_array();
    }

    public function get_by_key($role_key)
    {
        $this->db->group_start();
        $this->db->where('role_key', $role_key);
        $this->db->or_where('LOWER(role_key)', strtolower($role_key));
        $this->db->or_where('role_name', $role_key);
        $this->db->or_where('LOWER(role_name)', strtolower($role_key));
        if (is_numeric($role_key)) {
            $this->db->or_where('role_id', (int)$role_key);
        }
        $this->db->group_end();
        $this->db->where('status !=', 'Delete');
        return $this->db->get('role_info')->row_array();
    }

    public function insert($data)
    {
        if (!isset($data['created_date'])) {
            $data['created_date'] = date('Y-m-d H:i:s');
        }
        $this->db->insert('role_info', $data);
        return $this->db->insert_id();
    }

    public function update($role_id, $data)
    {
        $data['updated_date'] = date('Y-m-d H:i:s');
        $this->db->where('role_id', (int)$role_id);
        return $this->db->update('role_info', $data);
    }

    public function soft_delete($role_id)
    {
        return $this->db->update('role_info', array('status' => 'Delete'), array('role_id' => (int)$role_id));
    }

    /**
     * Returns permission map keyed by menu_id:
     *   [menu_id => ['view'=>, 'add'=>, 'edit'=>, 'delete'=>]]
     */
    public function get_permissions($role_id)
    {
        $rows = $this->db->get_where('role_permission', array('role_id' => (int)$role_id))->result_array();
        $map = array();
        foreach ($rows as $row) {
            $map[(int)$row['menu_id']] = array(
                'view'   => (int)$row['can_view'],
                'add'    => (int)$row['can_add'],
                'edit'   => (int)$row['can_edit'],
                'delete' => (int)$row['can_delete'],
            );
        }
        return $map;
    }

    /**
     * Replace all permissions for a role.
     *
     * $perms is a map of menu_id => ['view'=>, 'add'=>, 'edit'=>, 'delete'=>].
     */
    public function save_permissions($role_id, $perms)
    {
        $this->db->trans_start();

        $this->db->delete('role_permission', array('role_id' => (int)$role_id));

        foreach ($perms as $menu_id => $flags) {
            $can_add    = empty($flags['add']) ? 0 : 1;
            $can_edit   = empty($flags['edit']) ? 0 : 1;
            $can_delete = empty($flags['delete']) ? 0 : 1;
            // If any action is enabled, view is automatically enabled
            $can_view   = (!empty($flags['view']) || $can_add || $can_edit || $can_delete) ? 1 : 0;

            $this->db->insert('role_permission', array(
                'role_id'    => (int)$role_id,
                'menu_id'    => (int)$menu_id,
                'can_view'   => $can_view,
                'can_add'    => $can_add,
                'can_edit'   => $can_edit,
                'can_delete' => $can_delete,
            ));
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * Distinct roles currently referenced by users (for mapping existing users).
     */
    public function get_user_levels()
    {
        $this->db->distinct();
        $this->db->select('level');
        $this->db->where('status !=', 'Delete');
        $this->db->where("level IS NOT NULL");
        $this->db->where("level != ''");
        return $this->db->get('user_login_info')->result_array();
    }
}
