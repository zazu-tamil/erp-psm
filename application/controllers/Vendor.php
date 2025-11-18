<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Vendor extends CI_Controller
{

    public function vendor_rate_enquiry()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        if ($this->session->userdata(SESS_HD . 'level') != 'Admin' && $this->session->userdata(SESS_HD . 'level') != 'Staff') {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }

        $data['js'] = 'vendor/vendor-rate-enquiry.inc';
        $data['title'] = 'Vendor Rate Enquiry';

        if ($this->input->post('mode') == 'Add') {
            $this->db->trans_start();

            $insert_data = array(
                'customer_id' => $this->input->post('srch_customer_id'),
                'tender_enquiry_id' => $this->input->post('srch_tender_enquiry_id'),
                'vendor_id' => $this->input->post('srch_vendor_id'),
                'vendor_contact_person_id' => $this->input->post('srch_vendor_contact_id'),
                'enquiry_date' => $this->input->post('enquiry_date') ? date('Y-m-d H:i:s', strtotime($this->input->post('enquiry_date'))) : null,
                'opening_date' => $this->input->post('opening_date') ? date('Y-m-d H:i:s', strtotime($this->input->post('opening_date'))) : null,
                'closing_date' => $this->input->post('closing_date') ? date('Y-m-d H:i:s', strtotime($this->input->post('closing_date'))) : null,
                'enquiry_no' => $this->input->post('enquiry_no'),
                'status' => $this->input->post('status') ?: 'Active',
                'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'created_date' => date('Y-m-d H:i:s')
            );
            $this->db->insert('vendor_rate_enquiry_info', $insert_data);
            $vendor_rate_enquiry_id = $this->db->insert_id();

            $selected_items = $this->input->post('selected_items');
            $tender_enquiry_item_id = $this->input->post('tender_enquiry_item_id');
            $category_ids = $this->input->post('category_id');
            $item_ids = $this->input->post('item_id');
            $item_descs = $this->input->post('item_desc');
            $uoms = $this->input->post('uom');
            $qtys = $this->input->post('qty');

            if (!empty($selected_items)) {
                foreach ($selected_items as $index) {
                    if (!empty($item_ids[$index])) {
                        $insert_item_data = array(
                            'vendor_rate_enquiry_id' => $vendor_rate_enquiry_id,
                            'tender_enquiry_item_id' => $tender_enquiry_item_id[$index] ?? 0,
                            'category_id' => $category_ids[$index] ?? 0,
                            'item_id' => $item_ids[$index],
                            'item_desc' => $item_descs[$index] ?? '',
                            'uom' => $uoms[$index] ?? '',
                            'qty' => $qtys[$index] ?? 0.00,
                            'status' => 'Active',
                            'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                            'created_date' => date('Y-m-d H:i:s')
                        );
                        $this->db->insert('vendor_rate_enquiry_item_info', $insert_item_data);
                    }
                }
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Error saving data. Please try again.');
            } else {
                $this->session->set_flashdata('success', 'Tender Enquiry saved successfully.');
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

        $data['customer_opt'] = [];
        $data['vendor_contact_opt'] = [];
        $data['vendor_opt'] = [];
        $data['country_opt'] = [];

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

        //     $sql = "
        //     SELECT vendor_contact_id, contact_person_name
        //     FROM vendor_contact_info
        //     WHERE status = 'Active' 
        //     ORDER BY contact_person_name ASC
        // ";
        // $query = $this->db->query($sql);
        // foreach ($query->result_array() as $row) {

        //     $data['vendor_contact_opt'][$row['vendor_contact_id']] = $row['contact_person_name'];
        // }

        $sql = "
           SELECT
                a.enquiry_no,
                b.company_code,
                c.customer_code,
                a.company_sno,
                a.tender_enquiry_id,
                a.customer_sno
            FROM tender_enquiry_info AS a LEFT JOIN company_info as b on a.company_id = b.company_id and b.status='Active' 
            LEFT JOIN customer_info as c on a.customer_id = c.customer_id and c.status='Active' 
            WHERE a.status = 'Active' ORDER BY a.tender_enquiry_id , a.enquiry_no ASC
        ";
        $query = $this->db->query($sql);
        $data['tender_enquiry_opt'] = array();
        foreach ($query->result_array() as $row) {
            $data['tender_enquiry_opt'][$row['tender_enquiry_id']] = $row['company_code'] . ' -> ' . $row['company_sno'] . ' -> ' . $row['customer_code'] . ' -> ' . $row['customer_sno'] . ' -> ' . $row['enquiry_no'];
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
        $this->load->view('page/vendor/vendor-rate-enquiry', $data);
    }

    public function vendor_rate_enquiry_edit($id = 0)
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();
        if ($this->session->userdata(SESS_HD . 'level') != 'Admin' && $this->session->userdata(SESS_HD . 'level') != 'Staff') {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }
        if ($id <= 0)
            redirect('vendor-rate-enquiry-list');

        $data['js'] = 'vendor/vendor-rate-enquiry-edit.inc';
        $data['title'] = 'Edit Vendor Rate Enquiry';

        if ($this->input->post('mode') == 'Edit') {
            $this->db->trans_start();
            $update_data = array(
                'customer_id' => $this->input->post('srch_customer_id'),
                'tender_enquiry_id' => $this->input->post('srch_tender_enquiry_id'),
                'vendor_id' => $this->input->post('srch_vendor_id'),
                'vendor_contact_person_id' => $this->input->post('srch_vendor_contact_id'),
                'enquiry_date' => $this->input->post('enquiry_date') ? date('Y-m-d H:i:s', strtotime($this->input->post('enquiry_date'))) : null,
                'opening_date' => $this->input->post('opening_date') ? date('Y-m-d H:i:s', strtotime($this->input->post('opening_date'))) : null,
                'closing_date' => $this->input->post('closing_date') ? date('Y-m-d H:i:s', strtotime($this->input->post('closing_date'))) : null,
                'enquiry_no' => $this->input->post('enquiry_no'),
                'status' => $this->input->post('status') ?: 'Active',
                'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'updated_date' => date('Y-m-d H:i:s')
            );
            $this->db->where('vendor_rate_enquiry_id', $id);
            $this->db->update('vendor_rate_enquiry_info', $update_data);

            $this->db->where('vendor_rate_enquiry_id', $id);
            $this->db->delete('vendor_rate_enquiry_item_info');

            $tender_enquiry_item_ids = $this->input->post('tender_enquiry_item_id');
            $category_ids = $this->input->post('category_id');
            $item_ids = $this->input->post('item_id');
            $item_descs = $this->input->post('item_desc');
            $uoms = $this->input->post('uom');
            $qtys = $this->input->post('qty');
            $rates = $this->input->post('rate');
            $gsts = $this->input->post('gst');
            $amounts = $this->input->post('amount');

            if (!empty($item_ids) && is_array($item_ids)) {
                foreach ($item_ids as $index => $item_id) {
                    if (!empty($item_id)) {
                        $insert_item_data = array(
                            'vendor_rate_enquiry_id' => $id,
                            'tender_enquiry_item_id' => $tender_enquiry_item_ids[$index] ?? 0,
                            'category_id' => $category_ids[$index] ?? 0,
                            'item_id' => $item_id,
                            'item_desc' => $item_descs[$index] ?? '',
                            'uom' => $uoms[$index] ?? '',
                            'qty' => $qtys[$index] ?? 0.00,
                            'rate' => $rates[$index] ?? 0.00,
                            'gst' => $gsts[$index] ?? 0.00,
                            'amount' => $amounts[$index] ?? 0.00,
                            'status' => 'Active',
                            'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                            'created_date' => date('Y-m-d H:i:s')
                        );
                        $this->db->insert('vendor_rate_enquiry_item_info', $insert_item_data);
                    }
                }
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Error updating data.');
            } else {
                $this->session->set_flashdata('success', 'Vendor Rate Enquiry updated successfully.');
            }
            redirect('vendor-rate-enquiry-list/');
        }

        // Load main record
        $sql = "
        SELECT * FROM 
        vendor_rate_enquiry_info 
        WHERE vendor_rate_enquiry_id = ? 
        AND status != 'Delete'
    ";
        $query = $this->db->query($sql, array($id));
        if ($query->num_rows() == 0)
            redirect('vendor-rate-enquiry-list');
        $data['main'] = $query->row_array();

        $data['customer_opt'] = [];
        $data['vendor_contact_opt'] = [];
        $data['vendor_opt'] = [];
        $data['tender_enquiry_opt'] = [];
        $data['gst_opt'] = [];

        // Load GST options
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
        SELECT vendor_id, vendor_name 
        FROM vendor_info 
        WHERE status = 'Active' 
        ORDER BY vendor_name ASC
    ";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['vendor_opt'][$row['vendor_id']] = $row['vendor_name'];
        }

        $sql = "
         SELECT
            customer_id,
            customer_name
        FROM
            customer_info
        WHERE
        STATUS
            = 'Active'
        ORDER BY
            customer_name ASC 
    ";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['customer_opt'][$row['customer_id']] = $row['customer_name'];
        }

        // Load vendor contacts for the selected vendor on page load
        if (!empty($data['main']['vendor_id'])) {
            $sql = "
            SELECT vendor_contact_id, contact_person_name
            FROM vendor_contact_info
            WHERE status = 'Active' 
            AND vendor_id = ?
            ORDER BY contact_person_name ASC
        ";
            $query = $this->db->query($sql, array($data['main']['vendor_id']));
            foreach ($query->result_array() as $row) {
                $data['vendor_contact_opt'][$row['vendor_contact_id']] = $row['contact_person_name'];
            }
        }

        $sql = "
        SELECT a.enquiry_no,
                b.company_code,
                c.customer_code,
                a.company_sno,
                a.tender_enquiry_id,
                a.customer_sno
        FROM tender_enquiry_info AS a
        LEFT JOIN company_info as b on a.company_id = b.company_id and b.status='Active'
        LEFT JOIN customer_info as c on a.customer_id = c.customer_id and c.status='Active'
        WHERE a.status = 'Active'
        ORDER BY a.tender_enquiry_id , a.enquiry_no ASC
    ";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['tender_enquiry_opt'][$row['tender_enquiry_id']] = $row['company_code'] . ' -> ' . $row['company_sno'] . ' -> ' . $row['customer_code'] . ' -> ' . $row['customer_sno'] . ' -> ' . $row['enquiry_no'];
        }


        $sql = "
       SELECT
            a.*,
            b.category_name,
            c.item_name
        FROM
            vendor_rate_enquiry_item_info a
        LEFT JOIN category_info b ON
            a.category_id = b.category_id
        LEFT JOIN item_info c ON
            a.item_id = c.item_id
        WHERE
        a.status ='Active'
        and a.vendor_rate_enquiry_id = ?
        ORDER BY a.vendor_rate_enquiry_item_id ASC
    ";
        $query = $this->db->query($sql, array($id));
        $data['item_rows'] = $query->result_array();

        $this->load->view('page/vendor/vendor-rate-enquiry-edit', $data);
    }



    public function vendor_rate_enquiry_print($vendor_rate_enquiry_id = 0)
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        if (!$vendor_rate_enquiry_id) {
            show_404();
        }

        // Fetch main record
        $sql = "
        SELECT 
            vrei.*,
            c.customer_name,
            v.vendor_name,
            v.mobile AS vendor_mobile,
            v.address AS vendor_address,
            te.enquiry_no AS tender_enquiry_no,
            ci.company_name AS our_company
        FROM vendor_rate_enquiry_info vrei
        LEFT JOIN customer_info c ON vrei.customer_id = c.customer_id
        LEFT JOIN vendor_info v ON vrei.vendor_id = v.vendor_id
        LEFT JOIN tender_enquiry_info te ON vrei.tender_enquiry_id = te.tender_enquiry_id
        LEFT JOIN company_info ci ON te.company_id = ci.company_id AND ci.status = 'Active'
        WHERE vrei.vendor_rate_enquiry_id = ? AND vrei.status != 'Delete'
    ";
        $query = $this->db->query($sql, [$vendor_rate_enquiry_id]);
        $data['record'] = $query->row_array();

        if (!$data['record']) {
            show_404();
        }

        // Fetch items
        $sql = "
        SELECT 
            vrei_item.*,
            cat.category_name,
            item.item_name,
            item.item_description,
            item.uom AS item_uom
        FROM vendor_rate_enquiry_item_info vrei_item
        LEFT JOIN category_info cat ON vrei_item.category_id = cat.category_id
        LEFT JOIN item_info item ON vrei_item.item_id = item.item_id
        WHERE vrei_item.vendor_rate_enquiry_id = ? AND vrei_item.status = 'Active'
        ORDER BY vrei_item.vendor_rate_enquiry_item_id
    ";
        $query = $this->db->query($sql, [$vendor_rate_enquiry_id]);
        $data['items'] = $query->result_array();

        // Calculate totals
        $data['grand_total'] = 0;
        $data['total_gst'] = 0;
        foreach ($data['items'] as &$item) {
            $item['amount'] = $item['rate'] * $item['qty'];
            $item['gst_amount'] = $item['amount'] * ($item['gst'] / 100);
            $data['grand_total'] += $item['amount'];
            $data['total_gst'] += $item['gst_amount'];
        }
        $data['final_total'] = $data['grand_total'] + $data['total_gst'];

        $this->load->view('page/vendor/vendor-rate-enquiry-print', $data);
    }

    public function vendor_po_add()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        if (
            $this->session->userdata(SESS_HD . 'level') != 'Admin' &&
            $this->session->userdata(SESS_HD . 'level') != 'Staff'
        ) {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }

        $data['js'] = 'vendor/vendor-po-add.inc';
        $data['title'] = 'Add Vendor PO';

        if ($this->input->post('mode') == 'Add') {

            $this->db->trans_start();
            $header = [
                'company_id' => $this->input->post('srch_company_id'),
                'customer_id' => $this->input->post('srch_customer_id'),
                'tender_enquiry_id' => $this->input->post('srch_tender_enquiry_id'),
                'vendor_id' => $this->input->post('srch_vendor_id'),
                'vendor_rate_enquiry_id' => $this->input->post('vendor_rate_enquiry_id'),
                'vendor_contact_person_id' => $this->input->post('srch_vendor_contact_person_id'),
                'po_no' => $this->input->post('po_no'),
                'po_date' => $this->input->post('po_date'),
                'opening_date' => $this->input->post('opening_date'),
                'closing_date' => $this->input->post('closing_date'),
                'remarks' => $this->input->post('remarks'),
                'terms' => $this->input->post('terms'),
                'po_status' => $this->input->post('po_status'),
                'status' => $this->input->post('status'),
                'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'created_date' => date('Y-m-d H:i:s'),
            ];

            $this->db->insert('vendor_po_info', $header);
            $vendor_po_id = $this->db->insert_id();
            $selected_items = $this->input->post('selected_items') ?? [];

            if (!empty($selected_items)) {

                $vendor_rate_enquiry_item_id = $this->input->post('vendor_rate_enquiry_item_id') ?? [];
                $category_id = $this->input->post('category_id') ?? [];
                $item_id = $this->input->post('item_id') ?? [];
                $item_desc = $this->input->post('item_desc') ?? [];
                $uom = $this->input->post('uom') ?? [];
                $qty = $this->input->post('qty') ?? [];
                $rate = $this->input->post('rate') ?? [];
                $gst = $this->input->post('gst') ?? [];
                $amount = $this->input->post('amount') ?? [];

                foreach ($selected_items as $idx) {

                    $item = [
                        'vendor_po_id' => $vendor_po_id,
                        'vendor_rate_enquiry_item_id' => $vendor_rate_enquiry_item_id[$idx] ?? 0,
                        'category_id' => $category_id[$idx] ?? 0,
                        'item_id' => $item_id[$idx] ?? 0,
                        'item_desc' => $item_desc[$idx] ?? '',
                        'uom' => $uom[$idx] ?? '',
                        'qty' => $qty[$idx] ?? 0,
                        'rate' => $rate[$idx] ?? 0,
                        'gst' => $gst[$idx] ?? 0,
                        'amount' => $amount[$idx] ?? 0,
                        'status' => 'Active',
                        'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                        'created_date' => date('Y-m-d H:i:s'),
                    ];

                    $this->db->insert('vendor_po_item_info', $item);
                }
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Error saving Vendor PO. Please try again.');
            } else {
                $this->session->set_flashdata('success', 'Vendor PO saved successfully.');
            }

            redirect('vendor-po-list/');
        }
        $sql = "
            SELECT company_id, company_name 
            FROM company_info 
            WHERE status = 'Active' 
            ORDER BY company_name ASC";
        $query = $this->db->query($sql);
        $data['company_opt'] = [];
        foreach ($query->result_array() as $row) {
            $data['company_opt'][$row['company_id']] = $row['company_name'];
        }

        $sql = "
            SELECT gst_id, gst_percentage 
            FROM gst_info 
            WHERE status = 'Active' 
            ORDER BY gst_percentage ASC";
        $query = $this->db->query($sql);
        $data['gst_opt'] = [];
        foreach ($query->result_array() as $row) {
            $data['gst_opt'][$row['gst_id']] = $row['gst_percentage'];
        }

        $data['vendor_opt'] = [];
        $data['vendor_contact_opt'] = [];
        $sql = "
            SELECT vendor_id,vendor_name 
            FROM vendor_info 
            WHERE status = 'Active' 
            ORDER BY vendor_name ASC";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['vendor_opt'][$row['vendor_id']] = $row['vendor_name'];
        }

        // $sql = "
        //     SELECT vendor_contact_id, contact_person_name
        //     FROM vendor_contact_info
        //     WHERE status = 'Active' 
        //     ORDER BY contact_person_name ASC
        // ";
        // $query = $this->db->query($sql);
        // foreach ($query->result_array() as $row) {

        //     $data['vendor_contact_opt'][$row['vendor_contact_id']] = $row['contact_person_name'];
        // }


        $this->load->view('page/vendor/vendor-po-add', $data);
    }

    public function vendor_po_list()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        $data = array();
        $data['js'] = 'vendor/vendor-po-list.inc';
        $data['s_url'] = 'vendor-po-list';
        $data['title'] = 'Vendor PO List';

        // echo '<pre>';
        // print_r($_POST);
        // echo '</pre>';
        $where = "1 = 1";

        // Customer Filter
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

        // Vendor Filter
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
        if ($this->input->post('srch_po_status') !== null) {
            $data['srch_po_status'] = $srch_po_status = $this->input->post('srch_po_status');
            $this->session->set_userdata('srch_po_status', $srch_po_status);
        } elseif ($this->session->userdata('srch_po_status')) {
            $data['srch_po_status'] = $srch_po_status = $this->session->userdata('srch_po_status');
        } else {
            $data['srch_po_status'] = $srch_po_status = '';
        }
        if (!empty($srch_po_status) && $srch_po_status !== 'All') {
            $where .= " AND a.po_status = '" . $this->db->escape_str($srch_po_status) . "'";
        }

        $sql_count = "SELECT COUNT(*) as total FROM vendor_po_info a WHERE a.status != 'Delete' AND $where";
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


        $sql = "
            SELECT company_id, company_name 
            FROM company_info 
            WHERE status = 'Active' 
            ORDER BY company_name ASC";
        $query = $this->db->query($sql);
        $data['company_opt'] = [];
        foreach ($query->result_array() as $row) {
            $data['company_opt'][$row['company_id']] = $row['company_name'];
        }
        $sql = "
            SELECT  customer_id, customer_name
            FROM customer_info 
            WHERE status = 'Active' 
            ORDER BY customer_name ASC";
        $query = $this->db->query($sql);
        $data['customer_opt'] = [];
        foreach ($query->result_array() as $row) {
            $data['customer_opt'][$row['customer_id']] = $row['customer_name'];
        }
        $data['tender_enquiry_opt'] = [];
        if (!empty($srch_customer_id  || $srch_tender_enquiry_id)) {
            $sql = "
                SELECT
                    a.enquiry_no,
                    b.company_code,
                    c.customer_code,
                    a.company_sno,
                    a.tender_enquiry_id,
                    a.customer_sno
                FROM
                    tender_enquiry_info AS a
                LEFT JOIN company_info AS b
                ON
                    a.company_id = b.company_id AND b.status = 'Active'
                LEFT JOIN customer_info AS c
                ON
                    a.customer_id = c.customer_id AND c.status = 'Active'
                WHERE
                    a.status = 'Active' 
                and a.customer_id= '".$srch_customer_id."'
                 ORDER BY
                    a.tender_enquiry_id,
                    a.enquiry_no ASC
        ";
            $query = $this->db->query($sql);
            $data['tender_enquiry_opt'] = [];
            foreach ($query->result_array() as $row) {
                $data['tender_enquiry_opt'][$row['tender_enquiry_id']] = $row['company_code'] . ' -> ' . $row['company_sno'] . ' -> ' . $row['customer_code'] . ' -> ' . $row['customer_sno'] . ' -> ' . $row['enquiry_no'];
            }
        }

        $data['po_status_opt'] = [
            '' => 'All',
            'Open' => 'Open',
            'In Progress' => 'In Progress',
            'Completed' => 'Completed',
            'Cancelled' => 'Cancelled',
        ];

        // === FETCH RECORDS ===
        $sql = "
             SELECT 
                a.po_no,
                a.po_date,
                a.opening_date,
                a.closing_date,
                a.vendor_po_id,
                a.status,
                a.customer_id,
                a.vendor_id,
                a.po_status,
                a.tender_enquiry_id,
                a.po_date,
                c.customer_name,
                v.vendor_name,
                a.company_id,
                a.customer_id, 
                t.enquiry_no AS tender_enquiry_no
            FROM vendor_po_info as  a
            LEFT JOIN customer_info c ON a.customer_id = c.customer_id AND c.status = 'Active'
            LEFT JOIN vendor_info v ON a.vendor_id = v.vendor_id AND v.status = 'Active'
            LEFT JOIN tender_enquiry_info t ON a.tender_enquiry_id = t.tender_enquiry_id AND t.status != 'Delete'
            WHERE a.status != 'Delete' AND $where
            ORDER BY a.vendor_po_id DESC
            LIMIT " . $this->uri->segment(2, 0) . ", " . $config['per_page'];

        $query = $this->db->query($sql);
        $data['record_list'] = $query->result_array();



        $this->load->view('page/vendor/vendor-po-list', $data);
    }

    public function vendor_po_edit($vendor_po_id = 0)
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        if (
            $this->session->userdata(SESS_HD . 'level') != 'Admin' &&
            $this->session->userdata(SESS_HD . 'level') != 'Staff'
        ) {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }

        $data['js'] = 'vendor/vendor-po-edit.inc';
        $data['title'] = 'Edit Vendor PO';

        // ==================== UPDATE MODE ====================
        if ($this->input->post('mode') == 'Edit') {
            $this->db->trans_start();

            $header = [
                'company_id' => $this->input->post('srch_company_id'),
                'customer_id' => $this->input->post('srch_customer_id'),
                'tender_enquiry_id' => $this->input->post('srch_tender_enquiry_id'),
                'vendor_id' => $this->input->post('srch_vendor_id'),
                'vendor_rate_enquiry_id' => $this->input->post('vendor_rate_enquiry_id'),
                'vendor_contact_person_id' => $this->input->post('srch_vendor_contact_person_id') ? $this->input->post('srch_vendor_contact_person_id') : null,
                'po_no' => $this->input->post('po_no'),
                'po_date' => $this->input->post('po_date'),
                'opening_date' => $this->input->post('opening_date'),
                'closing_date' => $this->input->post('closing_date'),
                'remarks' => $this->input->post('remarks'),
                'terms' => $this->input->post('terms'),
                'po_status' => $this->input->post('po_status'),
                'status' => $this->input->post('status'),
                'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'updated_date' => date('Y-m-d H:i:s'),
            ];

            $this->db->where('vendor_po_id', $this->input->post('vendor_po_id'));
            $this->db->update('vendor_po_info', $header);

            $vendor_po_id = $this->input->post('vendor_po_id');

            // Remove all existing items first (we'll re-insert only selected ones)
            $this->db->where('vendor_po_id', $vendor_po_id);
            $this->db->delete('vendor_po_item_info');

            // Insert only selected items
            $selected_items = $this->input->post('selected_items') ?? [];

            if (!empty($selected_items)) {
                $vendor_rate_enquiry_item_id = $this->input->post('vendor_rate_enquiry_item_id') ?? [];
                $category_id = $this->input->post('category_id') ?? [];
                $item_id = $this->input->post('item_id') ?? [];
                $item_desc = $this->input->post('item_desc') ?? [];
                $uom = $this->input->post('uom') ?? [];
                $qty = $this->input->post('qty') ?? [];
                $rate = $this->input->post('rate') ?? [];
                $gst = $this->input->post('gst') ?? [];
                $amount = $this->input->post('amount') ?? [];

                foreach ($selected_items as $idx) {
                    $item = [
                        'vendor_po_id' => $vendor_po_id,
                        'vendor_rate_enquiry_item_id' => $vendor_rate_enquiry_item_id[$idx] ?? 0,
                        'category_id' => $category_id[$idx] ?? 0,
                        'item_id' => $item_id[$idx] ?? 0,
                        'item_desc' => $item_desc[$idx] ?? '',
                        'uom' => $uom[$idx] ?? '',
                        'qty' => $qty[$idx] ?? 0,
                        'rate' => $rate[$idx] ?? 0,
                        'gst' => $gst[$idx] ?? 0,
                        'amount' => $amount[$idx] ?? 0,
                        'status' => 'Active',
                        'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                        'created_date' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->insert('vendor_po_item_info', $item);
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Error updating Vendor PO.');
            } else {
                $this->session->set_flashdata('success', 'Vendor PO updated successfully.');
            }
            redirect('vendor-po-list/');
        }
        // Load dependent dropdowns (same AJAX will refill others)
        $data['customer_opt'] = ['' => 'Select Customer'];
        $data['tender_enquiry_opt'] = ['' => 'Select Enquiry'];
        $data['vendor_rate_enquiry_opt'] = ['' => 'Select Enquiry No'];
        $data['vendor_opt'] = ['' => 'Select Vendor'];
        $data['vendor_contact_opt'] = ['' => 'Select'];

        // ==================== LOAD DATA FOR EDIT ====================
        if (!$vendor_po_id) {
            show_404();
        }


        // Items with category & item name

        $sql = "
            SELECT 
                vpo.*
            FROM vendor_po_info AS vpo
            WHERE vpo.vendor_po_id = ?
            AND vpo.status = 'Active'
            ORDER BY vpo.vendor_po_id ASC
        ";
        $query = $this->db->query($sql, [$vendor_po_id]);
        $data['header'] = $query->row_array();   // ✅ Single row

        $sql = "
        SELECT 
            vpi.*,
            ci.category_name,
            ii.item_name
        FROM vendor_po_item_info vpi
        LEFT JOIN category_info ci ON vpi.category_id = ci.category_id
        LEFT JOIN item_info ii ON vpi.item_id = ii.item_id
        WHERE vpi.vendor_po_id = ? AND vpi.status = 'Active'
        ORDER BY vpi.vendor_po_item_id ASC
    ";
        $query = $this->db->query($sql, [$vendor_po_id]);
        $data['items'] = $query->result_array();

        // Company dropdown
        $sql = "
            SELECT company_id, company_name FROM 
            company_info 
            WHERE status = 'Active' 
            ORDER BY company_name ASC
        ";
        $query = $this->db->query($sql);
        $data['company_opt'] = ['' => 'Select Company'];
        foreach ($query->result_array() as $row) {
            $data['company_opt'][$row['company_id']] = $row['company_name'];
        }

        $data['customer_opt'] = ['' => 'Select Customer'];
        $sql = "
            SELECT customer_id, customer_name FROM 
            customer_info 
            WHERE status = 'Active' 
            ORDER BY customer_name ASC
        ";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['customer_opt'][$row['customer_id']] = $row['customer_name'];
        }
        $sql = "
            SELECT customer_id, customer_name FROM 
            customer_info 
            WHERE status = 'Active' 
            ORDER BY customer_name ASC
        ";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['customer_opt'][$row['customer_id']] = $row['customer_name'];
        }
        $sql = "
            SELECT
                a.*
                FROM
                  tender_enquiry_info  as a 
                WHERE
                    a.status = 'Active' 
                GROUP by a.tender_enquiry_id 
                ORDER by a.tender_enquiry_id
        ";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['tender_enquiry_opt'][$row['tender_enquiry_id']] = $row['enquiry_no'];
        }

        $sql = "
            SELECT
                a.*
                FROM
                  vendor_rate_enquiry_info  as a 
                WHERE
                    a.status = 'Active' 
                 ORDER by a.vendor_rate_enquiry_id
        ";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['vendor_rate_enquiry_opt'][$row['vendor_rate_enquiry_id']] = $row['enquiry_no'];
        }
        $sql = "
            SELECT
                a.*
                FROM
                  vendor_rate_enquiry_info  as a 
                WHERE
                    a.status = 'Active' 
                 ORDER by a.vendor_rate_enquiry_id
        ";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['vendor_rate_enquiry_opt'][$row['vendor_rate_enquiry_id']] = $row['enquiry_no'];
        }

        // GST options
        $sql = "SELECT gst_percentage FROM gst_info WHERE status = 'Active' ORDER BY gst_percentage ASC";
        $query = $this->db->query($sql);
        $data['gst_opt'] = [];
        foreach ($query->result_array() as $row) {
            $data['gst_opt'][] = $row['gst_percentage'];
        }

        // Vendor dropdown
        $sql = "SELECT vendor_id, vendor_name FROM vendor_info WHERE status = 'Active' ORDER BY vendor_name ASC";
        $query = $this->db->query($sql);
        $data['vendor_opt'] = ['' => 'Select'];
        foreach ($query->result_array() as $row) {
            $data['vendor_opt'][$row['vendor_id']] = $row['vendor_name'];
        }
        // Vendor dropdown
        $sql = "
            SELECT vendor_contact_id, contact_person_name 
            FROM 
                vendor_contact_info 
            WHERE status = 'Active' 
            ORDER BY contact_person_name ASC
        ";
        $query = $this->db->query($sql);
        $data['vendor_contact_opt'] = ['' => 'Select'];
        foreach ($query->result_array() as $row) {
            $data['vendor_contact_opt'][$row['vendor_contact_id']] = $row['contact_person_name'];
        }





        $this->load->view('page/vendor/vendor-po-edit', $data); // Create this view (use the one I gave earlier)
    }
    public function get_vendor_rate_enquiry()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $vendor_rate_enquiry_id = $this->input->post('vendor_rate_enquiry_id');

        if (empty($vendor_rate_enquiry_id)) {
            echo json_encode([]);
            return;
        }

        $sql = "
            SELECT
                a.vendor_rate_enquiry_item_id,
                a.vendor_rate_enquiry_id,
                a.category_id,
                a.item_id,
                a.item_desc,
                a.uom,
                a.qty,
                a.rate,
                a.gst,
                a.amount 
            FROM
                vendor_rate_enquiry_item_info as a 
            LEFT JOIN category_info as  ci ON
                a.category_id = ci.category_id
            LEFT JOIN item_info ii ON
                a.item_id = ii.item_id
            WHERE  a.status = 'Active'
            and a.vendor_rate_enquiry_id = ?
            ORDER BY
                a.vendor_rate_enquiry_item_id ASC
            ";

        $query = $this->db->query($sql, [$vendor_rate_enquiry_id]);
        $result = $query->result_array();
        echo json_encode($result);
    }

    public function get_vendor_rate_enquiry_list()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }
        $tender_enquiry_id = $this->input->post('srch_tender_enquiry_id');



        $sql = "
            SELECT
                b.enquiry_no,
                    c.company_code,
                    d.customer_code,
                    b.company_sno,
                    b.tender_enquiry_id,
                    b.customer_sno
                FROM
                    vendor_rate_enquiry_info AS a
                LEFT JOIN tender_enquiry_info AS b
                ON
                    a.tender_enquiry_id = b.tender_enquiry_id
                    LEFT JOIN company_info as c on b.company_id = c.company_id and c.status='Active'
                    LEFT JOIN customer_info as d on a.customer_id = d.customer_id and d.status='Active'
                WHERE
                    a.status = 'Active' AND b.tender_enquiry_id = ?
                GROUP by a.tender_enquiry_id 
                ORDER by a.tender_enquiry_id
        ";

        $query = $this->db->query($sql, [$tender_enquiry_id]);
        $result = $query->result_array();
        echo json_encode($result);
    }


    public function ajax_add_master_inline()
    {
        if ($this->input->post('mode') == 'Add Vendor') {

            $data = [
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
                'status' => $this->input->post('status'),
                'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'created_date' => date('Y-m-d H:i:s'),
                'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'updated_date' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('vendor_info', $data);
            $insert_id = $this->db->insert_id();

            // Return response for JS
            echo json_encode([
                'status' => 'success',
                'message' => 'Vendor added successfully!',
                'id' => $insert_id,
                'name' => $data['vendor_name']
            ]);
            return; // Important: Stop execution after sending JSON
        }

        /* ===================== ADD CONTACT PERSON ===================== */
        if ($this->input->post('mode') == 'Add Contact Person') {
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
            $insert_id = $this->db->insert_id();

            // Return JSON response instead of redirect
            echo json_encode([
                'status' => 'success',
                'message' => 'Contact Person added successfully!',
                'id' => $insert_id,
                'name' => $ins['contact_person_name']
            ]);
            return; // Important: Stop execution after sending JSON
        }
    }

    public function get_data()
    {
        $table = $this->input->post('tbl');
        $rec_id = $this->input->post('id');

        $this->db->query('SET SQL_BIG_SELECTS=1');
        $rec_list = array();

        if ($table == 'get-tender-enquiry-item-list-rate') {
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

        // Get vendor contacts by vendor_id
        if ($table == 'get-vendor-contacts') {
            $query = $this->db->query("
            SELECT
                vendor_contact_id,
                contact_person_name,
                mobile,
                email,
                department,
                designation
            FROM
                vendor_contact_info
            WHERE
                status = 'Active' AND vendor_id = '" . $rec_id . "'
            ORDER BY
                contact_person_name ASC
        ");
            $rec_list = $query->result_array();
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($rec_list);
    }

    public function vendor_rate_enquiry_list()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        $data = array();
        $data['js'] = 'vendor/vendor-rate-enquiry-list.inc';
        $data['s_url'] = 'vendor-rate-enquiry-list';
        $data['title'] = 'Vendor Rate Enquiry List';

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
            $where .= " AND a.customer_id = '" . $this->db->escape_str($srch_customer_id) . "'";
        }

        // Vendor Filter
        if ($this->input->post('srch_vendor_id') !== null) {
            $data['srch_vendor_id'] = $srch_vendor_id = $this->input->post('srch_vendor_id');
            $this->session->set_userdata('srch_vendor_id', $srch_vendor_id);
        } elseif ($this->session->userdata('srch_vendor_id')) {
            $data['srch_vendor_id'] = $srch_vendor_id = $this->session->userdata('srch_vendor_id');
        } else {
            $data['srch_vendor_id'] = $srch_vendor_id = '';
        }
        if (!empty($srch_vendor_id)) {
            $where .= " AND a.vendor_id = '" . $this->db->escape_str($srch_vendor_id) . "'";
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
        $sql_count = "SELECT COUNT(*) as total FROM vendor_rate_enquiry_info a WHERE a.status != 'Delete' AND $where";
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
                a.vendor_rate_enquiry_id,
                a.enquiry_date,
                a.enquiry_no,
                a.opening_date,
                a.closing_date,
                a.status,
                c.customer_name,
                v.vendor_name,
                t.enquiry_no AS tender_enquiry_no
            FROM vendor_rate_enquiry_info a
            LEFT JOIN customer_info c ON a.customer_id = c.customer_id AND c.status = 'Active'
            LEFT JOIN vendor_info v ON a.vendor_id = v.vendor_id AND v.status = 'Active'
            LEFT JOIN tender_enquiry_info t ON a.tender_enquiry_id = t.tender_enquiry_id AND t.status != 'Delete'
            WHERE a.status != 'Delete' AND $where
            ORDER BY a.vendor_rate_enquiry_id DESC
            LIMIT " . $this->uri->segment(2, 0) . ", " . $config['per_page'];

        $query = $this->db->query($sql);
        $data['record_list'] = $query->result_array();

        // === DROPDOWNS ===
        $data['customer_opt'] = ['' => 'All'];
        $sql = "SELECT customer_id, customer_name FROM customer_info WHERE status = 'Active' ORDER BY customer_name";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['customer_opt'][$row['customer_id']] = $row['customer_name'];
        }

        $data['vendor_opt'] = ['' => 'All'];
        $sql = "SELECT vendor_id, vendor_name FROM vendor_info WHERE status = 'Active' ORDER BY vendor_name";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['vendor_opt'][$row['vendor_id']] = $row['vendor_name'];
        }

        $data['status_opt'] = ['' => 'All', 'Active' => 'Active', 'Inactive' => 'Inactive'];

        $this->load->view('page/vendor/vendor-rate-enquiry-list', $data);
    }

    public function delete_record()
    {
        $table = $this->input->post('tbl');
        $rec_id = $this->input->post('id');

        if ($table == 'vendor_rate_enquiry_info' && !empty($rec_id)) {
            $this->db->where('vendor_rate_enquiry_id', $rec_id);
            $this->db->update('vendor_rate_enquiry_info', [
                'status' => 'Delete',
                'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'updated_date' => date('Y-m-d H:i:s')
            ]);
            echo 'Vendor Rate Enquiry marked as deleted.';
        } else {
            echo 'Invalid request.';
        }
    }


    public function get_all_customers()
    {
        $sql = "
        SELECT customer_id, customer_name
        FROM customer_info
        WHERE status = 'Active'
        ORDER BY customer_name ASC
    ";
        $query = $this->db->query($sql);
        echo json_encode($query->result_array());
    }

    // Add this method to get tender enquiries by customer
    public function get_tender_enquiries_by_customer()
    {
        $company_id = $this->input->post('company_id');
        $customer_id = $this->input->post('customer_id');

        $sql = "
        SELECT 
            a.tender_enquiry_id,
            a.enquiry_no,
            b.company_name,
            c.customer_name 
        FROM tender_enquiry_info AS a
        LEFT JOIN company_info AS b ON a.company_id = b.company_id AND b.status = 'Active'
        LEFT JOIN customer_info AS c ON a.customer_id = c.customer_id AND c.status = 'Active'
        WHERE a.company_id = ? AND a.customer_id = ? AND a.status = 'Active'
        ORDER BY a.tender_enquiry_id, a.enquiry_no ASC
    ";
        $query = $this->db->query($sql, [$company_id, $customer_id]);
        $result = [];
        foreach ($query->result_array() as $row) {
            $result[] = [
                'tender_enquiry_id' => $row['tender_enquiry_id'],
                'display' => $row['tender_enquiry_id'] . ' -> ' . $row['enquiry_no'] . ' -> ' . $row['company_name'] . ' -> ' . $row['customer_name']
            ];
        }
        echo json_encode($result);
    }

}