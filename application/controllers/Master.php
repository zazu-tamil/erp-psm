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
            $ins = array(
                'company_name' => $this->input->post('company_name'),
                'contact_name' => $this->input->post('contact_name'),
                'crno' => $this->input->post('crno'),
                'address' => $this->input->post('address'),
                'GST' => $this->input->post('GST'),
                'mobile' => $this->input->post('mobile'),
                'country' => $this->input->post('country'),
                'email' => $this->input->post('email'),
                'status' => $this->input->post('status')
            );

            $this->db->insert('company_info', $ins);
            redirect('company-list');
        }

        // Handle Edit (only one allowed)
        if ($this->input->post('mode') == 'Edit') {
            $upd = array(
                'company_name' => $this->input->post('company_name'),
                'contact_name' => $this->input->post('contact_name'),
                'crno' => $this->input->post('crno'),
                'address' => $this->input->post('address'),
                'GST' => $this->input->post('GST'),
                'mobile' => $this->input->post('mobile'),
                'country' => $this->input->post('country'),
                'email' => $this->input->post('email'),
                'status' => $this->input->post('status')
            );

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
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();
        if (
            $this->session->userdata(SESS_HD . 'level') != 'Admin'
            && $this->session->userdata(SESS_HD . 'level') != 'Staff'
        ) {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }


        $data['js'] = 'items-list.inc';
        $data['title'] = 'Items List';
        $where = "i.status != 'Delete'";


        // Filters (Company, Customer, Project, Vendor)
        if ($this->input->post('srch_category_id') !== null) {
            $data['srch_category_id'] = $srch_category_id = $this->input->post('srch_category_id');
            $this->session->set_userdata('srch_category_id', $srch_category_id);
        } elseif ($this->session->userdata('srch_category_id')) {
            $data['srch_category_id'] = $srch_category_id = $this->session->userdata('srch_category_id');
        } else {
            $data['srch_category_id'] = $srch_category_id = '';
        }

        if (!empty($srch_category_id)) {
            $where .= " AND (i.category_id = '" . $this->db->escape_str($srch_category_id) . "')";
        }

        $data['record_list'] = array();


        // ADD Item
        if ($this->input->post('mode') == 'Add') {

            $item_name = $this->input->post('item_name');
            $item_image = '';

            // 1. Handle file uploads
            $upload_path = 'Item_doc/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size'] = 2048;

            $this->load->library('upload', $config);

            if (!empty($_FILES['item_image']['name'])) {
                if ($this->upload->do_upload('item_image')) {
                    $upload_data = $this->upload->data();
                    $item_image = $upload_path . $upload_data['file_name']; // Full path saved
                }
            }

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

        // EDIT Item
        if ($this->input->post('mode') == 'Edit') {
            $item_id = $this->input->post('item_id');
            $item_name = $this->input->post('item_name');

            // 1. Handle file uploads
            $upload_path = 'Item_doc/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size'] = 2048;

            $this->load->library('upload', $config);

            $item_image = ''; // Initialize

            if (!empty($_FILES['item_image']['name'])) {
                // Upload new image
                if ($this->upload->do_upload('item_image')) {
                    $upload_data = $this->upload->data();
                    $item_image = $upload_path . $upload_data['file_name']; // Full path saved
                }
            } else {
                // No new image uploaded → keep old image
                $old = $this->db->get_where('item_info', ['item_id' => $item_id])->row();
                if ($old && !empty($old->item_image)) {
                    $item_image = $old->item_image;
                }
            }

            // Prepare update array
            $upd = array(
                'category_id' => $this->input->post('category_id'),
                'brand_id' => $this->input->post('brand_id'),
                'item_name' => $item_name,
                'item_description' => $this->input->post('item_description'),
                'uom' => $this->input->post('uom'),
                'hsn_code' => $this->input->post('hsn_code'),
                'gst' => $this->input->post('gst'),
                'item_image' => $item_image,
                // 'item_code' => $this->input->post('item_code'),
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
                i.*,
                c.category_name,
                b.brand_name
            FROM
                item_info i
            LEFT JOIN category_info c ON
                i.category_id = c.category_id
            LEFT JOIN brand_info b ON
                i.brand_id = b.brand_id
            WHERE
                i.status != 'Delete'
            and $where
            ORDER BY
                i.item_name ASC
            limit " . $this->uri->segment(2, 0) . "," . $config['per_page'] . "                
         ";

        $query = $this->db->query($sql);
        $data['record_list'] = $query->result_array();


        $sql = "
                select 
                a.uom_id,
                a.uom_name,                
                a.status
                from uom_info as a 
                where status != 'Delete'
                order by a.status asc , a.uom_name asc 
         ";

        $query = $this->db->query($sql);
        $data['uom_opt'] = array();
        foreach ($query->result_array() as $row) {
            $data['uom_opt'][$row['uom_name']] = $row['uom_name'];
        }



        $sql = "
                SELECT category_id,
                category_name FROM
                category_info WHERE
                status != 'Delete' 
                ORDER BY 
                category_name ASC
            ";
        $query = $this->db->query($sql);
        $data['category_opt'] = array();
        foreach ($query->result_array() as $row) {
            $data['category_opt'][$row['category_id']] = $row['category_name'];
        }
        $sql = "
               select 
               a.gst_id, 
               a.gst_percentage 
            from gst_info as a 
            where status != 'Delete' 
            order by a.status asc , a.gst_percentage asc
        ";
        $query = $this->db->query($sql);
        $data['gst_opt'] = array();
        foreach ($query->result_array() as $row) {
            $data['gst_opt'][$row['gst_percentage']] = $row['gst_percentage'];
        }

        // Brands
        $sql = " 
            SELECT brand_id, brand_name 
            FROM brand_info WHERE status != 'Delete'
            ORDER BY brand_name ASC
        ";

        $query = $this->db->query($sql);
        $data['brand_opt'] = array();
        foreach ($query->result_array() as $row) {
            $data['brand_opt'][$row['brand_id']] = $row['brand_name'];
        }

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

        $this->load->library('pagination');

        $this->db->where('status != ', 'Delete');
        $this->db->from('customer_info');
        $data['total_records'] = $cnt = $this->db->count_all_results();

        $data['sno'] = $this->uri->segment(2, 0);

        $config['base_url'] = site_url('customer-list');
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
            SELECT c.* 
            FROM customer_info c 
            WHERE c.status != 'Delete'
            ORDER BY c.status ASC, c.customer_name ASC 
            LIMIT " . $this->uri->segment(2, 0) . "," . $config['per_page'] . "
        ";

        $query = $this->db->query($sql);
        $data['record_list'] = $query->result_array();


        $data['pagination'] = $this->pagination->create_links();

        $this->load->view('page/master/customer-list', $data);
    }
}