<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Master extends CI_Controller
{


    public function index()
    {
        $this->load->view('page/dashboard');
    }
    public function user_list()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        if (
            $this->session->userdata(SESS_HD . 'level') != 'Admin'
            && $this->session->userdata(SESS_HD . 'level') != 'Staff'
        ) {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }


        $data['js'] = 'user-list.inc';


        if ($this->input->post('mode') == 'Add') {
            $ins = array(
                'staff_name' => $this->input->post('staff_name'),
                'user_name' => $this->input->post('user_name'),
                'user_pwd' => $this->input->post('user_pwd'),
                'level' => 'Admin',
                'ref_id' => '0',
                'status' => $this->input->post('status')
            );

            $this->db->insert('user_login_info', $ins);
            redirect('user-list/');
        }

        if ($this->input->post('mode') == 'Edit') {
            $upd = array(
                'staff_name' => $this->input->post('staff_name'),
                'user_name' => $this->input->post('user_name'),
                'user_pwd' => $this->input->post('user_pwd'),
                'level' => 'Admin',
                'ref_id' => '0',
                'status' => $this->input->post('status')
            );

            $this->db->where('user_id', $this->input->post('user_id'));
            $this->db->update('user_login_info', $upd);

            redirect('user-list/');
        }


        $this->load->library('pagination');

        $this->db->where('status !=', 'Delete');
        $this->db->where('user_id !=', '1');
        $this->db->from('user_login_info');
        $data['total_records'] = $cnt = $this->db->count_all_results();


        $data['sno'] = $this->uri->segment(2, 0);

        $config['base_url'] = trim(site_url('user-list') . '/' . $this->uri->segment(2, 0));
        $config['total_rows'] = $cnt;
        $config['per_page'] = 50;
        $config['uri_segment'] = 2;
        //$config['num_links'] = 2; 
        $config['attributes'] = array('class' => 'page-link');
        $config['full_tag_open'] = '<ul class="pagination pagination-sm no-margin pull-right">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a href="#" class="page-link">';
        $config['cur_tag_close'] = '<span class="sr-only">(current)</span></a></li>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['prev_link'] = "Prev";
        $config['next_link'] = "Next";
        $this->pagination->initialize($config);

        $sql = "
            SELECT *
            FROM user_login_info
            WHERE status != 'Delete'
            and user_id != '1'
            order by user_id desc 
            limit " . $this->uri->segment(2, 0) . "," . $config['per_page'] . "                
        ";

        $data['record_list'] = array();

        $query = $this->db->query($sql);

        foreach ($query->result_array() as $row) {
            $data['record_list'][] = $row;
        }



        $data['pagination'] = $this->pagination->create_links();

        $this->load->view('page/master/user-list', $data);
    }
    public function company_list()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        if ($this->session->userdata(SESS_HD . 'level') != 'Admin' && $this->session->userdata(SESS_HD . 'level') != 'Staff') {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }

        $data['js'] = 'company-list.inc';

        // Handle Add (only if none exists)
        if ($this->input->post('mode') == 'Add') {

            // 1. Handle file uploads
            $upload_path = 'letterpad';
            $folder = 'letterpad';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size'] = 2048;

            $this->load->library('upload', $config);

            $ltr_header_img = '';

            if (!empty($_FILES['ltr_header_img']['name'])) {
                if ($this->upload->do_upload('ltr_header_img')) {
                    $ltr_header_img = $this->upload->data('file_name');
                }
            }

            $ins = array(
                'company_name' => $this->input->post('company_name'),
                'contact_name' => $this->input->post('contact_name'),
                'crno' => $this->input->post('crno'),
                'address' => $this->input->post('address'),
                'GST' => $this->input->post('GST'),
                'company_code' => $this->input->post('company_code'),
                'mobile' => $this->input->post('mobile'),
                'quote_terms' => $this->input->post('quote_terms'),
                'invoice_terms' => $this->input->post('invoice_terms'),
                'country' => $this->input->post('country'),
                'ltr_header_img' => $folder . '/' . $ltr_header_img,
                'email' => $this->input->post('email'),
                'status' => $this->input->post('status')
            );

            $this->db->insert('company_info', $ins);
            redirect('company-list');
        }

        // Handle Edit (only one allowed)
        if ($this->input->post('mode') == 'Edit') {

            // 1. Handle file uploads
            $upload_path = 'letterpad';
            $folder = 'letterpad';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size'] = 2048;

            $this->load->library('upload', $config);

            $upd = array(
                'company_name' => $this->input->post('company_name'),
                'contact_name' => $this->input->post('contact_name'),
                'crno' => $this->input->post('crno'),
                'address' => $this->input->post('address'),
                'GST' => $this->input->post('GST'),
                'mobile' => $this->input->post('mobile'),
                'country' => $this->input->post('country'),
                'quote_terms' => $this->input->post('quote_terms'),
                'invoice_terms' => $this->input->post('invoice_terms'),
                'company_code' => $this->input->post('company_code'),
                'email' => $this->input->post('email'),
                'status' => $this->input->post('status')
            );

            if (!empty($_FILES['ltr_header_img']['name'])) {
                if ($this->upload->do_upload('ltr_header_img')) {
                    $upd['ltr_header_img'] = $folder . '/' . $this->upload->data('file_name');
                }
            }

            $this->db->where('company_id', $this->input->post('company_id'));
            $this->db->update('company_info', $upd);
            redirect('company-list');
        }

        $this->load->library('pagination');

        $this->db->where('status != ', 'Delete');
        $this->db->from('company_info');
        $data['total_records'] = $cnt = $this->db->count_all_results();

        $data['sno'] = $this->uri->segment(2, 0);

        $config['base_url'] = trim(site_url('company-list') . '/' . $this->uri->segment(2, 0));
        $config['total_rows'] = $cnt;
        $config['per_page'] = 50;
        $config['uri_segment'] = 2;
        //$config['num_links'] = 2; 
        $config['attributes'] = array('class' => 'page-link');
        $config['full_tag_open'] = '<ul class="pagination pagination-sm no-margin pull-right">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a href="#" class="page-link">';
        $config['cur_tag_close'] = '<span class="sr-only">(current)</span></a></li>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['prev_link'] = "Prev";
        $config['next_link'] = "Next";
        $this->pagination->initialize($config);

        $sql = "
          SELECT
                a.country_id,
                a.country_name
            FROM
                country_info AS a
            WHERE
                a.status != 'Delete'
            ORDER BY
                a.country_name ASC
         ";

        $query = $this->db->query($sql);
        $data['country_opt'] = array();
        foreach ($query->result_array() as $row) {
            $data['country_opt'][$row['country_name']] = $row['country_name'];
        }


        $sql = "
            SELECT *
            FROM company_info
            WHERE status != 'Delete'
            order by company_id desc
            limit " . $this->uri->segment(2, 0) . "," . $config['per_page'] . "                
        ";

        $data['record_list'] = array();

        $query = $this->db->query($sql);

        foreach ($query->result_array() as $row) {
            $data['record_list'][] = $row;
        }



        $data['pagination'] = $this->pagination->create_links();



        $this->load->view('page/master/company-list', $data);
    }
    public function category_list()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        if (
            $this->session->userdata(SESS_HD . 'level') != 'Admin'
            && $this->session->userdata(SESS_HD . 'level') != 'Staff'
        ) {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }


        $data['js'] = 'category-list.inc';


        if ($this->input->post('mode') == 'Add') {
            $ins = array(
                'category_name' => $this->input->post('category_name'),
                'status' => $this->input->post('status')
            );

            $this->db->insert('category_info', $ins);
            redirect('category-list/');
        }

        if ($this->input->post('mode') == 'Edit') {
            $upd = array(
                'category_name' => $this->input->post('category_name'),
                'status' => $this->input->post('status')
            );

            $this->db->where('category_id', $this->input->post('category_id'));
            $this->db->update('category_info', $upd);

            redirect('category-list/');
        }


        $this->load->library('pagination');

        $this->db->where('status != ', 'Delete');
        $this->db->from('category_info');
        $data['total_records'] = $cnt = $this->db->count_all_results();

        $data['sno'] = $this->uri->segment(2, 0);

        $config['base_url'] = trim(site_url('category-list') . '/' . $this->uri->segment(2, 0));
        $config['total_rows'] = $cnt;
        $config['per_page'] = 50;
        $config['uri_segment'] = 2;
        //$config['num_links'] = 2; 
        $config['attributes'] = array('class' => 'page-link');
        $config['full_tag_open'] = '<ul class="pagination pagination-sm no-margin pull-right">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a href="#" class="page-link">';
        $config['cur_tag_close'] = '<span class="sr-only">(current)</span></a></li>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['prev_link'] = "Prev";
        $config['next_link'] = "Next";
        $this->pagination->initialize($config);

        $sql = "
            SELECT *
            FROM category_info
            WHERE status != 'Delete'
            order by category_name desc
            limit " . $this->uri->segment(2, 0) . "," . $config['per_page'] . "                
        ";

        $data['record_list'] = array();

        $query = $this->db->query($sql);

        foreach ($query->result_array() as $row) {
            $data['record_list'][] = $row;
        }



        $data['pagination'] = $this->pagination->create_links();

        $this->load->view('page/master/category-list', $data);
    }
    public function brand_list()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        if (
            $this->session->userdata(SESS_HD . 'level') != 'Admin'
            && $this->session->userdata(SESS_HD . 'level') != 'Staff'
        ) {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }


        $data['js'] = 'brand-list.inc';


        if ($this->input->post('mode') == 'Add') {
            $ins = array(
                'brand_name' => $this->input->post('brand_name'),
                'status' => $this->input->post('status')
            );

            $this->db->insert('brand_info', $ins);
            redirect('brand-list/');
        }

        if ($this->input->post('mode') == 'Edit') {
            $upd = array(
                'brand_name' => $this->input->post('brand_name'),
                'status' => $this->input->post('status')
            );

            $this->db->where('brand_id', $this->input->post('brand_id'));
            $this->db->update('brand_info', $upd);

            redirect('brand-list/');
        }


        $this->load->library('pagination');

        $this->db->where('status != ', 'Delete');
        $this->db->from('brand_info');
        $data['total_records'] = $cnt = $this->db->count_all_results();

        $data['sno'] = $this->uri->segment(2, 0);

        $config['base_url'] = trim(site_url('brand-list') . '/' . $this->uri->segment(2, 0));
        $config['total_rows'] = $cnt;
        $config['per_page'] = 50;
        $config['uri_segment'] = 2;
        //$config['num_links'] = 2; 
        $config['attributes'] = array('class' => 'page-link');
        $config['full_tag_open'] = '<ul class="pagination pagination-sm no-margin pull-right">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a href="#" class="page-link">';
        $config['cur_tag_close'] = '<span class="sr-only">(current)</span></a></li>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['prev_link'] = "Prev";
        $config['next_link'] = "Next";
        $this->pagination->initialize($config);

        $sql = "
            SELECT *
            FROM brand_info
            WHERE status != 'Delete'
            order by brand_name desc
            limit " . $this->uri->segment(2, 0) . "," . $config['per_page'] . "                
        ";

        $data['record_list'] = array();

        $query = $this->db->query($sql);

        foreach ($query->result_array() as $row) {
            $data['record_list'][] = $row;
        }



        $data['pagination'] = $this->pagination->create_links();

        $this->load->view('page/master/brand-list', $data);
    }
    public function uom_list()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        if (
            $this->session->userdata(SESS_HD . 'level') != 'Admin'
            && $this->session->userdata(SESS_HD . 'level') != 'Staff'
        ) {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }


        $data['js'] = 'uom-list.inc';


        if ($this->input->post('mode') == 'Add') {
            $ins = array(
                'uom_name' => $this->input->post('uom_name'),
                'status' => $this->input->post('status')
            );

            $this->db->insert('uom_info', $ins);
            redirect('uom-list/');
        }

        if ($this->input->post('mode') == 'Edit') {
            $upd = array(
                'uom_name' => $this->input->post('uom_name'),
                'status' => $this->input->post('status')
            );

            $this->db->where('uom_id', $this->input->post('uom_id'));
            $this->db->update('uom_info', $upd);

            redirect('uom-list/');
        }


        $this->load->library('pagination');

        $this->db->where('status != ', 'Delete');
        $this->db->from('uom_info');
        $data['total_records'] = $cnt = $this->db->count_all_results();

        $data['sno'] = $this->uri->segment(2, 0);

        $config['base_url'] = trim(site_url('uom-list') . '/' . $this->uri->segment(2, 0));
        $config['total_rows'] = $cnt;
        $config['per_page'] = 50;
        $config['uri_segment'] = 2;
        //$config['num_links'] = 2; 
        $config['attributes'] = array('class' => 'page-link');
        $config['full_tag_open'] = '<ul class="pagination pagination-sm no-margin pull-right">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a href="#" class="page-link">';
        $config['cur_tag_close'] = '<span class="sr-only">(current)</span></a></li>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['prev_link'] = "Prev";
        $config['next_link'] = "Next";
        $this->pagination->initialize($config);

        $sql = "
            SELECT *
            FROM uom_info
            WHERE status != 'Delete'
            order by uom_name desc
            limit " . $this->uri->segment(2, 0) . "," . $config['per_page'] . "                
        ";

        $data['record_list'] = array();

        $query = $this->db->query($sql);

        foreach ($query->result_array() as $row) {
            $data['record_list'][] = $row;
        }



        $data['pagination'] = $this->pagination->create_links();

        $this->load->view('page/master/uom-list', $data);
    }
    public function gst_list()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        if (
            $this->session->userdata(SESS_HD . 'level') != 'Admin'
            && $this->session->userdata(SESS_HD . 'level') != 'Staff'
        ) {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }


        $data['js'] = 'gst-list.inc';


        if ($this->input->post('mode') == 'Add') {
            $ins = array(
                'gst_percentage' => $this->input->post('gst_percentage'),
                'status' => $this->input->post('status')
            );

            $this->db->insert('gst_info', $ins);
            redirect('gst-list/');
        }

        if ($this->input->post('mode') == 'Edit') {
            $upd = array(
                'gst_percentage' => $this->input->post('gst_percentage'),
                'status' => $this->input->post('status')
            );

            $this->db->where('gst_id', $this->input->post('gst_id'));
            $this->db->update('gst_info', $upd);

            redirect('gst-list/');
        }


        $this->load->library('pagination');

        $this->db->where('status != ', 'Delete');
        $this->db->from('gst_info');
        $data['total_records'] = $cnt = $this->db->count_all_results();

        $data['sno'] = $this->uri->segment(2, 0);

        $config['base_url'] = trim(site_url('gst-list') . '/' . $this->uri->segment(2, 0));
        $config['total_rows'] = $cnt;
        $config['per_page'] = 50;
        $config['uri_segment'] = 2;
        //$config['num_links'] = 2; 
        $config['attributes'] = array('class' => 'page-link');
        $config['full_tag_open'] = '<ul class="pagination pagination-sm no-margin pull-right">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a href="#" class="page-link">';
        $config['cur_tag_close'] = '<span class="sr-only">(current)</span></a></li>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['prev_link'] = "Prev";
        $config['next_link'] = "Next";
        $this->pagination->initialize($config);

        $sql = "
            SELECT *
            FROM gst_info
            WHERE status != 'Delete'
            order by gst_percentage desc
            limit " . $this->uri->segment(2, 0) . "," . $config['per_page'] . "                
        ";

        $data['record_list'] = array();

        $query = $this->db->query($sql);

        foreach ($query->result_array() as $row) {
            $data['record_list'][] = $row;
        }



        $data['pagination'] = $this->pagination->create_links();

        $this->load->view('page/master/gst-list', $data);
    }
    
    public function items_list()
    {
        // Check if user is logged in
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        if (
            $this->session->userdata(SESS_HD . 'level') != 'Admin' &&
            $this->session->userdata(SESS_HD . 'level') != 'Staff'
        ) {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }

        $data['js'] = 'items-list.inc';
        $data['title'] = 'Items List';
        $where = "i.status != 'Delete'";

        if (isset($_POST['srch_item_name'])) {
            $data['srch_item_name'] = $srch_item_name = $this->input->post('srch_item_name');
            $this->session->set_userdata('srch_item_name', $srch_item_name);
        } elseif ($this->session->userdata('srch_item_name')) {
            $data['srch_item_name'] = $srch_item_name = $this->session->userdata('srch_item_name');
        } else {
            $data['srch_item_name'] = $srch_item_name = '';
        }
        if (!empty($srch_item_name)) {
            $where .= " AND (i.item_name  LIKE '%" . $this->db->escape_str($srch_item_name) . "%')";
        }

        if (isset($_POST['srch_item_code'])) {
            $data['srch_item_code'] = $srch_item_code = $this->input->post('srch_item_code');
            $this->session->set_userdata('srch_item_code', $srch_item_code);
        } elseif ($this->session->userdata('srch_item_code')) {
            $data['srch_item_code'] = $srch_item_code = $this->session->userdata('srch_item_code');
        } else {
            $data['srch_item_code'] = $srch_item_code = '';
        }
        if (!empty($srch_item_code)) {
            $where .= " AND (i.item_code  LIKE '%" . $this->db->escape_str($srch_item_code) . "%')";
        }



        $data['record_list'] = array();

        // ========== ADD ITEM MODE ==========
        if ($this->input->post('mode') == 'Add') {
            $item_name = $this->input->post('item_name');
            $item_image = '';

            // Configure file upload
            $upload_path = 'Item_doc/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size'] = 2048;

            $this->load->library('upload', $config);

            // Handle image upload
            if (!empty($_FILES['item_image']['name'])) {
                if ($this->upload->do_upload('item_image')) {
                    $upload_data = $this->upload->data();
                    $item_image = $upload_path . $upload_data['file_name'];
                }
            }

            // Insert new item
            $ins = array(
                'category_id' => $this->input->post('category_id'),
                'brand_id' => $this->input->post('brand_id'),
                'item_name' => $item_name,
                'item_description' => $this->input->post('item_description'),
                'uom' => $this->input->post('uom'),
                'hsn_code' => $this->input->post('hsn_code'),
                'item_code' => $this->input->post('item_code'),
                'gst' => $this->input->post('gst'),
                'item_image' => $item_image,
                'status' => $this->input->post('status'),
                'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'created_date' => date('Y-m-d H:i:s')
            );

            $this->db->insert('item_info', $ins);
            redirect('items-list');
        }

        // ========== EDIT ITEM MODE ==========
        if ($this->input->post('mode') == 'Edit') {
            $item_id = $this->input->post('item_id');
            $item_name = $this->input->post('item_name');

            // Configure file upload
            $upload_path = 'Item_doc/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size'] = 2048;

            $this->load->library('upload', $config);

            $item_image = '';
            if (!empty($_FILES['item_image']['name'])) {
                if ($this->upload->do_upload('item_image')) {
                    $upload_data = $this->upload->data();
                    $item_image = $upload_path . $upload_data['file_name'];
                }
            } else {
                // Keep existing image if no new upload
                $old = $this->db->get_where('item_info', ['item_id' => $item_id])->row();
                if ($old && !empty($old->item_image)) {
                    $item_image = $old->item_image;
                }
            }
            $upd = array(
                'category_id' => $this->input->post('category_id'),
                'brand_id' => $this->input->post('brand_id'),
                'item_name' => $item_name,
                'item_description' => $this->input->post('item_description'),
                'uom' => $this->input->post('uom'),
                'hsn_code' => $this->input->post('hsn_code'),
                'gst' => $this->input->post('gst'),
                'item_image' => $item_image,
                'item_code' => $this->input->post('item_code'),
                'status' => $this->input->post('status'),
                'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'updated_date' => date('Y-m-d H:i:s')
            );

            $this->db->where('item_id', $item_id);
            $this->db->update('item_info', $upd);
            redirect('items-list');
        }

        $this->load->library('pagination');
        $this->db->where('i.status != ', 'Delete');
        $this->db->where($where);
        $this->db->from('item_info as i');
        $data['total_records'] = $cnt = $this->db->count_all_results();
        $data['sno'] = $this->uri->segment(2, 0);
        $config['base_url'] = trim(site_url('items-list/'), '/' . $this->uri->segment(2, 0));
        $config['total_rows'] = $cnt;
        $config['per_page'] = 50;
        $config['uri_segment'] = 2;
        $config['attributes'] = array('class' => 'page-link');
        $config['full_tag_open'] = '<ul class="pagination pagination-sm no-margin pull-right">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a href="#" class="page-link">';
        $config['cur_tag_close'] = '<span class="sr-only">(current)</span></a></li>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['prev_link'] = "Prev";
        $config['next_link'] = "Next";

        $this->pagination->initialize($config);


        $sql = "
            SELECT i.* 
            FROM item_info i 
            WHERE i.status != 'Delete' 
                AND $where
            ORDER BY i.item_name ASC
            LIMIT " . $this->uri->segment(2, 0) . "," . $config['per_page'] . "                
        ";

        $query = $this->db->query($sql);
        $data['record_list'] = $query->result_array();

        $sql = "
            SELECT 
                a.uom_id,
                a.uom_name,                
                a.status
            FROM uom_info AS a 
            WHERE status != 'Delete'
            ORDER BY a.status ASC, a.uom_name ASC 
        ";

        $query = $this->db->query($sql);
        $data['uom_opt'] = array();
        foreach ($query->result_array() as $row) {
            $data['uom_opt'][$row['uom_name']] = $row['uom_name'];
        }


        $sql = "
            SELECT category_id, category_name 
            FROM category_info 
            WHERE status != 'Delete' 
            ORDER BY category_name ASC
        ";

        $query = $this->db->query($sql);
        $data['category_opt'] = array();
        foreach ($query->result_array() as $row) {
            $data['category_opt'][$row['category_id']] = $row['category_name'];
        }


        $sql = "
            SELECT 
                a.gst_id, 
                a.gst_percentage 
            FROM gst_info AS a 
            WHERE status != 'Delete' 
            ORDER BY a.status ASC, a.gst_percentage ASC
        ";

        $query = $this->db->query($sql);
        $data['gst_opt'] = array();
        foreach ($query->result_array() as $row) {
            $data['gst_opt'][$row['gst_percentage']] = $row['gst_percentage'];
        }
        $sql = " 
            SELECT brand_id, brand_name 
            FROM brand_info 
            WHERE status != 'Delete'
            ORDER BY brand_name ASC
        ";

        $query = $this->db->query($sql);
        $data['brand_opt'] = array();
        foreach ($query->result_array() as $row) {
            $data['brand_opt'][$row['brand_id']] = $row['brand_name'];
        }

        // ========== LOAD VIEW ==========
        $data['pagination'] = $this->pagination->create_links();
        $this->load->view('page/master/items-list', $data);
    }

    public function vendor_list()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        if (
            $this->session->userdata(SESS_HD . 'level') != 'Admin'
            && $this->session->userdata(SESS_HD . 'level') != 'Staff'
        ) {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }

        $data['js'] = 'vendor-list.inc';

        /* ===================== ADD ===================== */
        if ($this->input->post('mode') == 'Add') {
            $ins = array(
                'vendor_name' => $this->input->post('vendor_name'),
                'contact_name' => $this->input->post('contact_name'),
                'crno' => $this->input->post('crno'),
                'country' => $this->input->post('country'),
                'address' => $this->input->post('address'),
                'mobile' => $this->input->post('mobile'),
                'mobile_alt' => $this->input->post('mobile_alt'),
                'email' => $this->input->post('email'),
                'remarks' => $this->input->post('remarks'),
                'gst' => $this->input->post('gst'),
                'latitude' => $this->input->post('latitude'),
                'longitude' => $this->input->post('longitude'),
                'google_map_location' => $this->input->post('google_map_location'),
                'status' => $this->input->post('status'),
                'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'created_date' => date('Y-m-d H:i:s'),
            );
            $this->db->insert('vendor_info', $ins);
            $vendor_id = $this->db->insert_id();

            /* Create Ledger Account */
            $ins1 = array(
                'group_id' => SUNDRY_CREDITORS, // Sundry Creditors
                'ledger_name' => $this->input->post('vendor_name'),
                'opening_balance' => 0,
                'opening_type' => 'Credit',
                'ref_tbl' => 'Vendor',
                'ref_id' => $vendor_id,
                'status' => $this->input->post('status'),
            );
            $this->db->insert('ledger_accounts', $ins1);

            $ledger_id = $this->db->insert_id();

            $this->db->where('vendor_id', $vendor_id);
            $this->db->update('vendor_info', array('ledger_id' => $ledger_id));

            redirect('vendor-list/');
        }
        if ($this->input->post('mode') == 'Edit') {
            $this->db->where('vendor_id', $this->input->post('vendor_id'));
            $upd = array(
                'vendor_name' => $this->input->post('vendor_name'),
                'contact_name' => $this->input->post('contact_name'),
                'crno' => $this->input->post('crno'),
                'address' => $this->input->post('address'),
                'country' => $this->input->post('country'),
                'mobile' => $this->input->post('mobile'),
                'mobile_alt' => $this->input->post('mobile_alt'),
                'email' => $this->input->post('email'),
                'remarks' => $this->input->post('remarks'),
                'gst' => $this->input->post('gst'),
                'latitude' => $this->input->post('latitude'),
                'longitude' => $this->input->post('longitude'),
                'google_map_location' => $this->input->post('google_map_location'),
                'status' => $this->input->post('status'),
                'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'updated_date' => date('Y-m-d H:i:s'),
            );
            $this->db->where('vendor_id', $this->input->post('vendor_id'));
            $this->db->update('vendor_info', $upd);

            redirect('vendor-list/');
        }

        $this->load->library('pagination');

        $this->db->where('status != ', 'Delete');
        $this->db->from('vendor_info');
        $data['total_records'] = $cnt = $this->db->count_all_results();

        $data['sno'] = $this->uri->segment(2, 0);

        $config['base_url'] = site_url('vendor-list');
        $config['total_rows'] = $cnt;
        $config['per_page'] = 20;
        $config['uri_segment'] = 2;
        $config['attributes'] = array('class' => 'page-link');
        $config['full_tag_open'] = '<ul class="pagination pagination-sm no-margin pull-right">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a href="#" class="page-link">';
        $config['cur_tag_close'] = '</a></li>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = "Prev";
        $config['next_link'] = "Next";
        $this->pagination->initialize($config);
        $sql = "
          SELECT
                a.country_id,
                a.country_name
            FROM
                country_info AS a
            WHERE
                a.status != 'Delete'
            ORDER BY
                a.country_name ASC
         ";

        $query = $this->db->query($sql);
        $data['country_opt'] = array();
        foreach ($query->result_array() as $row) {
            $data['country_opt'][$row['country_name']] = $row['country_name'];
        }

        $sql = "
            SELECT v.* 
            FROM vendor_info v
           
            WHERE v.status != 'Delete'
            ORDER BY v.status ASC, v.vendor_name ASC 
            LIMIT " . $this->uri->segment(2, 0) . "," . $config['per_page'] . "
        ";

        $query = $this->db->query($sql);
        $data['record_list'] = $query->result_array();


        $data['pagination'] = $this->pagination->create_links();

        $this->load->view('page/master/vendor-list', $data);
    }
    public function customer_list()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        if (
            $this->session->userdata(SESS_HD . 'level') != 'Admin'
            && $this->session->userdata(SESS_HD . 'level') != 'Staff'
        ) {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }

        $data['js'] = 'customer-list.inc';

        /* ===================== ADD ===================== */
        if ($this->input->post('mode') == 'Add') {
            $ins = array(
                'customer_name' => $this->input->post('customer_name'),
                'contact_name' => $this->input->post('contact_name'),
                'crno' => $this->input->post('crno'),
                'country' => $this->input->post('country'),
                'address' => $this->input->post('address'),
                'mobile' => $this->input->post('mobile'),
                'mobile_alt' => $this->input->post('mobile_alt'),
                'customer_code' => $this->input->post('customer_code'),
                'email' => $this->input->post('email'),
                'remarks' => $this->input->post('remarks'),
                'gst' => $this->input->post('gst'),
                'latitude' => $this->input->post('latitude'),
                'longitude' => $this->input->post('longitude'),
                'google_map_location' => $this->input->post('google_map_location'),
                'status' => $this->input->post('status'),
                'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'created_date' => date('Y-m-d H:i:s'),
            );
            $this->db->insert('customer_info', $ins);

            $customer_id = $this->db->insert_id();

            /* Create Ledger Account */
            $ins1 = array(
                'group_id' => SUNDRY_DEBTORS, // Sundry Debtors
                'ledger_name' => $this->input->post('customer_name'),
                'opening_balance' => 0,
                'opening_type' => 'Debit',
                'ref_tbl' => 'Customer',
                'ref_id' => $customer_id,
                'status' => $this->input->post('status'),
            );
            $this->db->insert('ledger_accounts', $ins1);

            $ledger_id = $this->db->insert_id();

            $this->db->where('customer_id', $customer_id);
            $this->db->update('customer_info', array('ledger_id' => $ledger_id));

            redirect('customer-list/');
        }
        if ($this->input->post('mode') == 'Edit') {
            $upd = array(
                'customer_name' => $this->input->post('customer_name'),
                'contact_name' => $this->input->post('contact_name'),
                'crno' => $this->input->post('crno'),
                'country' => $this->input->post('country'),
                'address' => $this->input->post('address'),
                'mobile' => $this->input->post('mobile'),
                'mobile_alt' => $this->input->post('mobile_alt'),
                'customer_code' => $this->input->post('customer_code'),
                'email' => $this->input->post('email'),
                'remarks' => $this->input->post('remarks'),
                'gst' => $this->input->post('gst'),
                'latitude' => $this->input->post('latitude'),
                'longitude' => $this->input->post('longitude'),
                'google_map_location' => $this->input->post('google_map_location'),
                'status' => $this->input->post('status'),
                'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'updated_date' => date('Y-m-d H:i:s'),
            );
            $this->db->where('customer_id', $this->input->post('customer_id'));
            $this->db->update('customer_info', $upd);

            redirect('customer-list/');
        }


        $sql = "
          SELECT
                a.country_id,
                a.country_name
            FROM
                country_info AS a
            WHERE
                a.status != 'Delete'
            ORDER BY
                a.country_name ASC
         ";

        $query = $this->db->query($sql);
        $data['country_opt'] = array();
        foreach ($query->result_array() as $row) {
            $data['country_opt'][$row['country_name']] = $row['country_name'];
        }

        $sql = "
            SELECT 
                c.*,
                SUBSTR(c.address, 1, 50) AS address
            FROM customer_info c
            WHERE c.status != 'Delete'
            ORDER BY c.customer_id DESC 
        ";

        $query = $this->db->query($sql);
        $data['record_list'] = $query->result_array();


        $this->load->view('page/master/customer-list', $data);
    }

    public function customer_contact_list()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        if (
            $this->session->userdata(SESS_HD . 'level') != 'Admin'
            && $this->session->userdata(SESS_HD . 'level') != 'Staff'
        ) {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }

        $data['js'] = 'customer-contact-list.inc';


        // === FILTERS ===
        $where = "1 = 1";

        // Customer Filter
        if ($this->input->post('srch_customer_id') !== null) {
            $data['srch_customer_id'] = $srch_customer_id = $this->input->post('srch_customer_id');
            $this->session->set_userdata('srch_customer_id', $srch_customer_id);
        } elseif ($this->session->userdata('srch_customer_id')) {
            $data['srch_customer_id'] = $srch_customer_id = $this->session->userdata('srch_customer_id');
        } else {
            $data['srch_customer_id'] = $srch_customer_id = '';
        }
        if (!empty($srch_customer_id)) {
            $where .= " AND cci.customer_id = '" . $this->db->escape_str($srch_customer_id) . "'";
        }


        /* ===================== ADD ===================== */
        if ($this->input->post('mode') == 'Add') {
            $ins = array(
                'customer_id' => $this->input->post('customer_id'),
                'contact_person_name' => $this->input->post('contact_person_name'),
                'mobile' => $this->input->post('mobile'),
                'email' => $this->input->post('email'),
                'address' => $this->input->post('address'),
                'department' => $this->input->post('department'),
                'designation' => $this->input->post('designation'),
                'status' => $this->input->post('status'),
                'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'created_date' => date('Y-m-d H:i:s'),
            );
            $this->db->insert('customer_contact_info', $ins);
            redirect('customer-contact-list/');
        }

        /* ===================== EDIT ===================== */
        if ($this->input->post('mode') == 'Edit') {
            $upd = array(
                'customer_id' => $this->input->post('customer_id'),
                'contact_person_name' => $this->input->post('contact_person_name'),
                'mobile' => $this->input->post('mobile'),
                'email' => $this->input->post('email'),
                'address' => $this->input->post('address'),
                'department' => $this->input->post('department'),
                'designation' => $this->input->post('designation'),
                'status' => $this->input->post('status'),
                'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'updated_date' => date('Y-m-d H:i:s'),
            );
            $this->db->where('customer_contact_id', $this->input->post('customer_contact_id'));
            $this->db->update('customer_contact_info', $upd);
            redirect('customer-contact-list/');
        }

        $this->load->library('pagination');

        $this->db->where('status != ', 'Delete');
        $this->db->where($where);
        $this->db->from('customer_contact_info cci');
        $data['total_records'] = $cnt = $this->db->count_all_results();

        $data['sno'] = $this->uri->segment(2, 0);

        $config['base_url'] = site_url('customer-contact-list');
        $config['total_rows'] = $cnt;
        $config['per_page'] = 20;
        $config['uri_segment'] = 2;
        $config['attributes'] = array('class' => 'page-link');
        $config['full_tag_open'] = '<ul class="pagination pagination-sm no-margin pull-right">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a href="#" class="page-link">';
        $config['cur_tag_close'] = '</a></li>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = "Prev";
        $config['next_link'] = "Next";
        $this->pagination->initialize($config);

        $data['customer_opt'] = array('' => 'All');
        $sql = "
            SELECT customer_id,customer_name
            FROM customer_info
            WHERE status = 'Active' 
            ORDER BY customer_name ASC
        ";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['customer_opt'][$row['customer_id']] = $row['customer_name'];
        }

        $sql = "
        SELECT cci.*, ci.customer_name 
        FROM customer_contact_info cci
        LEFT JOIN customer_info ci ON cci.customer_id = ci.customer_id
        WHERE cci.status != 'Delete'
        and $where
        ORDER BY cci.status ASC, ci.customer_name ASC, cci.contact_person_name ASC
        LIMIT " . $this->uri->segment(2, 0) . "," . $config['per_page'] . "
    ";
        $query = $this->db->query($sql);
        $data['record_list'] = $query->result_array();

        $data['pagination'] = $this->pagination->create_links();

        $this->load->view('page/master/customer-contact-list', $data);
    }
    public function vendor_contact_list()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        if (
            $this->session->userdata(SESS_HD . 'level') != 'Admin'
            && $this->session->userdata(SESS_HD . 'level') != 'Staff'
        ) {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }

        $data['js'] = 'vendor-contact-list.inc';


        // === FILTERS ===
        $where = "1 = 1";

        // Customer Filter
        if ($this->input->post('srch_vendor_id') !== null) {
            $data['srch_vendor_id'] = $srch_vendor_id = $this->input->post('srch_vendor_id');
            $this->session->set_userdata('srch_vendor_id', $srch_vendor_id);
        } elseif ($this->session->userdata('srch_vendor_id')) {
            $data['srch_vendor_id'] = $srch_vendor_id = $this->session->userdata('srch_vendor_id');
        } else {
            $data['srch_vendor_id'] = $srch_vendor_id = '';
        }
        if (!empty($srch_vendor_id)) {
            $where .= " AND vci.vendor_id = '" . $this->db->escape_str($srch_vendor_id) . "'";
        }


        /* ===================== ADD ===================== */
        if ($this->input->post('mode') == 'Add') {
            $ins = array(
                'vendor_id' => $this->input->post('vendor_id'),
                'contact_person_name' => $this->input->post('contact_person_name'),
                'mobile' => $this->input->post('mobile'),
                'email' => $this->input->post('email'),
                'address' => $this->input->post('address'),
                'department' => $this->input->post('department'),
                'designation' => $this->input->post('designation'),
                'status' => $this->input->post('status'),
                'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'created_date' => date('Y-m-d H:i:s'),
            );
            $this->db->insert('vendor_contact_info', $ins);
            redirect('vendor-contact-list/');
        }

        /* ===================== EDIT ===================== */
        if ($this->input->post('mode') == 'Edit') {
            $upd = array(
                'vendor_id' => $this->input->post('vendor_id'),
                'contact_person_name' => $this->input->post('contact_person_name'),
                'mobile' => $this->input->post('mobile'),
                'email' => $this->input->post('email'),
                'address' => $this->input->post('address'),
                'department' => $this->input->post('department'),
                'designation' => $this->input->post('designation'),
                'status' => $this->input->post('status'),
                'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'updated_date' => date('Y-m-d H:i:s'),
            );
            $this->db->where('vendor_contact_id', $this->input->post('vendor_contact_id'));
            $this->db->update('vendor_contact_info', $upd);
            redirect('vendor-contact-list/');
        }

        $this->load->library('pagination');

        $this->db->where('status != ', 'Delete');
        $this->db->where($where);
        $this->db->from('vendor_contact_info vci');
        $data['total_records'] = $cnt = $this->db->count_all_results();

        $data['sno'] = $this->uri->segment(2, 0);

        $config['base_url'] = site_url('vendor-contact-list');
        $config['total_rows'] = $cnt;
        $config['per_page'] = 20;
        $config['uri_segment'] = 2;
        $config['attributes'] = array('class' => 'page-link');
        $config['full_tag_open'] = '<ul class="pagination pagination-sm no-margin pull-right">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a href="#" class="page-link">';
        $config['cur_tag_close'] = '</a></li>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = "Prev";
        $config['next_link'] = "Next";
        $this->pagination->initialize($config);

        $data['vendor_opt'] = array('' => 'All');
        $sql = "
            SELECT vendor_id,vendor_name
            FROM vendor_info
            WHERE status = 'Active' 
            ORDER BY vendor_name ASC
        ";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['vendor_opt'][$row['vendor_id']] = $row['vendor_name'];
        }

        $sql = "
        SELECT vci.*, ci.vendor_name 
        FROM vendor_contact_info vci
        LEFT JOIN vendor_info ci ON vci.vendor_id = ci.vendor_id
        WHERE vci.status != 'Delete'
        and $where
        ORDER BY vci.status ASC, ci.vendor_name ASC, vci.contact_person_name ASC
        LIMIT " . $this->uri->segment(2, 0) . "," . $config['per_page'] . "
    ";
        $query = $this->db->query($sql);
        $data['record_list'] = $query->result_array();

        $data['pagination'] = $this->pagination->create_links();

        $this->load->view('page/master/vendor-contact-list', $data);
    }

    public function currency_list()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        if (!in_array($this->session->userdata(SESS_HD . 'level'), ['Admin', 'Staff'])) {
            echo "<h3 style='color:red;'>Permission Denied</h3>";

        }

        $data['js'] = 'currency-list.inc';

        /* ---------- INSERT ---------- */
        if ($this->input->post('mode') == 'Add') {
            $ins = [
                'currency_code' => strtoupper($this->input->post('currency_code')),
                'currency_name' => $this->input->post('currency_name'),
                'symbol' => $this->input->post('symbol'),
                'country_name' => $this->input->post('country_name'),
                'exchange_rate' => $this->input->post('exchange_rate'),
                'decimal_point' => $this->input->post('decimal_point'),
                'is_base_currency' => $this->input->post('is_base_currency') ?: 0,
                'status' => $this->input->post('status')
            ];
            $this->db->insert('currencies_info', $ins);
            redirect('currency-list');
        }

        /* ---------- UPDATE ---------- */
        if ($this->input->post('mode') == 'Edit') {
            $upd = [
                'currency_code' => strtoupper($this->input->post('currency_code')),
                'currency_name' => $this->input->post('currency_name'),
                'symbol' => $this->input->post('symbol'),
                'country_name' => $this->input->post('country_name'),
                'decimal_point' => $this->input->post('decimal_point'),
                'exchange_rate' => $this->input->post('exchange_rate'),
                'is_base_currency' => $this->input->post('is_base_currency') ?: 0,
                'status' => $this->input->post('status')
            ];
            $this->db->where('currency_id', $this->input->post('currency_id'))
                ->update('currencies_info', $upd);
            redirect('currency-list');
        }

        /* ---------- PAGINATION ---------- */
        $this->load->library('pagination');

        $this->db->where('status !=', 'Delete');
        $data['total_records'] = $this->db->count_all_results('currencies_info');

        $data['sno'] = $segment = (int) $this->uri->segment(2, 0);

        $config['base_url'] = site_url('currency-list');
        $config['total_rows'] = $data['total_records'];
        $config['per_page'] = 50;
        $config['uri_segment'] = 2;
        $config['attributes'] = ['class' => 'page-link'];
        $config['full_tag_open'] = '<ul class="pagination pagination-sm no-margin pull-right">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a href="#" class="page-link">';
        $config['cur_tag_close'] = '</a></li>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['prev_link'] = 'Prev';
        $config['next_link'] = 'Next';
        $this->pagination->initialize($config);

        $sql = "SELECT * FROM currencies_info 
            WHERE status != 'Delete' 
            ORDER BY currency_name ASC 
            LIMIT $segment, {$config['per_page']}";

        $query = $this->db->query($sql);
        $data['record_list'] = $query->result_array();
        $data['pagination'] = $this->pagination->create_links();

        $this->load->view('page/master/currency-list', $data);
    }
    public function settings_list()
    {
        // Check if user is logged in
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        if (
            $this->session->userdata(SESS_HD . 'level') != 'Admin' &&
            $this->session->userdata(SESS_HD . 'level') != 'Staff'
        ) {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }

        $data['js'] = 'settings-list.inc';
        $data['title'] = 'Settings';




        $data['record_list'] = array();


        // ========== EDIT ITEM MODE ==========
        if ($this->input->post('mode') == 'Update') {
            $setting_key = $this->input->post('setting_key');

            $upd = array(

                'setting_value' => $this->input->post('setting_value'),
                'status' => $this->input->post('status')

            );

            $this->db->where('setting_key', $setting_key);
            $this->db->update('app_settings', $upd);
            redirect('settings');
        }

        $this->load->library('pagination');
        $this->db->where('a.status != ', 'Delete');
        $this->db->from('app_settings as a');
        $data['total_records'] = $cnt = $this->db->count_all_results();
        $data['sno'] = $this->uri->segment(2, 0);
        $config['base_url'] = trim(site_url('settings/'), '/' . $this->uri->segment(2, 0));
        $config['total_rows'] = $cnt;
        $config['per_page'] = 50;
        $config['uri_segment'] = 2;
        $config['attributes'] = array('class' => 'page-link');
        $config['full_tag_open'] = '<ul class="pagination pagination-sm no-margin pull-right">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a href="#" class="page-link">';
        $config['cur_tag_close'] = '<span class="sr-only">(current)</span></a></li>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['prev_link'] = "Prev";
        $config['next_link'] = "Next";

        $this->pagination->initialize($config);


        $sql = "
            SELECT
                a.setting_key,
                a.setting_value,
                status
            FROM app_settings as a
            WHERE a.status != 'Delete'
            ORDER BY a.setting_key asc 
            LIMIT " . $this->uri->segment(2, 0) . "," . $config['per_page'] . "                
        ";

        $query = $this->db->query($sql);
        $data['record_list'] = $query->result_array();


        // ========== LOAD VIEW ==========
        $data['pagination'] = $this->pagination->create_links();
        $this->load->view('page/master/settings-list', $data);
    }

    public function country_list()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        if (
            $this->session->userdata(SESS_HD . 'level') != 'Admin'
            && $this->session->userdata(SESS_HD . 'level') != 'Staff'
        ) {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }


        $data['js'] = 'country-list.inc';


        if ($this->input->post('mode') == 'Add') {
            $ins = array(
                'country_name' => $this->input->post('country_name'),
                'status' => $this->input->post('status')
            );

            $this->db->insert('country_info', $ins);
            redirect('country-list/');
        }

        if ($this->input->post('mode') == 'Edit') {
            $upd = array(
                'country_name' => $this->input->post('country_name'),
                'status' => $this->input->post('status')
            );

            $this->db->where('country_id', $this->input->post('country_id'));
            $this->db->update('country_info', $upd);

            redirect('country-list/');
        }


        $this->load->library('pagination');

        $this->db->where('status != ', 'Delete');
        $this->db->from('country_info');
        $data['total_records'] = $cnt = $this->db->count_all_results();

        $data['sno'] = $this->uri->segment(2, 0);

        $config['base_url'] = trim(site_url('country-list/'), '/' . $this->uri->segment(2, 0));
        $config['total_rows'] = $cnt;
        $config['per_page'] = 10;
        $config['uri_segment'] = 2;
         $config['attributes'] = array('class' => 'page-link');
        $config['full_tag_open'] = '<ul class="pagination pagination-sm no-margin pull-right">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a href="#" class="page-link">';
        $config['cur_tag_close'] = '<span class="sr-only">(current)</span></a></li>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['prev_link'] = "Prev";
        $config['next_link'] = "Next";
        $this->pagination->initialize($config);

        $sql = "
            SELECT *
            FROM country_info
            WHERE status != 'Delete'
            order by country_name desc
            limit " . $this->uri->segment(2, 0) . "," . $config['per_page'] . "                
        ";

        $data['record_list'] = array();

        $query = $this->db->query($sql);

        foreach ($query->result_array() as $row) {
            $data['record_list'][] = $row;
        }



        $data['pagination'] = $this->pagination->create_links();

        $this->load->view('page/master/country-list', $data);
    }

    public function vat_filing_head_list()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        if (
            $this->session->userdata(SESS_HD . 'level') != 'Admin'
            && $this->session->userdata(SESS_HD . 'level') != 'Staff'
        ) {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }


        $data['js'] = 'vat-filing-head-list.inc';


        if ($this->input->post('mode') == 'Add') {

            $ins = array(
                'vat_filing_head_name' => $this->input->post('vat_filing_head_name'),
                'vat_filing_head_type' => $this->input->post('vat_filing_head_type'),
                'status' => $this->input->post('status')
            );

            $this->db->insert('vat_filing_head_info', $ins);
            redirect('vat-filing-head-list/');

        }

        if ($this->input->post('mode') == 'Edit') {
            $upd = array(
                'vat_filing_head_name' => $this->input->post('vat_filing_head_name'),
                'vat_filing_head_type' => $this->input->post('vat_filing_head_type'),
                'status' => $this->input->post('status')
            );

            $this->db->where('vat_filing_head_id', $this->input->post('vat_filing_head_id'));
            $this->db->update('vat_filing_head_info', $upd);

            redirect('vat-filing-head-list/');
        }


        $this->load->library('pagination');

        $this->db->where('status != ', 'Delete');
        $this->db->from('vat_filing_head_info');
        $data['total_records'] = $cnt = $this->db->count_all_results();

        $data['sno'] = $this->uri->segment(2, 0);

        $config['base_url'] = trim(site_url('vat-filing-head-list/'), '/' . $this->uri->segment(2, 0));
        $config['total_rows'] = $cnt;
        $config['per_page'] = 50;
        $config['uri_segment'] = 2;
         $config['attributes'] = array('class' => 'page-link');
        $config['full_tag_open'] = '<ul class="pagination pagination-sm no-margin pull-right">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a href="#" class="page-link">';
        $config['cur_tag_close'] = '<span class="sr-only">(current)</span></a></li>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['prev_link'] = "Prev";
        $config['next_link'] = "Next";
        $this->pagination->initialize($config);

        $sql = "
            SELECT *
            FROM vat_filing_head_info
            WHERE status != 'Delete'
            order by vat_filing_head_type, vat_filing_head_name asc
            limit " . $this->uri->segment(2, 0) . "," . $config['per_page'] . "                
        ";

        $data['record_list'] = array();

        $query = $this->db->query($sql);

        foreach ($query->result_array() as $row) {
            $data['record_list'][] = $row;
        }



        $data['pagination'] = $this->pagination->create_links();

        $this->load->view('page/master/vat-filing-head-list', $data);
    }

    public function addt_charges_type_list()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        if (
            $this->session->userdata(SESS_HD . 'level') != 'Admin'
            && $this->session->userdata(SESS_HD . 'level') != 'Staff'
        ) {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }

        $data['js'] = 'addt-charges-type-list.inc';

        if ($this->input->post('mode') == 'Add') {
            $ins = array(
                'addt_charges_type_name' => $this->input->post('addt_charges_type_name'),
                'status' => $this->input->post('status')
            );

            $this->db->insert('addt_charges_type_info', $ins);
            redirect('addt-charges-type-list/');
        }

        if ($this->input->post('mode') == 'Edit') {
            $upd = array(
                'addt_charges_type_name' => $this->input->post('addt_charges_type_name'),
                'status' => $this->input->post('status')
            );

            $this->db->where('addt_charges_type_id', $this->input->post('addt_charges_type_id'));
            $this->db->update('addt_charges_type_info', $upd);

            redirect('addt-charges-type-list/');
        }

        $this->load->library('pagination');

        $this->db->where('status != ', 'Delete');
        $this->db->from('addt_charges_type_info');
        $data['total_records'] = $cnt = $this->db->count_all_results();

        $data['sno'] = $this->uri->segment(2, 0);

        $config['base_url'] = trim(site_url('addt-charges-type-list/'), '/' . $this->uri->segment(2, 0));
        $config['total_rows'] = $cnt;
        $config['per_page'] = 10;
        $config['uri_segment'] = 2;
        $config['attributes'] = array('class' => 'page-link');
        $config['full_tag_open'] = '<ul class="pagination pagination-sm no-margin pull-right">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a href="#" class="page-link">';
        $config['cur_tag_close'] = '<span class="sr-only">(current)</span></a></li>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['prev_link'] = "Prev";
        $config['next_link'] = "Next";
        $this->pagination->initialize($config);

        $sql = "
            SELECT *
            FROM addt_charges_type_info
            WHERE status != 'Delete'
            ORDER BY addt_charges_type_name desc
            LIMIT " . $this->uri->segment(2, 0) . "," . $config['per_page'] . "                
        ";

        $data['record_list'] = array();

        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['record_list'][] = $row;
        }

        $data['pagination'] = $this->pagination->create_links();
        $this->load->view('page/master/addt-charges-type-list', $data);
    }
}

