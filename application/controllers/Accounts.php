<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Accounts extends CI_Controller
{

    // public function project_list()
    // {
    //     if(!$this->session->userdata(SESS_HD .'logged_in'))  redirect(); 

    //     if($this->session->userdata(SESS_HD .'level') != 'Admin')
    //     { 
    //         echo "<h3 style='color:red;'>Permission Denied</h3>"; exit;
    //     }  

    //     $data['js'] = 'accounts/project.inc';  

    //     if($this->input->post('mode') == 'Add')
    //     {
    //         $ins = array(
    //                 'project_name' => $this->input->post('project_name'),
    //                 'project_desc' => $this->input->post('project_desc'), 
    //                 'status' => $this->input->post('status'),
    //                 'created_by' => $this->session->userdata('cr_user_id'),                          
    //                 'created_datetime' => date('Y-m-d H:i:s')                            
    //         );

    //         //print_r($ins); exit;

    //         $this->db->insert('project_info', $ins); 
    //         redirect('project-list');
    //     }

    //     if($this->input->post('mode') == 'Edit')
    //     {
    //         $upd = array(
    //                 'project_name' => $this->input->post('project_name'),
    //                 'project_desc' => $this->input->post('project_desc'), 
    //                 'status' => $this->input->post('status'),
    //                 'updated_by' => $this->session->userdata('cr_user_id'),                          
    //                 'updated_datetime' => date('Y-m-d H:i:s')              
    //         );

    //         $this->db->where('project_id', $this->input->post('project_id'));
    //         $this->db->update('project_info', $upd); 

    //         redirect('project-list/' . $this->uri->segment(2, 0)); 
    //     } 



    //     $this->load->library('pagination'); 


    //     $this->db->where('a.status != ', 'Delete');  
    //     $this->db->from('project_info as a');         
    //     $data['total_records'] = $cnt  = $this->db->count_all_results();  

    //     $data['sno'] = $this->uri->segment(2, 0);		

    //     $config['base_url'] = trim(site_url('account-head-list/'), '/'. $this->uri->segment(2, 0));
    //     $config['total_rows'] = $cnt;
    //     $config['per_page'] = 25;
    //     $config['uri_segment'] = 2;
    //     //$config['num_links'] = 2; 
    //     $config['attributes'] = array('class' => 'page-link');
    //     $config['full_tag_open'] = '<ul class="pagination pagination-sm no-margin pull-right">';
    //     $config['full_tag_close'] = '</ul>';
    //     $config['num_tag_open'] = '<li class="page-item">';
    //     $config['num_tag_close'] = '</li>';
    //     $config['cur_tag_open'] = '<li class="page-item active"><a href="#" class="page-link">';
    //     $config['cur_tag_close'] = '<span class="sr-only">(current)</span></a></li>';
    //     $config['prev_tag_open'] = '<li class="page-item">';
    //     $config['prev_tag_close'] = '</li>';
    //     $config['next_tag_open'] = '<li class="page-item">';
    //     $config['next_tag_close'] = '</li>';
    //     $config['first_tag_open'] = '<li class="page-item">';
    //     $config['first_tag_close'] = '</li>';
    //     $config['last_tag_open'] = '<li class="page-item">';
    //     $config['last_tag_close'] = '</li>';
    //     $config['prev_link'] =  "Prev";
    //     $config['next_link'] =  "Next";
    //     $this->pagination->initialize($config);   

    //     $sql = "
    //             select 
    //             a.*
    //             from project_info as a 
    //             where status != 'Delete' 
    //             order by a.status asc , a.work_name asc 
    //             limit ". $this->uri->segment(2, 0) .",". $config['per_page'] ."                
    //     ";



    //     $query = $this->db->query($sql);

    //     $data['record_list'] = array();

    //     foreach ($query->result_array() as $row)
    //     {
    //         $data['record_list'][] = $row;     
    //     }

    //     $data['pagination'] = $this->pagination->create_links();

    //     $this->load->view('page/accounts/project-list',$data); 
    // }

    public function account_head_list()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        if ($this->session->userdata(SESS_HD . 'level') != 'Admin') {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }

        $data['js'] = 'accounts/account-head.inc';

        if ($this->input->post('mode') == 'Add') {
            $ins = array(
                'franchise_id' => ($this->session->userdata('cr_franchise_id') == '' ? 0 : $this->session->userdata('cr_franchise_id')),
                'account_head_name' => $this->input->post('account_head_name'),
                'type' => $this->input->post('type'),
                'ac_table' => ($this->input->post('ac_table') == 1 ? '1' : '0'),
                'status' => $this->input->post('status'),
                'created_by' => $this->session->userdata('cr_user_id'),
                'created_datetime' => date('Y-m-d H:i:s')
            );

            //print_r($ins); exit;

            $this->db->insert('cb_account_head_info', $ins);
            redirect('account-head-list');
        }

        if ($this->input->post('mode') == 'Edit') {
            $upd = array(
                'franchise_id' => ($this->session->userdata('cr_franchise_id') == '' ? 0 : $this->session->userdata('cr_franchise_id')),
                'account_head_name' => $this->input->post('account_head_name'),
                'type' => $this->input->post('type'),
                'ac_table' => ($this->input->post('ac_table') == 1 ? '1' : '0'),
                'status' => $this->input->post('status'),
                'updated_by' => $this->session->userdata('cr_user_id'),
                'updated_datetime' => date('Y-m-d H:i:s')
            );

            $this->db->where('account_head_id', $this->input->post('account_head_id'));
            $this->db->update('cb_account_head_info', $upd);

            redirect('account-head-list/' . $this->uri->segment(2, 0));
        }


        if (isset($_POST['srch_type'])) {
            $data['srch_type'] = $srch_type = $this->input->post('srch_type');
            $this->session->set_userdata('srch_type', $this->input->post('srch_type'));
        } elseif ($this->session->userdata('srch_type')) {
            $data['srch_type'] = $srch_type = $this->session->userdata('srch_type');
        } else {
            $data['srch_type'] = $srch_type = '';
        }

        if ($srch_type != '') {
            $where = 'a.type = "' . $srch_type . '"';
        } else {
            $where = "1=1";
        }
        $this->db->where('a.status != ', 'Delete');
        if ($srch_type != '')
            $this->db->where($where);
        $this->db->from('cb_account_head_info as a');

        $sql = "
                select 
                a.*
                from cb_account_head_info as a 
                where status != 'Delete'
                and $where
                order by a.status asc , a.account_head_name asc 
         ";



        $query = $this->db->query($sql);

        $data['record_list'] = array();

        foreach ($query->result_array() as $row) {
            $data['record_list'][] = $row;
        }


        $this->load->view('page/accounts/account-head-list', $data);
    }


    public function sub_account_head_list()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        if ($this->session->userdata(SESS_HD . 'level') != 'Admin') {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }

        $data['js'] = 'accounts/sub-account-head.inc';

        if ($this->input->post('mode') == 'Add') {
            $ins = array(
                'franchise_id' => ($this->session->userdata('cr_franchise_id') == '' ? 0 : $this->session->userdata('cr_franchise_id')),
                'account_head_id' => $this->input->post('account_head_id'),
                'sub_account_head_name' => $this->input->post('sub_account_head_name'),
                'type' => $this->input->post('type'),
                'ac_table' => ($this->input->post('ac_table') == 1 ? '1' : '0'),
                'status' => $this->input->post('status'),
                'created_by' => $this->session->userdata('cr_user_id'),
                'created_datetime' => date('Y-m-d H:i:s')
            );

            //print_r($ins); exit;

            $this->db->insert('cb_sub_account_head_info', $ins);
            redirect('sub-account-head-list');
        }

        if ($this->input->post('mode') == 'Edit') {
            $upd = array(
                'franchise_id' => ($this->session->userdata('cr_franchise_id') == '' ? 0 : $this->session->userdata('cr_franchise_id')),
                'account_head_id' => $this->input->post('account_head_id'),
                'sub_account_head_name' => $this->input->post('sub_account_head_name'),
                'type' => $this->input->post('type'),
                'ac_table' => ($this->input->post('ac_table') == 1 ? '1' : '0'),
                'status' => $this->input->post('status'),
                'updated_by' => $this->session->userdata('cr_user_id'),
                'updated_datetime' => date('Y-m-d H:i:s')
            );

            $this->db->where('sub_account_head_id', $this->input->post('sub_account_head_id'));
            $this->db->update('cb_sub_account_head_info', $upd);

            redirect('sub-account-head-list/' . $this->uri->segment(2, 0));
        }


        if (isset($_POST['srch_type'])) {
            $data['srch_type'] = $srch_type = $this->input->post('srch_type');
            $this->session->set_userdata('srch_type', $this->input->post('srch_type'));
        } elseif ($this->session->userdata('srch_type')) {
            $data['srch_type'] = $srch_type = $this->session->userdata('srch_type');
        } else {
            $data['srch_type'] = $srch_type = '';
        }

        if ($srch_type != '') {
            $where = 'a.type = "' . $srch_type . '"';
        } else {
            $where = "1=1";
        }







        $this->db->where('a.status != ', 'Delete');
        if ($srch_type != '')
            $this->db->where($where);
        $this->db->from('cb_sub_account_head_info as a');

        $sql = "
                select 
                a.*,
                b.account_head_name
                from cb_sub_account_head_info as a 
                left join cb_account_head_info as b on b.account_head_id = a.account_head_id
                where a.status != 'Delete' and b.status != 'Delete'
                and $where
                order by a.status asc , b.account_head_name , a.sub_account_head_name asc 
         ";

        $query = $this->db->query($sql);

        $data['record_list'] = array();

        foreach ($query->result_array() as $row) {
            $data['record_list'][] = $row;
        }
        $this->load->view('page/accounts/sub-account-head-list', $data);
    }

    public function account_head_for_list()
    {

        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        if ($this->session->userdata(SESS_HD . 'level') != 'Admin') {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }

        $data['js'] = 'accounts/account-head-for-list.inc';

        if ($this->input->post('mode') == 'Add') {
            $ins = array(
                'account_head_id' => $this->input->post('account_head_id'),
                'sub_account_head_id' => $this->input->post('sub_account_head_id'),
                'sub_account_headlvl3_name' => $this->input->post('sub_account_headlvl3_name'),
                'type' => $this->input->post('type'),
                'status' => $this->input->post('status'),
                'created_by' => $this->session->userdata('cr_user_id'),
                'created_datetime' => date('Y-m-d H:i:s')
            );

            //print_r($ins); exit;

            $this->db->insert('cb_sub_account_head_lvl3_info', $ins);
            redirect('account-head-for-list');
        }

        if ($this->input->post('mode') == 'Edit') {
            $upd = array(
                'account_head_id' => $this->input->post('account_head_id'),
                'sub_account_head_id' => $this->input->post('sub_account_head_id'),
                'sub_account_headlvl3_name' => $this->input->post('sub_account_headlvl3_name'),
                'type' => $this->input->post('type'),
                'status' => $this->input->post('status'),
                'updated_by' => $this->session->userdata('cr_user_id'),
                'updated_datetime' => date('Y-m-d H:i:s')
            );

            $this->db->where('sub_account_headlvl3_id', $this->input->post('sub_account_headlvl3_id'));
            $this->db->update('cb_sub_account_head_lvl3_info', $upd);

            redirect('account-head-for-list/' . $this->uri->segment(2, 0));
        }

        $this->db->where('status != ', 'Delete');
        $this->db->from('cb_sub_account_head_lvl3_info');

        $sql = "
                select 
                a.*,
                b.account_head_name,
                c.sub_account_head_name
                from cb_sub_account_head_lvl3_info as a 
                left join cb_account_head_info as b on b.account_head_id = a.account_head_id
                left join cb_sub_account_head_info as c on c.sub_account_head_id = a.sub_account_head_id
                where a.status != 'Delete' and b.status != 'Delete'
                order by a.status asc , b.account_head_name , c.sub_account_head_name , a.sub_account_headlvl3_name asc 
         ";

        $query = $this->db->query($sql);

        $data['record_list'] = array();

        foreach ($query->result_array() as $row) {
            $data['record_list'][] = $row;
        }
        $this->load->view('page/accounts/account-head-for-list', $data);

    }

    public function cash_inward_list()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        /*if($this->session->userdata('m_is_admin') != USER_ADMIN) 
        {
            echo "<h3 style='color:red;'>Permission Denied</h3>"; exit;
        }  */

        $data['js'] = 'accounts/cash-inward.inc';

        if ($this->input->post('mode') == 'Add') {
            $ins = array(
                'franchise_id' => ($this->session->userdata('cr_franchise_id') == '' ? 0 : $this->session->userdata('cr_franchise_id')),
                'cash_inward_id ' => $this->input->post('cash_inward_id '),
                'company_id' => $this->input->post('company_id'),
                'project_id' => $this->input->post('project_id'),
                'agent_id' => $this->input->post('agent_id'),
                'voucher_type_id' => $this->input->post('voucher_type_id'),
                'vno' => $this->input->post('vno'),
                'ac_type' => $this->input->post('ac_type'),
                'inward_date' => $this->input->post('inward_date'),
                'account_head_id' => $this->input->post('account_head_id'),
                'sub_account_head_id' => $this->input->post('sub_account_head_id'),
                'sub_account_headlvl3_id' => $this->input->post('sub_account_headlvl3_id'),
                'amount' => $this->input->post('amount'),
                'remarks' => $this->input->post('remarks'),
                'status' => $this->input->post('status'),
                'created_by' => $this->session->userdata('cr_user_id'),
                'created_datetime' => date('Y-m-d H:i:s')
            );

            //print_r($ins); exit;

            $this->db->insert('cb_cash_inward_info', $ins);
            redirect('inward-list');
        }

        if ($this->input->post('mode') == 'Edit') {
            $upd = array(
                'franchise_id' => ($this->session->userdata('cr_franchise_id') == '' ? 0 : $this->session->userdata('cr_franchise_id')),
                'cash_inward_id ' => $this->input->post('cash_inward_id'),
                'company_id' => $this->input->post('company_id'),
                'project_id' => $this->input->post('project_id'),
                'agent_id' => $this->input->post('agent_id'),
                'voucher_type_id' => $this->input->post('voucher_type_id'),
                'vno' => $this->input->post('vno'),
                'ac_type' => $this->input->post('ac_type'),
                'inward_date' => $this->input->post('inward_date'),
                'account_head_id' => $this->input->post('account_head_id'),
                'sub_account_head_id' => $this->input->post('sub_account_head_id'),
                'sub_account_headlvl3_id' => $this->input->post('sub_account_headlvl3_id'),
                'amount' => $this->input->post('amount'),
                'remarks' => $this->input->post('remarks'),
                'status' => $this->input->post('status'),
                'updated_by' => $this->session->userdata('cr_user_id'),
                'updated_datetime' => date('Y-m-d H:i:s')
            );

            $this->db->where('cash_inward_id', $this->input->post('cash_inward_id'));
            $this->db->update('cb_cash_inward_info', $upd);

            redirect('inward-list/' . $this->uri->segment(2, 0));
        }

        $where = " a.franchise_id = '" . ($this->session->userdata('cr_franchise_id') == '' ? 0 : $this->session->userdata('cr_franchise_id')) . "'";


        if (isset($_POST['srch_from_date'])) {
            $data['srch_from_date'] = $srch_from_date = $this->input->post('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->input->post('srch_to_date');
            $this->session->set_userdata('srch_from_date', $this->input->post('srch_from_date'));
            $this->session->set_userdata('srch_to_date', $this->input->post('srch_to_date'));

        } elseif ($this->session->userdata('srch_from_date')) {
            $data['srch_from_date'] = $srch_from_date = $this->session->userdata('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->session->userdata('srch_to_date');
        } else {
            $data['srch_from_date'] = $srch_from_date = date('Y-m-') . '01';
            $data['srch_to_date'] = $srch_to_date = date('Y-m-d');
        }

        if (!empty($srch_from_date) && !empty($srch_to_date)) {
            $where .= " and a.inward_date between '" . $srch_from_date . "' and  '" . $srch_to_date . "'";
        }


        $this->load->library('pagination');


        $this->db->where('a.status != ', 'Delete');
        $this->db->where($where);
        $this->db->from('cb_cash_inward_info as a');
        $data['total_records'] = $cnt = $this->db->count_all_results();

        $data['sno'] = $this->uri->segment(2, 0);

        $config['base_url'] = trim(site_url('cash-inward-list/'), '/' . $this->uri->segment(2, 0));
        $config['total_rows'] = $cnt;
        $config['per_page'] = 25;
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
                select 
                a.*,
                f.company_name, 
                g.tender_enquiry_id, 
                b.account_head_name,
                c.sub_account_head_name,
                g.enquiry_no,
                DATEDIFF(current_date(), a.inward_date) as days,
                d.voucher_type_name,
                d.prefix,
                e.sub_account_headlvl3_name as in_from
                from cb_cash_inward_info as a 
                left join cb_account_head_info as b on b.account_head_id = a.account_head_id and b.status != 'Delete'
                left join cb_sub_account_head_info as c on c.sub_account_head_id = a.sub_account_head_id and c.status != 'Delete'
                left join cb_voucher_type_info as d on d.voucher_type_id = a.voucher_type_id and d.status != 'Delete'
                left join cb_sub_account_head_lvl3_info as e on e.sub_account_headlvl3_id = a.sub_account_headlvl3_id and e.status != 'Delete'
                left JOIN company_info as f on f.company_id = a.company_id and f.status='Active'
                left JOIN tender_enquiry_info as g on g.tender_enquiry_id = a.project_id and f.status='Active'
                left join customer_info as h on h.customer_id = g.customer_id and h.status='Active'
                 
                where a.status != 'Delete' and $where   
                order by  a.inward_date desc , a.cash_inward_id desc
                limit " . $this->uri->segment(2, 0) . "," . $config['per_page'] . "                
        ";

        $query = $this->db->query($sql);

        $data['record_list'] = array();

        foreach ($query->result_array() as $row) {
            $data['record_list'][] = $row;
        }



        $sql = "
                select 
                a.account_head_id,                
                a.account_head_name             
                from cb_account_head_info as a  
                where a.status = 'Active' and a.type = 'Inward'
                and a.franchise_id = '" . ($this->session->userdata('cr_franchise_id') == '' ? 0 : $this->session->userdata('cr_franchise_id')) . "'
                order by a.account_head_name asc                 
        ";

        $query = $this->db->query($sql);

        $data['account_head_opt'] = array();

        foreach ($query->result_array() as $row) {
            $data['account_head_opt'][$row['account_head_id']] = $row['account_head_name'];
        }


        $sql = "
                select 
                a.voucher_type_id,                
                a.voucher_type_name ,
                a.prefix            
                from cb_voucher_type_info as a  
                where a.status = 'Active' and a.h_type = 'Inward'
                order by a.voucher_type_name asc                 
        ";

        $query = $this->db->query($sql);

        $data['voucher_type_opt'] = array();

        foreach ($query->result_array() as $row) {
            $data['voucher_type_opt'][$row['voucher_type_id']] = $row['voucher_type_name'] . "[ " . $row['prefix'] . " ]";
        }


        $sql = "
               select 
                a.company_id,                
                a.company_name            
                from company_info as a  
                where a.status = 'Active'
                order by a.company_name asc             
        ";

        $query = $this->db->query($sql);

        $data['company_name_opt'] = array();

        foreach ($query->result_array() as $row) {
            $data['company_name_opt'][$row['company_id']] = $row['company_name'];
        }

        $sql = "
              SELECT a.tender_enquiry_id
                FROM tender_enquiry_info AS a
                INNER JOIN company_info as b
                on b.company_id = a.company_id
                WHERE a.status = 'Active'  
                AND b.status= 'Active'      
        ";

        $query = $this->db->query($sql);

        $data['project_opt'] = array();

        foreach ($query->result_array() as $row) {
            $data['project_opt'][$row['tender_enquiry_id']] = $row['tender_enquiry_id'];
        }



        $data['pagination'] = $this->pagination->create_links();

        $data['ac_type_opt'] = array('Bank' => 'Bank', 'Cash' => 'Cash');

        $this->load->view('page/accounts/cash-inward-list', $data);
    }

    public function cash_outward_list()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        /*if($this->session->userdata('m_is_admin') != USER_ADMIN) 
        {
            echo "<h3 style='color:red;'>Permission Denied</h3>"; exit;
        }  */

        $data['js'] = 'accounts/cash-outward.inc';

        if ($this->input->post('mode') == 'Add') {

            // Dynamic upload folder
            $upload_path = 'bill_photo/' . COMPANY . '/' . $this->session->userdata('cr_franchise_id');

            // Create folder if it doesn't exist
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            $photo_path = ''; // Default empty

            // Only try upload if a file is selected
            if (!empty($_FILES['bill_photo']['name'])) {

                $config['upload_path'] = $upload_path;
                $config['file_name'] = $this->input->post('project_id') . "_bill_" . date('YmdHis');
                $config['allowed_types'] = 'gif|jpg|png|jpeg';

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('bill_photo')) {
                    $file_array = $this->upload->data();

                    $photo_path = $upload_path . '/' . $file_array['file_name'];
                } else {

                    echo $this->upload->display_errors();
                }
            }

            // Prepare data for insert
            $ins = array(
                'franchise_id' => ($this->session->userdata('cr_franchise_id') == '' ? 0 : $this->session->userdata('cr_franchise_id')),
                'project_id' => $this->input->post('project_id'),
                'company_id' => $this->input->post('company_id'),
                'voucher_type_id' => $this->input->post('voucher_type_id'),
                'outward_date' => $this->input->post('outward_date'),
                'ac_type' => $this->input->post('ac_type'),
                'account_head_id' => $this->input->post('account_head_id'),
                'sub_account_head_id' => $this->input->post('sub_account_head_id'),
                'sub_account_headlvl3_id' => $this->input->post('sub_account_headlvl3_id'),
                'amount' => $this->input->post('amount'),
                'remarks' => $this->input->post('remarks'),
                'bill_type' => $this->input->post('bill_type'),
                'bill_photo' => $photo_path,
                'status' => $this->input->post('status'),
                'created_by' => $this->session->userdata('cr_user_id'),
                'created_datetime' => date('Y-m-d H:i:s')
            );

            $this->db->insert('cb_cash_outward_info', $ins);

            redirect('outward-list');
        }

        if ($this->input->post('mode') == 'Edit') {

            // Dynamic upload folder
            $upload_path = 'bill_photo/' . COMPANY . '/' . $this->session->userdata('cr_franchise_id');

            // Create folder if it doesn't exist
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            $photo_path = $this->input->post('bill_photo_path'); // Default to existing file

            // Check if a new file is uploaded
            if (!empty($_FILES['bill_photo']['name'])) {

                $config['upload_path'] = $upload_path;
                $config['file_name'] = $this->input->post('project_id') . "_bill_" . date('YmdHis');
                $config['allowed_types'] = 'gif|jpg|png|jpeg|pdf';

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('bill_photo')) {
                    $file_array = $this->upload->data();
                    $photo_path = $upload_path . '/' . $file_array['file_name'];
                } else {
                    // Optional: show upload error
                    echo $this->upload->display_errors();
                }
            }

            // Prepare data for update
            $upd = array(
                'franchise_id' => ($this->session->userdata('cr_franchise_id') == '' ? 0 : $this->session->userdata('cr_franchise_id')),
                'project_id' => $this->input->post('project_id'),
                'company_id' => $this->input->post('company_id'),
                'voucher_type_id' => $this->input->post('voucher_type_id'),
                'outward_date' => $this->input->post('outward_date'),
                'ac_type' => $this->input->post('ac_type'),
                'vno' => $this->input->post('vno'),
                'account_head_id' => $this->input->post('account_head_id'),
                'sub_account_head_id' => $this->input->post('sub_account_head_id'),
                'sub_account_headlvl3_id' => $this->input->post('sub_account_headlvl3_id'),
                'amount' => $this->input->post('amount'),
                'remarks' => $this->input->post('remarks'),
                'bill_type' => $this->input->post('bill_type'),
                'bill_photo' => $photo_path,
                'status' => $this->input->post('status'),
                'updated_by' => $this->session->userdata('cr_user_id'),
                'updated_datetime' => date('Y-m-d H:i:s')
            );

            // Update the record
            $this->db->where('cash_outward_id', $this->input->post('cash_outward_id'));
            $this->db->update('cb_cash_outward_info', $upd);

            redirect('outward-list/' . $this->uri->segment(2, 0));
        }

        $where = " a.franchise_id = '" . ($this->session->userdata('cr_franchise_id') == '' ? 0 : $this->session->userdata('cr_franchise_id')) . "'";


        if (isset($_POST['srch_from_date'])) {
            $data['srch_from_date'] = $srch_from_date = $this->input->post('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->input->post('srch_to_date');
            $this->session->set_userdata('srch_from_date', $this->input->post('srch_from_date'));
            $this->session->set_userdata('srch_to_date', $this->input->post('srch_to_date'));

        } elseif ($this->session->userdata('srch_from_date')) {
            $data['srch_from_date'] = $srch_from_date = $this->session->userdata('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->session->userdata('srch_to_date');
        } else {
            $data['srch_from_date'] = $srch_from_date = date('Y-m-' . '01');
            $data['srch_to_date'] = $srch_to_date = date('Y-m-d');
        }

        if (!empty($srch_from_date) && !empty($srch_to_date)) {
            $where .= " and a.outward_date between '" . $srch_from_date . "' and  '" . $srch_to_date . "'";
        }


        $this->load->library('pagination');


        $this->db->where('a.status != ', 'Delete');
        $this->db->where($where);
        $this->db->from('cb_cash_outward_info as a');
        $data['total_records'] = $cnt = $this->db->count_all_results();

        $data['sno'] = $this->uri->segment(2, 0);

        $config['base_url'] = trim(site_url('outward-list/'), '/' . $this->uri->segment(2, 0));
        $config['total_rows'] = $cnt;
        $config['per_page'] = 25;
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
                select 
                a.*,
                b.account_head_name,
                c.sub_account_head_name,
                DATEDIFF(current_date(), a.outward_date) as days ,
                d.voucher_type_name,
                d.prefix,
                e.sub_account_headlvl3_name as out_for, 
                f.enquiry_no
                from cb_cash_outward_info as a 
                left join cb_account_head_info as b on b.account_head_id = a.account_head_id and b.status != 'Delete'
                left join cb_sub_account_head_info as c on c.sub_account_head_id = a.sub_account_head_id and c.status != 'Delete'
                left join cb_voucher_type_info as d on d.voucher_type_id = a.voucher_type_id and d.status != 'Delete'
                left join cb_sub_account_head_lvl3_info as e on e.sub_account_headlvl3_id = a.sub_account_headlvl3_id and e.status != 'Delete'
                left join tender_enquiry_info as f on f.tender_enquiry_id = a.project_id and f.status != 'Delete'
                where a.status != 'Delete' and $where  
                order by a.outward_date desc , a.cash_outward_id desc
                limit " . $this->uri->segment(2, 0) . "," . $config['per_page'] . "                
        ";

        $query = $this->db->query($sql);

        $data['record_list'] = array();

        foreach ($query->result_array() as $row) {
            $data['record_list'][] = $row;
        }

         $sql = "
              SELECT a.tender_enquiry_id, a.enquiry_no
                FROM tender_enquiry_info AS a
                INNER JOIN company_info as b
                on b.company_id = a.company_id
                WHERE a.status = 'Active'  
                AND b.status= 'Active'      
        ";


        $query = $this->db->query($sql);

        $data['project_opt'] = array();

        foreach ($query->result_array() as $row) {
            $data['project_opt'][$row['tender_enquiry_id']] = $row['enquiry_no'];
        }


        $sql = "
                select 
                a.account_head_id,                
                a.account_head_name             
                from cb_account_head_info as a  
                where a.status = 'Active' and a.type = 'Outward'
                and a.franchise_id = '" . ($this->session->userdata('cr_franchise_id') == '' ? 0 : $this->session->userdata('cr_franchise_id')) . "'
                order by a.account_head_name asc                 
        ";

        $query = $this->db->query($sql);

        $data['account_head_opt'] = array();

        foreach ($query->result_array() as $row) {
            $data['account_head_opt'][$row['account_head_id']] = $row['account_head_name'];
        }

        $sql = "
                select 
                a.voucher_type_id,                
                a.voucher_type_name ,
                a.prefix            
                from cb_voucher_type_info as a  
                where a.status = 'Active' and a.h_type = 'Outward'
                order by a.voucher_type_name asc                 
        ";

        $query = $this->db->query($sql);

        $data['voucher_type_opt'] = array();

        foreach ($query->result_array() as $row) {
            $data['voucher_type_opt'][$row['voucher_type_id']] = $row['voucher_type_name'] . "[ " . $row['prefix'] . " ]";
        }



        $sql = "
               select 
                a.company_id,                
                a.company_name            
                from company_info as a  
                where a.status = 'Active'
                order by a.company_name asc             
        ";

        $query = $this->db->query($sql);

        $data['company_name_opt'] = array();

        foreach ($query->result_array() as $row) {
            $data['company_name_opt'][$row['company_id']] = $row['company_name'];
        }

       

        $data['pagination'] = $this->pagination->create_links();

        $data['ac_type_opt'] = array('Bank' => 'Bank', 'Cash' => 'Cash');

        $this->load->view('page/accounts/cash-outward-list', $data);
    }

    public function print_voucher($cash_outward_id)
    {
        $this->load->model('cce_model');
        $sql = "
                select 
                a.*,
                fiscal_year(a.outward_date) as fyr,
                b.account_head_name,
                c.sub_account_head_name,
                d.voucher_type_name,
                d.prefix 
                from cb_cash_outward_info as a 
                left join cb_account_head_info as b on b.account_head_id = a.account_head_id and b.status != 'Delete'
                left join cb_sub_account_head_info as c on c.sub_account_head_id = a.sub_account_head_id and c.status != 'Delete'
                left join cb_voucher_type_info as d on d.voucher_type_id = a.voucher_type_id and d.status != 'Delete'
                where a.status != 'Delete' and   
                a.cash_outward_id = $cash_outward_id
                order by a.status asc , a.outward_date desc 
                              
        ";

        $query = $this->db->query($sql);

        $data['record_list'] = array();

        foreach ($query->result_array() as $row) {
            $data['record_list'] = $row;
        }

        $this->load->view('page/accounts/print-voucher', $data);

    }

    public function print_receipt($cash_inward_id)
    {
        $this->load->model('cce_model');
        $sql = "
                select 
                a.*,
                fiscal_year(a.inward_date) as fyr,
                b.account_head_name,
                c.sub_account_head_name,
                d.voucher_type_name,
                d.prefix 
                from cb_cash_inward_info as a 
                left join cb_account_head_info as b on b.account_head_id = a.account_head_id and b.status != 'Delete'
                left join cb_sub_account_head_info as c on c.sub_account_head_id = a.sub_account_head_id and c.status != 'Delete'
                left join cb_voucher_type_info as d on d.voucher_type_id = a.voucher_type_id and d.status != 'Delete'
                where a.status != 'Delete' and   
                a.cash_inward_id = $cash_inward_id
                order by a.status asc , a.inward_date desc 
                              
        ";

        $query = $this->db->query($sql);

        $data['record_list'] = array();

        foreach ($query->result_array() as $row) {
            $data['record_list'] = $row;
        }

        $this->load->view('page/accounts/print-receipt', $data);

    }

    public function cash_ledger()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        if ($this->session->userdata(SESS_HD . 'level') != 'Admin') {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }

        $data['js'] = 'accounts/cash-ledger.inc';

        if (isset($_POST['srch_from_date'])) {
            $data['srch_from_date'] = $srch_from_date = $this->input->post('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->input->post('srch_to_date');

        } else {
            $data['srch_from_date'] = $srch_from_date = date('Y-m-') . '01';
            $data['srch_to_date'] = $srch_to_date = date('Y-m-d');
        }


        if (isset($_POST['srch_ac_type'])) {
            $data['srch_ac_type'] = $srch_ac_type = $this->input->post('srch_ac_type');

        } else {
            $data['srch_ac_type'] = $srch_ac_type = '';
        }







        $data['record_list'] = array();

        //if(!empty($srch_agent_id)) 
        {

            /*  
            $sql_op = "

               select 

               '" . $srch_from_date . "' as t_date,

               '' as vno,

               'Opening Balance' as particular,

               (sum(z.cash_in) - sum(z.cash_out)) as cash_in,

               0 as cash_out

               from 

               (
                   (
                      select  
                      sum(a.received_amount) as cash_in ,
                      0 as cash_out
                      from receipt_info as a 
                      where a.`status` = 'Active'
                      and a.receipt_date > '2023-10-01'
                      and a.receipt_date < '" . $srch_from_date . "'  

                   ) union all ( 
                      select  
                      sum(a.amount) as cash_in, 
                      0 as cash_out 
                      from cb_cash_inward_info as a  
                      where a.inward_date < '" . $srch_from_date . "'   
                      and a.status='Active'
                   )   union all ( 
                      select    
                      0 as cash_in, 
                      sum(a.amount) as cash_out 
                      from cb_cash_outward_info as a   
                      where a.outward_date < '" . $srch_from_date . "'  
                      and a.status='Active' 
                    )

                ) as z



              ";


              $sql_tr = "

               select 

               z.t_date,

               z.vno,

               z.particular,

               (z.cash_in) as cash_in,

               (z.cash_out) as cash_out

               from 

               (

                  (
                      select 
                      0 as sort,
                      a.receipt_date as t_date,
                      0 as vno,
                      c.company_name as particular,
                      sum(a.received_amount) as cash_in ,
                      0 as cash_out
                      from receipt_info as a
                      left join invoice_info as b on b.invoice_id = a.invoice_id 
                      left join client_info as c on c.client_id = b.client_id 
                      where a.`status` = 'Active'
                      and a.receipt_date > '2023-10-01'
                      and a.receipt_date between '" . $srch_from_date . "' and  '" . $srch_to_date .  "'
                      group by a.receipt_date , b.client_id
                      order by a.receipt_date desc

                   ) union all (

                     select 

                      1 as sort,

                      a.inward_date as t_date,

                      concat(d.prefix , a.vno) as vno,

                      CONCAT(a.ac_type , ' : ', b.account_head_name,' - ', c.sub_account_head_name , '<br>', a.remarks ) as particular,

                      a.amount as cash_in,

                      0 as cash_out

                      from cb_cash_inward_info as a

                      left join cb_account_head_info as b on b.account_head_id = a.account_head_id

                      left join cb_sub_account_head_info as c on c.sub_account_head_id  = a.sub_account_head_id

                      left join cb_voucher_type_info as d on d.voucher_type_id  = a.voucher_type_id

                      where a.inward_date between '" . $srch_from_date . "' and  '" . $srch_to_date .  "'

                      and a.status='Active'

                      order by a.inward_date asc , a.cash_inward_id 

                   ) union all (

                      select 

                      4 as sort,

                      a.outward_date as t_date,

                      concat(d.prefix , a.vno) as vno,

                      CONCAT(a.ac_type , ' : ', b.account_head_name,' - ', c.sub_account_head_name , '<br>', a.remarks ) as particular,

                      0 as cash_in,

                      a.amount as cash_out

                      from cb_cash_outward_info as a

                      left join cb_account_head_info as b on b.account_head_id = a.account_head_id

                      left join cb_sub_account_head_info as c on c.sub_account_head_id  = a.sub_account_head_id

                      left join cb_voucher_type_info as d on d.voucher_type_id  = a.voucher_type_id

                      where a.outward_date between '" . $srch_from_date . "' and  '" . $srch_to_date .  "' 

                      and a.status='Active'

                      order by a.outward_date asc , a.cash_outward_id 

                    )

                ) as z

                order by z.t_date asc , z.sort asc             

              ";


             $sql = "

              select 

               q.t_date,

               q.vno,

               q.particular,

               (q.cash_in) as cash_in,

               (q.cash_out) as cash_out

               from (

                      (" . $sql_op . ") union all (" . $sql_tr . ") 

                    ) as q

               order by q.t_date asc      

                  ";

               */


            if (!empty($srch_ac_type)) {
                $where = " and a.ac_type = '" . $srch_ac_type . "'";
            } else {
                $where = " and 1=1 ";
            }


            /*

             $sql_op = "

             select 

             z1.ac_type,

             '" . $srch_from_date . "' as t_date,

             '' as vno, 

             'Opening Balance' as particular,

             (sum(z1.cash_in) - sum(z1.cash_out)) as cash_in,

             0 as cash_out

             from 

             (
                 ( 
                    select  
                    a.ac_type,
                    sum(a.amount) as cash_in, 
                    0 as cash_out 
                    from cb_cash_inward_info as a  
                    where a.inward_date < '" . $srch_from_date . "'   
                    and a.status='Active'
                    $where
                    group by a.ac_type
                 )   union all ( 
                    select  
                    a.ac_type, 
                    0 as cash_in, 
                    sum(a.amount) as cash_out 
                    from cb_cash_outward_info as a   
                    where a.outward_date < '" . $srch_from_date . "'  
                    and a.status='Active' 
                    $where
                    group by a.ac_type
                  )

              ) as z1

              group by z1.ac_type              

            "; */



            $sql_op = "

         select 
         
         z1.ac_type,

         '" . $srch_from_date . "' as t_date,
         
         '' as vno, 

         'Opening Balance' as particular,

         (sum(z1.cash_in) - sum(z1.cash_out)) as cash_in,

         0 as cash_out

         from 

         (
             (
                select  
                w1.ac_type,
                sum(w1.amount) as cash_in, 
                0 as cash_out 
                from cb_opening_balance_info as w1
                where  w1.opening_balance_id  = (
                	ifnull((
                	select 
                	(w.opening_balance_id)
                	from cb_opening_balance_info as w
                	where w.ac_type = w1.ac_type
                	and w.opening_date <= '" . $srch_from_date . "' order by w.opening_date desc  limit 1)
                	,'0') 
                ) 
                group by w1.ac_type 
             
             )   union all (  
                select  
                a.ac_type,
                sum(a.amount) as cash_in, 
                0 as cash_out 
                from cb_cash_inward_info as a  
                where a.inward_date between (
                	ifnull((
                	select max(w.opening_date)
                	from cb_opening_balance_info as w
                	where w.ac_type = a.ac_type
                	and w.opening_date <= '" . $srch_from_date . "')
                	,'2024-04-01') 
                ) and DATE_SUB('" . $srch_from_date . "',INTERVAL 1 day)  
                and a.status='Active'
                $where
                group by a.ac_type
             )   union all ( 
                select  
                a.ac_type, 
                0 as cash_in, 
                sum(a.amount) as cash_out 
                from cb_cash_outward_info as a   
                where a.outward_date between (
                	ifnull((
                	select max(w.opening_date)
                	from cb_opening_balance_info as w
                	where w.ac_type = a.ac_type
                	and w.opening_date <= '" . $srch_from_date . "')
                	,'2024-04-01') 
                ) and DATE_SUB('" . $srch_from_date . "',INTERVAL 1 day)    
                and a.status='Active' 
                $where
                group by a.ac_type
              )

          ) as z1

          group by z1.ac_type              

        ";


            $sql_tr = "

         select 
         
         z.ac_type,

         z.t_date,
         
         z.vno,

         z.particular,

         (z.cash_in) as cash_in,

         (z.cash_out) as cash_out

         from 

         (

             (

               select 

                1 as sort,
                
                a.ac_type,

                a.inward_date as t_date,
                
                concat(d.prefix , a.vno) as vno,

                CONCAT(b.account_head_name,' - ', c.sub_account_head_name , '<br>', ifnull(e.sub_account_headlvl3_name,\"-\") , '<br>', a.remarks  ) as particular,

                a.amount as cash_in,

                0 as cash_out

                from cb_cash_inward_info as a

                left join cb_account_head_info as b on b.account_head_id = a.account_head_id

                left join cb_sub_account_head_info as c on c.sub_account_head_id  = a.sub_account_head_id
                
                left join cb_voucher_type_info as d on d.voucher_type_id  = a.voucher_type_id
                
                left join cb_sub_account_head_lvl3_info as e on e.sub_account_headlvl3_id  = a.sub_account_headlvl3_id

                where a.inward_date between '" . $srch_from_date . "' and  '" . $srch_to_date . "'
                
                and a.status='Active' 
                
                $where
                  
                order by a.ac_type , a.inward_date asc , a.cash_inward_id 

             ) union all (

                select 

                4 as sort,
                
                a.ac_type,

                a.outward_date as t_date,
                
                concat(d.prefix , a.vno) as vno,

                CONCAT( b.account_head_name,' - ', c.sub_account_head_name  , '<br>', ifnull(e.sub_account_headlvl3_name,\"-\") , '<br>', a.remarks ) as particular,

                0 as cash_in,

                a.amount as cash_out

                from cb_cash_outward_info as a

                left join cb_account_head_info as b on b.account_head_id = a.account_head_id

                left join cb_sub_account_head_info as c on c.sub_account_head_id  = a.sub_account_head_id
                
                left join cb_voucher_type_info as d on d.voucher_type_id  = a.voucher_type_id
                
                left join cb_sub_account_head_lvl3_info as e on e.sub_account_headlvl3_id  = a.sub_account_headlvl3_id

                where a.outward_date between '" . $srch_from_date . "' and  '" . $srch_to_date . "' 
                
                and a.status='Active'
                
                $where
                
                order by a.ac_type , a.outward_date asc , a.cash_outward_id 

              )

          ) as z

          order by z.ac_type , z.t_date asc , z.sort asc             

        ";


            $sql = "

        select 

         q.ac_type, 
            
         q.t_date,
         
         q.vno,

         q.particular,

         (q.cash_in) as cash_in,

         (q.cash_out) as cash_out

         from (

                (" . $sql_op . ") union all (" . $sql_tr . ") 

              ) as q

         order by q.ac_type, q.t_date asc      

            ";






            $query = $this->db->query($sql);



            foreach ($query->result_array() as $row) {

                $data['record_list'][$row['ac_type']][] = $row;

            }

            // echo "<pre>";    
            // print_r($sql_tr);
            // print_r($data['record_list']); 
            // echo "</pre>";    

        }

        $data['ac_type_opt'] = array('' => 'All', 'Bank' => 'Bank', 'Cash' => 'Cash');

        $this->load->view('page/accounts/cash-ledger', $data);
    }

    public function cash_in_statement()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        /*if($this->session->userdata('m_is_admin') != USER_ADMIN) 
        {
            echo "<h3 style='color:red;'>Permission Denied</h3>"; exit;
        }  */

        $data['js'] = 'accounts/cash-in-statement.inc';

        if ($this->session->userdata('cr_level') != 'Admin') {
            $data['min_date'] = date('Y-m-d', strtotime(date('Y-m-d') . ' - 2 days'));
            ;
            $data['max_date'] = date('Y-m-d');
        } else {
            $data['min_date'] = $data['max_date'] = '';
        }


        if (isset($_POST['srch_from_date'])) {
            $data['srch_from_date'] = $srch_from_date = $this->input->post('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->input->post('srch_to_date');

        } else {
            if ($this->session->userdata('cr_level') != 'Admin')
                $data['srch_from_date'] = $srch_from_date = $data['min_date'];
            else
                $data['srch_from_date'] = $srch_from_date = date('Y-m') . '-01';
            $data['srch_to_date'] = $srch_to_date = date('Y-m-d');
        }

        if (isset($_POST['srch_ac_type'])) {
            $data['srch_ac_type'] = $srch_ac_type = $this->input->post('srch_ac_type');
        } else {
            $data['srch_ac_type'] = $srch_ac_type = '';
        }

        if (isset($_POST['srch_account_head_id'])) {
            $data['srch_account_head_id'] = $srch_account_head_id = $this->input->post('srch_account_head_id');
        } else {
            $data['srch_account_head_id'] = $srch_account_head_id = '';
        }

        if (isset($_POST['srch_sub_account_head_id'])) {
            $data['srch_sub_account_head_id'] = $srch_sub_account_head_id = $this->input->post('srch_sub_account_head_id');
        } else {
            $data['srch_sub_account_head_id'] = $srch_sub_account_head_id = '';
        }

        if (isset($_POST['srch_inward_from'])) {
            $data['srch_inward_from'] = $srch_inward_from = $this->input->post('srch_inward_from');
        } else {
            $data['srch_inward_from'] = $srch_inward_from = '';
        }



        $sql = "
                select 
                a.account_head_id,                
                a.account_head_name             
                from cb_account_head_info as a  
                where a.status = 'Active' 
                and a.type = 'Inward'
                and a.ac_table = '1'
                order by a.account_head_name asc                 
        ";

        $query = $this->db->query($sql);

        $data['account_head_opt'] = array();
        $data['sub_account_head_opt'] = array();
        $data['inward_from_opt'] = array();

        foreach ($query->result_array() as $row) {
            $data['account_head_opt'][$row['account_head_id']] = $row['account_head_name'];
        }


        $data['ac_type_opt'] = array('Bank' => 'Bank', 'Cash' => 'Cash');



        $data['record_list'] = array();

        $where = "";

        if (!empty($srch_account_head_id)) {
            $where .= " and a.account_head_id = '" . $srch_account_head_id . "'";
        } else {
            $where .= " and 1=1 ";
        }


        if (!empty($srch_sub_account_head_id)) {
            $where .= " and a.sub_account_head_id = '" . $srch_sub_account_head_id . "'";
        } else {
            $where .= " and 1=1 ";
        }

        if (!empty($srch_ac_type)) {
            $where .= " and a.ac_type = '" . $srch_ac_type . "'";
        } else {
            $where .= " and 1=1 ";
        }

        if (!empty($srch_inward_from)) {
            $where .= " and a.sub_account_headlvl3_id = '" . $srch_inward_from . "'";
        } else {
            $where .= " and 1=1 ";
        }



        $sql = "
            select 
            a.inward_date,
            a.ac_type,
            concat(d.prefix , a.vno) as vno,
            b.account_head_name as account_head,
            c.sub_account_head_name as sub_account_head,
            a.amount,
            a.remarks,
            e.sub_account_headlvl3_name
            from cb_cash_inward_info as a
            left join cb_account_head_info as b on b.account_head_id = a.account_head_id
            left join cb_sub_account_head_info as c on c.sub_account_head_id = a.sub_account_head_id
            left join cb_voucher_type_info as d on d.voucher_type_id  = a.voucher_type_id
            left join cb_sub_account_head_lvl3_info as e on e.sub_account_headlvl3_id  = a.sub_account_headlvl3_id
            where a.`status` = 'Active'
            and b.ac_table = '1' and c.ac_table = '1'
            and a.inward_date between '" . $srch_from_date . "' and  '" . $srch_to_date . "' 
            $where
            order by a.inward_date , a.cash_inward_id asc  
            ";




        $query = $this->db->query($sql);



        foreach ($query->result_array() as $row) {

            $data['record_list'][$row['ac_type']][] = $row;

        }

        if (!empty($srch_account_head_id)) {
            $sql = "
                    select 
                    a.sub_account_head_id,
                    a.sub_account_head_name
                    from cb_sub_account_head_info as a
                    where a.`status` = 'Active'
                    and a.`type` = 'Inward'
                    and a.ac_table = '1'
                    and a.account_head_id = '" . $srch_account_head_id . "'
                    order by a.sub_account_head_name asc                 
            ";

            $query = $this->db->query($sql);

            foreach ($query->result_array() as $row) {
                $data['sub_account_head_opt'][$row['sub_account_head_id']] = $row['sub_account_head_name'];
            }

        }

        if (!empty($srch_sub_account_head_id)) {
            $sql = "
                    select 
                    a.sub_account_headlvl3_id,
                    a.sub_account_headlvl3_name
                    from cb_sub_account_head_lvl3_info as a
                    where a.`status` = 'Active'
                    and a.`type` = 'Inward'  
                    and a.sub_account_head_id = '" . $srch_sub_account_head_id . "'
                    order by a.sub_account_headlvl3_name asc                 
            ";

            $query = $this->db->query($sql);

            foreach ($query->result_array() as $row) {
                $data['inward_from_opt'][$row['sub_account_headlvl3_id']] = $row['sub_account_headlvl3_name'];
            }

        }

        $this->load->view('page/accounts/cash-in-statement', $data);
    }


    public function na_cash_in_statement()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        /*if($this->session->userdata('m_is_admin') != USER_ADMIN) 
        {
            echo "<h3 style='color:red;'>Permission Denied</h3>"; exit;
        }  */

        $data['js'] = 'accounts/cash-in-statement.inc';

        if ($this->session->userdata('cr_level') != 'Admin') {
            $data['min_date'] = date('Y-m-d', strtotime(date('Y-m-d') . ' - 2 days'));
            ;
            $data['max_date'] = date('Y-m-d');
        } else {
            $data['min_date'] = $data['max_date'] = '';
        }


        if (isset($_POST['srch_from_date'])) {
            $data['srch_from_date'] = $srch_from_date = $this->input->post('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->input->post('srch_to_date');

        } else {
            if ($this->session->userdata('cr_level') != 'Admin')
                $data['srch_from_date'] = $srch_from_date = $data['min_date'];
            else
                $data['srch_from_date'] = $srch_from_date = date('Y-m') . '-01';
            $data['srch_to_date'] = $srch_to_date = date('Y-m-d');
        }

        if (isset($_POST['srch_ac_type'])) {
            $data['srch_ac_type'] = $srch_ac_type = $this->input->post('srch_ac_type');
        } else {
            $data['srch_ac_type'] = $srch_ac_type = '';
        }

        if (isset($_POST['srch_account_head_id'])) {
            $data['srch_account_head_id'] = $srch_account_head_id = $this->input->post('srch_account_head_id');
        } else {
            $data['srch_account_head_id'] = $srch_account_head_id = '';
        }

        if (isset($_POST['srch_sub_account_head_id'])) {
            $data['srch_sub_account_head_id'] = $srch_sub_account_head_id = $this->input->post('srch_sub_account_head_id');
        } else {
            $data['srch_sub_account_head_id'] = $srch_sub_account_head_id = '';
        }

        if (isset($_POST['srch_inward_from'])) {
            $data['srch_inward_from'] = $srch_inward_from = $this->input->post('srch_inward_from');
        } else {
            $data['srch_inward_from'] = $srch_inward_from = '';
        }



        $sql = "
                select 
                a.account_head_id,                
                a.account_head_name             
                from cb_account_head_info as a  
                where a.status = 'Active' 
                and a.type = 'Inward'
                and a.ac_table != '1'
                order by a.account_head_name asc                 
        ";

        $query = $this->db->query($sql);

        $data['account_head_opt'] = array();
        $data['sub_account_head_opt'] = array();
        $data['inward_from_opt'] = array();

        foreach ($query->result_array() as $row) {
            $data['account_head_opt'][$row['account_head_id']] = $row['account_head_name'];
        }


        $data['ac_type_opt'] = array('Bank' => 'Bank', 'Cash' => 'Cash');



        $data['record_list'] = array();

        $where = "";

        if (!empty($srch_account_head_id)) {
            $where .= " and a.account_head_id = '" . $srch_account_head_id . "'";
        } else {
            $where .= " and 1=1 ";
        }


        if (!empty($srch_sub_account_head_id)) {
            $where .= " and a.sub_account_head_id = '" . $srch_sub_account_head_id . "'";
        } else {
            $where .= " and 1=1 ";
        }

        if (!empty($srch_ac_type)) {
            $where .= " and a.ac_type = '" . $srch_ac_type . "'";
        } else {
            $where .= " and 1=1 ";
        }

        if (!empty($srch_inward_from)) {
            $where .= " and a.sub_account_headlvl3_id = '" . $srch_inward_from . "'";
        } else {
            $where .= " and 1=1 ";
        }



        $sql = "
            select 
            a.inward_date,
            a.ac_type,
            concat(d.prefix , a.vno) as vno,
            b.account_head_name as account_head,
            c.sub_account_head_name as sub_account_head,
            a.amount,
            a.remarks,
            e.sub_account_headlvl3_name
            from cb_cash_inward_info as a
            left join cb_account_head_info as b on b.account_head_id = a.account_head_id
            left join cb_sub_account_head_info as c on c.sub_account_head_id = a.sub_account_head_id
            left join cb_voucher_type_info as d on d.voucher_type_id  = a.voucher_type_id
            left join cb_sub_account_head_lvl3_info as e on e.sub_account_headlvl3_id  = a.sub_account_headlvl3_id
            where a.`status` = 'Active'
            and b.ac_table != '1' and c.ac_table != '1'
            and a.inward_date between '" . $srch_from_date . "' and  '" . $srch_to_date . "' 
            $where
            order by a.inward_date , a.cash_inward_id asc  
            ";




        $query = $this->db->query($sql);



        foreach ($query->result_array() as $row) {

            $data['record_list'][$row['ac_type']][] = $row;

        }

        if (!empty($srch_account_head_id)) {
            $sql = "
                    select 
                    a.sub_account_head_id,
                    a.sub_account_head_name
                    from cb_sub_account_head_info as a
                    where a.`status` = 'Active'
                    and a.`type` = 'Inward'
                    and a.ac_table != '1'
                    and a.account_head_id = '" . $srch_account_head_id . "'
                    order by a.sub_account_head_name asc                 
            ";

            $query = $this->db->query($sql);

            foreach ($query->result_array() as $row) {
                $data['sub_account_head_opt'][$row['sub_account_head_id']] = $row['sub_account_head_name'];
            }

        }

        if (!empty($srch_sub_account_head_id)) {
            $sql = "
                    select 
                    a.sub_account_headlvl3_id,
                    a.sub_account_headlvl3_name
                    from cb_sub_account_head_lvl3_info as a
                    where a.`status` = 'Active'
                    and a.`type` = 'Inward'  
                    and a.sub_account_head_id = '" . $srch_sub_account_head_id . "'
                    order by a.sub_account_headlvl3_name asc                 
            ";

            $query = $this->db->query($sql);

            foreach ($query->result_array() as $row) {
                $data['inward_from_opt'][$row['sub_account_headlvl3_id']] = $row['sub_account_headlvl3_name'];
            }

        }

        $this->load->view('page/accounts/na-cash-in-statement', $data);
    }

    public function cash_out_statement()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        /*if($this->session->userdata('m_is_admin') != USER_ADMIN) 
        {
            echo "<h3 style='color:red;'>Permission Denied</h3>"; exit;
        }  */

        $data['js'] = 'accounts/cash-out-statement.inc';

        if ($this->session->userdata('cr_level') != 'Admin') {
            $data['min_date'] = date('Y-m-d', strtotime(date('Y-m-d') . ' - 2 days'));
            ;
            $data['max_date'] = date('Y-m-d');
        } else {
            $data['min_date'] = $data['max_date'] = '';
        }





        if (isset($_POST['srch_from_date'])) {
            $data['srch_from_date'] = $srch_from_date = $this->input->post('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->input->post('srch_to_date');

        } else {
            if ($this->session->userdata('cr_level') != 'Admin')
                $data['srch_from_date'] = $srch_from_date = $data['min_date'];
            else
                $data['srch_from_date'] = $srch_from_date = date('Y-m') . '-01';
            $data['srch_to_date'] = $srch_to_date = date('Y-m-d');
        }

        if (isset($_POST['srch_ac_type'])) {
            $data['srch_ac_type'] = $srch_ac_type = $this->input->post('srch_ac_type');
        } else {
            $data['srch_ac_type'] = $srch_ac_type = '';
        }

        if (isset($_POST['srch_account_head_id'])) {
            $data['srch_account_head_id'] = $srch_account_head_id = $this->input->post('srch_account_head_id');
        } else {
            $data['srch_account_head_id'] = $srch_account_head_id = '';
        }
        if (isset($_POST['srch_sub_account_head_id'])) {
            $data['srch_sub_account_head_id'] = $srch_sub_account_head_id = $this->input->post('srch_sub_account_head_id');
        } else {
            $data['srch_sub_account_head_id'] = $srch_sub_account_head_id = '';
        }

        if (isset($_POST['srch_outward_for'])) {
            $data['srch_outward_for'] = $srch_outward_for = $this->input->post('srch_outward_for');
        } else {
            $data['srch_outward_for'] = $srch_outward_for = '';
        }

        if (isset($_POST['srch_project_id'])) {
            $data['srch_project_id'] = $srch_project_id = $this->input->post('srch_project_id');
        } else {
            $data['srch_project_id'] = $srch_project_id = '';
        }




        $sql = "
                select 
                a.account_head_id,                
                a.account_head_name             
                from cb_account_head_info as a  
                where a.status = 'Active' 
                and a.type = 'Outward'
                and a.ac_table = '1'
                order by a.account_head_name asc                 
        ";

        $query = $this->db->query($sql);

        $data['account_head_opt'] = array();
        $data['sub_account_head_opt'] = array();
        $data['outward_for_opt'] = array();

        foreach ($query->result_array() as $row) {
            $data['account_head_opt'][$row['account_head_id']] = $row['account_head_name'];
        }


        $data['ac_type_opt'] = array('Bank' => 'Bank', 'Cash' => 'Cash');


        $sql = "
                select 
                a.tender_enquiry_id,
                a.enquiry_no
                from tender_enquiry_info as a  
                where a.status = 'Active'  
                order by a.enquiry_no desc                 
        ";

        $query = $this->db->query($sql);

        $data['project_opt'] = array();

        foreach ($query->result_array() as $row) {
            $data['project_opt'][$row['tender_enquiry_id']] = $row['enquiry_no'];
        }


        $data['record_list'] = array();

        $where = "";

        if (!empty($srch_account_head_id)) {
            $where .= " and a.account_head_id = '" . $srch_account_head_id . "'";
        } else {
            $where .= " and 1=1 ";
        }

        if (!empty($srch_sub_account_head_id)) {
            $where .= " and a.sub_account_head_id = '" . $srch_sub_account_head_id . "'";
        } else {
            $where .= " and 1=1 ";
        }

        if (!empty($srch_ac_type)) {
            $where .= " and a.ac_type = '" . $srch_ac_type . "'";
        } else {
            $where .= " and 1=1 ";
        }

        if (!empty($srch_outward_for)) {
            $where .= " and a.sub_account_headlvl3_id = '" . $srch_outward_for . "'";
        } else {
            $where .= " and 1=1 ";
        }

        if (!empty($srch_project_id)) {
            $where .= " and a.project_id = '" . $srch_project_id . "'";
        } else {
            $where .= " and 1=1 ";
        }





        $sql = "
            select 
            a.outward_date,
            a.ac_type,
            concat(d.prefix , a.vno) as vno,
            b.account_head_name as account_head,
            c.sub_account_head_name as sub_account_head,
            a.amount,
            a.remarks,
            a.bill_photo,
            e.sub_account_headlvl3_name as outward_for ,
            f.enquiry_no
            from cb_cash_outward_info as a
            left join cb_account_head_info as b on b.account_head_id = a.account_head_id
            left join cb_sub_account_head_info as c on c.sub_account_head_id = a.sub_account_head_id
            left join cb_voucher_type_info as d on d.voucher_type_id  = a.voucher_type_id
            left join cb_sub_account_head_lvl3_info as e on e.sub_account_headlvl3_id  = a.sub_account_headlvl3_id
            left join tender_enquiry_info as f on f.tender_enquiry_id  = a.project_id
            where a.`status` = 'Active'
            and b.ac_table = '1'
            and c.ac_table = '1'
            and a.outward_date between '" . $srch_from_date . "' and  '" . $srch_to_date . "' 
            $where
            order by a.outward_date , a.cash_outward_id asc  
            ";




        $query = $this->db->query($sql);



        foreach ($query->result_array() as $row) {

            $data['record_list'][$row['ac_type']][] = $row;

        }

        if (!empty($srch_account_head_id)) {

            $sql = "
                select 
                a.sub_account_head_id,
                a.sub_account_head_name
                from cb_sub_account_head_info as a
                where a.`status` = 'Active'
                and a.`type` = 'Outward'
                and a.ac_table = '1'
                and a.account_head_id = '" . $srch_account_head_id . "'
                order by a.sub_account_head_name asc                 
        ";

            $query = $this->db->query($sql);

            foreach ($query->result_array() as $row) {
                $data['sub_account_head_opt'][$row['sub_account_head_id']] = $row['sub_account_head_name'];
            }


            if (!empty($srch_sub_account_head_id)) {
                $sql = "
                    select 
                    a.sub_account_headlvl3_id,
                    a.sub_account_headlvl3_name
                    from cb_sub_account_head_lvl3_info as a
                    where a.`status` = 'Active'
                    and a.`type` = 'Outward'
                    and a.sub_account_head_id = '" . $srch_sub_account_head_id . "'
                    order by a.sub_account_headlvl3_name asc                 
            ";

                $query = $this->db->query($sql);

                foreach ($query->result_array() as $row) {
                    $data['outward_for_opt'][$row['sub_account_headlvl3_id']] = $row['sub_account_headlvl3_name'];
                }

            }

        }

        $this->load->view('page/accounts/cash-out-statement', $data);
    }


    public function na_cash_out_statement()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        /*if($this->session->userdata('m_is_admin') != USER_ADMIN) 
        {
            echo "<h3 style='color:red;'>Permission Denied</h3>"; exit;
        }  */

        $data['js'] = 'accounts/cash-out-statement.inc';

        if ($this->session->userdata('cr_level') != 'Admin') {
            $data['min_date'] = date('Y-m-d', strtotime(date('Y-m-d') . ' - 2 days'));
            ;
            $data['max_date'] = date('Y-m-d');
        } else {
            $data['min_date'] = $data['max_date'] = '';
        }





        if (isset($_POST['srch_from_date'])) {
            $data['srch_from_date'] = $srch_from_date = $this->input->post('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->input->post('srch_to_date');

        } else {
            if ($this->session->userdata('cr_level') != 'Admin')
                $data['srch_from_date'] = $srch_from_date = $data['min_date'];
            else
                $data['srch_from_date'] = $srch_from_date = date('Y-m') . '-01';
            $data['srch_to_date'] = $srch_to_date = date('Y-m-d');
        }

        if (isset($_POST['srch_ac_type'])) {
            $data['srch_ac_type'] = $srch_ac_type = $this->input->post('srch_ac_type');
        } else {
            $data['srch_ac_type'] = $srch_ac_type = '';
        }

        if (isset($_POST['srch_account_head_id'])) {
            $data['srch_account_head_id'] = $srch_account_head_id = $this->input->post('srch_account_head_id');
        } else {
            $data['srch_account_head_id'] = $srch_account_head_id = '';
        }
        if (isset($_POST['srch_sub_account_head_id'])) {
            $data['srch_sub_account_head_id'] = $srch_sub_account_head_id = $this->input->post('srch_sub_account_head_id');
        } else {
            $data['srch_sub_account_head_id'] = $srch_sub_account_head_id = '';
        }

        if (isset($_POST['srch_outward_for'])) {
            $data['srch_outward_for'] = $srch_outward_for = $this->input->post('srch_outward_for');
        } else {
            $data['srch_outward_for'] = $srch_outward_for = '';
        }

        if (isset($_POST['srch_project_id'])) {
            $data['srch_project_id'] = $srch_project_id = $this->input->post('srch_project_id');
        } else {
            $data['srch_project_id'] = $srch_project_id = '';
        }


        $sql = "
                select 
                a.account_head_id,                
                a.account_head_name             
                from cb_account_head_info as a  
                where a.status = 'Active' 
                and a.type = 'Outward'
                and a.ac_table != '1'
                order by a.account_head_name asc                 
        ";

        $query = $this->db->query($sql);

        $data['account_head_opt'] = array();
        $data['sub_account_head_opt'] = array();
        $data['outward_for_opt'] = array();

        foreach ($query->result_array() as $row) {
            $data['account_head_opt'][$row['account_head_id']] = $row['account_head_name'];
        }


        $data['ac_type_opt'] = array('Bank' => 'Bank', 'Cash' => 'Cash');


        $sql = "
                select 
                a.tender_enquiry_id,
                a.enquiry_no
                from tender_enquiry_info as a  
                where a.status = 'Active'  
                order by a.enquiry_no desc                 
        ";

        $query = $this->db->query($sql);

        $data['project_opt'] = array();

        foreach ($query->result_array() as $row) {
            $data['project_opt'][$row['tender_enquiry_id']] = $row['enquiry_no'];
        }

        $data['record_list'] = array();

        $where = "";

        if (!empty($srch_account_head_id)) {
            $where .= " and a.account_head_id = '" . $srch_account_head_id . "'";
        } else {
            $where .= " and 1=1 ";
        }

        if (!empty($srch_sub_account_head_id)) {
            $where .= " and a.sub_account_head_id = '" . $srch_sub_account_head_id . "'";
        } else {
            $where .= " and 1=1 ";
        }

        if (!empty($srch_ac_type)) {
            $where .= " and a.ac_type = '" . $srch_ac_type . "'";
        } else {
            $where .= " and 1=1 ";
        }

        if (!empty($srch_outward_for)) {
            $where .= " and a.sub_account_headlvl3_id = '" . $srch_outward_for . "'";
        } else {
            $where .= " and 1=1 ";
        }

        if (!empty($srch_project_id)) {
            $where .= " and a.tender_enquiry_id = '" . $srch_project_id . "'";
        } else {
            $where .= " and 1=1 ";
        }


        $sql = "
            select 
            a.outward_date,
            a.ac_type,
            concat(d.prefix , a.vno) as vno,
            b.account_head_name as account_head,
            c.sub_account_head_name as sub_account_head,
            a.amount,
            a.remarks,
            a.bill_photo,
            e.sub_account_headlvl3_name as outward_for 
            from cb_cash_outward_info as a
            left join cb_account_head_info as b on b.account_head_id = a.account_head_id
            left join cb_sub_account_head_info as c on c.sub_account_head_id = a.sub_account_head_id
            left join cb_voucher_type_info as d on d.voucher_type_id  = a.voucher_type_id
            left join cb_sub_account_head_lvl3_info as e on e.sub_account_headlvl3_id  = a.sub_account_headlvl3_id
            where a.`status` = 'Active'
            and b.ac_table != '1'
            and c.ac_table != '1'
            and a.outward_date between '" . $srch_from_date . "' and  '" . $srch_to_date . "' 
            $where
            order by a.outward_date , a.cash_outward_id asc  
            ";




        $query = $this->db->query($sql);



        foreach ($query->result_array() as $row) {

            $data['record_list'][$row['ac_type']][] = $row;

        }

        if (!empty($srch_account_head_id)) {

            $sql = "
                select 
                a.sub_account_head_id,
                a.sub_account_head_name
                from cb_sub_account_head_info as a
                where a.`status` = 'Active'
                and a.`type` = 'Outward'
                and a.ac_table != '1'
                and a.account_head_id = '" . $srch_account_head_id . "'
                order by a.sub_account_head_name asc                 
        ";

            $query = $this->db->query($sql);

            foreach ($query->result_array() as $row) {
                $data['sub_account_head_opt'][$row['sub_account_head_id']] = $row['sub_account_head_name'];
            }


            if (!empty($srch_sub_account_head_id)) {
                $sql = "
                    select 
                    a.sub_account_headlvl3_id,
                    a.sub_account_headlvl3_name
                    from cb_sub_account_head_lvl3_info as a
                    where a.`status` = 'Active'
                    and a.`type` = 'Outward'
                    and a.sub_account_head_id = '" . $srch_sub_account_head_id . "'
                    order by a.sub_account_headlvl3_name asc                 
            ";

                $query = $this->db->query($sql);

                foreach ($query->result_array() as $row) {
                    $data['outward_for_opt'][$row['sub_account_headlvl3_id']] = $row['sub_account_headlvl3_name'];
                }

            }

        }

        $this->load->view('page/accounts/na-cash-out-statement', $data);
    }



    public function inward_summary()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        /*if($this->session->userdata('m_is_admin') != USER_ADMIN) 
        {
            echo "<h3 style='color:red;'>Permission Denied</h3>"; exit;
        }  */

        $data['js'] = 'accounts/inward-summary.inc';

        if (isset($_POST['srch_from_date'])) {
            $data['srch_from_date'] = $srch_from_date = $this->input->post('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->input->post('srch_to_date');

        } else {
            $data['srch_from_date'] = $srch_from_date = date('Y-m');
            $data['srch_to_date'] = $srch_to_date = date('Y-m');
        }

        if (isset($_POST['srch_ac_type'])) {
            $data['srch_ac_type'] = $srch_ac_type = $this->input->post('srch_ac_type');
        } else {
            $data['srch_ac_type'] = $srch_ac_type = '';
        }



        $data['ac_type_opt'] = array('Bank' => 'Bank', 'Cash' => 'Cash');


        $data['record_list'] = array();

        $where = "";


        if (!empty($srch_ac_type)) {
            $where .= " and a.ac_type = '" . $srch_ac_type . "'";
        } else {
            $where .= " and 1=1 ";
        }






        $sql = "  
            select 
            a.account_head_id as id,
            DATE_FORMAT(a.inward_date,'%Y%m') num_mon, 
            DATE_FORMAT(a.inward_date,'%b - %Y') as ap_mon, 
            a.ac_type ,  
            b.account_head_name as account_head,
            sum(a.amount) as inward 
            from cb_cash_inward_info as a
            left join cb_account_head_info as b on b.account_head_id = a.account_head_id
            where a.`status` != 'Delete' and b.`status` != 'Delete'
            and b.ac_table = '1'
            and DATE_FORMAT(a.inward_date,'%Y-%m') between '" . $srch_from_date . "' and '" . $srch_to_date . "'
            $where
            group by DATE_FORMAT(a.inward_date,'%Y%m') , a.ac_type , a.account_head_id 
            order by DATE_FORMAT(a.inward_date,'%Y%m') , a.ac_type , a.account_head_id;
            
        ";
        $query = $this->db->query($sql);

        $data['inward_rec'] = array();
        $data['ap_mon'] = array();

        foreach ($query->result_array() as $row) {
            $data['inward_rec'][$row['ac_type']][$row['account_head']][$row['ap_mon']] = $row;
            $data['ap_mon'][$row['num_mon']] = $row['ap_mon'];
        }


        $this->load->view('page/accounts/inward-summary', $data);
    }


    public function outward_summary()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        /*if($this->session->userdata('m_is_admin') != USER_ADMIN) 
        {
            echo "<h3 style='color:red;'>Permission Denied</h3>"; exit;
        }  */

        $data['js'] = 'accounts/outward-summary.inc';

        if (isset($_POST['srch_from_date'])) {
            $data['srch_from_date'] = $srch_from_date = $this->input->post('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->input->post('srch_to_date');

        } else {
            $data['srch_from_date'] = $srch_from_date = date('Y-m');
            $data['srch_to_date'] = $srch_to_date = date('Y-m');
        }

        if (isset($_POST['srch_ac_type'])) {
            $data['srch_ac_type'] = $srch_ac_type = $this->input->post('srch_ac_type');
        } else {
            $data['srch_ac_type'] = $srch_ac_type = '';
        }



        $data['ac_type_opt'] = array('Bank' => 'Bank', 'Cash' => 'Cash');


        $data['record_list'] = array();

        $where = "";


        if (!empty($srch_ac_type)) {
            $where .= " and a.ac_type = '" . $srch_ac_type . "'";
        } else {
            $where .= " and 1=1 ";
        }



        $sql = " 
        
            select 
            a.account_head_id as id,
            DATE_FORMAT(a.outward_date,'%Y%m') num_mon, 
            DATE_FORMAT(a.outward_date,'%b - %Y') as ap_mon, 
            a.ac_type ,  
            b.account_head_name as account_head,
            sum(a.amount) as outward 
            from cb_cash_outward_info as a
            left join cb_account_head_info as b on b.account_head_id = a.account_head_id
            where a.`status` != 'Delete' and b.`status` != 'Delete'
            and b.ac_table = '1'
            and DATE_FORMAT(a.outward_date,'%Y-%m') between '" . $srch_from_date . "' and '" . $srch_to_date . "'
            $where
            group by DATE_FORMAT(a.outward_date,'%Y%m') , a.ac_type , a.account_head_id 
            order by DATE_FORMAT(a.outward_date,'%Y%m') , a.ac_type , a.account_head_id;
            
        ";


        $query = $this->db->query($sql);

        $data['outward_rec'] = array();
        $data['ap_mon'] = array();

        foreach ($query->result_array() as $row) {
            $data['outward_rec'][$row['ac_type']][$row['account_head']][$row['ap_mon']] = $row;
            $data['ap_mon'][$row['num_mon']] = $row['ap_mon'];
        }


        $this->load->view('page/accounts/outward-summary', $data);
    }

    public function voucher_type_list()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        if ($this->session->userdata(SESS_HD . 'level') != 'Admin') {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }


        $data['js'] = 'accounts/voucher-type.inc';

        if ($this->input->post('mode') == 'Add') {
            $ins = array(
                'voucher_type_name' => $this->input->post('voucher_type_name'),
                'prefix' => $this->input->post('prefix'),
                'h_type' => $this->input->post('h_type'),
                'status' => $this->input->post('status'),
            );

            $this->db->insert('cb_voucher_type_info', $ins);
            redirect('voucher-type-list');
        }

        if ($this->input->post('mode') == 'Edit') {
            $upd = array(
                'voucher_type_name' => $this->input->post('voucher_type_name'),
                'prefix' => $this->input->post('prefix'),
                'h_type' => $this->input->post('h_type'),
                'status' => $this->input->post('status'),
            );

            $this->db->where('voucher_type_id', $this->input->post('voucher_type_id'));
            $this->db->update('cb_voucher_type_info', $upd);

            redirect('voucher-type-list/' . $this->uri->segment(2, 0));
        }





        $this->db->where('status != ', 'Delete');
        $this->db->from('cb_voucher_type_info');



        $sql = "
                select 
                a.voucher_type_id,
                a.voucher_type_name,   
                a.prefix,
                a.h_type,             
                a.status
                from cb_voucher_type_info as a 
                where a.status != 'Delete'
                order by a.status asc , a.h_type, a.voucher_type_name asc 
         ";

        //a.status = 'Booked'  

        $query = $this->db->query($sql);

        $data['record_list'] = array();
        foreach ($query->result_array() as $row) {
            $data['record_list'][] = $row;
        }


        $this->load->view('page/accounts/voucher-type-list', $data);
    }


    public function opening_balance_list()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        if ($this->session->userdata(SESS_HD . 'level') != 'Admin') {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }


        $data['js'] = 'accounts/opening-balance.inc';

        if ($this->input->post('mode') == 'Add') {
            $ins = array(
                'opening_date' => $this->input->post('opening_date'),
                'ac_type' => $this->input->post('ac_type'),
                'amount' => $this->input->post('amount'),
            );

            $this->db->insert('cb_opening_balance_info', $ins);
            redirect('opening-balance-list');
        }

        if ($this->input->post('mode') == 'Edit') {
            $upd = array(

                'opening_balance_id' => $this->input->post('opening_balance_id'),
                'opening_date' => $this->input->post('opening_date'),
                'ac_type' => $this->input->post('ac_type'),
                'amount' => $this->input->post('amount'),
            );

            $this->db->where('opening_balance_id', $this->input->post('opening_balance_id'));
            $this->db->update('cb_opening_balance_info', $upd);

            redirect('opening-balance-list/' . $this->uri->segment(2, 0));
        }

        $this->db->where('status != ', 'Delete');
        $this->db->from('cb_opening_balance_info');

        $sql = "
                select 
                a.*
                from cb_opening_balance_info as a 
                where a.status != 'Delete'
                order by a.opening_date desc,  a.opening_balance_id desc 
         ";

        //a.status = 'Booked'  

        $query = $this->db->query($sql);

        $data['record_list'] = array();
        foreach ($query->result_array() as $row) {
            $data['record_list'][] = $row;
        }

        $data['ac_type_opt'] = array('Bank' => 'Bank', 'Cash' => 'Cash');


        $this->load->view('page/accounts/opening-balance-list', $data);
    }



    public function tds_report()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();


        if ($this->input->post('mode') == 'Update TDS') {
            /*
            echo "<pre>";
            print_r($_POST);
            echo "</pre>"; 
            */

            $cash_outward_id = $this->input->post('cash_outward_id');
            $tds_prt = $this->input->post('tds_prt');
            $tds_amt = $this->input->post('tds_amt');
            $tds_updt_by = $this->input->post('tds_updt_by');

            foreach ($cash_outward_id as $key => $co_id) {
                if (!empty($tds_prt[$co_id])) {
                    $upd = array(

                        'tds_prt' => $tds_prt[$co_id],
                        'tds_amt' => $tds_amt[$co_id],
                        'tds_updt_by' => $tds_updt_by,
                        'updated_by' => $this->session->userdata('cr_user_id'),
                        'updated_datetime' => date('Y-m-d H:i:s')
                    );

                    $this->db->where('cash_outward_id', $co_id);
                    $this->db->update('cb_cash_outward_info', $upd);
                }
            }

            redirect('tds-report');
        }

        /*if($this->session->userdata('m_is_admin') != USER_ADMIN) 
        {
            echo "<h3 style='color:red;'>Permission Denied</h3>"; exit;
        }  */

        $data['js'] = 'accounts/tds-report.inc';


        $data['min_date'] = $data['max_date'] = '';





        if (isset($_POST['srch_from_date'])) {
            $data['srch_from_date'] = $srch_from_date = $this->input->post('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->input->post('srch_to_date');
            $this->session->set_userdata('srch_from_date', $this->input->post('srch_from_date'));
            $this->session->set_userdata('srch_to_date', $this->input->post('srch_to_date'));
        } elseif ($this->session->userdata('srch_from_date')) {
            $data['srch_from_date'] = $srch_from_date = $this->session->userdata('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->session->userdata('srch_to_date');
        } else {
            $data['srch_from_date'] = $srch_from_date = date('Y-m-01');
            $data['srch_to_date'] = $srch_to_date = date('Y-m-d');
        }

        if (isset($_POST['srch_ac_type'])) {
            $data['srch_ac_type'] = $srch_ac_type = $this->input->post('srch_ac_type');
            $this->session->set_userdata('srch_ac_type', $this->input->post('srch_ac_type'));
        } elseif ($this->session->userdata('srch_ac_type')) {
            $data['srch_ac_type'] = $srch_ac_type = $this->session->userdata('srch_ac_type');
        } else {
            $data['srch_ac_type'] = $srch_ac_type = '';
        }

        if (isset($_POST['srch_account_head_id'])) {
            $data['srch_account_head_id'] = $srch_account_head_id = $this->input->post('srch_account_head_id');
            $this->session->set_userdata('srch_account_head_id', $this->input->post('srch_account_head_id'));
        } elseif ($this->session->userdata('srch_account_head_id')) {
            $data['srch_account_head_id'] = $srch_account_head_id = $this->session->userdata('srch_account_head_id');
        } else {
            $data['srch_account_head_id'] = $srch_account_head_id = '';
        }
        if (isset($_POST['srch_sub_account_head_id'])) {
            $data['srch_sub_account_head_id'] = $srch_sub_account_head_id = $this->input->post('srch_sub_account_head_id');
            $this->session->set_userdata('srch_sub_account_head_id', $this->input->post('srch_sub_account_head_id'));
        } elseif ($this->session->userdata('srch_sub_account_head_id')) {
            $data['srch_sub_account_head_id'] = $srch_sub_account_head_id = $this->session->userdata('srch_sub_account_head_id');
        } else {
            $data['srch_sub_account_head_id'] = $srch_sub_account_head_id = '';
        }

        if (isset($_POST['srch_outward_for'])) {
            $data['srch_outward_for'] = $srch_outward_for = $this->input->post('srch_outward_for');
            $this->session->set_userdata('srch_outward_for', $this->input->post('srch_outward_for'));
        } elseif ($this->session->userdata('srch_outward_for')) {
            $data['srch_outward_for'] = $srch_outward_for = $this->session->userdata('srch_outward_for');
        } else {
            $data['srch_outward_for'] = $srch_outward_for = '';
        }




        $sql = "
                select 
                a.account_head_id,                
                a.account_head_name             
                from cb_account_head_info as a  
                where a.status = 'Active' 
                and a.type = 'Outward'
                and a.ac_table = '1'
                order by a.account_head_name asc                 
        ";

        $query = $this->db->query($sql);

        $data['account_head_opt'] = array();
        $data['sub_account_head_opt'] = array();
        $data['outward_for_opt'] = array();

        foreach ($query->result_array() as $row) {
            $data['account_head_opt'][$row['account_head_id']] = $row['account_head_name'];
        }


        $data['ac_type_opt'] = array('Bank' => 'Bank', 'Cash' => 'Cash');


        $data['record_list'] = array();

        $where = "";

        if (!empty($srch_account_head_id)) {
            $where .= " and a.account_head_id = '" . $srch_account_head_id . "'";
        } else {
            $where .= " and 1=1 ";
        }

        if (!empty($srch_sub_account_head_id)) {
            $where .= " and a.sub_account_head_id = '" . $srch_sub_account_head_id . "'";
        } else {
            $where .= " and 1=1 ";
        }

        if (!empty($srch_ac_type)) {
            $where .= " and a.ac_type = '" . $srch_ac_type . "'";
        } else {
            $where .= " and 1=1 ";
        }

        if (!empty($srch_outward_for)) {
            $where .= " and a.sub_account_headlvl3_id = '" . $srch_outward_for . "'";
        } else {
            $where .= " and 1=1 ";
        }





        $sql = "
            select 
            a.cash_outward_id,
            a.outward_date,
            a.ac_type,
            concat(d.prefix , a.vno) as vno,
            b.account_head_name as account_head,
            c.sub_account_head_name as sub_account_head,
            a.amount,
            a.remarks,
            a.bill_photo,
            e.sub_account_headlvl3_name as outward_for ,
            a.tds_amt,
            a.tds_prt,
            a.tds_updt_by
            from cb_cash_outward_info as a
            left join cb_account_head_info as b on b.account_head_id = a.account_head_id
            left join cb_sub_account_head_info as c on c.sub_account_head_id = a.sub_account_head_id
            left join cb_voucher_type_info as d on d.voucher_type_id  = a.voucher_type_id
            left join cb_sub_account_head_lvl3_info as e on e.sub_account_headlvl3_id  = a.sub_account_headlvl3_id
            where a.`status` = 'Active'
            and b.ac_table = '1'
            and c.ac_table = '1'
            and a.outward_date between '" . $srch_from_date . "' and  '" . $srch_to_date . "' 
            $where
            order by a.outward_date , a.cash_outward_id asc  
            ";




        $query = $this->db->query($sql);



        foreach ($query->result_array() as $row) {

            $data['record_list'][$row['ac_type']][] = $row;

        }

        if (!empty($srch_account_head_id)) {

            $sql = "
                select 
                a.sub_account_head_id,
                a.sub_account_head_name
                from cb_sub_account_head_info as a
                where a.`status` = 'Active'
                and a.`type` = 'Outward'
                and a.ac_table = '1'
                and a.account_head_id = '" . $srch_account_head_id . "'
                order by a.sub_account_head_name asc                 
        ";

            $query = $this->db->query($sql);

            foreach ($query->result_array() as $row) {
                $data['sub_account_head_opt'][$row['sub_account_head_id']] = $row['sub_account_head_name'];
            }


            if (!empty($srch_sub_account_head_id)) {
                $sql = "
                    select 
                    a.sub_account_headlvl3_id,
                    a.sub_account_headlvl3_name
                    from cb_sub_account_head_lvl3_info as a
                    where a.`status` = 'Active'
                    and a.`type` = 'Outward'
                    and a.sub_account_head_id = '" . $srch_sub_account_head_id . "'
                    order by a.sub_account_headlvl3_name asc                 
            ";

                $query = $this->db->query($sql);

                foreach ($query->result_array() as $row) {
                    $data['outward_for_opt'][$row['sub_account_headlvl3_id']] = $row['sub_account_headlvl3_name'];
                }

            }

        }

        $data['tds_prt_opt'] = array(
            '' => 'Select',
            '0.5' => '0.5',
            '1.00' => '1.00',
            '2.00' => '2.00',
            '3.00' => '3.00',
            '4.00' => '4.00',
            '5.00' => '5.00',
            '6.00' => '6.00',
            '7.00' => '7.00',
            '8.00' => '8.00',
            '9.00' => '9.00',
            '10.00' => '10.00',
        );

        $this->load->view('page/accounts/tds-report', $data);
    }


    public function bills_report()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();


        if ($this->input->post('mode') == 'Update Bills Verification') {
            /*
            echo "<pre>";
            print_r($_POST);
            echo "</pre>"; 
            */

            $upd = array(
                'bill_status' => $this->input->post('bill_status'),
                'bill_remarks' => $this->input->post('bill_remarks'),
                // 'updated_by' => $this->session->userdata('cr_user_id'),                          
                // 'updated_datetime' => date('Y-m-d H:i:s')              
            );

            $this->db->where('cash_outward_id', $this->input->post('cash_outward_id'));
            $this->db->update('cb_cash_outward_info', $upd);

            redirect('bills-report');
        }

        /*if($this->session->userdata('m_is_admin') != USER_ADMIN) 
        {
            echo "<h3 style='color:red;'>Permission Denied</h3>"; exit;
        }  */

        $data['js'] = 'accounts/bills-report.inc';

        $data['min_date'] = $data['max_date'] = '';




        if (isset($_POST['srch_from_date'])) {
            $data['srch_from_date'] = $srch_from_date = $this->input->post('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->input->post('srch_to_date');
            $this->session->set_userdata('srch_from_date', $this->input->post('srch_from_date'));
            $this->session->set_userdata('srch_to_date', $this->input->post('srch_to_date'));
        } elseif ($this->session->userdata('srch_from_date')) {
            $data['srch_from_date'] = $srch_from_date = $this->session->userdata('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->session->userdata('srch_to_date');
        } else {
            $data['srch_from_date'] = $srch_from_date = date('Y-m-01');
            $data['srch_to_date'] = $srch_to_date = date('Y-m-d');
        }

        if (isset($_POST['srch_ac_type'])) {
            $data['srch_ac_type'] = $srch_ac_type = $this->input->post('srch_ac_type');
            $this->session->set_userdata('srch_ac_type', $this->input->post('srch_ac_type'));
        } elseif ($this->session->userdata('srch_ac_type')) {
            $data['srch_ac_type'] = $srch_ac_type = $this->session->userdata('srch_ac_type');
        } else {
            $data['srch_ac_type'] = $srch_ac_type = '';
        }

        if (isset($_POST['srch_account_head_id'])) {
            $data['srch_account_head_id'] = $srch_account_head_id = $this->input->post('srch_account_head_id');
            $this->session->set_userdata('srch_account_head_id', $this->input->post('srch_account_head_id'));
        } elseif ($this->session->userdata('srch_account_head_id')) {
            $data['srch_account_head_id'] = $srch_account_head_id = $this->session->userdata('srch_account_head_id');
        } else {
            $data['srch_account_head_id'] = $srch_account_head_id = '';
        }
        if (isset($_POST['srch_sub_account_head_id'])) {
            $data['srch_sub_account_head_id'] = $srch_sub_account_head_id = $this->input->post('srch_sub_account_head_id');
            $this->session->set_userdata('srch_sub_account_head_id', $this->input->post('srch_sub_account_head_id'));
        } elseif ($this->session->userdata('srch_sub_account_head_id')) {
            $data['srch_sub_account_head_id'] = $srch_sub_account_head_id = $this->session->userdata('srch_sub_account_head_id');
        } else {
            $data['srch_sub_account_head_id'] = $srch_sub_account_head_id = '';
        }

        if (isset($_POST['srch_outward_for'])) {
            $data['srch_outward_for'] = $srch_outward_for = $this->input->post('srch_outward_for');
            $this->session->set_userdata('srch_outward_for', $this->input->post('srch_outward_for'));
        } elseif ($this->session->userdata('srch_outward_for')) {
            $data['srch_outward_for'] = $srch_outward_for = $this->session->userdata('srch_outward_for');
        } else {
            $data['srch_outward_for'] = $srch_outward_for = '';
        }




        $sql = "
                select 
                a.account_head_id,                
                a.account_head_name             
                from cb_account_head_info as a  
                where a.status = 'Active' 
                and a.type = 'Outward'
                and a.ac_table = '1'
                order by a.account_head_name asc                 
        ";

        $query = $this->db->query($sql);

        $data['account_head_opt'] = array();
        $data['sub_account_head_opt'] = array();
        $data['outward_for_opt'] = array();

        foreach ($query->result_array() as $row) {
            $data['account_head_opt'][$row['account_head_id']] = $row['account_head_name'];
        }


        $data['ac_type_opt'] = array('Bank' => 'Bank', 'Cash' => 'Cash');


        $data['record_list'] = array();

        $where = "";

        if (!empty($srch_account_head_id)) {
            $where .= " and a.account_head_id = '" . $srch_account_head_id . "'";
        } else {
            $where .= " and 1=1 ";
        }

        if (!empty($srch_sub_account_head_id)) {
            $where .= " and a.sub_account_head_id = '" . $srch_sub_account_head_id . "'";
        } else {
            $where .= " and 1=1 ";
        }

        if (!empty($srch_ac_type)) {
            $where .= " and a.ac_type = '" . $srch_ac_type . "'";
        } else {
            $where .= " and 1=1 ";
        }

        if (!empty($srch_outward_for)) {
            $where .= " and a.sub_account_headlvl3_id = '" . $srch_outward_for . "'";
        } else {
            $where .= " and 1=1 ";
        }





        $sql = "
            select 
            a.cash_outward_id,
            a.outward_date,
            a.ac_type,
            concat(d.prefix , a.vno) as vno,
            b.account_head_name as account_head,
            c.sub_account_head_name as sub_account_head,
            a.amount,
            a.remarks,
            a.bill_photo,
            e.sub_account_headlvl3_name as outward_for ,
            a.bill_status,
            a.bill_remarks 
            from cb_cash_outward_info as a
            left join cb_account_head_info as b on b.account_head_id = a.account_head_id
            left join cb_sub_account_head_info as c on c.sub_account_head_id = a.sub_account_head_id
            left join cb_voucher_type_info as d on d.voucher_type_id  = a.voucher_type_id
            left join cb_sub_account_head_lvl3_info as e on e.sub_account_headlvl3_id  = a.sub_account_headlvl3_id
            where a.`status` = 'Active'
            and b.ac_table = '1'
            and c.ac_table = '1'
            and a.bill_photo != ''
            and a.outward_date between '" . $srch_from_date . "' and  '" . $srch_to_date . "' 
            $where
            order by a.outward_date , a.cash_outward_id asc  
            ";




        $query = $this->db->query($sql);



        foreach ($query->result_array() as $row) {

            $data['record_list'][$row['ac_type']][] = $row;

        }

        if (!empty($srch_account_head_id)) {

            $sql = "
                select 
                a.sub_account_head_id,
                a.sub_account_head_name
                from cb_sub_account_head_info as a
                where a.`status` = 'Active'
                and a.`type` = 'Outward'
                and a.ac_table = '1'
                and a.account_head_id = '" . $srch_account_head_id . "'
                order by a.sub_account_head_name asc                 
        ";

            $query = $this->db->query($sql);

            foreach ($query->result_array() as $row) {
                $data['sub_account_head_opt'][$row['sub_account_head_id']] = $row['sub_account_head_name'];
            }


            if (!empty($srch_sub_account_head_id)) {
                $sql = "
                    select 
                    a.sub_account_headlvl3_id,
                    a.sub_account_headlvl3_name
                    from cb_sub_account_head_lvl3_info as a
                    where a.`status` = 'Active'
                    and a.`type` = 'Outward'
                    and a.sub_account_head_id = '" . $srch_sub_account_head_id . "'
                    order by a.sub_account_headlvl3_name asc                 
            ";

                $query = $this->db->query($sql);

                foreach ($query->result_array() as $row) {
                    $data['outward_for_opt'][$row['sub_account_headlvl3_id']] = $row['sub_account_headlvl3_name'];
                }

            }

        }

        $data['tds_prt_opt'] = array(
            '' => 'Select',
            '0.5' => '0.5',
            '1.00' => '1.00',
            '2.00' => '2.00',
            '3.00' => '3.00',
            '4.00' => '4.00',
            '5.00' => '5.00',
            '6.00' => '6.00',
            '7.00' => '7.00',
            '8.00' => '8.00',
            '9.00' => '9.00',
            '10.00' => '10.00',
        );

        $this->load->view('page/accounts/bills-report', $data);
    }

    public function company_bank_list($page = 1)
    {
        // Check if user is logged in
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        // Check user permissions
        if ($this->session->userdata(SESS_HD . 'level') != 'Admin') {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }

        // Include JavaScript file
        $data['js'] = 'accounts/company-bank.inc';

        // Handle Add
        if ($this->input->post('mode') == 'Add') {

            $upload_path = './bank_qr_code/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size'] = 2048;

            $this->load->library('upload', $config);

            $qr_code_img = ''; // initialize properly

            if (!empty($_FILES['qr_code']['name'])) {
                if ($this->upload->do_upload('qr_code')) {
                    $qr_code_img = $this->upload->data('file_name');
                } else {
                    // Log upload error if needed
                    log_message('error', 'QR Upload failed: ' . $this->upload->display_errors());
                }
            }

            $ins = array(
                'company' => $this->input->post('company'),
                'bank_name' => $this->input->post('bank_name'),
                'branch' => $this->input->post('branch'),
                'account_type' => $this->input->post('account_type'),
                'account_no' => $this->input->post('account_no'),
                'IFSC_code' => $this->input->post('IFSC_code'),
                'remarks' => $this->input->post('remarks'),
                'qr_code' => $qr_code_img,
                'status' => $this->input->post('status') ?: 'Active'
            );

            $this->db->insert('company_bank_info', $ins);
            redirect('company-bank-list');
        }


        /* -------------------------------
           EDIT MODE
        -------------------------------- */
        if ($this->input->post('mode') == 'Edit') {

            $upload_path = './bank_qr_code/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size'] = 2048;

            $this->load->library('upload', $config);

            $qr_code_img = $this->input->post('existing_qr_code') ?? ''; // optional hidden field

            if (!empty($_FILES['qr_code']['name'])) {
                if ($this->upload->do_upload('qr_code')) {
                    $qr_code_img = $this->upload->data('file_name');
                } else {
                    log_message('error', 'QR Upload failed: ' . $this->upload->display_errors());
                }
            }

            $upd = array(
                'company' => $this->input->post('company'),
                'bank_name' => $this->input->post('bank_name'),
                'branch' => $this->input->post('branch'),
                'account_type' => $this->input->post('account_type'),
                'account_no' => $this->input->post('account_no'),
                'IFSC_code' => $this->input->post('IFSC_code'),
                'remarks' => $this->input->post('remarks'),
                'qr_code' => $qr_code_img,
                'status' => $this->input->post('status') ?: 'Active'
            );

            $this->db->where('bank_id', $this->input->post('bank_id'));
            $this->db->update('company_bank_info', $upd);
            redirect('company-bank-list');
        }


        // Handle Search Filter
        if ($this->input->post('srch_company')) {
            $data['srch_company'] = $srch_company = $this->input->post('srch_company');
            $this->session->set_userdata('srch_company', $srch_company);
        } elseif ($this->session->userdata('srch_company')) {
            $data['srch_company'] = $srch_company = $this->session->userdata('srch_company');
        } else {
            $data['srch_company'] = $srch_company = '';
        }

        // Pagination
        $this->load->library('pagination');
        $config['base_url'] = site_url('company-bank-list');
        $config['per_page'] = 10; // Adjust as needed
        $config['uri_segment'] = 2;
        $config['total_rows'] = $this->db->where('status !=', 'Delete')->count_all_results('company_bank_info');
        $this->pagination->initialize($config);

        $data['pagination'] = $this->pagination->create_links();
        $offset = ($page - 1) * $config['per_page'];

        // Build where condition
        $where = $srch_company != '' ? "AND a.company = '" . $this->db->escape_str($srch_company) . "'" : '';

        // Fetch Records
        $sql = "
            SELECT 
                a.*,
                b.company_name,
                CASE 
                    WHEN a.account_type = 1 THEN 'Savings'
                    WHEN a.account_type = 2 THEN 'Current'
                    WHEN a.account_type = 3 THEN 'OD Account'
                    ELSE 'N/A'
                END AS account_type_name
            FROM company_bank_info AS a 
            LEFT JOIN company_info AS b ON b.company_id = a.company
            WHERE a.status != 'Delete' 
            AND b.status != 'Delete'
            $where
            ORDER BY b.company_name, a.bank_name ASC
            LIMIT $offset, {$config['per_page']}
        ";

        $query = $this->db->query($sql);
        $data['record_list'] = $query->result_array();

        // Account Type Options
        $data['account_types'] = array(
            '' => 'Select Account Type',
            '1' => 'Savings',
            '2' => 'Current',
            '3' => 'OD Account'
        );

        // Fetch Company Dropdown Options
        $sql = "
            SELECT 
                company_id,
                company_name       
            FROM company_info
            WHERE status = 'Active'  
            ORDER BY company_name ASC
        ";

        $query = $this->db->query($sql);
        $data['company_opt'] = array('' => 'Select Company');
        foreach ($query->result_array() as $row) {
            $data['company_opt'][$row['company_id']] = $row['company_name'];
        }

        // Fetch Companies for Search Filter
        $sql_search = "
            SELECT 
                company_id,
                company_name       
            FROM company_info
            WHERE status = 'Active'  
            ORDER BY company_name ASC
        ";

        $query_search = $this->db->query($sql_search);
        $data['companies'] = $query_search->result_array();

        $this->load->view('page/accounts/company-bank-list', $data);
    }

    public function delete_record()
    {

        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        date_default_timezone_set("Asia/Calcutta");


        $table = $this->input->post('tbl');
        $rec_id = $this->input->post('id');

        if ($table == 'cash_inward_info') {
            $this->db->where('cash_inward_id', $rec_id);
            $this->db->update('crit_cash_inward_info', array(
                'status' => 'Delete',
                'updated_by' => $this->session->userdata('cr_user_id'),
                'updated_datetime' => date('Y-m-d H:i:s')
            ));
            echo 'Record Successfully deleted';
        }

        if ($table == 'cash_outward_info') {
            $this->db->where('cash_outward_id', $rec_id);
            $this->db->update('crit_cash_outward_info', array(
                'status' => 'Delete',
                'updated_by' => $this->session->userdata('cr_user_id'),
                'updated_datetime' => date('Y-m-d H:i:s')
            ));
            echo 'Record Successfully deleted';
        }

        if ($table == 'account_head_info') {
            $this->db->where('account_head_id', $rec_id);
            $this->db->update('cb_account_head_info', array(
                'status' => 'Delete',
                'updated_by' => $this->session->userdata('cr_user_id'),
                'updated_datetime' => date('Y-m-d H:i:s')
            ));
            echo 'Record Successfully deleted';
        }


        if ($table == 'sub_account_head_info') {
            $this->db->where('sub_account_head_id', $rec_id);
            $this->db->update('cb_sub_account_head_info', array(
                'status' => 'Delete',
                'updated_by' => $this->session->userdata('cr_user_id'),
                'updated_datetime' => date('Y-m-d H:i:s')
            ));
            echo 'Record Successfully deleted';
        }
        if ($table == 'cb_sub_account_head_lvl3_info') {
            $this->db->where('sub_account_headlvl3_id', $rec_id);
            $this->db->update('cb_sub_account_head_lvl3_info', array(
                'status' => 'Delete',
                'updated_by' => $this->session->userdata('cr_user_id'),
                'updated_datetime' => date('Y-m-d H:i:s')
            ));
            echo 'Record Successfully deleted';
        }

        if ($table == 'voucher_type_info') {
            $this->db->where('voucher_type_id', $rec_id);
            $this->db->update('cb_voucher_type_info', array('status' => 'Delete'));

            echo 'Record Successfully deleted';
        }


        if ($table == 'cb_opening_balance_info_delet') {
            $this->db->where('opening_balance_id', $rec_id);
            $this->db->update('cb_opening_balance_info', array('status' => 'Delete'));

            echo 'Record Successfully deleted';
        }

        if ($table == 'sub_account_head_lvl3_info') {
            $this->db->where('sub_account_headlvl3_id', $rec_id);
            $this->db->update('sub_account_head_lvl3_info', array('status' => 'Delete'));

            echo 'Record Successfully deleted';
        }
         

    }

}
?>