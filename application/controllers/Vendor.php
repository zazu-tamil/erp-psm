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
                'vendor_rate_enquiry_status' => $this->input->post('vendor_rate_enquiry_status'),
                'status' => $this->input->post('status') ?: 'Active',
                'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'created_date' => date('Y-m-d H:i:s')
            );
            $this->db->insert('vendor_rate_enquiry_info', $insert_data);
            $vendor_rate_enquiry_id = $this->db->insert_id();

            $selected_items = $this->input->post('selected_items');
            $tender_enquiry_item_id = $this->input->post('tender_enquiry_item_id');
            //$category_ids = $this->input->post('category_id');
            // $item_ids = $this->input->post('item_id');
            $item_codes = $this->input->post('item_code');
            $item_descs = $this->input->post('item_desc');
            $uoms = $this->input->post('uom');
            $qtys = $this->input->post('qty');

            if (!empty($selected_items)) {
                foreach ($selected_items as $index => $value) {
                    if (!empty($item_descs[$index])) {
                        $insert_item_data = array(
                            'vendor_rate_enquiry_id' => $vendor_rate_enquiry_id,
                            'tender_enquiry_item_id' => $tender_enquiry_item_id[$index] ?? 0,
                            'item_code' => $item_codes[$index],
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
        $data['vendor_RFQ_opt'] = ['Prepared RFQ' => 'Prepared RFQ', 'RFQ Sent' => 'RFQ Sent', 'Quotation Received' => 'Quotation Received'];

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

        // $sql = "
        //    SELECT
        //         a.enquiry_no,
        //         b.company_code,
        //         c.customer_code,
        //         a.company_sno,
        //         a.tender_enquiry_id,
        //         a.customer_sno
        //     FROM tender_enquiry_info AS a LEFT JOIN company_info as b on a.company_id = b.company_id and b.status='Active' 
        //     LEFT JOIN customer_info as c on a.customer_id = c.customer_id and c.status='Active' 
        //     WHERE a.status = 'Active' ORDER BY a.tender_enquiry_id , a.enquiry_no ASC
        // ";
        // $query = $this->db->query($sql);
        // $data['tender_enquiry_opt'] = array();
        // foreach ($query->result_array() as $row) {
        //     $data['tender_enquiry_opt'][$row['tender_enquiry_id']] = $row['company_code'] . ' -> ' . $row['company_sno'] . ' -> ' . $row['customer_code'] . ' -> ' . $row['customer_sno'] . ' -> ' . $row['enquiry_no'];
        // }


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
                'vendor_rate_enquiry_status' => $this->input->post('vendor_rate_enquiry_status'),
                'status' => $this->input->post('status') ?: 'Active',
                'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'updated_date' => date('Y-m-d H:i:s')
            );
            $this->db->where('vendor_rate_enquiry_id', $id);
            $this->db->update('vendor_rate_enquiry_info', $update_data);


            $selected_idxs = $this->input->post('selected_items') ?? [];

            $vendor_rate_enquiry_item_ids = $this->input->post('vendor_rate_enquiry_item_id') ?? [];
            $tender_enquiry_item_ids = $this->input->post('tender_enquiry_item_id') ?? [];
            $item_codes = $this->input->post('item_code') ?? [];
            $item_descs = $this->input->post('item_desc') ?? [];
            $uoms = $this->input->post('uom') ?? [];
            $qtys = $this->input->post('qty') ?? [];

            /*
            ----------------------------------------
            STEP 1 : Make all items inactive first
            ----------------------------------------
            */
            $this->db->where('vendor_rate_enquiry_id', $id);
            $this->db->update('vendor_rate_enquiry_item_info', [
                'status' => 'Delete'
            ]);


            foreach ($selected_idxs as $rowIndex) {

                $item_data = [
                    'vendor_rate_enquiry_id' => $id,
                    'tender_enquiry_item_id' => $tender_enquiry_item_ids[$rowIndex] ?? 0,
                    'item_code' => $item_codes[$rowIndex] ?? '',
                    'item_desc' => $item_descs[$rowIndex] ?? '',
                    'uom' => $uoms[$rowIndex] ?? '',
                    'qty' => $qtys[$rowIndex] ?? 0,
                    'status' => 'Active',
                    'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                    'updated_date' => date('Y-m-d H:i:s')
                ];

                // ✅ UPDATE
                if (!empty($vendor_rate_enquiry_item_ids[$rowIndex])) {

                    $this->db->where(
                        'vendor_rate_enquiry_item_id',
                        $vendor_rate_enquiry_item_ids[$rowIndex]
                    )->update('vendor_rate_enquiry_item_info', $item_data);
                } else {

                    $item_data['created_by'] =
                        $this->session->userdata(SESS_HD . 'user_id');

                    $item_data['created_date'] =
                        date('Y-m-d H:i:s');

                    $this->db->insert(
                        'vendor_rate_enquiry_item_info',
                        $item_data
                    );
                }
            }

            /* 
            $tender_enquiry_item_ids = $this->input->post('tender_enquiry_item_id');
           // $category_ids = $this->input->post('category_id');
           // $item_ids = $this->input->post('item_id');
            $item_codes = $this->input->post('item_code');
            $item_descs = $this->input->post('item_desc');
            $uoms = $this->input->post('uom');
            $qtys = $this->input->post('qty');
            //$rates = $this->input->post('rate');
            //$gsts = $this->input->post('gst');
            //$amounts = $this->input->post('amount');

            if (!empty($item_descs) && is_array($item_descs)) {
                foreach ($item_descs as $index => $item_desc) {
                    if (!empty($item_descs)) {
                        $insert_item_data = array(
                            'vendor_rate_enquiry_id' => $id,
                            'tender_enquiry_item_id' => $tender_enquiry_item_ids[$index] ?? 0,
                           // 'category_id' => $category_ids[$index] ?? 0,
                           // 'item_id' => $item_id,
                            'item_desc' => $item_descs[$index] ?? '',
                            'item_code' => $item_codes[$index] ?? '',
                            'uom' => $uoms[$index] ?? '',
                            'qty' => $qtys[$index] ?? 0.00,
                            //'rate' => $rates[$index] ?? 0.00,
                            //'gst' => $gsts[$index] ?? 0.00,
                            //'amount' => $amounts[$index] ?? 0.00,
                            'status' => 'Active',
                            'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                            'created_date' => date('Y-m-d H:i:s')
                        );
                        $this->db->insert('vendor_rate_enquiry_item_info', $insert_item_data);
                    }
                }
            } */

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Error updating data.');
            } else {
                $this->session->set_flashdata('success', 'Vendor Rate Enquiry updated successfully.');
            }
            redirect('vendor-rate-enquiry-edit/' . $id);
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
        $data['vendor_RFQ_opt'] = ['Prepared RFQ' => 'Prepared RFQ', 'RFQ Sent' => 'RFQ Sent', 'Quotation Received' => 'Quotation Received'];
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
                SELECT 
                    a.tender_enquiry_id, 
                    get_tender_info(a.tender_enquiry_id) as tender_details
                FROM tender_enquiry_info AS a 
                WHERE a.status = 'Active'  
                ORDER BY a.tender_enquiry_id, a.enquiry_no ASC
            ";


        $query = $this->db->query($sql);
        $data['tender_enquiry_opt'] = [];
        foreach ($query->result_array() as $row) {
            // $data['tender_enquiry_opt'][$row['tender_enquiry_id']] = $row['company_code'] . ' -> ' . $row['company_sno'] . ' -> ' . $row['customer_code'] . ' -> ' . $row['customer_sno'] . ' -> ' . $row['enquiry_no'];
            $data['tender_enquiry_opt'][$row['tender_enquiry_id']] = $row['tender_details'];
        }

        $sql = "
       SELECT
            a.vendor_rate_enquiry_item_id,
            a.vendor_rate_enquiry_id,
            a.tender_enquiry_item_id,
            a.item_code,
            a.item_desc,
            a.uom,
            a.qty     
        FROM
            vendor_rate_enquiry_item_info a 
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

        $data['title'] = 'Vendor Rate Enquiry Print';

        if (!$vendor_rate_enquiry_id) {
            show_404();
        }

        // Fetch main record
        $sql = "
        SELECT 
            vrei.*,
            c.customer_name,
            v.vendor_name,
            v.country as vendor_country,
            v.mobile AS vendor_mobile,
            v.address AS vendor_address,
            te.enquiry_no AS tender_enquiry_no,
            ci.company_name AS our_company,
            v.address,
            ci.ltr_header_img,
            vrei.enquiry_date,
            vrei.enquiry_no,
            ci.quote_terms
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
                'vendor_quote_id' => $this->input->post('srch_vendor_quote_id'),
                'vendor_contact_person_id' => $this->input->post('srch_vendor_contact_person_id'),
                'vendor_rate_enquiry_id' => $this->input->post('vendor_rate_enquiry_id'),
                'po_no' => $this->input->post('po_no'),
                'currency_id' => $this->input->post('currency_id'),
                'po_date' => $this->input->post('po_date'),
                'delivery_date' => $this->input->post('delivery_date'),
                'transport_charges' => $this->input->post('transport_charges'),
                'other_charges' => $this->input->post('other_charges'),
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
                $vendor_quote_item_ids = $this->input->post('vendor_quote_item_id') ?? [];
                //$category_id = $this->input->post('category_id') ?? [];
                $item_codes = $this->input->post('item_code') ?? [];
                $item_desc = $this->input->post('item_desc') ?? [];
                $uom = $this->input->post('uom') ?? [];
                $qty = $this->input->post('qty') ?? [];
                $rate = $this->input->post('rate') ?? [];
                $gst = $this->input->post('gst') ?? [];
                $amount = $this->input->post('amount') ?? [];

                foreach ($selected_items as $idx => $value) {

                    $item = [
                        'vendor_po_id' => $vendor_po_id,
                        'vendor_rate_enquiry_item_id' => $vendor_rate_enquiry_item_id[$idx] ?? 0,
                        'vendor_quote_item_id' => $vendor_quote_item_ids[$idx] ?? 0,
                        // 'category_id' => $category_id[$idx] ?? 0,
                        'item_code' => $item_codes[$idx] ?? 0,
                        'item_desc' => $item_desc[$idx] ?? '',
                        'uom' => $uom[$idx] ?? '',
                        'qty' => $qty[$idx] ?? 0,
                        'rate' => $rate[$idx] ?? 0,
                        'gst' => $gst[$idx] ?? 0,
                        'amount' => $amount[$idx] ?? 0,
                        'status' => 'Active',
                        'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                        'created_date' => date('Y-m-d H:i:s'),
                        'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                        'updated_date' => date('Y-m-d H:i:s'),
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

            redirect('vendor-po-edit/' . $vendor_po_id);
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

        $sql = "
            SELECT gst_id, gst_percentage 
            FROM gst_info 
            WHERE status = 'Active' 
            ORDER BY gst_percentage ASC";
        $query = $this->db->query($sql);
        $data['gst_opt'] = [];
        foreach ($query->result_array() as $row) {
            $data['gst_opt'][$row['gst_percentage']] = $row['gst_percentage'];
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

        $data['po_status_opt'] = ['' => 'Select PO Status', 'In Progress' => 'In Progress', 'Delivered' => 'Delivered'];


        $sql = "
            SELECT currency_id, currency_code
            FROM currencies_info
            WHERE status = 'Active'
            ORDER BY currency_name ASC
        ";
        $query = $this->db->query($sql);

        foreach ($query->result_array() as $row) {
            $data['currency_opt'][$row['currency_id']] = $row['currency_code'];
        }
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

        $where = "1=1";

        if (isset($_POST['srch_from_date'])) {
            $data['srch_from_date'] = $srch_from_date = $this->input->post('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->input->post('srch_to_date');
            $this->session->set_userdata('srch_from_date', $this->input->post('srch_from_date'));
            $this->session->set_userdata('srch_to_date', $this->input->post('srch_to_date'));
        } elseif ($this->session->userdata('srch_from_date')) {
            $data['srch_from_date'] = $srch_from_date = $this->session->userdata('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->session->userdata('srch_to_date');
        } else {
            $data['srch_from_date'] = $srch_from_date = '';
            $data['srch_to_date'] = $srch_to_date = '';
        }

        if (!empty($srch_from_date) && !empty($srch_to_date)) {
            $where .= " AND  ( a.po_date BETWEEN '" . $this->db->escape_str($srch_from_date) . "' AND '" . $this->db->escape_str($srch_to_date) . "') ";
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
            $data['srch_enquiry_no'] = $srch_enquiry_no = '';
        }

        // Vendor RFQ Filter
        if ($this->input->post('srch_customer_rfq_no') !== null) {
            $data['srch_customer_rfq_no'] = $srch_customer_rfq_no = $this->input->post('srch_customer_rfq_no');
            $this->session->set_userdata('srch_customer_rfq_no', $srch_customer_rfq_no);
        } elseif ($this->session->userdata('srch_customer_rfq_no')) {
            $data['srch_customer_rfq_no'] = $srch_customer_rfq_no = $this->session->userdata('srch_customer_rfq_no');
        } else {
            $data['srch_customer_rfq_no'] = $srch_customer_rfq_no = '';
        }
        if (!empty($srch_customer_rfq_no)) {
            $where = " ( t.enquiry_no = '" . $this->db->escape_str($srch_customer_rfq_no) . "')";
            $data['srch_customer_id'] = $srch_customer_id = '';
        }



        if ($this->input->post('srch_enquiry_no') !== null) {
            $data['srch_enquiry_no'] = $srch_enquiry_no = $this->input->post('srch_enquiry_no');
            $this->session->set_userdata('srch_enquiry_no', $srch_enquiry_no);
        } elseif ($this->session->userdata('srch_enquiry_no')) {
            $data['srch_enquiry_no'] = $srch_enquiry_no = $this->session->userdata('srch_enquiry_no');
        } else {
            $data['srch_enquiry_no'] = $srch_enquiry_no = '';
        }

        if (!empty($srch_enquiry_no)) {
            $where = " ( concat(ifnull(ci.company_code,'') , '/', ifnull(t.company_sno,'') ,  '/' , ifnull(c.customer_code,'') ,  '/' , ifnull(t.customer_sno,''),  '/' , DATE_FORMAT(t.enquiry_date,'%Y') ) like '%" . $this->db->escape_str($srch_enquiry_no) . "%' ) ";
            $data['srch_customer_id'] = $srch_customer_id = '';
        }


        // Vendor RFQ
        if ($this->input->post('srch_vendor_po_no') !== null) {
            $data['srch_vendor_po_no'] = $srch_vendor_po_no = $this->input->post('srch_vendor_po_no');
            $this->session->set_userdata('srch_vendor_po_no', $srch_vendor_po_no);
        } elseif ($this->session->userdata('srch_vendor_po_no')) {
            $data['srch_vendor_po_no'] = $srch_vendor_po_no = $this->session->userdata('srch_vendor_po_no');
        } else {
            $data['srch_vendor_po_no'] = $srch_vendor_po_no = '';
        }

        if (!empty($srch_vendor_po_no)) {
            $where = " (a.po_no = '" . $this->db->escape_str($srch_vendor_po_no) . "')";
        }


        // Enquiry Filter

        $this->db->from('vendor_po_info a');
        $this->db->join('company_info ci', 'a.company_id = ci.company_id AND ci.status = "Active"', 'left');
        $this->db->join('customer_info c', 'a.customer_id = c.customer_id AND c.status = "Active"', 'left');
        $this->db->join('tender_enquiry_info t', 'a.tender_enquiry_id = t.tender_enquiry_id AND t.status = "Active"', 'left');
        $this->db->where('a.status !=', 'Delete');
        $this->db->where($where);
        $data['total_records'] = $this->db->count_all_results();
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
        if (!empty($srch_customer_id)) {
            // $sql = "
            //     SELECT
            //         a.enquiry_no,
            //         b.company_code,
            //         c.customer_code,
            //         a.company_sno,
            //         a.tender_enquiry_id,
            //         a.customer_sno
            //     FROM
            //         tender_enquiry_info AS a
            //     LEFT JOIN company_info AS b
            //     ON
            //         a.company_id = b.company_id AND b.status = 'Active'
            //     LEFT JOIN customer_info AS c
            //     ON
            //         a.customer_id = c.customer_id AND c.status = 'Active'
            //     WHERE
            //         a.status = 'Active' 
            //     and a.customer_id= '" . $srch_customer_id . "'
            //      ORDER BY
            //         a.tender_enquiry_id,
            //         a.enquiry_no ASC
            // ";

            $sql = "
                SELECT 
                    a.tender_enquiry_id, 
                    get_tender_info(a.tender_enquiry_id) as tender_details
                FROM tender_enquiry_info AS a 
                WHERE a.status = 'Active' 
                and a.customer_id= '" . $srch_customer_id . "'
                ORDER BY a.tender_enquiry_id, a.enquiry_no ASC
            ";


            $query = $this->db->query($sql);
            $data['tender_enquiry_opt'] = [];
            foreach ($query->result_array() as $row) {
                // $data['tender_enquiry_opt'][$row['tender_enquiry_id']] = $row['company_code'] . ' -> ' . $row['company_sno'] . ' -> ' . $row['customer_code'] . ' -> ' . $row['customer_sno'] . ' -> ' . $row['enquiry_no'];
                $data['tender_enquiry_opt'][$row['tender_enquiry_id']] = $row['tender_details'];
            }
        }

        $data['po_status_opt'] = ['' => 'Select PO Status', 'In Progress' => 'In Progress', 'Delivered' => 'Delivered'];
        // === FETCH RECORDS ===
        $sql = "
             SELECT 
                a.po_no,
                a.po_date,  
                a.vendor_po_id,
                a.status,
                a.customer_id,
                a.vendor_id,
                a.po_status, 
                a.po_date,
                c.customer_name,
                v.vendor_name,
                a.company_id,
                a.customer_id, 
                ci.company_name,
                get_tender_info(a.tender_enquiry_id) as tender_details
            FROM vendor_po_info as  a
            LEFT JOIN customer_info c ON a.customer_id = c.customer_id AND c.status = 'Active'
            LEFT JOIN vendor_info v ON a.vendor_id = v.vendor_id AND v.status = 'Active'
            LEFT JOIN tender_enquiry_info t ON a.tender_enquiry_id = t.tender_enquiry_id AND t.status != 'Delete'
            left join company_info as ci on t.company_id = ci.company_id and ci.status = 'Active'
            WHERE a.status != 'Delete' 
            AND $where 
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
                'vendor_quote_id' => $this->input->post('srch_vendor_quote_id'),
                'vendor_contact_person_id' => $this->input->post('srch_vendor_contact_person_id'),
                'currency_id' => $this->input->post('currency_id'),
                'po_no' => $this->input->post('po_no'),
                'po_date' => $this->input->post('po_date'),
                'delivery_date' => $this->input->post('delivery_date'),
                'transport_charges' => $this->input->post('transport_charges'),
                'other_charges' => $this->input->post('other_charges'),
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
            // $this->db->where('vendor_po_id', $vendor_po_id);
            // $this->db->delete('vendor_po_item_info');

            // Insert only selected items 
            $selected_idxs = $this->input->post('selected_items') ?? [];

            /*if (!empty($selected_items)) {
                 foreach ($selected_items as $idx) {
                     $item = [
                         'vendor_quote_id' => $vendor_quote_id,
                         'vendor_rate_enquiry_item_id' => $this->input->post("vendor_rate_enquiry_item_id")[$idx] ?? 0,
                         'category_id' => $this->input->post("category_id")[$idx] ?? 0,
                         'item_id' => $this->input->post("item_id")[$idx] ?? 0,
                         'item_desc' => $this->input->post("item_desc")[$idx] ?? '',
                         'uom' => $this->input->post("uom")[$idx] ?? '',
                         'qty' => $this->input->post("qty")[$idx] ?? 0,
                         'rate' => $this->input->post("rate")[$idx] ?? 0,
                         'gst' => $this->input->post("gst")[$idx] ?? 0,
                         'amount' => $this->input->post("amount")[$idx] ?? 0,
                         'status' => 'Active',
                         'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                         'updated_date' => date('Y-m-d H:i:s'),
                     ];
                     $this->db->insert('vendor_quote_item_info', $item);
                 }
             }*/

            if (!empty($selected_idxs)) {
                // All arrays are posted with the SAME order as the rows
                $vendor_po_item_ids = $this->input->post('vendor_po_item_id') ?? [];
                $vendor_rate_enquiry_item_ids = $this->input->post('vendor_rate_enquiry_item_id') ?? [];
                $vendor_quote_item_ids = $this->input->post('vendor_quote_item_id') ?? [];

                $item_codes = $this->input->post('item_code') ?? [];
                $item_descs = $this->input->post('item_desc') ?? [];
                $uoms = $this->input->post('uom') ?? [];
                $qtys = $this->input->post('qty') ?? [];
                $gsts = $this->input->post('gst') ?? [];
                $rates = $this->input->post('rate') ?? [];
                $amounts = $this->input->post('amount') ?? [];

                foreach ($selected_idxs as $fld => $idx) {
                    //if($tender_quotation_item_ids[$idx]){  
                    $item_data = [
                        'vendor_po_id' => $vendor_po_id,
                        'vendor_rate_enquiry_item_id' => $vendor_rate_enquiry_item_ids[$idx] ?? 0,
                        'vendor_quote_item_id' => $vendor_quote_item_ids[$idx] ?? 0,
                        // 'category_id' => $category_ids[$idx] ?? 0,
                        'item_code' => $item_codes[$idx] ?? 0,
                        'item_desc' => $item_descs[$idx] ?? '',
                        'uom' => $uoms[$idx] ?? '',
                        'qty' => $qtys[$idx] ?? 0,
                        'gst' => $gsts[$idx] ?? 0,
                        'rate' => $rates[$idx] ?? 0,
                        'amount' => $amounts[$idx] ?? 0,
                        'status' => 'Active',
                        'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                        'created_date' => date('Y-m-d H:i:s')
                    ];

                    if (!empty($vendor_po_item_ids[$idx]) && $vendor_po_item_ids[$idx] > 0) {
                        // UPDATE existing item
                        $this->db->where('vendor_po_item_id', $vendor_po_item_ids[$idx])
                            ->update('vendor_po_item_info', $item_data);

                    } else {
                        // INSERT new item
                        $item_data['created_by'] = $this->session->userdata(SESS_HD . 'user_id');
                        $item_data['created_date'] = date('Y-m-d H:i:s');

                        if ($this->db->insert('vendor_po_item_info', $item_data)) {
                            $current_item_id = $this->db->insert_id();
                        } else {
                            $current_item_id = 0;
                        }
                        $miss_item_ids[] = $current_item_id;
                    }
                    $miss_item_ids[] = $vendor_po_item_ids[$idx];

                }
                // DELETE items which are not in the selected list
                if (!empty($miss_item_ids)) {
                    $this->db->where('vendor_po_id', $vendor_po_id);
                    $this->db->where_not_in('vendor_po_item_id', $miss_item_ids);
                    $this->db->update('vendor_po_item_info', ['status' => 'Deleted', 'updated_by' => $this->session->userdata(SESS_HD . 'user_id'), 'updated_date' => date('Y-m-d H:i:s')]);
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Error updating Vendor Quotation.');
            } else {
                $this->session->set_flashdata('success', 'Vendor Quotation updated successfully.');
            }


            redirect('vendor-po-edit/' . $vendor_po_id);
        }
        // Load dependent dropdowns (same AJAX will refill others)
        $data['customer_opt'] = ['' => 'Select Customer'];
        $data['tender_enquiry_opt'] = ['' => 'Select Enquiry'];
        $data['vendor_rate_enquiry_opt'] = ['' => 'Select Enquiry No'];
        $data['vendor_opt'] = ['' => 'Select Vendor'];
        $data['vendor_contact_opt'] = ['' => 'Select'];
        $data['po_status_opt'] = ['' => 'Select PO Status', 'In Progress' => 'In Progress', 'Delivered' => 'Delivered'];

        // ==================== LOAD DATA FOR EDIT ====================
        if (!$vendor_po_id) {
            show_404();
        }

        $sql = "
            SELECT 
                vpo.*
            FROM vendor_po_info AS vpo
            WHERE vpo.vendor_po_id = ?
            AND vpo.status = 'Active'
            ORDER BY vpo.vendor_po_id ASC
        ";
        $query = $this->db->query($sql, [$vendor_po_id]);
        $data['header'] = $query->row_array();



        $sql = "
            SELECT
                d.vendor_quote_item_id,
                d.vendor_quote_id,
                d.vendor_rate_enquiry_item_id,

                CASE 
                    WHEN b.vendor_po_item_id IS NOT NULL THEN b.vendor_po_item_id
                    ELSE NULL
                END AS vendor_po_item_id,

                CASE 
                    WHEN b.vendor_po_item_id IS NOT NULL THEN COALESCE(b.item_code, d.item_code)
                    ELSE d.item_code
                END AS item_code,

                CASE 
                    WHEN b.vendor_po_item_id IS NOT NULL THEN COALESCE(b.item_desc, d.item_desc)
                    ELSE d.item_desc
                END AS item_desc,

                CASE 
                    WHEN b.vendor_po_item_id IS NOT NULL THEN COALESCE(b.uom, d.uom)
                    ELSE d.uom
                END AS uom,

                CASE 
                    WHEN b.vendor_po_item_id IS NOT NULL THEN COALESCE(b.qty, d.qty)
                    ELSE d.qty
                END AS qty,
                
                CASE 
                    WHEN b.vendor_po_item_id IS NOT NULL THEN COALESCE(b.gst, d.gst)
                    ELSE d.rate
                END AS vat,

                CASE 
                    WHEN b.vendor_po_item_id IS NOT NULL THEN COALESCE(b.rate, d.rate)
                    ELSE d.rate
                END AS rate,
                
                CASE 
                    WHEN b.vendor_po_item_id IS NOT NULL THEN COALESCE(b.amount, d.amount)
                    ELSE d.amount
                END AS amount,

                CASE 
                    WHEN b.vendor_po_item_id IS NOT NULL 
                        THEN 'checked'
                    ELSE 'unchecked'
                END AS checkbox_status

            FROM vendor_quotation_info AS q
            LEFT JOIN vendor_quote_item_info AS d 
                ON q.vendor_quote_id = d.vendor_quote_id
                AND d.status = 'Active'
            LEFT JOIN vendor_po_item_info AS b 
                ON d.vendor_quote_item_id = b.vendor_quote_item_id
                AND b.vendor_po_id = ?
                AND b.status = 'Active'
            WHERE q.status = 'Active'
                AND q.vendor_rate_enquiry_id = ?
            ORDER BY d.vendor_quote_item_id;
        ";
        $query = $this->db->query($sql, [$vendor_po_id, $data['header']['vendor_rate_enquiry_id']]);
        $data['items'] = $query->result_array();


        $sql = "
            SELECT
                company_id,
                company_name
            FROM
                company_info
            WHERE
            STATUS
                = 'Active'
            ORDER BY
                company_name ASC
        ";
        $query = $this->db->query($sql);
        $data['company_opt'] = ['' => 'Select Company'];
        foreach ($query->result_array() as $row) {
            $data['company_opt'][$row['company_id']] = $row['company_name'];
        }

        // Customer Dropdown
        $sql = "
            SELECT 
            customer_id, 
            customer_name 
            FROM customer_info 
            WHERE status = 'Active' 
            ORDER BY customer_name ASC
        ";
        $query = $this->db->query($sql);
        $data['customer_opt'] = ['' => 'Select Customer'];
        foreach ($query->result_array() as $row) {
            $data['customer_opt'][$row['customer_id']] = $row['customer_name'];
        }

        // Tender Enquiry
        $sql = "
                SELECT 
                a.tender_enquiry_id, 
            get_tender_info(a.tender_enquiry_id) as enquiry_no 
            FROM tender_enquiry_info as a 
            WHERE a.status = 'Active' 
            GROUP BY a.tender_enquiry_id 
            ORDER BY a.tender_enquiry_id
        ";
        $query = $this->db->query($sql);
        $data['tender_enquiry_opt'] = ['' => 'Select Enquiry'];
        foreach ($query->result_array() as $row) {
            $data['tender_enquiry_opt'][$row['tender_enquiry_id']] = $row['enquiry_no'];
        }


        $sql = " 
            SELECT vendor_quote_id, quote_no 
            FROM vendor_quotation_info
             WHERE status = 'Active' 
             and vendor_id = " . $data['header']['vendor_id'] . "
             and tender_enquiry_id = " . $data['header']['tender_enquiry_id'] . "
             ORDER BY quote_no ASC
        ";
        $query = $this->db->query($sql);
        $data['vendor_quote_opt'] = ['' => 'Select Enquiry No'];
        foreach ($query->result_array() as $row) {
            $data['vendor_quote_opt'][$row['vendor_quote_id']] = $row['quote_no'];
        }


        $sql = "
            SELECT vendor_id, vendor_name 
            FROM vendor_info
            WHERE status = 'Active' 
            ORDER BY vendor_name ASC
        ";
        $query = $this->db->query($sql);
        $data['vendor_opt'] = ['' => 'Select Vendor'];
        foreach ($query->result_array() as $row) {
            $data['vendor_opt'][$row['vendor_id']] = $row['vendor_name'];
        }

        $sql = "SELECT vendor_contact_id, contact_person_name FROM vendor_contact_info WHERE status = 'Active' ORDER BY contact_person_name ASC";
        $query = $this->db->query($sql);
        $data['vendor_contact_opt'] = ['' => 'Select Contact'];
        foreach ($query->result_array() as $row) {
            $data['vendor_contact_opt'][$row['vendor_contact_id']] = $row['contact_person_name'];
        }

        $sql = "SELECT gst_percentage FROM gst_info WHERE status = 'Active' ORDER BY gst_percentage ASC";
        $query = $this->db->query($sql);
        $gst_opt = [];
        foreach ($query->result_array() as $row) {
            $gst_opt[$row['gst_percentage']] = $row['gst_percentage'];
        }
        $data['gst_opt'] = $gst_opt;

        // Status Options
        $data['quotation_status_opt'] = ['' => 'Select Tender Status', 'Pending' => 'Pending', 'Confirmed' => 'Confirmed', 'Rejected' => 'Rejected'];
        $data['currency_opt'] = array('' => 'Select');
        $sql = "
            SELECT currency_id, currency_code
            FROM currencies_info
            WHERE status = 'Active'
            ORDER BY currency_name ASC
        ";
        $query = $this->db->query($sql);

        foreach ($query->result_array() as $row) {
            $data['currency_opt'][$row['currency_id']] = $row['currency_code'];

        }

        $this->load->view('page/vendor/vendor-po-edit', $data);
    }

    public function vendor_po_view($vendor_po_id = 0)
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        if (!$vendor_po_id) {
            show_404();
        }

        // === MAIN RECORD ===
        $sql = "
            SELECT
                b.customer_name,
                c.company_name,
                a.po_date,
                a.po_no,
                a.other_charges,
                a.transport_charges,
                a.remarks,
                a.terms,
                e.vendor_name,
                e.address as vendor_address,
                e.mobile,
                c.ltr_header_img,
                e.address,
                e.vendor_id,
                e.country as vendor_country,
                curr.currency_code,
                curr.decimal_point,
                f.quote_no,
                f.quote_date,
                a.vendor_po_id,
                get_tender_info(a.tender_enquiry_id) as enquiry_no,
                d.enquiry_date,
                vc.contact_person_name
            FROM vendor_po_info as  a 
            LEFT JOIN customer_info as b on a.customer_id = b.customer_id  and b.status='Active'
            LEFT JOIN company_info as c  on a.company_id = c.company_id and c.status='Active'
            LEFT JOIN tender_enquiry_info as d  on a.tender_enquiry_id = d.tender_enquiry_id and d.status='Active'
            left join vendor_info as e on a.vendor_id = e.vendor_id and e.status='Active' 
            left join vendor_quotation_info as f on  a.vendor_quote_id = f.vendor_quote_id and f.`status`='Active'
            left join currencies_info  as curr on a.currency_id = curr.currency_id and curr.status='Active'
            left join vendor_contact_info as vc on a.vendor_contact_person_id = vc.vendor_contact_id and vc.status='Active'
            WHERE a.status='Active'
            and a.vendor_po_id = ?
        ";
        $query = $this->db->query($sql, [$vendor_po_id]);
        $data['record'] = $query->row_array();

        if (!$data['record']) {
            show_404();
        }

        // === ITEMS WITH RATE CALCULATION ===
        $sql = "
           select
            a.vendor_po_id,
            b.item_code,
            b.item_desc,
            b.uom,
            b.qty,
            b.rate,
            b.gst,
            b.amount,
            (b.rate * b.qty) AS Net_amount
            from vendor_po_info as a 
            left join vendor_po_item_info as b  on a.vendor_po_id = b.vendor_po_id and b.`status`='Active'
            left join currencies_info as c on a.currency_id = c.currency_id and c.`status`='Active'
            where a.`status`='Active'
            and a.vendor_po_id = ?
            order by a.vendor_po_id asc
        ";
        $query = $this->db->query($sql, [$vendor_po_id]);
        $data['item_list'] = $query->result_array();


        $this->load->view('page/vendor/vendor-po-print', $data);
    }

    public function vendor_pur_inward_add()
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
        $data['js'] = 'vendor/vendor-pur-inward-add.inc';
        $data['title'] = 'Add Vendor Purchase Inward';

        if ($this->input->post('mode') == 'Add') {
            // echo "<pre>";
            // print_r($_POST);
            // echo "</pre>";

            $this->db->trans_start();


            // 1. Handle file uploads
            $upload_path = 'vendor-pur-inward-documents/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size'] = 2048;

            $this->load->library('upload', $config);

            $dc_upload = '';


            if (!empty($_FILES['dc_upload']['name'])) {
                if ($this->upload->do_upload('dc_upload')) {
                    $dc_upload = $this->upload->data('file_name');
                }
            }


            $header = [
                'company_id' => $this->input->post('srch_company_id'),
                'customer_id' => $this->input->post('srch_customer_id'),
                'tender_enquiry_id' => $this->input->post('srch_tender_enquiry_id'),
                'vendor_id' => $this->input->post('srch_vendor_id'),
                'vendor_po_id' => $this->input->post('srch_vendor_po_id'),
                'vendor_contact_person_id' => $this->input->post('srch_vendor_contact_person_id'),
                'inward_date' => $this->input->post('inward_date'),
                'inward_no' => $this->input->post('inward_no'),
                'transport_charges' => $this->input->post('transport_charges'),
                'other_charges' => $this->input->post('other_charges'),
                'remarks' => $this->input->post('remarks'),
                'dc_upload' => 'vendor-pur-inward-documents/' . $dc_upload,
                'status' => $this->input->post('status'),
                'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'created_date' => date('Y-m-d H:i:s'),
            ];

            $this->db->insert('vendor_pur_inward_info', $header);
            $vendor_pur_inward_id = $this->db->insert_id();
            $selected_items = $this->input->post('selected_items') ?? [];

            if (!empty($selected_items)) {

                $vendor_po_item_id = $this->input->post('vendor_po_item_id') ?? [];
                $category_id = $this->input->post('category_id') ?? [];
                $item_code = $this->input->post('item_code') ?? [];
                $item_desc = $this->input->post('item_desc') ?? [];
                $uom = $this->input->post('uom') ?? [];
                $qty = $this->input->post('qty') ?? [];
                $rate = $this->input->post('rate') ?? [];
                $gst = $this->input->post('gst') ?? [];
                $amount = $this->input->post('amount') ?? [];

                foreach ($selected_items as $idx => $value) {

                    $item = [
                        'vendor_pur_inward_id' => $vendor_pur_inward_id,
                        'vendor_po_item_id' => $vendor_po_item_id[$idx] ?? 0,
                        'category_id' => $category_id[$idx] ?? 0,
                        'item_code' => $item_code[$idx] ?? 0,
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

                    $this->db->insert('vendor_pur_inward_item_info', $item);
                }
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Error saving Vendor PO. Please try again.');
            } else {
                $this->session->set_flashdata('success', 'Vendor PO saved successfully.');

            }

            redirect('vendor-pur-inward-list');
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

        $sql = "
            SELECT gst_id, gst_percentage 
            FROM gst_info 
            WHERE status = 'Active' 
            ORDER BY gst_percentage ASC";
        $query = $this->db->query($sql);
        $data['gst_opt'] = [];
        foreach ($query->result_array() as $row) {
            $data['gst_opt'][$row['gst_percentage']] = $row['gst_percentage'];
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
        $this->load->view('page/vendor/vendor-pur-inward-add', $data);
    }

    public function vendor_pur_inward_list()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        $data = array();
        $data['js'] = 'vendor/vendor-pur-inward-list.inc';
        $data['s_url'] = 'vendor-pur-inward-list';
        $data['title'] = 'Vendor Purchase Inward List';

        $where = "1=1";

        if (isset($_POST['srch_from_date'])) {
            $data['srch_from_date'] = $srch_from_date = $this->input->post('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->input->post('srch_to_date');
            $this->session->set_userdata('srch_from_date', $this->input->post('srch_from_date'));
            $this->session->set_userdata('srch_to_date', $this->input->post('srch_to_date'));
        } elseif ($this->session->userdata('srch_from_date')) {
            $data['srch_from_date'] = $srch_from_date = $this->session->userdata('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->session->userdata('srch_to_date');
        } else {
            $data['srch_from_date'] = $srch_from_date = '';
            $data['srch_to_date'] = $srch_to_date = '';
        }

        if (!empty($srch_from_date) && !empty($srch_to_date)) {
            $where .= " AND  ( a.inward_date BETWEEN '" . $this->db->escape_str($srch_from_date) . "' AND '" . $this->db->escape_str($srch_to_date) . "') ";
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


        // Company Filter
        if ($this->input->post('srch_customer_rfq_no') !== null) {
            $data['srch_customer_rfq_no'] = $srch_customer_rfq_no = $this->input->post('srch_customer_rfq_no');
            $this->session->set_userdata('srch_customer_rfq_no', $srch_customer_rfq_no);
        } elseif ($this->session->userdata('srch_customer_rfq_no')) {
            $data['srch_customer_rfq_no'] = $srch_customer_rfq_no = $this->session->userdata('srch_customer_rfq_no');
        } else {
            $data['srch_customer_rfq_no'] = $srch_customer_rfq_no = '';
        }
        if (!empty($srch_customer_rfq_no)) {
            $where = " (t.enquiry_no = '" . $this->db->escape_str($srch_customer_rfq_no) . "')";
            $data['srch_vendor_id'] = $srch_vendor_id = '';
        }



        if ($this->input->post('srch_enquiry_no') !== null) {
            $data['srch_enquiry_no'] = $srch_enquiry_no = $this->input->post('srch_enquiry_no');
            $this->session->set_userdata('srch_enquiry_no', $srch_enquiry_no);
        } elseif ($this->session->userdata('srch_enquiry_no')) {
            $data['srch_enquiry_no'] = $srch_enquiry_no = $this->session->userdata('srch_enquiry_no');
        } else {
            $data['srch_enquiry_no'] = $srch_enquiry_no = '';
        }
        if (!empty($srch_enquiry_no)) {
            $where = " ( concat(ifnull(ci.company_code,'') , '/', ifnull(t.company_sno,'') ,  '/' , ifnull(c.customer_code,'') ,  '/' , ifnull(t.customer_sno,''),  '/' , DATE_FORMAT(t.enquiry_date,'%Y') ) like '%" . $this->db->escape_str($srch_enquiry_no) . "%' ) ";

            $data['srch_customer_id'] = $srch_customer_id = '';
            $data['srch_vendor_id'] = $srch_vendor_id = '';
        }

        // Company Filter
        if ($this->input->post('srch_inward_no') !== null) {
            $data['srch_inward_no'] = $srch_inward_no = $this->input->post('srch_inward_no');
            $this->session->set_userdata('srch_inward_no', $srch_inward_no);
        } elseif ($this->session->userdata('srch_inward_no')) {
            $data['srch_inward_no'] = $srch_inward_no = $this->session->userdata('srch_inward_no');
        } else {
            $data['srch_inward_no'] = $srch_inward_no = '';
        }
        if (!empty($srch_inward_no)) {
            $where = " (a.inward_no = '" . $this->db->escape_str($srch_inward_no) . "')";
            $data['srch_vendor_id'] = $srch_vendor_id = '';
            $data['srch_customer_id'] = $srch_customer_id = '';
        }




        $this->db->from('vendor_pur_inward_info a');
        $this->db->join('company_info ci', 'a.company_id = ci.company_id AND ci.status = "Active"', 'left');
        $this->db->join('customer_info c', 'a.customer_id = c.customer_id AND c.status = "Active"', 'left');
        $this->db->join('tender_enquiry_info t', 'a.tender_enquiry_id = t.tender_enquiry_id AND t.status = "Active"', 'left');
        $this->db->where('a.status !=', 'Delete');
        $this->db->where($where);

        $data['total_records'] = $this->db->count_all_results();



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

        $data['vendor_opt'] = [];


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
        if (!empty($srch_customer_id)) {

            $sql = "
                SELECT 
                    a.tender_enquiry_id, 
                    get_tender_info(a.tender_enquiry_id) as tender_details
                FROM tender_enquiry_info AS a 
                WHERE a.status = 'Active' 
                and a.customer_id= '" . $srch_customer_id . "'
                ORDER BY a.tender_enquiry_id, a.enquiry_no ASC
            ";


            $query = $this->db->query($sql);
            $data['tender_enquiry_opt'] = [];
            foreach ($query->result_array() as $row) {
                $data['tender_enquiry_opt'][$row['tender_enquiry_id']] = $row['tender_details'];
            }
        }


        $sql = "
                SELECT 
                    a.vendor_id,
                   a.vendor_name
                FROM  vendor_info AS a 
                where a.`status`='Active'  
        ";


        $query = $this->db->query($sql);

        foreach ($query->result_array() as $row) {
            $data['vendor_opt'][$row['vendor_id']] = $row['vendor_name'];
        }

        $data['po_status_opt'] = ['' => 'Select PO Status', 'In Progress' => 'In Progress', 'Delivered' => 'Delivered'];
        // === FETCH RECORDS ===
        $sql = "
            SELECT 
                a.inward_no, 
                a.vendor_pur_inward_id ,
                a.status,
                a.customer_id,
                a.vendor_id, 
                a.inward_date,
                c.customer_name,
                v.vendor_name,
                a.company_id,
                t.enquiry_no as customer_rfq_no,
                a.customer_id, 
                ci.company_name,
                get_tender_info(a.tender_enquiry_id) as tender_details
            FROM vendor_pur_inward_info as  a
            LEFT JOIN customer_info c ON a.customer_id = c.customer_id AND c.status = 'Active'
            LEFT JOIN vendor_info v ON a.vendor_id = v.vendor_id AND v.status = 'Active'
            LEFT JOIN tender_enquiry_info t ON a.tender_enquiry_id = t.tender_enquiry_id AND t.status != 'Delete'
            left join company_info as ci on t.company_id = ci.company_id and ci.status = 'Active'
            WHERE a.status != 'Delete' AND $where 
            ORDER BY a.vendor_pur_inward_id  DESC
            LIMIT " . $this->uri->segment(2, 0) . ", " . $config['per_page'];

        $query = $this->db->query($sql);
        $data['record_list'] = $query->result_array();



        $this->load->view('page/vendor/vendor-pur-inward-list', $data);
    }

    public function vendor_pur_inward_edit($vendor_pur_inward_id = 0)
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

        $data['js'] = 'vendor/vendor-pur-inward-edit.inc';
        $data['title'] = ' Edit Vendor Purchase Inward';

        // ==================== UPDATE ====================
        if ($this->input->post('mode') == 'Edit') {

            $this->db->trans_start();

            // === 1. File Upload Settings ===
            $upload_folder = 'vendor-pur-inward-documents/';
            $upload_path = FCPATH . $upload_folder;

            // Create directory if not exists
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            $config = [
                'upload_path' => $upload_path,
                'allowed_types' => 'jpg|jpeg|png|pdf|doc|docx',
                'max_size' => 4096,
                'encrypt_name' => TRUE,
            ];

            $this->load->library('upload', $config);

            // === 2. Header Data ===
            $header = [
                'company_id' => $this->input->post('srch_company_id'),
                'customer_id' => $this->input->post('srch_customer_id'),
                'tender_enquiry_id' => $this->input->post('srch_tender_enquiry_id'),
                'vendor_id' => $this->input->post('srch_vendor_id'),
                'vendor_po_id' => $this->input->post('srch_vendor_po_id'),
                'vendor_contact_person_id' => $this->input->post('srch_vendor_contact_person_id'),
                'inward_no' => $this->input->post('inward_no'),
                'inward_date' => $this->input->post('inward_date'),
                'transport_charges' => $this->input->post('transport_charges'),
                'other_charges' => $this->input->post('other_charges'),
                'remarks' => $this->input->post('remarks'),
                'status' => $this->input->post('status'),
                'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'updated_date' => date('Y-m-d H:i:s'),
            ];

            // === 3. Upload File ===
            if (!empty($_FILES['dc_upload']['name'])) {

                if ($this->upload->do_upload('dc_upload')) {

                    $fileData = $this->upload->data();

                    // Store relative path in DB
                    $header['dc_upload'] = $upload_folder . $fileData['file_name'];

                } else {
                    // Upload error handling
                    $this->session->set_flashdata('error', 'Upload Failed: ' . $this->upload->display_errors());
                    redirect('vendor-pur-inward-list/');
                }
            }

            // === 4. UPDATE Header ===
            $vendor_pur_inward_id = $this->input->post('vendor_pur_inward_id');

            $this->db->where('vendor_pur_inward_id', $vendor_pur_inward_id);
            $this->db->update('vendor_pur_inward_info', $header);

            // // === 5. DELETE Old Items ===
            // $this->db->where('vendor_pur_inward_id', $vendor_pur_inward_id);
            // $this->db->delete('vendor_pur_inward_item_info');

            // === 6. INSERT New Items ===
            $selected_items = $this->input->post('selected_items') ?? [];
            $vendor_pur_inward_item_ids = $this->input->post('vendor_pur_inward_item_id') ?? [];

            if (!empty($selected_items)) {

                $vendor_po_item_id = $this->input->post('vendor_po_item_id') ?? [];
                $category_id = $this->input->post('category_id') ?? [];
                $item_id = $this->input->post('item_id') ?? [];
                $item_desc = $this->input->post('item_desc') ?? [];
                $uom = $this->input->post('uom') ?? [];
                $qty = $this->input->post('qty') ?? [];
                $rate = $this->input->post('rate') ?? [];
                $gst = $this->input->post('gst') ?? [];
                $amount = $this->input->post('amount') ?? [];

                foreach ($selected_items as $idx => $value) {

                    $item = [
                        'vendor_pur_inward_id' => $vendor_pur_inward_id,
                        'vendor_po_item_id' => $vendor_po_item_id[$idx] ?? 0,
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

                    if (!empty($vendor_pur_inward_item_ids[$idx])) {
                        $item['vendor_pur_inward_item_id'] = $vendor_pur_inward_item_ids[$idx];
                        $this->db->where('vendor_pur_inward_item_id', $vendor_pur_inward_item_ids[$idx]);
                        $this->db->update('vendor_pur_inward_item_info', $item);
                    } else {
                        $this->db->insert('vendor_pur_inward_item_info', $item);
                    }
                }
            }

            // === 7. FINISH TRANSACTION ===
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Error updating Vendor Inward.');
            } else {
                $this->session->set_flashdata('success', 'Vendor Inward updated successfully.');
            }

            redirect('vendor-pur-inward-list/');
        }

        if (!$vendor_pur_inward_id)
            show_404();

        $sql = "
            SELECT * FROM vendor_pur_inward_info
            WHERE vendor_pur_inward_id = ? AND status = 'Active'
        ";
        $data['header'] = $this->db->query($sql, [$vendor_pur_inward_id])->row_array();

        $sql = "
            SELECT 
                vpi.*,
                ci.category_name,
                ii.item_code
            FROM vendor_pur_inward_item_info vpi
            LEFT JOIN category_info ci ON vpi.category_id = ci.category_id
            LEFT JOIN item_info ii ON vpi.item_id = ii.item_id
            WHERE vpi.vendor_pur_inward_id = ? AND vpi.status = 'Active'
            ORDER BY vpi.vendor_pur_inward_item_id ASC
        ";
        $data['items'] = $this->db->query($sql, [$vendor_pur_inward_id])->result_array();

        $sql = "
            SELECT
                company_id,
                company_name
            FROM
                company_info
            WHERE
            STATUS
                = 'Active'
            ORDER BY
                company_name ASC
        ";
        $query = $this->db->query($sql);
        $data['company_opt'] = ['' => 'Select Company'];
        foreach ($query->result_array() as $row) {
            $data['company_opt'][$row['company_id']] = $row['company_name'];
        }

        // Customer Dropdown
        $sql = "
            SELECT 
            customer_id, 
            customer_name 
            FROM customer_info 
            WHERE status = 'Active' 
            ORDER BY customer_name ASC
        ";
        $query = $this->db->query($sql);
        $data['customer_opt'] = ['' => 'Select Customer'];
        foreach ($query->result_array() as $row) {
            $data['customer_opt'][$row['customer_id']] = $row['customer_name'];
        }

        // Tender Enquiry
        $sql = "
                SELECT 
                a.tender_enquiry_id, 
            get_tender_info(a.tender_enquiry_id) as enquiry_no 
            FROM tender_enquiry_info as a 
            WHERE a.status = 'Active' 
            GROUP BY a.tender_enquiry_id 
            ORDER BY a.tender_enquiry_id
        ";
        $query = $this->db->query($sql);
        $data['tender_enquiry_opt'] = ['' => 'Select Enquiry'];
        foreach ($query->result_array() as $row) {
            $data['tender_enquiry_opt'][$row['tender_enquiry_id']] = $row['enquiry_no'];
        }

        $sql = "SELECT vendor_po_id, po_no FROM vendor_po_info WHERE status = 'Active' ORDER BY po_no ASC";
        $query = $this->db->query($sql);
        $data['vendor_po_opt'] = ['' => 'Select Enquiry No'];
        foreach ($query->result_array() as $row) {
            $data['vendor_po_opt'][$row['vendor_po_id']] = $row['po_no'];
        }

        $sql = "SELECT vendor_id, vendor_name FROM vendor_info WHERE status = 'Active' ORDER BY vendor_name ASC";
        $query = $this->db->query($sql);
        $data['vendor_opt'] = ['' => 'Select Vendor'];
        foreach ($query->result_array() as $row) {
            $data['vendor_opt'][$row['vendor_id']] = $row['vendor_name'];
        }

        $sql = "SELECT vendor_contact_id, contact_person_name FROM vendor_contact_info WHERE status = 'Active' ORDER BY contact_person_name ASC";
        $query = $this->db->query($sql);
        $data['vendor_contact_opt'] = ['' => 'Select Contact'];
        foreach ($query->result_array() as $row) {
            $data['vendor_contact_opt'][$row['vendor_contact_id']] = $row['contact_person_name'];
        }

        $sql = "SELECT gst_percentage FROM gst_info WHERE status = 'Active' ORDER BY gst_percentage ASC";
        $query = $this->db->query($sql);
        $gst_opt = [];
        foreach ($query->result_array() as $row) {
            $gst_opt[$row['gst_percentage']] = $row['gst_percentage'];
        }
        $data['gst_opt'] = $gst_opt;

        $this->load->view('page/vendor/vendor-pur-inward-edit', $data);
    }

    public function vendor_quotation_add()
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

        $data['js'] = 'vendor/vendor-quotation-add.inc';
        $data['title'] = 'Vendor Quotation Add';

        if ($this->input->post('mode') == 'Add') {
            $this->db->trans_start();

            // echo "<pre>";
            // print_r($_POST);    
            // echo "</pre>";

            // 1. Handle file uploads
            $upload_path = 'vendor-quotations-documents/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size'] = 2048;

            $this->load->library('upload', $config);

            $quote_doc_upload = '';


            if (!empty($_FILES['quote_doc_upload']['name'])) {
                if ($this->upload->do_upload('quote_doc_upload')) {
                    $quote_doc_upload = $this->upload->data('file_name');
                }
            }

            $header = [
                'company_id' => $this->input->post('srch_company_id'),
                'customer_id' => $this->input->post('srch_customer_id'),
                'tender_enquiry_id' => $this->input->post('srch_tender_enquiry_id'),
                'vendor_id' => $this->input->post('srch_vendor_id'),
                'vendor_rate_enquiry_id' => $this->input->post('srch_vendor_rate_enquiry_id'),
                'vendor_contact_person_id' => $this->input->post('srch_vendor_contact_person_id'),
                'currency_id' => $this->input->post('currency_id'),
                'quote_date' => $this->input->post('quote_date'),
                'quote_no' => $this->input->post('quote_no'),
                'remarks' => $this->input->post('remarks'),
                'terms' => $this->input->post('terms'),
                'quote_doc_upload' => 'vendor-quotations-documents/' . $quote_doc_upload,
                'quote_status' => $this->input->post('quote_status'),
                'status' => $this->input->post('status'),
                'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'created_date' => date('Y-m-d H:i:s'),
            ];

            $this->db->insert('vendor_quotation_info', $header);
            $vendor_quote_id = $this->db->insert_id();
            $selected_items = $this->input->post('selected_items') ?? [];

            //print_r($selected_items);

            if (!empty($selected_items)) {

                $vendor_rate_enquiry_item_id = $this->input->post('vendor_rate_enquiry_item_id') ?? [];
                //$category_id = $this->input->post('category_id') ?? [];
                //$item_id = $this->input->post('item_id') ?? [];
                $item_codes = $this->input->post('item_code') ?? [];
                $item_desc = $this->input->post('item_desc') ?? [];
                $uom = $this->input->post('uom') ?? [];
                $qty = $this->input->post('qty') ?? [];
                $rate = $this->input->post('rate') ?? [];
                $gst = $this->input->post('gst') ?? [];
                $amount = $this->input->post('amount') ?? [];

                foreach ($selected_items as $idx => $value) {

                    $item = [
                        'vendor_quote_id' => $vendor_quote_id,
                        'vendor_rate_enquiry_item_id' => $vendor_rate_enquiry_item_id[$idx] ?? 0,
                        //  'category_id' => $category_id[$idx] ?? 0,
                        //   'item_id' => $item_id[$idx] ?? 0,
                        'item_code' => $item_codes[$idx] ?? '',
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
                    // echo "<pre>";
                    // print_r($item);
                    // echo "</pre>";

                    $this->db->insert('vendor_quote_item_info', $item);
                }
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Error saving Vendor PO. Please try again.');
            } else {
                $this->session->set_flashdata('success', 'Vendor PO saved successfully.');

            }

            redirect('vendor-quotation-edit/' . $vendor_quote_id);
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
        $data['quotation_status_opt'] = ['' => 'Select Tender Status', 'Pending' => 'Pending', 'Confirmed' => 'Confirmed', 'Rejected' => 'Rejected'];

        $data['default_currency_id'] = '';

        $sql = "
            SELECT currency_id, currency_code
            FROM currencies_info
            WHERE status = 'Active'
            ORDER BY currency_name ASC
        ";
        $query = $this->db->query($sql);

        foreach ($query->result_array() as $row) {
            $data['currency_opt'][$row['currency_id']] = $row['currency_code'];

            if ($row['currency_code'] === 'BHD') {
                $data['default_currency_id'] = $row['currency_id'];
            }
        }

        $this->load->view('page/vendor/vendor-quotation-add', $data);
    }

    public function vendor_quotation_list()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        $data = array();
        $data['js'] = 'vendor/vendor-quotation-list.inc';
        $data['s_url'] = 'vendor-quotation-list';
        $data['title'] = 'Vendor Quotation List';

        $where = "1=1";

        if (isset($_POST['srch_from_date'])) {
            $data['srch_from_date'] = $srch_from_date = $this->input->post('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->input->post('srch_to_date');
            $this->session->set_userdata('srch_from_date', $this->input->post('srch_from_date'));
            $this->session->set_userdata('srch_to_date', $this->input->post('srch_to_date'));
        } elseif ($this->session->userdata('srch_from_date')) {
            $data['srch_from_date'] = $srch_from_date = $this->session->userdata('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->session->userdata('srch_to_date');
        } else {
            $data['srch_from_date'] = $srch_from_date = '';
            $data['srch_to_date'] = $srch_to_date = '';
        }

        if (!empty($srch_from_date) && !empty($srch_to_date)) {
            $where .= " AND  ( a.quote_date BETWEEN '" . $this->db->escape_str($srch_from_date) . "' AND '" . $this->db->escape_str($srch_to_date) . "') ";
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
            $data['srch_enquiry_no'] = $srch_enquiry_no = '';
        }

        // Vendor RFQ Filter
        if ($this->input->post('srch_customer_rfq_no') !== null) {
            $data['srch_customer_rfq_no'] = $srch_customer_rfq_no = $this->input->post('srch_customer_rfq_no');
            $this->session->set_userdata('srch_customer_rfq_no', $srch_customer_rfq_no);
        } elseif ($this->session->userdata('srch_customer_rfq_no')) {
            $data['srch_customer_rfq_no'] = $srch_customer_rfq_no = $this->session->userdata('srch_customer_rfq_no');
        } else {
            $data['srch_customer_rfq_no'] = $srch_customer_rfq_no = '';
        }
        if (!empty($srch_customer_rfq_no)) {
            $where = " ( d.enquiry_no = '" . $this->db->escape_str($srch_customer_rfq_no) . "')";
            $data['srch_customer_id'] = $srch_customer_id = '';
        }



        if ($this->input->post('srch_enquiry_no') !== null) {
            $data['srch_enquiry_no'] = $srch_enquiry_no = $this->input->post('srch_enquiry_no');
            $this->session->set_userdata('srch_enquiry_no', $srch_enquiry_no);
        } elseif ($this->session->userdata('srch_enquiry_no')) {
            $data['srch_enquiry_no'] = $srch_enquiry_no = $this->session->userdata('srch_enquiry_no');
        } else {
            $data['srch_enquiry_no'] = $srch_enquiry_no = '';
        }

        if (!empty($srch_enquiry_no)) {
            $where = " ( concat(ifnull(b.company_code,'') , '/', ifnull(d.company_sno,'') ,  '/' , ifnull(c.customer_code,'') ,  '/' , ifnull(d.customer_sno,''),  '/' , DATE_FORMAT(d.enquiry_date,'%Y') ) like '%" . $this->db->escape_str($srch_enquiry_no) . "%' ) ";

            $data['srch_customer_id'] = $srch_customer_id = '';

        }

        // Vendor RFQ
        if ($this->input->post('srch_vendor_quotation_no') !== null) {
            $data['srch_vendor_quotation_no'] = $srch_vendor_quotation_no = $this->input->post('srch_vendor_quotation_no');
            $this->session->set_userdata('srch_vendor_quotation_no', $srch_vendor_quotation_no);
        } elseif ($this->session->userdata('srch_vendor_quotation_no')) {
            $data['srch_vendor_quotation_no'] = $srch_vendor_quotation_no = $this->session->userdata('srch_vendor_quotation_no');
        } else {
            $data['srch_vendor_quotation_no'] = $srch_vendor_quotation_no = '';
        }
        if (!empty($srch_vendor_quotation_no)) {
            $where = " (a.quote_no = '" . $this->db->escape_str($srch_vendor_quotation_no) . "')";
        }


        // Enquiry Filter

        $this->db->from('vendor_quotation_info a');
        $this->db->join('company_info b', 'a.company_id = b.company_id AND b.status = "Active"', 'left');
        $this->db->join('customer_info c', 'a.customer_id = c.customer_id AND c.status = "Active"', 'left');
        $this->db->join('tender_enquiry_info d', 'a.tender_enquiry_id = d.tender_enquiry_id AND d.status = "Active"', 'left');
        $this->db->where('a.status !=', 'Delete');
        $this->db->where($where);
        $data['total_records'] = $this->db->count_all_results();

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
        $srch_tender_enquiry_id = $this->input->post('srch_enquiry_no');
        $data['tender_enquiry_opt'] = [];
        if (!empty($srch_customer_id || $srch_tender_enquiry_id)) {
            $sql = "
                SELECT 
                    a.tender_enquiry_id, 
                    get_tender_info(a.tender_enquiry_id) as tender_details
                FROM tender_enquiry_info AS a 
                WHERE a.status = 'Active' 
                and a.customer_id= '" . $srch_customer_id . "'
                ORDER BY a.tender_enquiry_id, a.enquiry_no ASC
            ";


            $query = $this->db->query($sql);
            $data['tender_enquiry_opt'] = [];
            foreach ($query->result_array() as $row) {
                // $data['tender_enquiry_opt'][$row['tender_enquiry_id']] = $row['company_code'] . ' -> ' . $row['company_sno'] . ' -> ' . $row['customer_code'] . ' -> ' . $row['customer_sno'] . ' -> ' . $row['enquiry_no'];
                $data['tender_enquiry_opt'][$row['tender_enquiry_id']] = $row['tender_details'];
            }
        }
        $data['quotation_status_opt'] = ['' => 'Select Tender Status', 'Completed' => 'Completed', 'Pending' => 'Pending', 'Rejected' => 'Rejected'];
        // === FETCH RECORDS ===
        $sql = "
            select 
            a.company_id,
            a.customer_id,
            a.vendor_quote_id,
            a.tender_enquiry_id,
            a.vendor_rate_enquiry_id,
            a.vendor_quote_id,
            b.company_name,
            c.customer_name,
            get_tender_info(a.tender_enquiry_id) tender_enquery_no,
            a.quote_status,
            a.quote_date,
            a.quote_no,
			e.vendor_name ,           
            d.enquiry_no as customer_rfq_no
            from vendor_quotation_info as a 
            left join company_info as b on a.company_id = b.company_id and b.`status`='Active'
            left join customer_info as c on a.customer_id = c.customer_id and c.`status`='Active'
            left join tender_enquiry_info as d on a.tender_enquiry_id = d.tender_enquiry_id and d.`status`='Active'
            left join vendor_info as e on a.vendor_id = e.vendor_id and e.`status`='Active'
            where a.`status`='Active'
            AND $where 
            order by a.vendor_quote_id desc 
            LIMIT " . $this->uri->segment(2, 0) . ", " . $config['per_page'];

        $query = $this->db->query($sql);
        $data['record_list'] = $query->result_array();



        $this->load->view('page/vendor/vendor-quotation-list', $data);
    }

    public function vendor_quotation_edit($vendor_quote_id = 0)
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

        $data['js'] = 'vendor/vendor-quotation-edit.inc';
        $data['title'] = 'Edit Vendor Quotation Details';

        if ($this->input->post('mode') == 'Edit') {
            $this->db->trans_start();

            // 1. Handle file uploads
            $upload_folder = 'vendor-quotations-documents/';   // relative folder for DB path
            $upload_path = FCPATH . $upload_folder;            // actual physical folder path

            // Create folder if not exists
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size'] = 2048;

            $this->load->library('upload', $config);
            $header = [
                'company_id' => $this->input->post('srch_company_id'),
                'customer_id' => $this->input->post('srch_customer_id'),
                'tender_enquiry_id' => $this->input->post('srch_tender_enquiry_id'),
                'vendor_id' => $this->input->post('srch_vendor_id'),
                'vendor_rate_enquiry_id' => $this->input->post('vendor_rate_enquiry_id'),
                'vendor_contact_person_id' => $this->input->post('srch_vendor_contact_person_id'),
                'currency_id' => $this->input->post('currency_id'),
                'quote_date' => $this->input->post('quote_date'),
                'quote_no' => $this->input->post('quote_no'),
                'remarks' => $this->input->post('remarks'),
                'terms' => $this->input->post('terms'),
                'quote_status' => $this->input->post('quote_status'),
                'status' => $this->input->post('status'),
                'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'updated_date' => date('Y-m-d H:i:s'),

            ];

            // Check file upload
            if (!empty($_FILES['quote_doc_upload']['name'])) {

                if ($this->upload->do_upload('quote_doc_upload')) {

                    $fileData = $this->upload->data();

                    // Correct: store folder + filename for DB
                    $header['quote_doc_upload'] = $upload_folder . $fileData['file_name'];
                }
            }
            $this->db->where('vendor_quote_id', $this->input->post('vendor_quote_id'));
            $this->db->update('vendor_quotation_info', $header);

            $vendor_quote_id = $this->input->post('vendor_quote_id');

            // Delete all old items
            // $this->db->where('vendor_quote_id', $vendor_quote_id);
            // $this->db->delete('vendor_quote_item_info');

            // Re-insert only selected (checked) items
            $selected_idxs = $this->input->post('selected_items') ?? [];

            /*if (!empty($selected_items)) {
                 foreach ($selected_items as $idx) {
                     $item = [
                         'vendor_quote_id' => $vendor_quote_id,
                         'vendor_rate_enquiry_item_id' => $this->input->post("vendor_rate_enquiry_item_id")[$idx] ?? 0,
                         'category_id' => $this->input->post("category_id")[$idx] ?? 0,
                         'item_id' => $this->input->post("item_id")[$idx] ?? 0,
                         'item_desc' => $this->input->post("item_desc")[$idx] ?? '',
                         'uom' => $this->input->post("uom")[$idx] ?? '',
                         'qty' => $this->input->post("qty")[$idx] ?? 0,
                         'rate' => $this->input->post("rate")[$idx] ?? 0,
                         'gst' => $this->input->post("gst")[$idx] ?? 0,
                         'amount' => $this->input->post("amount")[$idx] ?? 0,
                         'status' => 'Active',
                         'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                         'updated_date' => date('Y-m-d H:i:s'),
                     ];
                     $this->db->insert('vendor_quote_item_info', $item);
                 }
             }*/

            if (!empty($selected_idxs)) {
                // All arrays are posted with the SAME order as the rows
                $vendor_quote_item_ids = $this->input->post('vendor_quote_item_id') ?? [];
                $vendor_rate_enquiry_item_ids = $this->input->post('vendor_rate_enquiry_item_id') ?? [];

                $item_codes = $this->input->post('item_code') ?? [];
                $item_descs = $this->input->post('item_desc') ?? [];
                $uoms = $this->input->post('uom') ?? [];
                $qtys = $this->input->post('qty') ?? [];
                $gsts = $this->input->post('gst') ?? [];
                $rates = $this->input->post('rate') ?? [];
                $amounts = $this->input->post('amount') ?? [];

                foreach ($selected_idxs as $idx => $value) {
                    //if($tender_quotation_item_ids[$idx]){  
                    $item_data = [
                        'vendor_quote_id' => $vendor_quote_id,
                        'vendor_rate_enquiry_item_id' => $vendor_rate_enquiry_item_ids[$idx] ?? 0,
                        // 'category_id' => $category_ids[$idx] ?? 0,
                        'item_code' => $item_codes[$idx] ?? 0,
                        'item_desc' => $item_descs[$idx] ?? '',
                        'uom' => $uoms[$idx] ?? '',
                        'qty' => $qtys[$idx] ?? 0,
                        'gst' => $gsts[$idx] ?? 0,
                        'rate' => $rates[$idx] ?? 0,
                        'amount' => $amounts[$idx] ?? 0,
                        'status' => 'Active',
                        'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                        'created_date' => date('Y-m-d H:i:s')
                    ];

                    if (!empty($vendor_quote_item_ids[$idx]) && $vendor_quote_item_ids[$idx] > 0) {
                        // UPDATE existing item
                        $this->db->where('vendor_quote_item_id', $vendor_quote_item_ids[$idx])
                            ->update('vendor_quote_item_info', $item_data);

                    } else {
                        // INSERT new item
                        $item_data['created_by'] = $this->session->userdata(SESS_HD . 'user_id');
                        $item_data['created_date'] = date('Y-m-d H:i:s');

                        if ($this->db->insert('vendor_quote_item_info', $item_data)) {
                            $current_item_id = $this->db->insert_id();
                        } else {
                            $current_item_id = 0;
                        }
                        $miss_item_ids[] = $current_item_id;
                    }
                    $miss_item_ids[] = $vendor_quote_item_ids[$idx];

                }
                // DELETE items which are not in the selected list
                if (!empty($miss_item_ids)) {
                    $this->db->where('vendor_quote_id', $vendor_quote_id);
                    $this->db->where_not_in('vendor_quote_item_id', $miss_item_ids);
                    $this->db->update('vendor_quote_item_info', ['status' => 'Deleted', 'updated_by' => $this->session->userdata(SESS_HD . 'user_id'), 'updated_date' => date('Y-m-d H:i:s')]);
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Error updating Vendor Quotation.');
            } else {
                $this->session->set_flashdata('success', 'Vendor Quotation updated successfully.');
            }
            redirect('vendor-quotation-edit/' . $vendor_quote_id);
        }

        // ==================== LOAD DATA FOR EDIT ====================
        if (!$vendor_quote_id) {
            show_404();
        }

        // Header
        $sql = "SELECT vpo.* FROM vendor_quotation_info AS vpo WHERE vpo.vendor_quote_id = ? AND vpo.status = 'Active'";
        $query = $this->db->query($sql, [$vendor_quote_id]);
        $data['header'] = $query->row_array();

        if (!$data['header']) {
            show_404();
        }

        // Items
        $sql = "
            SELECT 
            a.vendor_quote_item_id,
            b.vendor_rate_enquiry_item_id,
            IF(b.tender_enquiry_item_id != a.vendor_rate_enquiry_item_id, a.item_code,b.item_code) AS item_code,
            IF(b.tender_enquiry_item_id != a.vendor_rate_enquiry_item_id, a.item_desc,b.item_desc) AS item_desc, 
            if(b.tender_enquiry_item_id != a.vendor_rate_enquiry_item_id, a.uom,b.uom) AS uom, 
            if(b.tender_enquiry_item_id != a.vendor_rate_enquiry_item_id, a.qty,b.qty) AS qty,
            a.rate,
            a.gst as vat,
            a.amount 
            FROM vendor_rate_enquiry_item_info AS b
            left join vendor_quote_item_info AS a 
            ON a.vendor_rate_enquiry_item_id = b.vendor_rate_enquiry_item_id AND a.status='Active' AND a.vendor_quote_id = ?
            WHERE b.status = 'Active'
            and b.vendor_rate_enquiry_id =  ?
            ORDER BY b.vendor_rate_enquiry_item_id ASC
        ";

        $query = $this->db->query($sql, [$vendor_quote_id, $data['header']['vendor_rate_enquiry_id']]);
        $data['items'] = $query->result_array();

        $sql = "
            SELECT
                company_id,
                company_name
            FROM
                company_info
            WHERE
            STATUS
                = 'Active'
            ORDER BY
                company_name ASC
        ";
        $query = $this->db->query($sql);
        $data['company_opt'] = ['' => 'Select Company'];
        foreach ($query->result_array() as $row) {
            $data['company_opt'][$row['company_id']] = $row['company_name'];
        }

        // Customer Dropdown
        $sql = "
            SELECT 
            customer_id, 
            customer_name 
            FROM customer_info 
            WHERE status = 'Active' 
            ORDER BY customer_name ASC
        ";
        $query = $this->db->query($sql);
        $data['customer_opt'] = ['' => 'Select Customer'];
        foreach ($query->result_array() as $row) {
            $data['customer_opt'][$row['customer_id']] = $row['customer_name'];
        }

        // Tender Enquiry
        $sql = "
                SELECT 
                a.tender_enquiry_id, 
            get_tender_info(a.tender_enquiry_id) as enquiry_no 
            FROM tender_enquiry_info as a 
            WHERE a.status = 'Active' 
            GROUP BY a.tender_enquiry_id 
            ORDER BY a.tender_enquiry_id
        ";
        $query = $this->db->query($sql);
        $data['tender_enquiry_opt'] = ['' => 'Select Enquiry'];
        foreach ($query->result_array() as $row) {
            $data['tender_enquiry_opt'][$row['tender_enquiry_id']] = $row['enquiry_no'];
        }

        $sql = "SELECT vendor_rate_enquiry_id, enquiry_no, DATE_FORMAT(enquiry_date, '%d-%m-%Y') AS enquiry_date FROM vendor_rate_enquiry_info WHERE status = 'Active' ORDER BY vendor_rate_enquiry_id DESC";
        $query = $this->db->query($sql);
        $data['vendor_rate_enquiry_opt'] = ['' => 'Select Enquiry No'];
        foreach ($query->result_array() as $row) {
            $data['vendor_rate_enquiry_opt'][$row['vendor_rate_enquiry_id']] = $row['enquiry_no'] . ' [' . $row['enquiry_date'] . ']';
        }

        $sql = "SELECT vendor_id, vendor_name FROM vendor_info WHERE status = 'Active' ORDER BY vendor_name ASC";
        $query = $this->db->query($sql);
        $data['vendor_opt'] = ['' => 'Select Vendor'];
        foreach ($query->result_array() as $row) {
            $data['vendor_opt'][$row['vendor_id']] = $row['vendor_name'];
        }

        $sql = "SELECT vendor_contact_id, contact_person_name FROM vendor_contact_info WHERE status = 'Active' ORDER BY contact_person_name ASC";
        $query = $this->db->query($sql);
        $data['vendor_contact_opt'] = ['' => 'Select Contact'];
        foreach ($query->result_array() as $row) {
            $data['vendor_contact_opt'][$row['vendor_contact_id']] = $row['contact_person_name'];
        }

        $sql = "SELECT gst_percentage FROM gst_info WHERE status = 'Active' ORDER BY gst_percentage ASC";
        $query = $this->db->query($sql);
        $gst_opt = [];
        foreach ($query->result_array() as $row) {
            $gst_opt[$row['gst_percentage']] = $row['gst_percentage'];
        }
        $data['gst_opt'] = $gst_opt;

        // Status Options
        $data['quotation_status_opt'] = ['' => 'Select Tender Status', 'Pending' => 'Pending', 'Confirmed' => 'Confirmed', 'Rejected' => 'Rejected'];
        $data['currency_opt'] = ['' => 'Select Currency'];
        $sql = "
            SELECT currency_id, currency_code
            FROM currencies_info
            WHERE status = 'Active'
            ORDER BY currency_name ASC
        ";
        $query = $this->db->query($sql);

        foreach ($query->result_array() as $row) {
            $data['currency_opt'][$row['currency_id']] = $row['currency_code'];
        }

        $this->load->view('page/vendor/vendor-quotation-edit', $data);
    }

    public function vendor_quotation_print($vendor_quote_id = 0)
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        if (!$vendor_quote_id) {
            show_404();
        }

        // === MAIN RECORD ===
        $sql = "
         SELECT
           vqi.*,
             v.vendor_name,
             v.address as vendor_address,
            v.country AS vendor_country,
            ci.company_name AS our_company,
           	vqi.quote_no,
            ci.ltr_header_img
        FROM
            vendor_quotation_info vqi
        LEFT JOIN customer_info c ON
           vqi.customer_id = c.customer_id AND c.status = 'Active'
        LEFT JOIN company_info ci ON
           vqi.company_id = ci.company_id AND ci.status = 'Active'
        LEFT JOIN tender_enquiry_info te ON
           vqi.tender_enquiry_id = te.tender_enquiry_id AND te.status = 'Active'
        LEFT JOIN vendor_info as v on vqi.vendor_id = v.vendor_id and v.status='Active'
        WHERE
           vqi.vendor_quote_id = ? AND vqi.status != 'Delete'
        ";
        $query = $this->db->query($sql, [$vendor_quote_id]);
        $data['record'] = $query->row_array();

        if (!$data['record']) {
            show_404();
        }

        // === ITEMS WITH RATE CALCULATION ===
        $sql = "
            SELECT 
                vqi.*,
                item.item_code,
                cat.category_name,
                item.item_name,
                item.item_description,
                item.uom AS item_uom
            FROM vendor_quote_item_info vqi
            LEFT JOIN category_info cat ON vqi.category_id = cat.category_id
            LEFT JOIN item_info item ON vqi.item_id = item.item_id
            WHERE vqi.vendor_quote_id = ? 
            AND vqi.status IN ('Active', 'Inactive')
            ORDER BY vqi.vendor_quote_item_id
        ";
        $query = $this->db->query($sql, [$vendor_quote_id]);
        $items = $query->result_array();

        $data['items'] = [];
        $gst_summary = [];

        foreach ($items as $item) {
            $qty = floatval($item['qty']);
            $gst = floatval($item['gst']);
            $amount = floatval($item['amount']);
            $rate = floatval($item['rate']);


            // === GST Amount ===
            $gst_amount = $amount - ($qty * $rate);

            // === Store in item ===
            $item['rate'] = $rate;
            $item['gst_amount'] = $gst_amount;
            $item['base_amount'] = $qty * $rate;

            $data['items'][] = $item;

            // === GST Summary ===
            $gst_key = number_format($gst, 2);
            if (!isset($gst_summary[$gst_key])) {
                $gst_summary[$gst_key] = ['gst' => $gst, 'base' => 0, 'gst_amount' => 0];
            }
            $gst_summary[$gst_key]['base'] += $qty * $rate;
            $gst_summary[$gst_key]['gst_amount'] += $gst_amount;
        }

        $data['gst_summary'] = $gst_summary;
        $data['grand_total'] = array_sum(array_column($data['items'], 'base_amount'));
        $data['total_gst'] = array_sum(array_column($data['items'], 'gst_amount'));
        $data['final_total'] = $data['grand_total'] + $data['total_gst'];

        $this->load->view('page/vendor/vendor-quotation-print', $data);
    }
    public function get_vendor_quotation_rate_enquiry()
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
                a.item_code,
                a.item_desc, 
                a.uom,
                a.qty,
                a.rate,
                a.gst as vat,
                a.amount
            FROM vendor_rate_enquiry_item_info a 
            WHERE a.status = 'Active'
            AND a.vendor_rate_enquiry_id = ?
            ORDER BY a.vendor_rate_enquiry_item_id ASC;

        ";

        $query = $this->db->query($sql, [$vendor_rate_enquiry_id]);
        echo json_encode($query->result_array());
    }
    public function get_vendor_quotation_data_load_po()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $srch_vendor_quote_id = $this->input->post('srch_vendor_quote_id');

        if (empty($srch_vendor_quote_id)) {
            echo json_encode([]);
            return;
        }

        $sql = "
            SELECT
            a.vendor_quote_item_id,
            b.vendor_rate_enquiry_id,
            a.vendor_rate_enquiry_item_id,
            a.item_code,
            a.item_desc,
            a.uom,
            a.qty,
            a.rate,
            a.gst as vat,  
            a.amount        
            FROM vendor_quote_item_info a 
            left join vendor_quotation_info b on a.vendor_quote_id = b.vendor_quote_id and b.`status` = 'Active'
            WHERE a.status = 'Active'
            AND a.vendor_quote_id = ?
            ORDER BY a.vendor_rate_enquiry_item_id ASC
        ";

        $query = $this->db->query($sql, [$srch_vendor_quote_id]);
        echo json_encode($query->result_array());
    }

    public function get_vendor_pur_inward_item_load()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $srch_vendor_po_id = $this->input->post('srch_vendor_po_id');

        if (empty($srch_vendor_po_id)) {
            echo json_encode([]);
            return;
        }

        $sql = "
            SELECT
                a.vendor_po_id,
                a.vendor_rate_enquiry_item_id, 
                a.uom,
                a.qty,
                a.vendor_po_item_id,
                a.item_id,
                a.item_desc,
                a.category_id,
                a.item_code,
                a.rate,
                a.gst,
                a.amount
               
            FROM  vendor_po_item_info a 
            WHERE a.status = 'Active'
                AND a.vendor_po_id = ?
            ORDER BY a.vendor_rate_enquiry_item_id ASC
        ";

        $query = $this->db->query($sql, [$srch_vendor_po_id]);
        echo json_encode($query->result_array());
    }

    public function get_vendor_list_quotation()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $tender_enquiry_id = $this->input->post('srch_tender_enquiry_id');

        $sql = "
            SELECT 
                e.vendor_id,
                e.vendor_name
            FROM vendor_rate_enquiry_info AS a
            LEFT JOIN tender_enquiry_info AS b 
                ON a.tender_enquiry_id = b.tender_enquiry_id AND b.status = 'Active'
            LEFT JOIN customer_info AS c 
                ON a.customer_id = c.customer_id AND c.status = 'Active'
            LEFT JOIN company_info AS d 
                ON a.company_id = d.company_id AND d.status = 'Active'
            LEFT JOIN vendor_info AS e 
                ON a.vendor_id = e.vendor_id AND e.status = 'Active'
            WHERE a.status = 'Active'
            AND a.tender_enquiry_id = ?
            GROUP BY a.vendor_id
            ORDER BY e.vendor_name ASC
        ";

        $query = $this->db->query($sql, [$tender_enquiry_id]);
        echo json_encode($query->result_array());
    }


    public function get_vendor_list_purchase_inward()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $tender_enquiry_id = $this->input->post('srch_tender_enquiry_id');

        $sql = "
            SELECT 
                d.vendor_id,
                d.vendor_name
            FROM  vendor_po_info AS a
            left join company_info as b on a.company_id = b.company_id and b.`status`='Active'
            left join customer_info as c on a.customer_id = c.customer_id and c.`status`='Active'
            left join vendor_info as d on a.vendor_id = d.vendor_id and d.status='Active'
            where a.`status`='Active'
            and a.tender_enquiry_id= ?
            group by d.vendor_id ASC  
        ";

        $query = $this->db->query($sql, [$tender_enquiry_id]);
        echo json_encode($query->result_array());
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
                ci.category_name,
                ii.item_name,
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
                    a.vendor_rate_enquiry_id,
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
                            b.enquiry_no,
                             date_format(b.closing_date, '%Y-%m-%d') as closing_date,
                            a.item_code,
                            a.item_desc,
                            a.uom,
                            a.qty,
                            0 as rate,
                            0 as vat,
                            0 as amount                           
                        FROM tender_enquiry_item_info AS a
                        LEFT JOIN tender_enquiry_info AS b ON a.tender_enquiry_id = b.tender_enquiry_id AND b.status = 'Active' 
                        WHERE  a.status = 'Active' 
                        AND a.tender_enquiry_id = '" . $rec_id . "'
                        order by a.tender_enquiry_item_id asc
                    ");
            $rec_list = $query->result_array();
        }

        if ($table == 'get-tender-enquiry-item-list-rate1') {
            $query = $this->db->query("
                SELECT
                    a.tender_enquiry_item_id,
                    d.category_id,
                    d.category_name,
                    c.item_id,
                    c.item_name,
                    a.item_code,
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

        if ($table == 'get-vendor-rate-enquiry-list') {

            $query = $this->db->query("
                SELECT
                    a.vendor_rate_enquiry_id,
                    DATE_FORMAT(a.enquiry_date, '%d-%m-%Y') AS enquiry_date,
                    a.enquiry_no AS vendor_rate_enquiry_no,
                    b.vendor_id,
                    b.vendor_name,
                    c.vendor_contact_id,
                    c.contact_person_name
                FROM vendor_rate_enquiry_info AS a
                LEFT JOIN vendor_info AS b 
                    ON a.vendor_id = b.vendor_id AND b.status = 'Active'
                LEFT JOIN vendor_contact_info AS c 
                    ON a.vendor_contact_person_id = c.vendor_contact_id 
                    AND c.vendor_id = b.vendor_id 
                    AND c.status = 'Active'
                WHERE a.status = 'Active'
                AND a.vendor_id = '" . $rec_id . "'
                AND a.tender_enquiry_id = '" . $this->input->post('tender_enquiry_id') . "'                
                ORDER BY vendor_rate_enquiry_id desc, enquiry_no desc
            ");

            //and a.vendor_rate_enquiry_status = 'Quotation Received'

            $rec_list = $query->result_array();
            echo json_encode($rec_list);
            exit;
        }

        if ($table == 'get-vendor-quotation-load-enquiry-no') {


            $tender_enquiry_id = $this->input->post('tender_enquiry_id');
            $query = $this->db->query("
                   SELECT
                    a.vendor_quote_id,
                    a.quote_no as vendor_quote_no
                FROM vendor_quotation_info AS a
                LEFT JOIN vendor_info AS b 
                    ON a.vendor_id = b.vendor_id AND b.status = 'Active'
                LEFT JOIN vendor_contact_info AS c 
                    ON a.vendor_contact_person_id = c.vendor_contact_id 
                    AND c.vendor_id = b.vendor_id 
                    AND c.status = 'Active'
                WHERE a.status = 'Active'
                AND a.vendor_id = '" . $rec_id . "'
                AND a.tender_enquiry_id = '" . $tender_enquiry_id . "'
                and a.quote_status = 'Confirmed'
                ORDER BY a.quote_no ASC
            ");

            $rec_list = $query->result_array();
            echo json_encode($rec_list);
            exit;
        }

        if ($table == 'get-vendor-purchase-inward-load-list') {

            $tender_enquiry_id = $this->input->post('tender_enquiry_id');

            $query = $this->db->query("
                  SELECT
                    a.vendor_po_id,
                    a.vendor_id,
                    IF(NULLIF(a.po_no, '') IS NULL, '', a.po_no) AS vendor_po_no 
                    FROM vendor_po_info as a 
                    left JOIN vendor_info as b on a.vendor_id  = b.vendor_id and b.status='Active'
                    where a.status='Active' 
                    and a.tender_enquiry_id = '" . $tender_enquiry_id . "'
                    AND a.vendor_id =  '" . $rec_id . "'
                    ORDER BY a.po_no desc
            ");

            $rec_list = $query->result_array();
            echo json_encode($rec_list);
            exit;
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

        $where = "1=1";

        if (isset($_POST['srch_from_date'])) {
            $data['srch_from_date'] = $srch_from_date = $this->input->post('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->input->post('srch_to_date');
            $this->session->set_userdata('srch_from_date', $this->input->post('srch_from_date'));
            $this->session->set_userdata('srch_to_date', $this->input->post('srch_to_date'));
        } elseif ($this->session->userdata('srch_from_date')) {
            $data['srch_from_date'] = $srch_from_date = $this->session->userdata('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->session->userdata('srch_to_date');
        } else {
            $data['srch_from_date'] = $srch_from_date = '';
            $data['srch_to_date'] = $srch_to_date = '';
        }

        if (!empty($srch_from_date) && !empty($srch_to_date)) {
            $where .= " AND  ( a.enquiry_date BETWEEN '" . $this->db->escape_str($srch_from_date) . "' AND '" . $this->db->escape_str($srch_to_date) . "') ";
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


        // Company Filter
        if ($this->input->post('srch_customer_rfq_no') !== null) {
            $data['srch_customer_rfq_no'] = $srch_customer_rfq_no = $this->input->post('srch_customer_rfq_no');
            $this->session->set_userdata('srch_customer_rfq_no', $srch_customer_rfq_no);
        } elseif ($this->session->userdata('srch_customer_rfq_no')) {
            $data['srch_customer_rfq_no'] = $srch_customer_rfq_no = $this->session->userdata('srch_customer_rfq_no');
        } else {
            $data['srch_customer_rfq_no'] = $srch_customer_rfq_no = '';
        }
        if (!empty($srch_customer_rfq_no)) {
            $where = " (t.enquiry_no = '" . $this->db->escape_str($srch_customer_rfq_no) . "')";
        }



        if ($this->input->post('srch_enquiry_no') !== null) {
            $data['srch_enquiry_no'] = $srch_enquiry_no = $this->input->post('srch_enquiry_no');
            $this->session->set_userdata('srch_enquiry_no', $srch_enquiry_no);
        } elseif ($this->session->userdata('srch_enquiry_no')) {
            $data['srch_enquiry_no'] = $srch_enquiry_no = $this->session->userdata('srch_enquiry_no');
        } else {
            $data['srch_enquiry_no'] = $srch_enquiry_no = '';
        }


        if (!empty($srch_enquiry_no)) {
            $where = " ( concat(ifnull(com.company_code,'') , '/', ifnull(t.company_sno,'') ,  '/' , ifnull(c.customer_code,'') ,  '/' , ifnull(t.customer_sno,''),  '/' , DATE_FORMAT(t.enquiry_date,'%Y') ) like '%" . $this->db->escape_str($srch_enquiry_no) . "%' ) ";

            $data['srch_customer_id'] = $srch_customer_id = '';
        }


        $this->db->from('vendor_rate_enquiry_info a');
        $this->db->join('company_info com', 'a.company_id = com.company_id AND com.status = "Active"', 'left');
        $this->db->join('customer_info c', 'a.customer_id = c.customer_id AND c.status = "Active"', 'left');
        $this->db->join('tender_enquiry_info t', 'a.tender_enquiry_id = t.tender_enquiry_id AND t.status = "Active"', 'left');
        $this->db->where('a.status !=', 'Delete');
        $this->db->where($where);


        $data['total_records'] = $this->db->count_all_results();


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
                c.customer_code,
                com.company_code,
                c.customer_name,
                com.company_name,
                t.company_sno,
                t.customer_sno,
                t.enquiry_no as customer_rfq_no,
                v.vendor_name,
                a.tender_enquiry_id,
                get_tender_info(a.tender_enquiry_id) as tender_details,
                a.status,
                a.vendor_rate_enquiry_status
            FROM vendor_rate_enquiry_info a
            LEFT JOIN customer_info c ON a.customer_id = c.customer_id AND c.status = 'Active'
            LEFT JOIN vendor_info v ON a.vendor_id = v.vendor_id AND v.status = 'Active'
            LEFT JOIN tender_enquiry_info t ON a.tender_enquiry_id = t.tender_enquiry_id AND t.status != 'Delete'
            left join company_info com  on t.company_id = com.company_id and com.`status`='Active'
            WHERE a.status != 'Delete'  
            AND $where
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

        $sql = "
            SELECT 
            a.customer_id,
            b.customer_code
            FROM vendor_rate_enquiry_info as a 
            left join customer_info as b on a.customer_id = b.customer_id and b.`status`='Active'
            WHERE a.status = 'Active' 
            group by b.customer_id
            ORDER BY b.customer_id desc
        ";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['customer_code_opt'][$row['customer_code']] = $row['customer_code'];
        }

        $data['vendor_opt'] = ['' => 'All'];
        $sql = "SELECT vendor_id, vendor_name FROM vendor_info WHERE status = 'Active' ORDER BY vendor_name";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['vendor_opt'][$row['vendor_id']] = $row['vendor_name'];
        }

        // Tender Enquiry Dropdown (only when customer selected)
        $data['tender_enquiry_opt'] = ['' => 'All'];
        if (!empty($srch_customer_id)) {
            $sql = "
                SELECT 
                    a.tender_enquiry_id, 
                    get_tender_info(a.tender_enquiry_id) as tender_details
                FROM tender_enquiry_info AS a 
                WHERE a.status = 'Active' 
                and a.customer_id= '" . $srch_customer_id . "'
                ORDER BY a.tender_enquiry_id, a.enquiry_no ASC
            ";


            $query = $this->db->query($sql);
            $data['tender_enquiry_opt'] = [];
            foreach ($query->result_array() as $row) {
                // $data['tender_enquiry_opt'][$row['tender_enquiry_id']] = $row['company_code'] . ' -> ' . $row['company_sno'] . ' -> ' . $row['customer_code'] . ' -> ' . $row['customer_sno'] . ' -> ' . $row['enquiry_no'];
                $data['tender_enquiry_opt'][$row['tender_enquiry_id']] = $row['tender_details'];
            }
        }

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
            get_tender_info(a.tender_enquiry_id) AS tender_details
        FROM tender_enquiry_info AS a 
        WHERE a.status = 'Active' 
        AND a.customer_id = ?
        AND a.company_id = ?
        ORDER BY a.tender_enquiry_id desc
    ";

        // FIX: Correct parameter order
        $query = $this->db->query($sql, [$customer_id, $company_id]);

        $result = [];

        foreach ($query->result_array() as $row) {
            $result[] = [
                "tender_enquiry_id" => $row['tender_enquiry_id'],
                "display" => $row['tender_details']
            ];
        }

        echo json_encode($result);
    }



    public function get_vendor_rate_enquiries_by_customer()
    {
        $customer_id = $this->input->post('customer_id');

        $sql = "
            SELECT 
                a.tender_enquiry_id, 
                get_tender_info(a.tender_enquiry_id) AS tender_details
            FROM tender_enquiry_info AS a 
            WHERE a.status = 'Active' 
            AND a.customer_id = ?
            ORDER BY a.tender_enquiry_id desc
        ";

        // FIX: Correct parameter order
        $query = $this->db->query($sql, [$customer_id]);

        $result = [];

        foreach ($query->result_array() as $row) {
            $result[] = [
                "tender_enquiry_id" => $row['tender_enquiry_id'],
                "display" => $row['tender_details']
            ];
        }

        echo json_encode($result);
    }


    public function vendor_purchase_bill_add()
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
        $data['js'] = 'vendor/vendor-purchase-bill-add.inc';
        $data['title'] = 'Add Vendor Purchase Bill Entry';

        if ($this->input->post('mode') == 'Add') {
            // echo "<pre>";
            // print_r($_POST);
            // echo "</pre>";

            $this->db->trans_start();


            // 1. Handle file uploads
            $upload_path = 'vendor-pur-invoice-documents/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size'] = 2048;

            $this->load->library('upload', $config);

            $purchase_bill_upload = '';


            if (!empty($_FILES['purchase_bill_upload']['name'])) {
                if ($this->upload->do_upload('purchase_bill_upload')) {
                    $purchase_bill_upload = $this->upload->data('file_name');
                }
            }


            $header = [
                'company_id' => $this->input->post('srch_company_id'),
                'customer_id' => $this->input->post('srch_customer_id'),
                'tender_enquiry_id' => $this->input->post('srch_tender_enquiry_id'),
                'vendor_id' => $this->input->post('srch_vendor_id'),
                'vendor_po_id' => $this->input->post('srch_vendor_po_id'),
                'vendor_contact_person_id' => $this->input->post('srch_vendor_contact_person_id'),
                'invoice_date' => $this->input->post('invoice_date'),
                'entry_date' => $this->input->post('entry_date'),
                'invoice_no' => $this->input->post('invoice_no'),
                'vat_payer_purchase_grp' => $this->input->post('vat_payer_purchase_grp'),
                'declaration_no' => $this->input->post('declaration_no'),
                'declaration_date' => $this->input->post('declaration_date'),
                'total_amount_wo_tax' => $this->input->post('total_amount_wo_tax'),
                'tax_amount' => $this->input->post('total_vat_amount'),
                'total_amount' => $this->input->post('total_amount'),
                'door_delivery' => $this->input->post('door_delivery'),
                'fix_theamount_total' => $this->input->post('fix_theamount_total'),
                'remarks' => $this->input->post('remarks'),
                'purchase_bill_upload' => 'vendor-pur-invoice-documents/' . $purchase_bill_upload,
                'status' => $this->input->post('status'),
                'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'created_date' => date('Y-m-d H:i:s'),
            ];

            $this->db->insert('vendor_purchase_invoice_info', $header);
            $vendor_purchase_invoice_id = $this->db->insert_id();
            $selected_items = $this->input->post('selected_items') ?? [];

            if (!empty($selected_items)) {

                $vendor_po_item_id = $this->input->post('vendor_po_item_id') ?? [];
                $category_id = $this->input->post('category_id') ?? [];
                $item_id = $this->input->post('item_id') ?? [];
                $item_desc = $this->input->post('item_desc') ?? [];
                $uom = $this->input->post('uom') ?? [];
                $qty = $this->input->post('qty') ?? [];
                $rate = $this->input->post('rate') ?? [];
                $gst = $this->input->post('gst') ?? [];
                $amount = $this->input->post('amount') ?? [];
                $conversion_rates = $this->input->post('conversion_rate') ?? [];
                $dutys = $this->input->post('duty') ?? [];

                foreach ($selected_items as $idx => $value) {
                    $item = [
                        'vendor_purchase_invoice_id' => $vendor_purchase_invoice_id,
                        'vendor_po_item_id' => $vendor_po_item_id[$idx] ?? 0,
                        'category_id' => $category_id[$idx] ?? 0,
                        'item_id' => $item_id[$idx] ?? 0,
                        'item_desc' => $item_desc[$idx] ?? '',
                        'uom' => $uom[$idx] ?? '',
                        'qty' => $qty[$idx] ?? 0,
                        'rate' => $rate[$idx] ?? 0,
                        'gst' => $gst[$idx] ?? 0,
                        'conversion_rate' => $conversion_rates[$idx] ?? 1,
                        'duty' => $dutys[$idx] ?? 0,
                        'amount' => $amount[$idx] ?? 0,
                        'status' => 'Active',
                        'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                        'created_date' => date('Y-m-d H:i:s'),
                        'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                        'updated_date' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->insert('vendor_purchase_invoice_item_info', $item);
                }
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Error saving Vendor Bill. Please try again.');
            } else {
                $this->session->set_flashdata('success', 'Vendor Invoice saved successfully.');

            }

            redirect('vendor-purchase-bill-list');
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

        $sql = "
            SELECT gst_id, gst_percentage 
            FROM gst_info 
            WHERE status = 'Active' 
            ORDER BY gst_percentage ASC";
        $query = $this->db->query($sql);
        $data['gst_opt'] = [];
        foreach ($query->result_array() as $row) {
            $data['gst_opt'][$row['gst_percentage']] = $row['gst_percentage'];
        }

        $data['vendor_opt'] = [];

        $sql = "
            SELECT 
            vat_filing_head_name 
            FROM vat_filing_head_info 
            WHERE status = 'Active' 
            and vat_filing_head_type = 'Purchase'
            ORDER BY vat_filing_head_id ASC
            ";
        $query = $this->db->query($sql);
        $data['vat_payer_purchase_opt'] = ['' => 'Select VAT Payer Purchase Category'];
        foreach ($query->result_array() as $row) {
            $data['vat_payer_purchase_opt'][$row['vat_filing_head_name']] = $row['vat_filing_head_name'];
        }

        // $data['vat_payer_purchase_opt'] = [
        //     '' => 'Select VAT Payer Purchase Category',
        //     'Standard Rated Domestic Purchases at 5% (Line 8 of the VAT Return)' => 'Standard Rated Domestic Purchases at 5% (Line 8 of the VAT Return)',
        //     'Standard Rated Domestic Purchases at 10% (Line 8 of the VAT Return)' => 'Standard Rated Domestic Purchases at 10% (Line 8 of the VAT Return)',
        //     'Import subject to VAT paid at customs (Line 9 of the VAT Return)' => 'Import subject to VAT paid at customs (Line 9 of the VAT Return)',
        //     'Imports subject to deferral at customs (Line 10 of the VAT Return)' => 'Imports subject to deferral at customs (Line 10 of the VAT Return)',
        //     'Import subject to VAT accounted for through reverse charge mechanism at 5% (Line 11 of the VAT Return)' => 'Import subject to VAT accounted for through reverse charge mechanism at 5% (Line 11 of the VAT Return)',
        //     'Import subject to VAT accounted for through reverse charge mechanism at 10% (Line 11 of the VAT Return)' => 'Import subject to VAT accounted for through reverse charge mechanism at 10% (Line 11 of the VAT Return)',
        //     'Purchases subject to domestic reverse charge mechanism (Line 12 of the VAT Return)' => 'Purchases subject to domestic reverse charge mechanism (Line 12 of the VAT Return)',
        //     'Purchases from non-register taxpayers, zero-rated/ exempt purchases (Line 13 of the VAT Return)' => 'Purchases from non-register taxpayers, zero-rated/ exempt purchases (Line 13 of the VAT Return)',
        // ];

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



        $this->load->view('page/vendor/vendor-purchase-bill-add', $data);
    }

    public function vendor_purchase_bill_list()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        $data = array();
        $data['js'] = 'vendor/vendor-purchase-bill-list.inc';
        $data['s_url'] = 'vendor-purchase-bill-list';
        $data['title'] = 'Vendor Purchase Invoice List';

        $where = "1=1";

        if (isset($_POST['srch_from_date'])) {
            $data['srch_from_date'] = $srch_from_date = $this->input->post('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->input->post('srch_to_date');
            $this->session->set_userdata('srch_from_date', $this->input->post('srch_from_date'));
            $this->session->set_userdata('srch_to_date', $this->input->post('srch_to_date'));
        } elseif ($this->session->userdata('srch_from_date')) {
            $data['srch_from_date'] = $srch_from_date = $this->session->userdata('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->session->userdata('srch_to_date');
        } else {
            $data['srch_from_date'] = $srch_from_date = '';
            $data['srch_to_date'] = $srch_to_date = '';
        }

        if (!empty($srch_from_date) && !empty($srch_to_date)) {
            $where .= " AND  ( a.invoice_date BETWEEN '" . $this->db->escape_str($srch_from_date) . "' AND '" . $this->db->escape_str($srch_to_date) . "') ";
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


        // Company Filter
        if ($this->input->post('srch_customer_rfq_no') !== null) {
            $data['srch_customer_rfq_no'] = $srch_customer_rfq_no = $this->input->post('srch_customer_rfq_no');
            $this->session->set_userdata('srch_customer_rfq_no', $srch_customer_rfq_no);
        } elseif ($this->session->userdata('srch_customer_rfq_no')) {
            $data['srch_customer_rfq_no'] = $srch_customer_rfq_no = $this->session->userdata('srch_customer_rfq_no');
        } else {
            $data['srch_customer_rfq_no'] = $srch_customer_rfq_no = '';
        }
        if (!empty($srch_customer_rfq_no)) {
            $where = " (t.enquiry_no = '" . $this->db->escape_str($srch_customer_rfq_no) . "')";
            $data['srch_vendor_id'] = $srch_vendor_id = '';
        }



        if ($this->input->post('srch_enquiry_no') !== null) {
            $data['srch_enquiry_no'] = $srch_enquiry_no = $this->input->post('srch_enquiry_no');
            $this->session->set_userdata('srch_enquiry_no', $srch_enquiry_no);
        } elseif ($this->session->userdata('srch_enquiry_no')) {
            $data['srch_enquiry_no'] = $srch_enquiry_no = $this->session->userdata('srch_enquiry_no');
        } else {
            $data['srch_enquiry_no'] = $srch_enquiry_no = '';
        }
        if (!empty($srch_enquiry_no)) {
            $where = " ( concat(ifnull(ci.company_code,'') , '/', ifnull(t.company_sno,'') ,  '/' , ifnull(c.customer_code,'') ,  '/' , ifnull(t.customer_sno,''),  '/' , DATE_FORMAT(t.enquiry_date,'%Y') ) like '%" . $this->db->escape_str($srch_enquiry_no) . "%' ) ";

            $data['srch_customer_id'] = $srch_customer_id = '';
            $data['srch_vendor_id'] = $srch_vendor_id = '';
        }

        // Company Filter
        if ($this->input->post('srch_invoice_no') !== null) {
            $data['srch_invoice_no'] = $srch_invoice_no = $this->input->post('srch_invoice_no');
            $this->session->set_userdata('srch_invoice_no', $srch_invoice_no);
        } elseif ($this->session->userdata('srch_invoice_no')) {
            $data['srch_invoice_no'] = $srch_invoice_no = $this->session->userdata('srch_invoice_no');
        } else {
            $data['srch_invoice_no'] = $srch_invoice_no = '';
        }
        if (!empty($srch_invoice_no)) {
            $where = " (a.invoice_no = '" . $this->db->escape_str($srch_invoice_no) . "')";
            $data['srch_vendor_id'] = $srch_vendor_id = '';
            $data['srch_customer_id'] = $srch_customer_id = '';
        }

        $this->db->from('vendor_purchase_invoice_info a');
        $this->db->join('company_info ci', 'a.company_id = ci.company_id AND ci.status = "Active"', 'left');
        $this->db->join('customer_info c', 'a.customer_id = c.customer_id AND c.status = "Active"', 'left');
        $this->db->join('tender_enquiry_info t', 'a.tender_enquiry_id = t.tender_enquiry_id AND t.status = "Active"', 'left');
        $this->db->where('a.status !=', 'Delete');
        $this->db->where($where);

        $data['total_records'] = $this->db->count_all_results();




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

        $data['vendor_opt'] = [];


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
        if (!empty($srch_customer_id)) {

            $sql = "
                SELECT 
                    a.tender_enquiry_id, 
                    get_tender_info(a.tender_enquiry_id) as tender_details
                FROM tender_enquiry_info AS a 
                WHERE a.status = 'Active' 
                and a.customer_id= '" . $srch_customer_id . "'
                ORDER BY a.tender_enquiry_id desc
            ";


            $query = $this->db->query($sql);
            $data['tender_enquiry_opt'] = [];
            foreach ($query->result_array() as $row) {
                $data['tender_enquiry_opt'][$row['tender_enquiry_id']] = $row['tender_details'];
            }
        }

        $sql = "
                SELECT 
                    b.vendor_id,
                    b.vendor_name
                FROM  vendor_po_info AS a
                LEFT JOIN vendor_info as b on  a.vendor_id  =  b.vendor_id and b.status='Active'
                where a.status='Active' 
            ";

        $query = $this->db->query($sql);

        foreach ($query->result_array() as $row) {
            $data['vendor_opt'][$row['vendor_id']] = $row['vendor_name'];
        }

        $data['po_status_opt'] = ['' => 'Select PO Status', 'In Progress' => 'In Progress', 'Delivered' => 'Delivered'];


        // === FETCH RECORDS ===
        $sql = "
            SELECT 
                a.invoice_no, 
                a.vendor_purchase_invoice_id ,
                a.status,
                a.customer_id,
                a.vendor_id, 
                a.invoice_date,
                c.customer_name,
                v.vendor_name,
                t.enquiry_no as customer_rfq_no,
                a.company_id,
                a.customer_id, 
                ci.company_name,
                get_tender_info(a.tender_enquiry_id) as tender_details
            FROM vendor_purchase_invoice_info as  a
            LEFT JOIN customer_info c ON a.customer_id = c.customer_id AND c.status = 'Active'
            LEFT JOIN vendor_info v ON a.vendor_id = v.vendor_id AND v.status = 'Active'
            LEFT JOIN tender_enquiry_info t ON a.tender_enquiry_id = t.tender_enquiry_id AND t.status != 'Delete'
            left join company_info as ci on t.company_id = ci.company_id and ci.status = 'Active'
            WHERE a.status != 'Delete' AND $where 
            ORDER BY a.vendor_purchase_invoice_id  DESC
            LIMIT " . $this->uri->segment(2, 0) . ", " . $config['per_page'];

        $query = $this->db->query($sql);
        $data['record_list'] = $query->result_array();



        $this->load->view('page/vendor/vendor-purchase-bill-list', $data);
    }


    public function vendor_purchase_bill_edit($vendor_purchase_invoice_id = 0)
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

        $data['js'] = 'vendor/vendor-purchase-bill-edit.inc';
        $data['title'] = ' Edit Vendor Purchase Invoice';

        // ==================== UPDATE ====================
        if ($this->input->post('mode') == 'Edit') {

            $this->db->trans_start();

            // === 1. File Upload Settings ===
            $upload_folder = 'vendor-pur-invoice-documents/';
            $upload_path = FCPATH . $upload_folder;

            // Create directory if not exists
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            $config = [
                'upload_path' => $upload_path,
                'allowed_types' => 'jpg|jpeg|png|pdf|doc|docx',
                'max_size' => 4096,
                'encrypt_name' => TRUE,
            ];

            $this->load->library('upload', $config);

            // === 2. Header Data ===
            $header = [
                'company_id' => $this->input->post('srch_company_id'),
                'customer_id' => $this->input->post('srch_customer_id'),
                'tender_enquiry_id' => $this->input->post('srch_tender_enquiry_id'),
                'vendor_id' => $this->input->post('srch_vendor_id'),
                'vendor_po_id' => $this->input->post('srch_vendor_po_id'),
                'vendor_contact_person_id' => $this->input->post('srch_vendor_contact_person_id'),
                'invoice_date' => $this->input->post('invoice_date'),
                'entry_date' => $this->input->post('entry_date'),
                'invoice_no' => $this->input->post('invoice_no'),
                'vat_payer_purchase_grp' => $this->input->post('vat_payer_purchase_grp'),
                'declaration_no' => $this->input->post('declaration_no'),
                'declaration_date' => $this->input->post('declaration_date'),
                'total_amount_wo_tax' => $this->input->post('total_amount_wo_tax'),
                'tax_amount' => $this->input->post('total_vat_amount'),
                'total_amount' => $this->input->post('total_amount'),
                'fix_theamount_total' => $this->input->post('fix_theamount_total'),
                'door_delivery' => $this->input->post('door_delivery'),
                'remarks' => $this->input->post('remarks'),
                'status' => $this->input->post('status'),
                'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'updated_date' => date('Y-m-d H:i:s'),
            ];

            // === 3. Upload File ===
            if (!empty($_FILES['purchase_bill_upload']['name'])) {

                if ($this->upload->do_upload('purchase_bill_upload')) {

                    $fileData = $this->upload->data();

                    // Store relative path in DB
                    $header['purchase_bill_upload'] = $upload_folder . $fileData['file_name'];

                } else {
                    // Upload error handling
                    $this->session->set_flashdata('error', 'Upload Failed: ' . $this->upload->display_errors());
                    redirect('vendor-purchase-bill-list/');
                }
            }

            // === 4. UPDATE Header ===
            $vendor_purchase_invoice_id = $this->input->post('vendor_purchase_invoice_id');

            $this->db->where('vendor_purchase_invoice_id', $vendor_purchase_invoice_id);
            $this->db->update('vendor_purchase_invoice_info', $header);

            // === 5. Delete unselected items ===
            $selected_items = $this->input->post('selected_items') ?? [];
            $vendor_purchase_invoice_item_id = $this->input->post('vendor_purchase_invoice_item_id') ?? [];

            // Get all existing item IDs for this invoice
            $existing_items = [];
            foreach ($vendor_purchase_invoice_item_id as $idx => $item_id) {
                if (!empty($item_id)) {
                    $existing_items[] = $item_id;
                }
            }

            // Delete items that are not in selected_items
            $items_to_delete = [];
            foreach ($vendor_purchase_invoice_item_id as $idx => $item_id) {
                if (!empty($item_id) && !in_array($idx, $selected_items)) {
                    $items_to_delete[] = $item_id;
                }
            }

            if (!empty($items_to_delete)) {
                $this->db->where_in('vendor_purchase_invoice_item_id', $items_to_delete);
                $this->db->update('vendor_purchase_invoice_item_info', [
                    'status' => 'Inactive',
                    'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                    'updated_date' => date('Y-m-d H:i:s')
                ]);
            }

            // === 6. Process Selected Items ===
            $vendor_po_item_id = $this->input->post('vendor_po_item_id') ?? [];
            $category_id = $this->input->post('category_id') ?? [];
            $item_id = $this->input->post('item_id') ?? [];
            $item_desc = $this->input->post('item_desc') ?? [];
            $uom = $this->input->post('uom') ?? [];
            $item_code = $this->input->post('item_code') ?? [];
            $qty = $this->input->post('qty') ?? [];
            $rate = $this->input->post('rate') ?? [];
            $gst = $this->input->post('gst') ?? [];
            $conversion_rates = $this->input->post('conversion_rate') ?? [];
            $dutys = $this->input->post('duty') ?? [];
            $amount = $this->input->post('amount') ?? [];

            foreach ($selected_items as $idx => $value) {

                $itemData = [
                    'vendor_purchase_invoice_id' => $vendor_purchase_invoice_id,
                    'vendor_po_item_id' => $vendor_po_item_id[$idx] ?? 0,
                    'category_id' => $category_id[$idx] ?? 0,
                    'item_id' => $item_id[$idx] ?? 0,
                    'item_desc' => $item_desc[$idx] ?? '',
                    'item_code' => $item_code[$idx] ?? '',
                    'uom' => $uom[$idx] ?? '',
                    'qty' => $qty[$idx] ?? 0,
                    'rate' => $rate[$idx] ?? 0,
                    'gst' => $gst[$idx] ?? 0,
                    'amount' => $amount[$idx] ?? 0,
                    'conversion_rate' => $conversion_rates[$idx] ?? 1,
                    'duty' => $dutys[$idx] ?? 0,
                    'status' => 'Active',
                    'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                    'updated_date' => date('Y-m-d H:i:s'),
                ];

                if (!empty($vendor_purchase_invoice_item_id[$idx])) {

                    $this->db->where(
                        'vendor_purchase_invoice_item_id',
                        $vendor_purchase_invoice_item_id[$idx]
                    );
                    $this->db->update('vendor_purchase_invoice_item_info', $itemData);

                } else {

                    $itemData['created_by'] = $this->session->userdata(SESS_HD . 'user_id');
                    $itemData['created_date'] = date('Y-m-d H:i:s');

                    $this->db->insert('vendor_purchase_invoice_item_info', $itemData);
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Error updating Vendor Purchase Bill.');
            } else {
                $this->session->set_flashdata('success', 'Vendor Purchase Bill updated successfully.');
            }

            redirect('vendor-purchase-bill-list/');
        }

        if (!$vendor_purchase_invoice_id)
            show_404();

        // === Fetch Header Data ===
        $sql = "
            SELECT * FROM vendor_purchase_invoice_info
            WHERE vendor_purchase_invoice_id = ? AND status = 'Active'
        ";
        $data['header'] = $this->db->query($sql, [$vendor_purchase_invoice_id])->row_array();

        if (empty($data['header'])) {
            show_404();
        }

        // === Fetch Saved Items (Already in Invoice) ===
        $sql = "
        SELECT
            a.vendor_purchase_invoice_item_id,
            a.vendor_po_item_id,
            a.category_id,
            a.item_id,
            a.item_desc,
            a.item_code,
            a.uom,
            a.qty,
            a.rate,
            a.gst,
            a.conversion_rate,
            a.duty,
            a.amount
        FROM vendor_purchase_invoice_item_info as a
        WHERE a.status='Active'
        AND a.vendor_purchase_invoice_id = ?
    ";
        $data['saved_items'] = $this->db->query($sql, [$vendor_purchase_invoice_id])->result_array();

        // === Fetch ALL Available Items from Vendor PO (for selection) ===
        $vendor_po_id = $data['header']['vendor_po_id'];
        $company_id = $data['header']['company_id'];
        $customer_id = $data['header']['customer_id'];
        $tender_enquiry_id = $data['header']['tender_enquiry_id'];

        $sql = "
        SELECT
            a.vendor_po_id, 
            b.company_id,
            b.customer_id,
            b.tender_enquiry_id,
            a.uom,
            a.qty,
            a.vendor_po_item_id,
            a.item_id,
            a.item_desc,
            a.category_id,
            a.item_code,
            a.rate,
            a.gst,
            a.amount  
        FROM vendor_po_item_info as a
        LEFT JOIN vendor_po_info as b ON a.vendor_po_id = b.vendor_po_id AND b.status='Active'
        WHERE a.status='Active'
        AND b.company_id = ?
        AND b.customer_id = ?
        AND b.tender_enquiry_id = ?
        AND a.vendor_po_id = ?
    ";
        $data['all_po_items'] = $this->db->query($sql, [
            $company_id,
            $customer_id,
            $tender_enquiry_id,
            $vendor_po_id
        ])->result_array();

        // === Create lookup array for saved items ===
        $saved_item_ids = [];
        foreach ($data['saved_items'] as $saved) {
            $saved_item_ids[$saved['vendor_po_item_id']] = $saved;
        }
        $data['saved_item_ids'] = $saved_item_ids;

        // === Fetch Options ===
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

        // Customers
        $query = $this->db->query("
        SELECT customer_id, customer_name 
        FROM customer_info 
        WHERE status = 'Active' 
        ORDER BY customer_name");
        $data['customer_opt'] = [];
        foreach ($query->result_array() as $row) {
            $data['customer_opt'][$row['customer_id']] = $row['customer_name'];
        }

        $sql = "
        SELECT gst_id, gst_percentage 
        FROM gst_info 
        WHERE status = 'Active' 
        ORDER BY gst_percentage ASC";
        $query = $this->db->query($sql);
        $data['gst_opt'] = [];
        foreach ($query->result_array() as $row) {
            $data['gst_opt'][$row['gst_percentage']] = $row['gst_percentage'];
        }

        $data['vendor_opt'] = [];

        $sql = "
            SELECT 
            vat_filing_head_name 
            FROM vat_filing_head_info 
            WHERE status = 'Active' 
            and vat_filing_head_type = 'Purchase'
            ORDER BY vat_filing_head_id ASC
            ";
        $query = $this->db->query($sql);
        $data['vat_payer_purchase_opt'] = ['' => 'Select VAT Payer Purchase Category'];
        foreach ($query->result_array() as $row) {
            $data['vat_payer_purchase_opt'][$row['vat_filing_head_name']] = $row['vat_filing_head_name'];
        }

        // $data['vat_payer_purchase_opt'] = [
        //     '' => 'Select VAT Payer Purchase Category',
        //     'Standard Rated Domestic Purchases at 5% (Line 8 of the VAT Return)' => 'Standard Rated Domestic Purchases at 5% (Line 8 of the VAT Return)',
        //     'Standard Rated Domestic Purchases at 10% (Line 8 of the VAT Return)' => 'Standard Rated Domestic Purchases at 10% (Line 8 of the VAT Return)',
        //     'Import subject to VAT paid at customs (Line 9 of the VAT Return)' => 'Import subject to VAT paid at customs (Line 9 of the VAT Return)',
        //     'Imports subject to deferral at customs (Line 10 of the VAT Return)' => 'Imports subject to deferral at customs (Line 10 of the VAT Return)',
        //     'Import subject to VAT accounted for through reverse charge mechanism at 5% (Line 11 of the VAT Return)' => 'Import subject to VAT accounted for through reverse charge mechanism at 5% (Line 11 of the VAT Return)',
        //     'Import subject to VAT accounted for through reverse charge mechanism at 10% (Line 11 of the VAT Return)' => 'Import subject to VAT accounted for through reverse charge mechanism at 10% (Line 11 of the VAT Return)',
        //     'Purchases subject to domestic reverse charge mechanism (Line 12 of the VAT Return)' => 'Purchases subject to domestic reverse charge mechanism (Line 12 of the VAT Return)',
        //     'Purchases from non-register taxpayers, zero-rated/ exempt purchases (Line 13 of the VAT Return)' => 'Purchases from non-register taxpayers, zero-rated/ exempt purchases (Line 13 of the VAT Return)',
        // ];


        $sql = "
        SELECT vendor_id, vendor_name 
        FROM vendor_info 
        WHERE status = 'Active' 
        ORDER BY vendor_name ASC";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['vendor_opt'][$row['vendor_id']] = $row['vendor_name'];
        }

        $data['vendor_contact_opt'] = [];
        $sql = "SELECT vendor_contact_id, contact_person_name FROM vendor_contact_info WHERE status = 'Active' ORDER BY contact_person_name ASC";
        $query = $this->db->query($sql);
        $data['vendor_contact_opt'] = ['' => 'Select Contact'];
        foreach ($query->result_array() as $row) {
            $data['vendor_contact_opt'][$row['vendor_contact_id']] = $row['contact_person_name'];
        }

        $sql = "
                SELECT 
                    a.tender_enquiry_id, 
                    get_tender_info(a.tender_enquiry_id) as tender_details
                FROM tender_enquiry_info AS a 
                WHERE a.status = 'Active' 
                 ORDER BY a.tender_enquiry_id, a.enquiry_no ASC
            ";


        $query = $this->db->query($sql);
        $data['tender_enquiry_opt'] = [];
        foreach ($query->result_array() as $row) {
            // $data['tender_enquiry_opt'][$row['tender_enquiry_id']] = $row['company_code'] . ' -> ' . $row['company_sno'] . ' -> ' . $row['customer_code'] . ' -> ' . $row['customer_sno'] . ' -> ' . $row['enquiry_no'];
            $data['tender_enquiry_opt'][$row['tender_enquiry_id']] = $row['tender_details'];
        }

        $sql = "SELECT vendor_po_id, po_no FROM vendor_po_info WHERE status = 'Active' ORDER BY po_no ASC";
        $query = $this->db->query($sql);
        $data['vendor_po_opt'] = ['' => 'Select Enquiry No'];
        foreach ($query->result_array() as $row) {
            $data['vendor_po_opt'][$row['vendor_po_id']] = $row['po_no'];
        }

        $this->load->view('page/vendor/vendor-purchase-bill-edit', $data);
    }

    public function vendor_srch_rfq_no()
    {
        $term = $this->input->post('search');

        $sql = "
            SELECT   
                a.vendor_rate_enquiry_id ,
                a.enquiry_no as vendor_rfq_no
            FROM vendor_rate_enquiry_info AS a 
            WHERE a.status = 'Active'
            AND a.enquiry_no LIKE '%" . $this->db->escape_like_str($term) . "%'
            ORDER BY a.tender_enquiry_id DESC, a.enquiry_no ASC 
        ";

        $query = $this->db->query($sql);

        $result = [];

        foreach ($query->result() as $row) {
            $result[] = [
                'label' => $row->vendor_rfq_no,   // text shown in dropdown
                'value' => $row->vendor_rfq_no,   // value inserted in input
                'vendor_rate_enquiry_id' => $row->vendor_rate_enquiry_id,
                'vendor_rfq_no' => $row->vendor_rfq_no
            ];
        }

        echo json_encode($result);
    }
    public function srch_vendor_quotation_no()
    {
        $term = $this->input->post('search');

        $sql = "
            SELECT   
                a.vendor_quote_id,
                a.quote_no
            FROM vendor_quotation_info AS a 
            WHERE a.status = 'Active'
            AND a.quote_no LIKE '%" . $this->db->escape_like_str($term) . "%'
            ORDER BY a.vendor_quote_id  DESC, a.quote_no ASC 
        ";

        $query = $this->db->query($sql);

        $result = [];

        foreach ($query->result() as $row) {
            $result[] = [
                'label' => $row->quote_no,   // text shown in dropdown
                'value' => $row->quote_no,   // value inserted in input
                'vendor_quote_id' => $row->vendor_quote_id,
                'quote_no' => $row->quote_no
            ];
        }

        echo json_encode($result);
    }
    public function srch_vendor_po_no()
    {
        $term = $this->input->post('search');

        $sql = "
            SELECT   
                a.vendor_po_id ,
                a.po_no
            FROM vendor_po_info AS a 
            WHERE a.status = 'Active'
            AND a.po_no LIKE '%" . $this->db->escape_like_str($term) . "%'
            ORDER BY a.vendor_po_id   DESC, a.po_no ASC 
        ";

        $query = $this->db->query($sql);

        $result = [];

        foreach ($query->result() as $row) {
            $result[] = [
                'label' => $row->po_no,   // text shown in dropdown
                'value' => $row->po_no,   // value inserted in input
                'vendor_po_id' => $row->vendor_po_id,
                'po_no' => $row->po_no
            ];
        }

        echo json_encode($result);
    }
    public function vendor_srch_inward_no()
    {
        $term = $this->input->post('search');

        $sql = "
            SELECT   
                a.vendor_pur_inward_id,
                a.inward_no
            FROM vendor_pur_inward_info AS a 
            WHERE a.status = 'Active'
            AND a.inward_no LIKE '%" . $this->db->escape_like_str($term) . "%'
            ORDER BY a.vendor_pur_inward_id   DESC, a.inward_no ASC 
        ";

        $query = $this->db->query($sql);

        $result = [];

        foreach ($query->result() as $row) {
            $result[] = [
                'label' => $row->inward_no,   // text shown in dropdown
                'value' => $row->inward_no,   // value inserted in input
                'vendor_pur_inward_id' => $row->vendor_pur_inward_id,
                'inward_no' => $row->inward_no
            ];
        }

        echo json_encode($result);
    }
    public function vendor_srch_invoice_no()
    {
        $term = $this->input->post('search');

        $sql = "
            SELECT   
                a.vendor_purchase_invoice_id,
                a.invoice_no
            FROM vendor_purchase_invoice_info AS a 
            WHERE a.status = 'Active'
            AND a.invoice_no LIKE '%" . $this->db->escape_like_str($term) . "%'
            ORDER BY a.vendor_purchase_invoice_id   DESC, a.invoice_no ASC 
        ";

        $query = $this->db->query($sql);

        $result = [];

        foreach ($query->result() as $row) {
            $result[] = [
                'label' => $row->invoice_no,
                'value' => $row->invoice_no,
                'vendor_purchase_invoice_id' => $row->vendor_purchase_invoice_id,
                'invoice_no' => $row->invoice_no
            ];
        }

        echo json_encode($result);
    }

    public function get_vendor_quotation_currency_id()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $vendor_quote_id = $this->input->post('srch_vendor_quote_id');

        if (empty($vendor_quote_id)) {
            echo json_encode([]);
            return;
        }
        $sql = "
            SELECT 
                curr.currency_id,
                curr.currency_code
            FROM vendor_quotation_info as a 
            LEFT JOIN currencies_info curr 
                ON a.currency_id = curr.currency_id 
                AND curr.status = 'Active'
            WHERE a.status = 'Active'
            AND a.vendor_quote_id = ?
            ORDER BY a.vendor_quote_id ASC
        ";

        $query = $this->db->query($sql, [$vendor_quote_id]);
        $result = $query->result_array();
        echo json_encode($result);
    }
}