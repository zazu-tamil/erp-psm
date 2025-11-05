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

        $sql = "
            SELECT COUNT(*) AS total_enquiry 
            FROM tender_enquiry_info 
            WHERE status != 'Delete' 
            AND DATE(created_date) = CURDATE()
        ";

        $query = $this->db->query($sql);
        $row = $query->row();
        $data['total_enquiry'] = $row ? $row->total_enquiry : 0;

        $sql = "
            SELECT COUNT(*) AS tender_quotation_count 
            FROM tender_quotation_info 
            WHERE status != 'Delete' 
            AND DATE(created_date) = CURDATE()
        ";

        $query = $this->db->query($sql);
        $row = $query->row();
        $data['tender_quotation_count'] = $row ? $row->tender_quotation_count : 0;

        $sql = "
            SELECT COUNT(*) AS vendor_enquiry_count 
            FROM vendor_rate_enquiry_info 
            WHERE status != 'Delete' 
            AND DATE(created_date) = CURDATE()
        ";

        $query = $this->db->query($sql);
        $row = $query->row();
        $data['vendor_enquiry_count'] = $row ? $row->vendor_enquiry_count : 0;





        $this->load->view('page/dashboard', $data);
    }


}