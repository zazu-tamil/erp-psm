<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Multiple_customer_vendor extends CI_Controller
{

    public function vendor_purchase_bill_multiple_customer_add()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        if (!authorize_page()) {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }
        $data['js'] = 'vendor/vendor-purchase-bill-multiple customer-add.inc';
        $data['title'] = 'Add Supplier Purchase Bill Multiple Entry';

        if ($this->input->post('mode') == 'Add') {
            // echo "<pre>";
            // print_r($_POST);
            // echo "</pre>";

            $this->db->trans_start();


            // 1. Handle file uploads
            $upload_path = 'vendor-pur-invoice-multiple-documents/';
            if (!is_dir(FCPATH . $upload_path)) {
                mkdir(FCPATH . $upload_path, 0777, true);
            }

            $config['upload_path'] = FCPATH . $upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size'] = 2048;

            $this->load->library('upload', $config);

            $purchase_bill_upload = '';

            if (!empty($_FILES['purchase_bill_upload']['name'])) {
                if ($this->upload->do_upload('purchase_bill_upload')) {
                    $purchase_bill_upload = $upload_path . $this->upload->data('file_name');
                }
            }
            $header = [
                'company_id' => $this->input->post('srch_company_id'),
                'vendor_id' => $this->input->post('srch_vendor_id'),
                 'vendor_contact_person_id' => $this->input->post('srch_vendor_contact_person_id'),
                'invoice_date' => $this->input->post('invoice_date'),
                'entry_date' => $this->input->post('entry_date'),
                'invoice_no' => $this->input->post('invoice_no'),
                'vat_payer_purchase_grp' => $this->input->post('vat_payer_purchase_grp'),

                'total_amount_wo_tax' => $this->input->post('total_amount_wo_tax'),
                'total_amount_wo_tax_inc_addl' => $this->input->post('total_amount_wo_tax_inc_addl'),
                'total_tax_amount_inc_addl' => $this->input->post('total_tax_amount_inc_addl'),
                'total_amount_inc_addl' => $this->input->post('total_amount_inc_addl'),
                'total_duty_amount' => $this->input->post('total_duty_amount'),
                'tax_amount' => $this->input->post('total_vat_amount'),
                'total_amount' => $this->input->post('total_amount'),
                'total_amount_wo_convert' => $this->input->post('total_amount_wo_convert'),
                'total_convert_amount' => $this->input->post('total_convert_amount'),
                'total_amount_after_convert' => $this->input->post('total_amount_after_convert'),
                'fix_theamount_total' => $this->input->post('fix_theamount_total'),
                'remarks' => $this->input->post('remarks'),
                'purchase_bill_upload' => ($purchase_bill_upload != '' ? 'vendor-pur-invoice-documents/' . $purchase_bill_upload : ''),
                'only_accounting_entry' => $this->input->post('only_accounting_entry'),
                'status' => $this->input->post('status'),
                'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'created_date' => date('Y-m-d H:i:s'),
            ];

            $this->db->insert('vendor_purchase_multiple_invoice_info', $header);
            $vendor_purchase_multiple_invoice_id = $this->db->insert_id();
            $selected_items = $this->input->post('selected_items') ?? [];

            if (!empty($selected_items)) {

                $vendor_po_item_id = $this->input->post('vendor_po_item_id') ?? [];
                $vendor_po_id = $this->input->post('vendor_po_id') ?? [];
                $category_id = $this->input->post('category_id') ?? [];
                $item_id = $this->input->post('item_id') ?? [];
                $item_desc = $this->input->post('item_desc') ?? [];
                $uom = $this->input->post('uom') ?? [];
                $qty = $this->input->post('qty') ?? [];
                $rate = $this->input->post('rate') ?? [];
                $gst = $this->input->post('gst') ?? [];
                $amount = $this->input->post('amount') ?? [];
                $conversion_rates = $this->input->post('conversion_rate') ?? [];
                $tender_enquiry_id = $this->input->post('tender_enquiry_id') ?? [];
                $customer_id = $this->input->post('customer_id') ?? [];

                foreach ($selected_items as $idx) {
                    $item = [
                        'vendor_purchase_multiple_invoice_id' => $vendor_purchase_multiple_invoice_id,
                        'customer_id' => $customer_id[$idx] ?? 0,
                        'tender_enquiry_id' => $tender_enquiry_id[$idx] ?? 0,
                        'vendor_po_id' => $vendor_po_id[$idx] ?? 0,
                        'vendor_po_item_id' => $vendor_po_item_id[$idx] ?? 0,
                        'category_id' => $category_id[$idx] ?? 0,
                        'item_id' => $item_id[$idx] ?? 0,
                        'item_desc' => $item_desc[$idx] ?? '',
                        'uom' => $uom[$idx] ?? '',
                        'qty' => $qty[$idx] ?? 0,
                        'rate' => $rate[$idx] ?? 0,
                        'gst' => $gst[$idx] ?? 0,
                        'conversion_rate' => $conversion_rates[$idx] ?? 1,
                        'amount' => $amount[$idx] ?? 0,
                        'status' => 'Active',
                        'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                        'created_date' => date('Y-m-d H:i:s'),
                        'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                        'updated_date' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->insert('vendor_purchase_multiple_invoice_item_info', $item);
                }
            }

            // Save Additional Charges
            $chk_addt_charges_type_id = $this->input->post('chk_addt_charges_type_id') ?? [];

            if (!empty($chk_addt_charges_type_id)) {
                $addt_charges_type_id = $this->input->post('addt_charges_type_id') ?? [];
                $addt_charges_amt = $this->input->post('addt_charges_amt') ?? [];
                $addt_charges_conversion_rate = $this->input->post('addt_charges_conversion_rate') ?? [];
                $addt_charges_conversion_amt = $this->input->post('addt_charges_conversion_amt') ?? [];
                $addt_charges_vat = $this->input->post('addt_charges_vat') ?? [];
                $addt_charges_vat_amt = $this->input->post('addt_charges_vat_amt') ?? [];
                $addt_charges_tot_amt = $this->input->post('addt_charges_tot_amt') ?? [];

                foreach ($chk_addt_charges_type_id as $chk_id) {
                    $addt_charges_data = [
                        'vendor_purchase_multiple_invoice_id' => $vendor_purchase_multiple_invoice_id,
                        'addt_charges_type_id' => $addt_charges_type_id[$chk_id] ?? 0,
                        'addt_charges_amt' => $addt_charges_amt[$chk_id] ?? 0,
                        'conversion_rate' => $addt_charges_conversion_rate[$chk_id] ?? 0.000,
                        'conversion_amt' => $addt_charges_conversion_amt[$chk_id] ?? 0.000,
                        'addt_charges_vat' => $addt_charges_vat[$chk_id] ?? 0,
                        'addt_charges_vat_amt' => $addt_charges_vat_amt[$chk_id] ?? 0,
                        'addt_charges_tot_amt' => $addt_charges_tot_amt[$chk_id] ?? 0,
                        'status' => 'Active'
                    ];
                    $this->db->insert('vendor_purchase_multiple_invoice_addtchrg_info', $addt_charges_data);
                }
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Error saving Vendor Bill. Please try again.');
            } else {
                $this->session->set_flashdata('success', 'Vendor Invoice saved successfully.');

            }

            redirect('vendor-purchase-bill-multiple customer-add');
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

        $data['delivery_partner_opt'] = [];
        $sql = "
            SELECT 
            delivery_partner_id,
            delivery_partner_name 
            FROM delivery_partner_info 
            WHERE status = 'Active' 
            ORDER BY delivery_partner_name ASC
        ";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['delivery_partner_opt'][$row['delivery_partner_id']] = $row['delivery_partner_name'];
        }

        $sql = "
            SELECT 
            *
            FROM addt_charges_type_info
            WHERE status = 'Active'
            ORDER BY addt_charges_type_name ASC
        ";
        $query = $this->db->query($sql);

        $data['addt_charges_list'] = $query->result_array();

        $this->load->view('page/vendor/vendor-purchase-bill-multiple customer-add', $data);
    }

    // AJAX: Load PO items for a specific vendor + enquiry search (grouped by customer)
    public function vendor_purchase_bill_multiple_customer_list()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }

        $data = array();
        $data['s_url'] = 'vendor-purchase-bill-multiple-customer-list';
        $data['title'] = 'Multiple Customer Supplier Purchase Invoice List';
        $data['js'] = 'vendor/vendor-purchase-bill-multiple-customer-list.inc';

        $where = "1=1";

        if (isset($_POST['srch_from_date'])) {
            $data['srch_from_date'] = $srch_from_date = $this->input->post('srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->input->post('srch_to_date');
            $this->session->set_userdata('multi_srch_from_date', $this->input->post('srch_from_date'));
            $this->session->set_userdata('multi_srch_to_date', $this->input->post('srch_to_date'));
        } elseif ($this->session->userdata('multi_srch_from_date')) {
            $data['srch_from_date'] = $srch_from_date = $this->session->userdata('multi_srch_from_date');
            $data['srch_to_date'] = $srch_to_date = $this->session->userdata('multi_srch_to_date');
        } else {
            $data['srch_from_date'] = $srch_from_date = '';
            $data['srch_to_date'] = $srch_to_date = '';
        }

        if (!empty($srch_from_date) && !empty($srch_to_date)) {
            $where .= " AND  ( a.invoice_date BETWEEN '" . $this->db->escape_str($srch_from_date) . "' AND '" . $this->db->escape_str($srch_to_date) . "') ";
        }

        if ($this->input->post('srch_vendor_id') !== null) {
            $data['srch_vendor_id'] = $srch_vendor_id = $this->input->post('srch_vendor_id');
            $this->session->set_userdata('multi_srch_vendor_id', $srch_vendor_id);
        } elseif ($this->session->userdata('multi_srch_vendor_id')) {
            $data['srch_vendor_id'] = $srch_vendor_id = $this->session->userdata('multi_srch_vendor_id');
        } else {
            $data['srch_vendor_id'] = $srch_vendor_id = '';
        }
        if (!empty($srch_vendor_id)) {
            $where .= " AND a.vendor_id = '" . $this->db->escape_str($srch_vendor_id) . "'";
        }

        if ($this->input->post('srch_invoice_no') !== null) {
            $data['srch_invoice_no'] = $srch_invoice_no = $this->input->post('srch_invoice_no');
            $this->session->set_userdata('multi_srch_invoice_no', $srch_invoice_no);
        } elseif ($this->session->userdata('multi_srch_invoice_no')) {
            $data['srch_invoice_no'] = $srch_invoice_no = $this->session->userdata('multi_srch_invoice_no');
        } else {
            $data['srch_invoice_no'] = $srch_invoice_no = '';
        }
        if (!empty($srch_invoice_no)) {
            $where .= " AND (a.invoice_no = '" . $this->db->escape_str($srch_invoice_no) . "')";
            $data['srch_vendor_id'] = $srch_vendor_id = '';
        }

        $this->db->from('vendor_purchase_multiple_invoice_info a');
        $this->db->join('company_info ci', 'a.company_id = ci.company_id AND ci.status = "Active"', 'left');
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
            SELECT vendor_id, vendor_name
            FROM vendor_info 
            WHERE status='Active' 
            ORDER BY vendor_name ASC
        ";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['vendor_opt'][$row['vendor_id']] = $row['vendor_name'];
        }

        // === FETCH RECORDS ===
        $sql = "
            SELECT 
                a.invoice_no, 
                a.vendor_purchase_multiple_invoice_id,
                a.status,
                a.vendor_id, 
                a.invoice_date,
                v.vendor_name,
                a.total_amount_wo_tax_inc_addl as amt_without_vat,
                a.total_tax_amount_inc_addl as vat_amt,
                a.total_amount_inc_addl as total_amt_with_vat,
                a.company_id,
                ci.company_name
            FROM vendor_purchase_multiple_invoice_info as a
            LEFT JOIN vendor_info v ON a.vendor_id = v.vendor_id AND v.status = 'Active'
            LEFT JOIN company_info as ci on a.company_id = ci.company_id and ci.status = 'Active'
            WHERE a.status != 'Delete' AND $where 
            ORDER BY a.invoice_date desc, a.vendor_purchase_multiple_invoice_id DESC
            LIMIT " . (int)$this->uri->segment(2, 0) . ", " . (int)$config['per_page'];

        $query = $this->db->query($sql);
        $data['record_list'] = $query->result_array();

        $this->load->view('page/vendor/vendor-purchase-bill-multiple-customer-list', $data);
    }

    public function get_multi_customer_po_items()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $vendor_id = $this->input->post('vendor_id');
        $enquiry_search = $this->input->post('enquiry_search');
        $tender_enquiry_id = $this->input->post('tender_enquiry_id');

        if (empty($vendor_id)) {
            echo json_encode(['error' => 'Vendor is required']);
            return;
        }

        $where_enquiry = '';
        if (!empty($tender_enquiry_id)) {
            $esc_id = $this->db->escape_str($tender_enquiry_id);
            $where_enquiry = "AND po.tender_enquiry_id = '$esc_id'";
        } elseif (!empty($enquiry_search)) {
            $esc = $this->db->escape_str($enquiry_search);
            $where_enquiry = "AND (te.enquiry_no LIKE '%$esc%' OR te.tender_name LIKE '%$esc%' OR po.po_no LIKE '%$esc%')";
        }

        $esc_vendor = $this->db->escape_str($vendor_id);

        $sql = "
            SELECT
                pi.vendor_po_item_id,
                pi.vendor_po_id,
                pi.item_code,
                pi.item_desc,
                pi.uom,
                pi.qty,
                pi.rate,
                pi.gst,
                pi.amount,
                pi.category_id,
                pi.item_id,
                po.po_no,
                po.tender_enquiry_id,
                IF(get_tender_info(po.tender_enquiry_id) IS NULL OR get_tender_info(po.tender_enquiry_id) = '', te.enquiry_no, get_tender_info(po.tender_enquiry_id)) as tender_details,
                te.enquiry_no,
                te.tender_name,
                c.customer_id,
                c.customer_name
            FROM vendor_po_item_info pi
            LEFT JOIN vendor_po_info po
                ON pi.vendor_po_id = po.vendor_po_id AND po.status = 'Active'
            LEFT JOIN tender_enquiry_info te
                ON po.tender_enquiry_id = te.tender_enquiry_id
            LEFT JOIN customer_info c
                ON te.customer_id = c.customer_id AND c.status = 'Active'
            WHERE pi.status = 'Active'
              AND po.vendor_id = '$esc_vendor'
              $where_enquiry
            ORDER BY c.customer_name ASC, te.enquiry_no ASC, pi.vendor_po_item_id ASC
        ";

        $query = $this->db->query($sql);
        $rows = $query->result_array();

        // Group by customer
        $grouped = [];
        foreach ($rows as $row) {
            $cid = $row['customer_id'] ?? 'unknown';
            if (!isset($grouped[$cid])) {
                $grouped[$cid] = [
                    'customer_id' => $row['customer_id'],
                    'customer_name' => $row['customer_name'],
                    'items' => [],
                ];
            }
            $grouped[$cid]['items'][] = $row;
        }

        header('Content-Type: application/json');
        echo json_encode(['customers' => array_values($grouped)]);
    }


    public function tender_enquiry_id_search()
    {
        $term = $this->input->post('search');
        $vendor_id = $this->input->post('vendor_id'); // Optional filter


        $vendor_join = " LEFT JOIN vendor_po_info AS po ON a.tender_enquiry_id = po.tender_enquiry_id AND po.status = 'Active' ";
        $vendor_where = "";
        if (!empty($vendor_id)) {
            $esc_vendor = $this->db->escape_str($vendor_id);
            // Ensure this tender enquiry has at least one PO for the selected vendor
            $vendor_join = " INNER JOIN vendor_po_info AS po ON a.tender_enquiry_id = po.tender_enquiry_id AND po.vendor_id = '$esc_vendor' AND po.status = 'Active' ";
        }

        $sql = "      
            SELECT DISTINCT
            concat(a.tender_enquiry_id , ' || ', ifnull(b.company_code,'') , ' || ', ifnull(a.company_sno,'') ,  ' || ' , ifnull(c.customer_code,'') ,  ' || ' , ifnull(a.customer_sno,''),  ' || ' , ifnull(a.enquiry_no,'')) as tender_ref,
            concat(ifnull(b.company_code,'') , '/', ifnull(a.company_sno,'') ,  '/' , ifnull(c.customer_code,'') ,  '/' , ifnull(a.customer_sno,''),  '/' , ifnull(a.enquiry_no,'')) as enq1,
            concat(ifnull(b.company_code,'') , '/', ifnull(a.company_sno,'') ,  '/' , ifnull(c.customer_code,'') ,  '/' , ifnull(a.customer_sno,''),  '/' , DATE_FORMAT(a.enquiry_date,'%Y') ) as enq,
            get_tender_info(a.tender_enquiry_id) as tender_details,
            po.po_no,
            a.company_id,
            a.customer_id,
            a.tender_enquiry_id,
            c.customer_name,
            a.enquiry_no,
            a.customer_contact_id
            FROM tender_enquiry_info AS a
            LEFT JOIN company_info AS b ON a.company_id = b.company_id AND b.status = 'Active'
            LEFT JOIN customer_info AS c ON a.customer_id = c.customer_id AND c.status = 'Active'
            $vendor_join
            WHERE  a.`status` = 'Active' 
            having (enq like '%" . $this->db->escape_like_str($term) . "%' OR tender_details like '%" . $this->db->escape_like_str($term) . "%' OR po_no like '%" . $this->db->escape_like_str($term) . "%')
            ORDER BY a.tender_enquiry_id desc, a.enquiry_no ASC  
        ";

        $query = $this->db->query($sql);

        $result = [];

        foreach ($query->result() as $row) {
            $display_label = !empty($row->po_no) ? $row->tender_details . ' (PO: ' . $row->po_no . ')' : $row->tender_details;
            $result[] = [
                'label' => $display_label,       // what user sees 
                'value' => $display_label,        // filled in textbox
                'company_id' => $row->company_id,
                'customer_id' => $row->customer_id,
                'tender_enquiry_id' => $row->tender_enquiry_id,
                'customer_name' => $row->customer_name,
                'enquiry_no' => $row->enquiry_no,
                'customer_contact_id' => $row->customer_contact_id
            ];
        }
        echo json_encode($result);

    }
    public function vendor_purchase_bill_multiple_customer_edit($vendor_purchase_multiple_invoice_id = 0)
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        if (!authorize_page()) {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }

        $data['js'] = 'vendor/vendor-purchase-bill-multiple customer-edit.inc';
        $data['title'] = 'Edit Supplier Purchase Bill Multiple Entry';

        if ($this->input->post('mode') == 'Edit') {

            $this->db->trans_start();

            // 1. Handle file uploads
            $upload_path = 'vendor-pur-invoice-multiple-documents/';
            if (!is_dir(FCPATH . $upload_path)) {
                mkdir(FCPATH . $upload_path, 0777, true);
            }

            $config['upload_path'] = FCPATH . $upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size'] = 2048;

            $this->load->library('upload', $config);

            $purchase_bill_upload = $this->input->post('old_purchase_bill_upload');

            if (!empty($_FILES['purchase_bill_upload']['name'])) {
                if ($this->upload->do_upload('purchase_bill_upload')) {
                    $purchase_bill_upload = $upload_path . $this->upload->data('file_name');
                }
            }

            $vendor_purchase_multiple_invoice_id = $this->input->post('vendor_purchase_multiple_invoice_id');

            $header = [
                'company_id' => $this->input->post('srch_company_id'),
                'vendor_id' => $this->input->post('srch_vendor_id'),
                'vendor_contact_person_id' => $this->input->post('srch_vendor_contact_person_id'),
                'invoice_date' => $this->input->post('invoice_date'),
                'entry_date' => $this->input->post('entry_date'),
                'invoice_no' => $this->input->post('invoice_no'),
                'vat_payer_purchase_grp' => $this->input->post('vat_payer_purchase_grp'),
                'total_amount_wo_tax' => $this->input->post('total_amount_wo_tax'),
                'total_amount_wo_tax_inc_addl' => $this->input->post('total_amount_wo_tax_inc_addl'),
                'total_tax_amount_inc_addl' => $this->input->post('total_tax_amount_inc_addl'),
                'total_amount_inc_addl' => $this->input->post('total_amount_inc_addl'),
                'total_duty_amount' => $this->input->post('total_duty_amount'),
                'tax_amount' => $this->input->post('total_vat_amount'),
                'total_amount' => $this->input->post('total_amount'),
                'total_amount_wo_convert' => $this->input->post('total_amount_wo_convert'),
                'total_convert_amount' => $this->input->post('total_convert_amount'),
                'total_amount_after_convert' => $this->input->post('total_amount_after_convert'),
                'fix_theamount_total' => $this->input->post('fix_theamount_total'),
                'remarks' => $this->input->post('remarks'),
                'purchase_bill_upload' => $purchase_bill_upload,
                'only_accounting_entry' => $this->input->post('only_accounting_entry'),
                'status' => $this->input->post('status'),
                'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'updated_date' => date('Y-m-d H:i:s'),
            ];

            $this->db->where('vendor_purchase_multiple_invoice_id', $vendor_purchase_multiple_invoice_id);
            $this->db->update('vendor_purchase_multiple_invoice_info', $header);

            $selected_items = $this->input->post('selected_items') ?? [];
            $vendor_purchase_multiple_invoice_item_id = $this->input->post('vendor_purchase_multiple_invoice_item_id') ?? [];

            // Delete missing items
            $existing_items = [];
            foreach ($vendor_purchase_multiple_invoice_item_id as $idx => $item_id) {
                if (!empty($item_id)) {
                    $existing_items[] = $item_id;
                }
            }

            $items_to_delete = [];
            foreach ($vendor_purchase_multiple_invoice_item_id as $idx => $item_id) {
                if (!empty($item_id) && !in_array($idx, $selected_items)) {
                    $items_to_delete[] = $item_id;
                }
            }

            if (!empty($items_to_delete)) {
                $this->db->where_in('vendor_purchase_multiple_invoice_item_id', $items_to_delete);
                $this->db->update('vendor_purchase_multiple_invoice_item_info', [
                    'status' => 'Delete',
                    'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                    'updated_date' => date('Y-m-d H:i:s')
                ]);
            }

            if (!empty($selected_items)) {
                $vendor_po_id = $this->input->post('vendor_po_id') ?? [];
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
                $tender_enquiry_id = $this->input->post('tender_enquiry_id') ?? [];
                $customer_id = $this->input->post('customer_id') ?? [];

                foreach ($selected_items as $idx) {
                    $item = [
                        'vendor_purchase_multiple_invoice_id' => $vendor_purchase_multiple_invoice_id,
                        'customer_id' => $customer_id[$idx] ?? 0,
                        'tender_enquiry_id' => $tender_enquiry_id[$idx] ?? 0,
                        'vendor_po_id' => $vendor_po_id[$idx] ?? 0,
                        'vendor_po_item_id' => $vendor_po_item_id[$idx] ?? 0,
                        'category_id' => $category_id[$idx] ?? 0,
                        'item_id' => $item_id[$idx] ?? 0,
                        'item_desc' => $item_desc[$idx] ?? '',
                        'uom' => $uom[$idx] ?? '',
                        'qty' => $qty[$idx] ?? 0,
                        'rate' => $rate[$idx] ?? 0,
                        'gst' => $gst[$idx] ?? 0,
                        'conversion_rate' => $conversion_rates[$idx] ?? 1,
                        'amount' => $amount[$idx] ?? 0,
                        'status' => 'Active',
                        'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                        'updated_date' => date('Y-m-d H:i:s'),
                    ];

                    if (!empty($vendor_purchase_multiple_invoice_item_id[$idx])) {
                        $this->db->where('vendor_purchase_multiple_invoice_item_id', $vendor_purchase_multiple_invoice_item_id[$idx]);
                        $this->db->update('vendor_purchase_multiple_invoice_item_info', $item);
                    } else {
                        $item['created_by'] = $this->session->userdata(SESS_HD . 'user_id');
                        $item['created_date'] = date('Y-m-d H:i:s');
                        $this->db->insert('vendor_purchase_multiple_invoice_item_info', $item);
                    }
                }
            }

            // Save Additional Charges
            $chk_addt_charges_type_id = $this->input->post('chk_addt_charges_type_id') ?? [];
            $vendor_purchase_multiple_invoice_addtchrg_id = $this->input->post('vendor_purchase_multiple_invoice_addtchrg_id') ?? [];
            $miss_addt_charges_id = [];

            if (!empty($chk_addt_charges_type_id)) {
                $addt_charges_amt = $this->input->post('addt_charges_amt') ?? [];
                $addt_charges_conversion_rate = $this->input->post('addt_charges_conversion_rate') ?? [];
                $addt_charges_conversion_amt = $this->input->post('addt_charges_conversion_amt') ?? [];
                $addt_charges_vat = $this->input->post('addt_charges_vat') ?? [];
                $addt_charges_vat_amt = $this->input->post('addt_charges_vat_amt') ?? [];
                $addt_charges_tot_amt = $this->input->post('addt_charges_tot_amt') ?? [];

                foreach ($chk_addt_charges_type_id as $chk_id) {
                    $addt_charges_data = [
                        'vendor_purchase_multiple_invoice_id' => $vendor_purchase_multiple_invoice_id,
                        'addt_charges_type_id' => $chk_id,
                        'addt_charges_amt' => $addt_charges_amt[$chk_id] ?? 0,
                        'conversion_rate' => $addt_charges_conversion_rate[$chk_id] ?? 0.000,
                        'conversion_amt' => $addt_charges_conversion_amt[$chk_id] ?? 0.000,
                        'addt_charges_vat' => $addt_charges_vat[$chk_id] ?? 0,
                        'addt_charges_vat_amt' => $addt_charges_vat_amt[$chk_id] ?? 0,
                        'addt_charges_tot_amt' => $addt_charges_tot_amt[$chk_id] ?? 0,
                        'status' => 'Active'
                    ];
                    
                    if (!empty($vendor_purchase_multiple_invoice_addtchrg_id[$chk_id]) && $vendor_purchase_multiple_invoice_addtchrg_id[$chk_id] > 0) {
                        $this->db->where('vendor_purchase_multiple_invoice_addtchrg_id', $vendor_purchase_multiple_invoice_addtchrg_id[$chk_id]);
                        $this->db->update('vendor_purchase_multiple_invoice_addtchrg_info', $addt_charges_data);
                        $miss_addt_charges_id[] = $vendor_purchase_multiple_invoice_addtchrg_id[$chk_id];
                    } else {
                        $this->db->insert('vendor_purchase_multiple_invoice_addtchrg_info', $addt_charges_data);
                        $miss_addt_charges_id[] = $this->db->insert_id();
                    }
                }
            }

            // Mark other charges for this invoice as Deleted
            $this->db->where('vendor_purchase_multiple_invoice_id', $vendor_purchase_multiple_invoice_id);
            if (!empty($miss_addt_charges_id)) {
                $this->db->where_not_in('vendor_purchase_multiple_invoice_addtchrg_id', $miss_addt_charges_id);
            }
            $this->db->update('vendor_purchase_multiple_invoice_addtchrg_info', ['status' => 'Delete']);

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Error updating Vendor Bill. Please try again.');
            } else {
                $this->session->set_flashdata('success', 'Vendor Invoice updated successfully.');
            }

            redirect('vendor-purchase-bill-multiple-customer-edit/' . $vendor_purchase_multiple_invoice_id);
        }

        if (!$vendor_purchase_multiple_invoice_id) {
            redirect('vendor-purchase-bill-multiple-customer-list');
        }

        // FETCH RECORD
        $this->db->where('vendor_purchase_multiple_invoice_id', $vendor_purchase_multiple_invoice_id);
        $query = $this->db->get('vendor_purchase_multiple_invoice_info');
        $data['record'] = $query->row_array();

        if (empty($data['record'])) {
            redirect('vendor-purchase-bill-multiple-customer-list');
        }

        // FETCH ITEMS
        $this->db->select('a.*, b.item_code as real_item_code, b.item_name as real_item_name, c.customer_name, te.enquiry_no, po.po_no, get_tender_info(a.tender_enquiry_id) as tender_details');
        $this->db->from('vendor_purchase_multiple_invoice_item_info a');
        $this->db->join('item_info b', 'a.item_id = b.item_id', 'left');
        $this->db->join('customer_info c', 'a.customer_id = c.customer_id', 'left');
        $this->db->join('tender_enquiry_info te', 'a.tender_enquiry_id = te.tender_enquiry_id', 'left');
        $this->db->join('vendor_po_info po', 'a.vendor_po_id = po.vendor_po_id', 'left');
        $this->db->where('a.vendor_purchase_multiple_invoice_id', $vendor_purchase_multiple_invoice_id);
        $this->db->where('a.status', 'Active');
        $query = $this->db->get();
        $data['items'] = $query->result_array();

        // FETCH ADDITIONAL CHARGES
        $this->db->where('vendor_purchase_multiple_invoice_id', $vendor_purchase_multiple_invoice_id);
        $this->db->where('status', 'Active');
        $query = $this->db->get('vendor_purchase_multiple_invoice_addtchrg_info');
        $saved_addt_charges = $query->result_array();
        
        $data['saved_addt_charges'] = [];
        foreach ($saved_addt_charges as $sac) {
            $data['saved_addt_charges'][$sac['addt_charges_type_id']] = $sac;
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


        $data['vendor_contact_opt'] = [];
        $sql = "
            SELECT vendor_contact_id as vendor_id,contact_person_name as vendor_name 
            FROM vendor_contact_info 
            WHERE status = 'Active' AND vendor_id = '" . $this->db->escape_str($data['record']['vendor_id']) . "'
            ORDER BY contact_person_name ASC";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['vendor_contact_opt'][$row['vendor_id']] = $row['vendor_name'];
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

        $data['delivery_partner_opt'] = [];
        $sql = "
            SELECT 
            delivery_partner_id,
            delivery_partner_name 
            FROM delivery_partner_info 
            WHERE status = 'Active' 
            ORDER BY delivery_partner_name ASC
        ";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['delivery_partner_opt'][$row['delivery_partner_id']] = $row['delivery_partner_name'];
        }

        $sql = "
            SELECT 
            *
            FROM addt_charges_type_info
            WHERE status = 'Active'
            ORDER BY addt_charges_type_name ASC
        ";
        $query = $this->db->query($sql);

        $data['addt_charges_list'] = $query->result_array();

        $this->load->view('page/vendor/vendor-purchase-bill-multiple customer-edit', $data);
    }
}
