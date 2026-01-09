<?php
class Settings_model extends CI_Model {

    public function get_all_settings()
    {
        $query = $this->db->get('app_settings');
        return $query->result();
    }
}
