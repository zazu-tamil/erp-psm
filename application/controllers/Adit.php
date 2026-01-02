<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Adit extends CI_Controller
{
    public function account_group_list()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        /*if($this->session->userdata('m_is_admin') != USER_ADMIN) 
        {
            echo "<h3 style='color:red;'>Permission Denied</h3>"; exit;
        }  */
        $data['title'] = 'Account Group List';
        $data['js'] = 'adit/account-group-list.inc';

        if ($this->input->post('mode') == 'Add') {
            $ins = array(
                'group_name' => $this->input->post('group_name'),
                'nature' => $this->input->post('nature'),
                'parent_group' => $this->input->post('parent_group'),
                'sequence' => $this->input->post('sequence'),
                'status' => $this->input->post('status'),

            );

            //print_r($ins); exit;

            $this->db->insert('account_groups', $ins);
            redirect('account-group-list');
        }

        if ($this->input->post('mode') == 'Edit') {
            $upd = array(
                'group_name' => $this->input->post('group_name'),
                'nature' => $this->input->post('nature'),
                'parent_group' => $this->input->post('parent_group'),
                'sequence' => $this->input->post('sequence'),
                'status' => $this->input->post('status'),
            );

            $this->db->where('group_id', $this->input->post('group_id'));
            $this->db->update('account_groups', $upd);

            redirect('account-group-list');
        }


        // === FILTERS ===
        $where = "1 = 1";
        // Company Filter
        if ($this->input->post('srch_group_id') !== null) {
            $data['srch_group_id'] = $srch_group_id = $this->input->post('srch_group_id');
            $this->session->set_userdata('srch_group_id', $srch_group_id);
        } elseif ($this->session->userdata('srch_group_id')) {
            $data['srch_group_id'] = $srch_group_id = $this->session->userdata('srch_group_id');
        } else {
            $data['srch_group_id'] = $srch_group_id = '';
        }
        if (!empty($srch_group_id)) {
            $where .= " AND a.group_id = '" . $this->db->escape_str($srch_group_id) . "'";
        }

        if ($this->input->post('srch_nature') !== null) {
            $data['srch_nature'] = $srch_nature = $this->input->post('srch_nature');
            $this->session->set_userdata('srch_nature', $srch_nature);
        } elseif ($this->session->userdata('srch_nature')) {
            $data['srch_nature'] = $srch_nature = $this->session->userdata('srch_nature');
        } else {
            $data['srch_nature'] = $srch_nature = '';
        }
        if (!empty($srch_nature)) {
            $where .= " AND a.nature = '" . $this->db->escape_str($srch_nature) . "'";
        }



        $this->load->library('pagination');
        $this->db->from('account_groups as a');
        $this->db->where('a.status', 'Active');
        $this->db->where($where);
        $data['total_records'] = $cnt = $this->db->count_all_results();

        $data['sno'] = $this->uri->segment(2);

        $config['base_url'] = site_url('account-group-list/');
        $config['total_rows'] = $cnt;
        $config['per_page'] = 15;
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
                a.*
               from account_groups as a
               where a.status = 'Active'    
                AND $where 
               order by a.group_id desc
               limit " . $config['per_page'] . "
        ";

        $query = $this->db->query($sql);

        $data['record_list'] = array();

        foreach ($query->result_array() as $row) {
            $data['record_list'][] = $row;
        }

        $data['group_opt'] = array('' => 'All');
        $sql = "SELECT group_id, group_name FROM account_groups WHERE status = 'Active'";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['group_opt'][$row['group_id']] = $row['group_name'];
        }
        //enum('Asset', 'Liability', 'Income', 'Expense')
        $data['nature_opt'] = array('' => 'All', 'Asset' => 'Asset', 'Liability' => 'Liability', 'Income' => 'Income', 'Expense' => 'Expense');

        $this->db->order_by('sequence', 'ASC');
        $data['groups'] = $this->db->get('account_groups')->result();


        $data['pagination'] = $this->pagination->create_links();
        $this->load->view('page/adit/account-group-list', $data);
    }
    public function ledger_accounts_list()
    {
        // ---------------- AUTH CHECK ----------------
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        // ---------------- BASIC VIEW DATA ----------------
        $data['title'] = 'Ledger Accounts List';
        $data['js'] = 'adit/ledger-accounts-list.inc';

        // ---------------- ADD LEDGER ----------------
        if ($this->input->post('mode') == 'Add') {
            $ins = array(
                'group_id' => $this->input->post('group_id'),
                'ledger_name' => $this->input->post('ledger_name'),
                'opening_balance' => $this->input->post('opening_balance'),
                'opening_type' => $this->input->post('opening_type'),
                'status' => $this->input->post('status'),
            );

            $this->db->insert('ledger_accounts', $ins);
            redirect('ledger-accounts-list');
        }

        // ---------------- EDIT LEDGER ----------------
        if ($this->input->post('mode') == 'Edit') {
            $upd = array(
                'group_id' => $this->input->post('group_id'),
                'ledger_name' => $this->input->post('ledger_name'),
                'opening_balance' => $this->input->post('opening_balance'),
                'opening_type' => $this->input->post('opening_type'),
                'status' => $this->input->post('status'),
            );

            $this->db->where('ledger_id', $this->input->post('ledger_id'));
            $this->db->update('ledger_accounts', $upd);

            redirect('ledger-accounts-list');
        }

        // === FILTERS ===
        $where = "1 = 1";
        // Company Filter
        if ($this->input->post('srch_group_id') !== null) {
            $data['srch_group_id'] = $srch_group_id = $this->input->post('srch_group_id');
            $this->session->set_userdata('srch_group_id', $srch_group_id);
        } elseif ($this->session->userdata('srch_group_id')) {
            $data['srch_group_id'] = $srch_group_id = $this->session->userdata('srch_group_id');
        } else {
            $data['srch_group_id'] = $srch_group_id = '';
        }
        if (!empty($srch_group_id)) {
            $where .= " AND a.group_id = '" . $this->db->escape_str($srch_group_id) . "'";
        }
        if ($this->input->post('srch_ledger_account') !== null) {
            $data['srch_ledger_account'] = $srch_ledger_account = $this->input->post('srch_ledger_account');
            $this->session->set_userdata('srch_ledger_account', $srch_ledger_account);
        } elseif ($this->session->userdata('srch_ledger_account')) {
            $data['srch_ledger_account'] = $srch_ledger_account = $this->session->userdata('srch_ledger_account');
        } else {
            $data['srch_ledger_account'] = $srch_ledger_account = '';
        }
        if (!empty($srch_ledger_account)) {
            $where .= " AND a.ledger_id = '" . $this->db->escape_str($srch_ledger_account) . "'";
        }

        if ($this->input->post('srch_opening_type') !== null) {
            $data['srch_opening_type'] = $srch_opening_type = $this->input->post('srch_opening_type');
            $this->session->set_userdata('srch_opening_type', $srch_opening_type);
        } elseif ($this->session->userdata('srch_opening_type')) {
            $data['srch_opening_type'] = $srch_opening_type = $this->session->userdata('srch_opening_type');
        } else {
            $data['srch_opening_type'] = $srch_opening_type = '';
        }
        if (!empty($srch_opening_type)) {
            $where .= " AND a.opening_type = '" . $this->db->escape_str($srch_opening_type) . "'";
        }



        $this->load->library('pagination');

        $this->db->from('ledger_accounts as a');
        $this->db->where('a.status', 'Active');
        $this->db->where($where);
        $total_rows = $this->db->count_all_results();


        $config['base_url'] = base_url('ledger-accounts-list/');
        $config['total_rows'] = $total_rows;
        $config['per_page'] = 15;
        $config['uri_segment'] = 2;


        $config['attributes'] = ['class' => 'page-link'];
        $config['full_tag_open'] = '<ul class="pagination pagination-sm no-margin pull-right">';
        $config['full_tag_close'] = '</ul>';

        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';

        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
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

        // Initialize pagination
        $this->pagination->initialize($config);

        $offset = ($this->uri->segment(2)) ? (int) $this->uri->segment(2) : 0;

        // ---------- SERIAL NUMBER ----------
        $data['sno'] = $offset + 1;


        $sql = "
            SELECT 
                a.*,
                b.group_name
            FROM ledger_accounts AS a
            LEFT JOIN account_groups AS b 
                ON a.group_id = b.group_id
            WHERE a.status = 'Active'
            AND $where
            ORDER BY a.ledger_id DESC
            LIMIT {$offset}, {$config['per_page']}
        ";

        $query = $this->db->query($sql);
        $data['record_list'] = $query->result_array();


        $sql = "
            SELECT  group_id, group_name
            FROM account_groups 
            WHERE status = 'Active' 
            ORDER BY group_id ASC";
        $query = $this->db->query($sql);
        $data['group_opt'] = [];
        foreach ($query->result_array() as $row) {
            $data['group_opt'][$row['group_id']] = $row['group_name'];
        }

        $sql = "
            SELECT  ledger_id, ledger_name
            FROM ledger_accounts  
            WHERE status = 'Active' 
            and group_id = '" . $this->db->escape_str($srch_group_id) . "'
            ORDER BY ledger_id desc";
        $query = $this->db->query($sql);
        $data['ledger_opt'] = [];
        foreach ($query->result_array() as $row) {
            $data['ledger_opt'][$row['ledger_id']] = $row['ledger_name'];
        }
        //enum('Debit', 'Credit')
        $data['opening_type_opt'] = array('' => '', 'Debit' => 'Debit', 'Credit' => 'Credit');




        // ---------- PAGINATION LINKS & TOTAL ----------
        $data['pagination'] = $this->pagination->create_links();
        $data['total_records'] = $total_rows;

        // ---------- LOAD VIEW ----------
        $this->load->view('page/adit/ledger-accounts-list', $data);
    }

    public function vouchers_list()
    {
        // ---------------- AUTH CHECK ----------------
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        // ---------------- BASIC VIEW DATA ----------------
        $data['title'] = 'Vouchers List';
        $data['js'] = 'adit/vouchers-list.inc';

        // ---------------- ADD LEDGER ----------------
        if ($this->input->post('mode') == 'Add') {
            $ins = array(
                'voucher_date' => $this->input->post('voucher_date'),
                'voucher_type' => $this->input->post('voucher_type'),
                'narration' => $this->input->post('narration'),
                'status' => $this->input->post('status'),
                'created_at' => date('Y-m-d H:i:s'),
            );

            $this->db->insert('vouchers', $ins);
            redirect('vouchers-list');
        }

        // ---------------- EDIT LEDGER ----------------
        if ($this->input->post('mode') == 'Edit') {
            $upd = array(
                'voucher_date' => $this->input->post('voucher_date'),
                'voucher_type' => $this->input->post('voucher_type'),
                'narration' => $this->input->post('narration'),
                'status' => $this->input->post('status'),
            );

            $this->db->where('voucher_id', $this->input->post('voucher_id'));
            $this->db->update('vouchers', $upd);

            redirect('vouchers-list');
        }

        $where = "1 = 1";

        if (isset($_POST['srch_from_date'])) {
            $data['srch_from_date'] = $srch_from_date = $this->input->post('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->input->post('srch_to_date');
            $this->session->set_userdata('srch_from_date', $this->input->post('srch_from_date'));
            $this->session->set_userdata('srch_to_date', $this->input->post('srch_to_date'));
        } elseif ($this->session->userdata('srch_from_date')) {
            $data['srch_from_date'] = $srch_from_date = $this->session->userdata('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->session->userdata('srch_to_date');
        } else {
            $data['srch_from_date'] = $srch_from_date = date('Y-m-d');
            $data['srch_to_date'] = $srch_to_date = date('Y-m-d');
        }

        if ($this->input->post('srch_voucher_narration_id') !== null) {
            $data['srch_voucher_narration_id'] = $srch_voucher_narration_id = $this->input->post('srch_voucher_narration_id');
            $this->session->set_userdata('srch_voucher_narration_id', $srch_voucher_narration_id);
        } elseif ($this->session->userdata('srch_voucher_narration_id')) {
            $data['srch_voucher_narration_id'] = $srch_voucher_narration_id = $this->session->userdata('srch_voucher_narration_id');
        } else {
            $data['srch_voucher_narration_id'] = $srch_voucher_narration_id = '';
        }
        if (!empty($srch_voucher_narration_id)) {
            $where .= " AND a.narration = '" . $this->db->escape_str($srch_voucher_narration_id) . "'";
        }

        if ($this->input->post('srch_voucher_type') !== null) {
            $data['srch_voucher_type'] = $srch_voucher_type = $this->input->post('srch_voucher_type');
            $this->session->set_userdata('srch_voucher_type', $srch_voucher_type);
        } elseif ($this->session->userdata('srch_voucher_type')) {
            $data['srch_voucher_type'] = $srch_voucher_type = $this->session->userdata('srch_voucher_type');
        } else {
            $data['srch_voucher_type'] = $srch_voucher_type = '';
        }
        if (!empty($srch_voucher_type)) {
            $where .= " AND a.voucher_type = '" . $this->db->escape_str($srch_voucher_type) . "'";
        }




        $this->load->library('pagination');
        $this->db->from('vouchers as a');
        $this->db->where('a.status', 'Active');
        $this->db->where('a.voucher_date BETWEEN "' . $srch_from_date . '" AND "' . $srch_to_date . '"');
        $this->db->where($where);
        $total_rows = $this->db->count_all_results();


        $config['base_url'] = base_url('vouchers-list/');
        $config['total_rows'] = $total_rows;
        $config['per_page'] = 25;
        $config['uri_segment'] = 2;


        $config['attributes'] = ['class' => 'page-link'];
        $config['full_tag_open'] = '<ul class="pagination pagination-sm no-margin pull-right">';
        $config['full_tag_close'] = '</ul>';

        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';

        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
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

        // Initialize pagination
        $this->pagination->initialize($config);

        $offset = ($this->uri->segment(2)) ? (int) $this->uri->segment(2) : 0;

        // ---------- SERIAL NUMBER ----------
        $data['sno'] = $offset + 1;


        $sql = "
            SELECT 
                a.voucher_id,
                a.voucher_date,
                a.voucher_type,
                a.narration,
                a.status
            FROM vouchers AS a 
            WHERE a.status = 'Active'
            and a.voucher_date BETWEEN '" . $srch_from_date . "' AND  '" . $srch_to_date . "'
            and $where
            ORDER BY a.voucher_id DESC
            LIMIT {$offset}, {$config['per_page']}
        ";

        $query = $this->db->query($sql);
        $data['record_list'] = $query->result_array();

        $data['voucher_narration_opt'] = ['' => 'All'];
        $sql = "
            SELECT 
                a.voucher_id, 
                a.narration,
                a.status
            FROM vouchers AS a 
            WHERE a.status = 'Active'
            ORDER BY a.voucher_id DESC 
        ";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['voucher_narration_opt'][$row['narration']] = $row['narration'];
        }



        //'Payment','Receipt','Journal','Contra','Sales','Purchase'
        $data['voucher_type_opt'] = array('' => 'All', 'Payment' => 'Payment', 'Receipt' => 'Receipt', 'Journal' => 'Journal', 'Contra' => 'Contra', 'Sales' => 'Sales', 'Purchase' => 'Purchase');


        $data['pagination'] = $this->pagination->create_links();
        $data['total_records'] = $total_rows;

        // ---------- LOAD VIEW ----------
        $this->load->view('page/adit/vouchers-list', $data);
    }
    public function voucher_entries_list()
    {
        // ---------------- AUTH CHECK ----------------
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        // ---------------- BASIC VIEW DATA ----------------
        $data['title'] = 'Voucher Entries List';
        $data['js'] = 'adit/voucher-entries-list.inc';

        // ---------------- ADD LEDGER ----------------
        if ($this->input->post('mode') == 'Add') {
            $ins = array(
                'voucher_id' => $this->input->post('voucher_id'),
                'ledger_id' => $this->input->post('ledger_id'),
                'debit' => $this->input->post('debit'),
                'credit' => $this->input->post('credit'),
                'status' => $this->input->post('status'),
            );

            $this->db->insert('voucher_entries', $ins);
            redirect('voucher-entries-list');
        }

        // ---------------- EDIT LEDGER ----------------
        if ($this->input->post('mode') == 'Edit') {
            $upd = array(
                'voucher_id' => $this->input->post('voucher_id'),
                'ledger_id' => $this->input->post('ledger_id'),
                'debit' => $this->input->post('debit'),
                'credit' => $this->input->post('credit'),
                'status' => $this->input->post('status'),
            );

            $this->db->where('entry_id', $this->input->post('entry_id'));
            $this->db->update('voucher_entries', $upd);

            redirect('voucher-entries-list');
        }
        $where = "1 = 1";

        if (isset($_POST['srch_from_date'])) {
            $data['srch_from_date'] = $srch_from_date = $this->input->post('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->input->post('srch_to_date');
            $this->session->set_userdata('srch_from_date', $this->input->post('srch_from_date'));
            $this->session->set_userdata('srch_to_date', $this->input->post('srch_to_date'));
        } elseif ($this->session->userdata('srch_from_date')) {
            $data['srch_from_date'] = $srch_from_date = $this->session->userdata('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->session->userdata('srch_to_date');
        } else {
            $data['srch_from_date'] = $srch_from_date = date('Y-m-d');
            $data['srch_to_date'] = $srch_to_date = date('Y-m-d');
        }

        if ($this->input->post('srch_voucher_narration_id') !== null) {
            $data['srch_voucher_narration_id'] = $srch_voucher_narration_id = $this->input->post('srch_voucher_narration_id');
            $this->session->set_userdata('srch_voucher_narration_id', $srch_voucher_narration_id);
        } elseif ($this->session->userdata('srch_voucher_narration_id')) {
            $data['srch_voucher_narration_id'] = $srch_voucher_narration_id = $this->session->userdata('srch_voucher_narration_id');
        } else {
            $data['srch_voucher_narration_id'] = $srch_voucher_narration_id = '';
        }
        if (!empty($srch_voucher_narration_id)) {
            $where .= " AND a.voucher_id = '" . $this->db->escape_str($srch_voucher_narration_id) . "'";
        }
        if ($this->input->post('srch_ledger_account_id') !== null) {
            $data['srch_ledger_account_id'] = $srch_ledger_account_id = $this->input->post('srch_ledger_account_id');
            $this->session->set_userdata('srch_ledger_account_id', $srch_ledger_account_id);
        } elseif ($this->session->userdata('srch_ledger_account_id')) {
            $data['srch_ledger_account_id'] = $srch_ledger_account_id = $this->session->userdata('srch_ledger_account_id');
        } else {
            $data['srch_ledger_account_id'] = $srch_ledger_account_id = '';
        }
        if (!empty($srch_ledger_account_id)) {
            $where .= " AND a.ledger_id = '" . $this->db->escape_str($srch_ledger_account_id) . "'";
        }


        $this->load->library('pagination');
        $this->db->from('voucher_entries as a');
        $this->db->join('vouchers as b', 'b.voucher_id = a.voucher_id', 'left');
        $this->db->where('a.status', 'Active');
        $this->db->where("b.voucher_date BETWEEN '" . $this->db->escape_str($srch_from_date) . "' AND '" . $this->db->escape_str($srch_to_date) . "'");
        $this->db->where($where);
        $total_rows = $this->db->count_all_results();


        $config['base_url'] = base_url('voucher-entries-list/');
        $config['total_rows'] = $total_rows;
        $config['per_page'] = 25;
        $config['uri_segment'] = 2;


        $config['attributes'] = ['class' => 'page-link'];
        $config['full_tag_open'] = '<ul class="pagination pagination-sm no-margin pull-right">';
        $config['full_tag_close'] = '</ul>';

        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';

        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
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

        // Initialize pagination
        $this->pagination->initialize($config);

        $offset = ($this->uri->segment(2)) ? (int) $this->uri->segment(2) : 0;

        // ---------- SERIAL NUMBER ----------
        $data['sno'] = $offset + 1;

        $data['voucher_list_opt'] = ['' => 'All'];
        $sql = "
            SELECT voucher_id, voucher_type, narration
            FROM vouchers 
            WHERE status = 'Active' 
            ORDER BY voucher_id desc";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['voucher_list_opt'][$row['voucher_id']] = $row['narration'] . ' [ ' . $row['voucher_type'] . ' ]';
        }

        $data['ledger_accounts_list_opt'] = ['' => 'All'];
        $sql = "
                SELECT
                a.voucher_id,
                b.ledger_id,
                b.ledger_name

                FROM voucher_entries as a 
                LEFT JOIN ledger_accounts as b on a.ledger_id = b.ledger_id and b.status='Active'
                WHERE a.status='Active'
                and a.voucher_id = '" . $this->db->escape_str($srch_voucher_narration_id) . "'

             ";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['ledger_accounts_list_opt'][$row['ledger_id']] = $row['ledger_name'];
        }

        $sql = "
            SELECT
                a.entry_id,
                b.voucher_date,
                b.voucher_type,
                b.narration,
                c.ledger_name,
                a.status,
                a.debit,
                a.credit
            FROM
                voucher_entries AS a
                JOIN vouchers as b on a.voucher_id = b.voucher_id and b.status='Active'
                JOIN ledger_accounts as c on a.ledger_id = c.ledger_id and c.status='Active'
            WHERE
                a.status = 'Active'
            and b.voucher_date between '" . $this->db->escape_str($srch_from_date) . "' and '" . $this->db->escape_str($srch_to_date) . "'
            and $where
            ORDER BY
                a.entry_id DESC 
            LIMIT {$offset}, {$config['per_page']}
        ";

        $query = $this->db->query($sql);
        $data['record_list'] = $query->result_array();


        $data['pagination'] = $this->pagination->create_links();
        $data['total_records'] = $total_rows;

        // ---------- LOAD VIEW ----------
        $this->load->view('page/adit/voucher-entries-list', $data);
    }
    public function get_data()
    {
        $table = $this->input->post('tbl');
        $rec_id = $this->input->post('id');

        if ($table == 'account_groups') {

            $sql = "
            SELECT a.*
            FROM account_groups AS a
            WHERE group_id = ?
              AND status = 'Active'
        ";

            $query = $this->db->query($sql, [$rec_id]);

            echo json_encode($query->row());
            return;
        }

        if ($table == 'account_groups_parent_list') {

            $sql = "
                SELECT
                a.group_name,
                a.group_id,
                a.parent_group,
                a.sequence
                FROM account_groups as a 
                WHERE a.status='Active'
                and a.nature= ?
                and ( a.parent_group=''  or a.parent_group IS null or a.parent_group='0' )
            ";

            $query = $this->db->query($sql, [$rec_id]);

            $rec_list = $query->result_array();

            echo json_encode($rec_list);
            return;
        }
        if ($table == 'ledger_accounts') {

            $sql = "
                SELECT
                 a.*
                FROM ledger_accounts as a 
                WHERE a.status='Active'
                and a.ledger_id= ?                
            ";

            $query = $this->db->query($sql, [$rec_id]);

            echo json_encode($query->row());
            return;
        }
        if ($table == 'vouchers_list') {

            $sql = "
                SELECT
                 a.*
                FROM vouchers as a 
                WHERE a.status='Active'
                and a.voucher_id= ?                
            ";

            $query = $this->db->query($sql, [$rec_id]);

            echo json_encode($query->row());
            return;
        }
        if ($table == 'vouchers_entries_list') {

            $sql = "
                SELECT
                 a.*
                FROM voucher_entries as a 
                WHERE a.status='Active'
                and a.entry_id= ?                
            ";

            $query = $this->db->query($sql, [$rec_id]);

            echo json_encode($query->row_array());
            return;
        }
        /*if ($table == 'group_ledger_list_load') {

            $sql = "
               SELECT
                a.ledger_id,
                a.ledger_name
                FROM
                    ledger_accounts as a 
                    JOIN account_groups as b on a.group_id = b.group_id and b.status='Active'
                WHERE a.status='Active'
                and a.group_id =  ?          
            ";

            $query = $this->db->query($sql, [$rec_id]);

        }*/

        if ($table == 'group_ledger_list_load') {
            $query = $this->db->query("
                SELECT
                a.group_id,
                a.ledger_id,
                a.ledger_name
                FROM
                    ledger_accounts as a 
                    JOIN account_groups as b on a.group_id = b.group_id and b.status='Active'
                WHERE a.status='Active'
                and a.group_id =  ?    
            ", [$rec_id]);
            $rec_list = $query->result_array();
        }
        if ($table == 'voucher_ledger_list_load') {
            $query = $this->db->query("
               SELECT
                a.voucher_id,
                b.ledger_id,
                b.ledger_name

                FROM voucher_entries as a 
                LEFT JOIN ledger_accounts as b on a.ledger_id = b.ledger_id and b.status='Active'
                WHERE a.status='Active'
                and a.voucher_id = ?

            ", [$rec_id]);
            $rec_list = $query->result_array();
        }
        header('Content-Type: application/json');
        echo json_encode($rec_list);

    }

    public function delete_record()
    {

        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        date_default_timezone_set("Asia/Calcutta");


        $table = $this->input->post('tbl');
        $rec_id = $this->input->post('id');


        if ($table == 'account_groups') {
            $this->db->where('group_id', $rec_id);
            $this->db->update('account_groups', array('status' => 'Delete'));
            echo "Record Deleted Successfully";
        }
        if ($table == 'ledger_accounts') {
            $this->db->where('ledger_id', $rec_id);
            $this->db->update('ledger_accounts', array('status' => 'Delete'));
            echo "Record Deleted Successfully";
        }
        if ($table == 'vouchers_list') {
            $this->db->where('voucher_id', $rec_id);
            $this->db->update('vouchers', array('status' => 'Delete'));
            echo "Record Deleted Successfully";
        }
        if ($table == 'vouchers_entries_list') {
            $this->db->where('entry_id', $rec_id);
            $this->db->update('voucher_entries', array('status' => 'Delete'));
            echo "Record Deleted Successfully";
        }
    }
}