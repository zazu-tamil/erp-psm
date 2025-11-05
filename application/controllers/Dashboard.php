<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends MY_Controller
{


    public function index()
    {

        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        date_default_timezone_set("Asia/Calcutta");

        $data = array();

        $data['js'] = 'dash.inc';

        $this->db->where('status !=', 'Delete');
        $this->db->where('user_id !=', '1');
        $data['total_user'] = $this->db->count_all_results('user_login_info');

        $this->db->where('status !=', 'Delete');
        $data['total_items'] = $this->db->count_all_results('item_info');

        $this->db->where('status !=', 'Delete');
        $data['total_category'] = $this->db->count_all_results('category_info');

        $this->db->where('status !=', 'Delete');
        $data['brand_count'] = $this->db->count_all_results('brand_info');

        $this->db->where('status !=', 'Delete');
        $data['vendor_count'] = $this->db->count_all_results('vendor_info');

        $this->db->where('status !=', 'Delete');
        $data['customer_count'] = $this->db->count_all_results('customer_info');





        $this->load->view('page/dashboard', $data);
    }


}