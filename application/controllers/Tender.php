<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tender extends CI_Controller
{

    public function add_tender_enquiry()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        $data = array();
        $data['js'] = 'tender/add-tender-enquiry.inc';
        $data['title'] = 'Tender Enquiry';

        $srch_company_id = $this->input->post('company_id');
        $srch_customer_id = $this->input->post('customer_id');

        // Handle Add Tender Enquiry with Items
        if ($this->input->post('mode') == 'Add') {
            $this->db->trans_start();

            $insert_data = array(
                'company_id' => $srch_company_id,
                'enquiry_date' => $this->input->post('enquiry_date'),
                'enquiry_no' => $this->input->post('enquiry_no'),
                'customer_id' => $srch_customer_id,
                'opening_date' => $this->input->post('opening_date') ? date('Y-m-d H:i:s', strtotime($this->input->post('opening_date'))) : null,
                'closing_date' => $this->input->post('closing_date') ? date('Y-m-d H:i:s', strtotime($this->input->post('closing_date'))) : null,
                'status' => $this->input->post('status') ?: 'Active',
                'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'created_date' => date('Y-m-d H:i:s')
            );
            $this->db->insert('tender_enquiry_info', $insert_data);
            $tender_enquiry_id = $this->db->insert_id();

            $category_ids = $this->input->post('category_id');
            $item_ids = $this->input->post('item_id');
            $item_descs = $this->input->post('item_desc');
            $uoms = $this->input->post('uom');
            $qtys = $this->input->post('qty');

            if (!empty($item_ids)) {
                foreach ($item_ids as $index => $item_id) {
                    if (!empty($item_id)) {
                        $insert_item_data = array(
                            'tender_enquiry_id' => $tender_enquiry_id,
                            'category_id' => $category_ids[$index] ?? 0,
                            'item_id' => $item_id,
                            'item_desc' => $item_descs[$index] ?? '',
                            'uom' => $uoms[$index] ?? '',
                            'qty' => $qtys[$index] ?? 0.00,
                            'status' => 'Active',
                            'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                            'created_date' => date('Y-m-d H:i:s')
                        );
                        $this->db->insert('tender_enquiry_item_info', $insert_item_data);
                    }
                }
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Error saving data. Please try again.');
            } else {
                $this->session->set_flashdata('success', 'Tender Enquiry saved successfully.');
            }
            redirect('tender-enquiry-list');
        }

        // Populate dropdowns
        $data['company_opt'] = [];
        $data['customer_opt'] = [];
        $data['category_opt'] = [];
        $data['uom_opt'] = [];

        // Companies
        $query = $this->db->query("
        SELECT 
        company_id, 
        company_name 
        FROM company_info 
        WHERE status = 'Active' 
        ORDER BY company_name");
        foreach ($query->result_array() as $row) {
            $data['company_opt'][$row['company_id']] = $row['company_name'];
        }

        // Customers
        $query = $this->db->query("
        SELECT 
        customer_id, 
        customer_name 
        FROM customer_info 
        WHERE status = 'Active' 
        ORDER BY customer_name");
        foreach ($query->result_array() as $row) {
            $data['customer_opt'][$row['customer_id']] = $row['customer_name'];
        }

        // Categories
        $query = $this->db->query("
        SELECT 
        category_id, 
        category_name 
        FROM category_info 
        WHERE status = 'Active' 
        ORDER BY category_name");
        foreach ($query->result_array() as $row) {
            $data['category_opt'][$row['category_id']] = $row['category_name'];
        }

        // UOMs (using uom_name as key and value)
        $query = $this->db->query("
        SELECT 
        uom_name 
        FROM uom_info 
        WHERE status = 'Active' 
        ORDER BY uom_name");
        foreach ($query->result_array() as $row) {
            $data['uom_opt'][$row['uom_name']] = $row['uom_name'];
        }

        $data['status_opt'] = ['Active' => 'Active', 'Inactive' => 'Inactive'];
        $this->load->view('page/tender/add-tender-enquiry', $data);
    }

    public function tender_enquiry_list()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        $data = array();
        $data['js'] = 'tender/tender-enquiry-list.inc';
        $data['s_url'] = 'tender-enquiry-list';
        $data['title'] = 'Tender Enquiry List';

        // === FILTERS ===
        $where = "1";

        // Company Filter
        if ($this->input->post('srch_company_id') !== null) {
            $data['srch_company_id'] = $srch_company_id = $this->input->post('srch_company_id');
            $this->session->set_userdata('srch_company_id', $srch_company_id);
        } elseif ($this->session->userdata('srch_company_id')) {
            $data['srch_company_id'] = $srch_company_id = $this->session->userdata('srch_company_id');
        } else {
            $data['srch_company_id'] = $srch_company_id = '';
        }
        if (!empty($srch_company_id)) {
            $where .= " AND a.company_id = '" . $this->db->escape_str($srch_company_id) . "'";
        }

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
            $where .= " AND a.customer_id = '" . $this->db->escape_str($srch_customer_id) . "'";
        }

        // Status Filter
        if ($this->input->post('srch_status') !== null) {
            $data['srch_status'] = $srch_status = $this->input->post('srch_status');
            $this->session->set_userdata('srch_status', $srch_status);
        } elseif ($this->session->userdata('srch_status')) {
            $data['srch_status'] = $srch_status = $this->session->userdata('srch_status');
        } else {
            $data['srch_status'] = $srch_status = '';
        }
        if (!empty($srch_status) && $srch_status !== 'All') {
            $where .= " AND a.status = '" . $this->db->escape_str($srch_status) . "'";
        }

        // === COUNT TOTAL ===
        $sql_count = "SELECT COUNT(*) as total FROM tender_enquiry_info a WHERE a.status != 'Delete' AND $where";
        $query_count = $this->db->query($sql_count);
        $data['total_records'] = $query_count->row()->total;

        // === PAGINATION ===
        $data['sno'] = $this->uri->segment(2, 0);
        $this->load->library('pagination');

        $config['base_url'] = trim(site_url($data['s_url']), '/' . $this->uri->segment(2, 0));
        $config['total_rows'] = $data['total_records'];
        $config['per_page'] = 25;
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
        $data['pagination'] = $this->pagination->create_links();

        // === FETCH RECORDS ===
        $sql = "
        SELECT 
            a.tender_enquiry_id,
            a.enquiry_date,
            a.enquiry_no,
            a.opening_date,
            a.closing_date,
            a.status,
            b.company_name,
            c.customer_name
        FROM tender_enquiry_info a
        LEFT JOIN company_info b ON a.company_id = b.company_id AND b.status = 'Active'
        LEFT JOIN customer_info c ON a.customer_id = c.customer_id AND c.status = 'Active'
        WHERE a.status != 'Delete' AND $where
        ORDER BY a.tender_enquiry_id DESC
        LIMIT " . $this->uri->segment(2, 0) . ", " . $config['per_page'];

        $query = $this->db->query($sql);
        $data['record_list'] = $query->result_array();

        // === DROPDOWNS ===
        $data['company_opt'] = ['' => 'All'];
        $sql = "
    SELECT 
    company_id, 
    company_name 
    FROM company_info 
    WHERE status = 'Active' 
    ORDER BY company_name";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['company_opt'][$row['company_id']] = $row['company_name'];
        }

        $data['customer_opt'] = ['' => 'All'];
        $sql = "
    SELECT 
    customer_id, 
    customer_name 
    FROM customer_info 
    WHERE status = 'Active' 
    ORDER BY customer_name";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['customer_opt'][$row['customer_id']] = $row['customer_name'];
        }

        $data['status_opt'] = ['' => 'All', 'Active' => 'Active', 'Inactive' => 'Inactive'];

        $this->load->view('page/tender/tender-enquiry-list', $data);
    }


    public function edit_tender_enquiry($tender_enquiry_id = 0)
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        if (!$tender_enquiry_id) {
            $this->session->set_flashdata('error', 'Invalid record ID.');
            redirect('tender-enquiry-list');
        }

        $data = [];
        $data['js'] = 'tender/edit-tender-enquiry.inc';
        $data['title'] = 'Edit Tender Enquiry';
        $data['tender_enquiry_id'] = $tender_enquiry_id;

        /* ---------- UPDATE ---------- */
        if ($this->input->post('mode') == 'Edit') {
            $this->db->trans_begin();

            // Main record update
            $main = [
                'company_id' => $this->input->post('company_id'),
                'enquiry_date' => $this->input->post('enquiry_date'),
                'enquiry_no' => $this->input->post('enquiry_no'),
                'customer_id' => $this->input->post('customer_id'),
                'opening_date' => $this->input->post('opening_date')
                    ? date('Y-m-d H:i:s', strtotime($this->input->post('opening_date'))) : null,
                'closing_date' => $this->input->post('closing_date')
                    ? date('Y-m-d H:i:s', strtotime($this->input->post('closing_date'))) : null,
                'status' => $this->input->post('status') ?: 'Active',
                'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'updated_date' => date('Y-m-d H:i:s')
            ];
            $this->db->where('tender_enquiry_id', $tender_enquiry_id)
                ->update('tender_enquiry_info', $main);

            // Get existing item IDs from database
            $existing = [];
            $q = $this->db->select('tender_enquiry_item_id')
                ->where('tender_enquiry_id', $tender_enquiry_id)
                ->where('status !=', 'Deleted')
                ->get('tender_enquiry_item_info')
                ->result_array();
            foreach ($q as $r) {
                $existing[] = $r['tender_enquiry_item_id'];
            }

            // Get posted data
            $cat_ids = $this->input->post('category_id') ?: [];
            $item_ids = $this->input->post('item_id') ?: [];
            $desc = $this->input->post('item_desc') ?: [];
            $uom = $this->input->post('uom') ?: [];
            $qty = $this->input->post('qty') ?: [];
            $rec_ids = $this->input->post('tender_enquiry_item_id') ?: [];

            $processed = [];

            foreach ($item_ids as $i => $itm) {
                if (empty($itm))
                    continue;

                $row = [
                    'tender_enquiry_id' => $tender_enquiry_id,
                    'category_id' => isset($cat_ids[$i]) ? $cat_ids[$i] : 0,
                    'item_id' => $itm,
                    'item_desc' => isset($desc[$i]) ? $desc[$i] : '',
                    'uom' => isset($uom[$i]) ? $uom[$i] : '',
                    'qty' => isset($qty[$i]) ? $qty[$i] : 0,
                    'status' => 'Active',
                    'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                    'updated_date' => date('Y-m-d H:i:s')
                ];

                $rec_id = isset($rec_ids[$i]) ? $rec_ids[$i] : 0;

                // If record ID exists and is in existing records, update it
                if ($rec_id && in_array($rec_id, $existing)) {
                    $this->db->where('tender_enquiry_item_id', $rec_id)
                        ->update('tender_enquiry_item_info', $row);
                    $processed[] = $rec_id;
                }
                // Otherwise insert new record
                else {
                    $row['created_by'] = $this->session->userdata(SESS_HD . 'user_id');
                    $row['created_date'] = date('Y-m-d H:i:s');
                    $this->db->insert('tender_enquiry_item_info', $row);
                }
            }

            // Mark removed items as Deleted
            $to_delete = array_diff($existing, $processed);
            foreach ($to_delete as $del) {
                $this->db->where('tender_enquiry_item_id', $del)
                    ->update('tender_enquiry_item_info', ['status' => 'Deleted']);
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('error', 'Error updating data.');
            } else {
                $this->db->trans_commit();
                $this->session->set_flashdata('success', 'Tender Enquiry updated successfully.');
            }
            redirect('tender-enquiry-list');
        }

        /* ---------- LOAD MAIN RECORD ---------- */
        $sql = "SELECT * FROM tender_enquiry_info WHERE tender_enquiry_id = ? AND status != 'Deleted'";
        $q = $this->db->query($sql, [$tender_enquiry_id]);
        $data['main_record'] = $q->row_array();

        if (!$data['main_record']) {
            $this->session->set_flashdata('error', 'Record not found.');
            redirect('tender-enquiry-list');
        }

        /* ---------- LOAD ITEMS ---------- */
        $sql = "
        SELECT tei.*,
               ii.item_name,
               ii.item_description,
               ii.uom AS item_uom
        FROM tender_enquiry_item_info tei
        LEFT JOIN item_info ii ON ii.item_id = tei.item_id AND ii.status = 'Active'
        WHERE tei.tender_enquiry_id = ? AND tei.status = 'Active'
        ORDER BY tei.tender_enquiry_item_id ASC
    ";
        $q = $this->db->query($sql, [$tender_enquiry_id]);
        $data['item_list'] = $q->result_array();

        // === DROPDOWNS ===
        $data['company_opt'] = ['' => 'Select Company'];
        $sql = "
    SELECT 
    company_id, 
    company_name 
    FROM company_info 
    WHERE status = 'Active' 
    ORDER BY company_name";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['company_opt'][$row['company_id']] = $row['company_name'];
        }

        $data['customer_opt'] = ['' => 'Select Customer'];
        $sql = "
    SELECT 
    customer_id, 
    customer_name 
    FROM customer_info 
    WHERE status = 'Active' 
    ORDER BY customer_name";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['customer_opt'][$row['customer_id']] = $row['customer_name'];
        }

        $data['category_opt'] = ['' => 'Select Category'];
        $sql = "
    SELECT 
    category_id, 
    category_name 
    FROM category_info 
    WHERE status = 'Active' 
    ORDER BY category_name";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['category_opt'][$row['category_id']] = $row['category_name'];
        }

        $data['uom_opt'] = [];
        $sql = "
    SELECT 
    uom_name 
    FROM uom_info 
    WHERE status = 'Active' 
    ORDER BY uom_name";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['uom_opt'][$row['uom_name']] = $row['uom_name'];
        }

        $data['status_opt'] = ['' => 'Select Status', 'Active' => 'Active', 'Inactive' => 'Inactive'];

        // JSON encode for JavaScript
        $data['category_json'] = json_encode($data['category_opt']);
        $data['uom_json'] = json_encode(['' => '—'] + $data['uom_opt']);

        $this->load->view('page/tender/edit-tender-enquiry', $data);
    }

    public function tender_quotation_add()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        if ($this->session->userdata(SESS_HD . 'level') != 'Admin' && $this->session->userdata(SESS_HD . 'level') != 'Staff') {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }

        $data['js'] = 'tender/tender-quotation-add.inc';
        $data['title'] = 'Add Tender Quotation';

        if ($this->input->post('mode') == 'Add') {
            $this->db->trans_start();

            /* ---- 1. Header record ------------------------------------------------ */
            $header = [
                'company_id' => $this->input->post('srch_company_id'),
                'customer_id' => $this->input->post('srch_customer_id'),
                'tender_enquiry_id' => $this->input->post('srch_tender_enquiry_id'),
                'quotation_no' => $this->input->post('quotation_no'),
                'tender_ref_no' => $this->input->post('tender_ref_no'),
                'quote_date' => $this->input->post('quote_date'),
                'remarks' => $this->input->post('remarks'),
                'status' => $this->input->post('status') ?: 'Pending',
                'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'created_date' => date('Y-m-d H:i:s')
            ];
            $this->db->insert('tender_quotation_info', $header);
            $tender_quotation_id = $this->db->insert_id();

            /* ---- 2. ONLY SELECTED items ------------------------------------------ */
            $selected_idxs = $this->input->post('selected_items') ?? [];   // array of "i" values

            if (!empty($selected_idxs)) {
                // All arrays are posted with the SAME order as the rows
                $tender_enquiry_item_ids = $this->input->post('tender_enquiry_item_id') ?? [];
                $category_ids = $this->input->post('category_id') ?? [];
                $item_ids = $this->input->post('item_id') ?? [];
                $item_descs = $this->input->post('item_desc') ?? [];
                $uoms = $this->input->post('uom') ?? [];
                $qtys = $this->input->post('qty') ?? [];
                 $gsts = $this->input->post('gst') ?? [];
                $amounts = $this->input->post('amount') ?? [];

                foreach ($selected_idxs as $idx) {
                    $item_data = [
                        'tender_quotation_id' => $tender_quotation_id,
                        'tender_enquiry_item_id' => $tender_enquiry_item_ids[$idx] ?? 0,
                        'category_id' => $category_ids[$idx] ?? 0,
                        'item_id' => $item_ids[$idx] ?? 0,
                        'item_desc' => $item_descs[$idx] ?? '',
                        'uom' => $uoms[$idx] ?? '',
                        'qty' => $qtys[$idx] ?? 0,
                         'gst' => $gsts[$idx] ?? 0,
                        'amount' => $amounts[$idx] ?? 0,
                        'status' => 'Active',
                        'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                        'created_date' => date('Y-m-d H:i:s')
                    ];
                    $this->db->insert('tender_quotation_item_info', $item_data);
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Error saving data. Please try again.');
            } else {
                $this->session->set_flashdata('success', 'Tender Quotation saved successfully.');
            }
            redirect('vendor-rate-enquiry-list/');
        }


        $this->load->library('pagination');

        $this->db->where('status != ', 'Delete');
        $this->db->from('vendor_rate_enquiry_info');
        $data['total_records'] = $cnt = $this->db->count_all_results();

        $data['sno'] = $this->uri->segment(2, 0);

        $config['base_url'] = trim(site_url('vendor-rate-enquiry-list') . '/' . $this->uri->segment(2, 0));
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

        $data['company_opt'] = [];
        $data['customer_opt'] = [];
        $data['vendor_opt'] = [];
        $data['tender_enquiry_opt'] = [];
        $data['gst_opt'] = [];

        $sql = "
            SELECT vendor_id,vendor_name 
            FROM vendor_info 
            WHERE status = 'Active' 
            ORDER BY vendor_name ASC";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['vendor_opt'][$row['vendor_id']] = $row['vendor_name'];
        }

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
            SELECT company_id,company_name
            FROM company_info
             WHERE status = 'Active' 
            ORDER BY company_name ASC
        ";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['company_opt'][$row['company_id']] = $row['company_name'];
        }

        $sql = "
            SELECT 
                a.tender_enquiry_id,
                a.enquiry_no,
                b.company_name,
                c.customer_name 
            FROM tender_enquiry_info AS a LEFT JOIN company_info as b on a.company_id = b.company_id and b.status='Active' 
            LEFT JOIN customer_info as c on a.customer_id = c.customer_id and c.status='Active' 
            WHERE a.status = 'Active' ORDER BY a.tender_enquiry_id , a.enquiry_no ASC
        ";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['tender_enquiry_opt'][$row['tender_enquiry_id']] = $row['tender_enquiry_id'] . ' -> ' . $row['enquiry_no'] . ' -> ' . $row['company_name'] . ' -> ' . $row['customer_name'];
        }

        $sql = "
            SELECT gst_id, gst_percentage
            FROM gst_info 
            WHERE status = 'Active'
            ORDER BY gst_percentage ASC
        ";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['gst_opt'][$row['gst_id']] = $row['gst_percentage'];
        }

        $sql = "
            SELECT * FROM company_info 
            WHERE status != 'Delete' 
            order by company_id desc 
            limit " . $this->uri->segment(2, 0) . "," . $config['per_page']
        ;
        $data['record_list'] = array();
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['record_list'][] = $row;
        }

        $data['pagination'] = $this->pagination->create_links();
        $this->load->view('page/tender/tender-quotation-add', $data);
    }


    public function tender_quotation_list()
{
    if (!$this->session->userdata(SESS_HD . 'logged_in')) {
        redirect();
    }

    $data = array();
    $data['js'] = 'tender/tender-quotation-list.inc';
    $data['s_url'] = 'tender-quotation-list';
    $data['title'] = 'Tender Quotation List';

    // === FILTERS ===
    $where = "1";

    // Company Filter
    if ($this->input->post('srch_company_id') !== null) {
        $data['srch_company_id'] = $srch_company_id = $this->input->post('srch_company_id');
        $this->session->set_userdata('srch_company_id', $srch_company_id);
    } elseif ($this->session->userdata('srch_company_id')) {
        $data['srch_company_id'] = $srch_company_id = $this->session->userdata('srch_company_id');
    } else {
        $data['srch_company_id'] = $srch_company_id = '';
    }
    if (!empty($srch_company_id)) {
        $where .= " AND a.company_id = '" . $this->db->escape_str($srch_company_id) . "'";
    }

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
        $where .= " AND a.customer_id = '" . $this->db->escape_str($srch_customer_id) . "'";
    }

    // Tender Enquiry Filter
    if ($this->input->post('srch_tender_enquiry_id') !== null) {
        $data['srch_tender_enquiry_id'] = $srch_tender_enquiry_id = $this->input->post('srch_tender_enquiry_id');
        $this->session->set_userdata('srch_tender_enquiry_id', $srch_tender_enquiry_id);
    } elseif ($this->session->userdata('srch_tender_enquiry_id')) {
        $data['srch_tender_enquiry_id'] = $srch_tender_enquiry_id = $this->session->userdata('srch_tender_enquiry_id');
    } else {
        $data['srch_tender_enquiry_id'] = $srch_tender_enquiry_id = '';
    }
    if (!empty($srch_tender_enquiry_id)) {
        $where .= " AND a.tender_enquiry_id = '" . $this->db->escape_str($srch_tender_enquiry_id) . "'";
    }

    // Status Filter
    if ($this->input->post('srch_status') !== null) {
        $data['srch_status'] = $srch_status = $this->input->post('srch_status');
        $this->session->set_userdata('srch_status', $srch_status);
    } elseif ($this->session->userdata('srch_status')) {
        $data['srch_status'] = $srch_status = $this->session->userdata('srch_status');
    } else {
        $data['srch_status'] = $srch_status = '';
    }
    if (!empty($srch_status) && $srch_status !== 'All') {
        $where .= " AND a.status = '" . $this->db->escape_str($srch_status) . "'";
    }

    // === COUNT TOTAL ===
    $sql_count = "SELECT COUNT(*) as total FROM tender_quotation_info a WHERE a.status != 'Delete' AND $where";
    $query_count = $this->db->query($sql_count);
    $data['total_records'] = $query_count->row()->total;

    // === PAGINATION ===
    $data['sno'] = $this->uri->segment(2, 0);
    $this->load->library('pagination');

    $config['base_url'] = trim(site_url($data['s_url']), '/' . $this->uri->segment(2, 0));
    $config['total_rows'] = $data['total_records'];
    $config['per_page'] = 25;
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
    $data['pagination'] = $this->pagination->create_links();

    // === FETCH RECORDS ===
    $sql = "
        SELECT 
            a.tender_quotation_id,
            a.quotation_no,
            a.tender_ref_no,
            a.quote_date,
            a.remarks,
            a.status,
            b.company_name,
            c.customer_name,
            d.enquiry_no AS tender_enquiry_no
        FROM tender_quotation_info a
        LEFT JOIN company_info b ON a.company_id = b.company_id AND b.status = 'Active'
        LEFT JOIN customer_info c ON a.customer_id = c.customer_id AND c.status = 'Active'
        LEFT JOIN tender_enquiry_info d ON a.tender_enquiry_id = d.tender_enquiry_id AND d.status = 'Active'
        WHERE a.status != 'Delete' AND $where
        ORDER BY a.tender_quotation_id DESC
        LIMIT " . $this->uri->segment(2, 0) . ", " . $config['per_page'];

    $query = $this->db->query($sql);
    $data['record_list'] = $query->result_array();

    // === DROPDOWNS ===
    $data['company_opt'] = ['' => 'All'];
    $sql = "SELECT company_id, company_name FROM company_info WHERE status = 'Active' ORDER BY company_name";
    $query = $this->db->query($sql);
    foreach ($query->result_array() as $row) {
        $data['company_opt'][$row['company_id']] = $row['company_name'];
    }

    $data['customer_opt'] = ['' => 'All'];
    $sql = "SELECT customer_id, customer_name FROM customer_info WHERE status = 'Active' ORDER BY customer_name";
    $query = $this->db->query($sql);
    foreach ($query->result_array() as $row) {
        $data['customer_opt'][$row['customer_id']] = $row['customer_name'];
    }

    $data['tender_enquiry_opt'] = ['' => 'All'];
    $sql = "SELECT tender_enquiry_id, enquiry_no FROM tender_enquiry_info WHERE status = 'Active' ORDER BY enquiry_no";
    $query = $this->db->query($sql);
    foreach ($query->result_array() as $row) {
        $data['tender_enquiry_opt'][$row['tender_enquiry_id']] = $row['enquiry_no'];
    }

    $data['status_opt'] = ['' => 'All', 'Active' => 'Active', 'Inactive' => 'Inactive'];

    $this->load->view('page/tender/tender-quotation-list', $data);
}



    public function get_data()
    {
        $table = $this->input->post('tbl');
        $rec_id = $this->input->post('id');
        $rec_list = array();

        if ($table == 'get-company-customer-list') {
            $query = $this->db->query("
                SELECT c.customer_id, c.customer_name
                FROM project_info p
                LEFT JOIN customer_info c ON c.customer_id = p.customer_id
                WHERE p.company_id = ? AND p.status = 'Active' AND c.status = 'Active'
                GROUP BY c.customer_id ORDER BY c.customer_name
            ", [$rec_id]);
            $rec_list = $query->result_array();
        }

        if ($table == 'get-category-item-list') {
            $query = $this->db->query("
                SELECT item_id, item_name
                FROM item_info
                WHERE category_id = ? AND status = 'Active'
                ORDER BY item_name
            ", [$rec_id]);
            $rec_list = $query->result_array();
        }

        if ($table == 'get-uom-desc-from-item') {
            $query = $this->db->query("
                SELECT item_id, uom, item_description, item_name
                FROM item_info
                WHERE item_id = ? AND status = 'Active'
            ", [$rec_id]);
            $rec_list = $query->result_array();
        }


        if ($table == 'get-tender-quotation-item-list-rate') {
            $query = $this->db->query("
            SELECT
                a.tender_enquiry_item_id,
                d.category_id,
                d.category_name,
                c.item_id,
                c.item_name,
                a.item_desc,
                a.uom,
                a.qty,
                a.status
            FROM
                tender_enquiry_item_info AS a
            LEFT JOIN tender_enquiry_info AS b
            ON
                a.tender_enquiry_id = b.tender_enquiry_id AND b.status = 'Active'
            LEFT JOIN item_info AS c
            ON
                a.item_id = c.item_id AND c.status = 'Active'
            LEFT JOIN category_info AS d
            ON
                a.category_id = d.category_id AND d.status = 'Active'
            WHERE
                a.status = 'Active' AND a.tender_enquiry_id = '" . $rec_id . "'
            ");
            $rec_list = $query->result_array();
        }

        header('Content-Type: application/json');
        echo json_encode($rec_list);
    }

    public function delete_record()
    {
        $table = $this->input->post('tbl');
        $rec_id = $this->input->post('id');

        if ($table == 'tender_enquiry_info') {
            $this->db->where('tender_enquiry_id', $rec_id);
            $this->db->update('tender_enquiry_info', ['status' => 'Delete']);
            echo 'Tender Enquiry deleted successfully.';
        } else {
            echo 'Invalid request.';
        }
    }
}