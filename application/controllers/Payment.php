<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Payment extends CI_Controller
{
    public function customer_invoice_recipt()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        if ($this->session->userdata(SESS_HD . 'level') != 'Admin' && $this->session->userdata(SESS_HD . 'level') != 'Staff') {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }

        $data['js'] = 'payment/customer-invoice-receipt.inc';
        $data['title'] = 'Tender Receipt List';

        if ($this->input->post('mode') == 'Add') {

            $this->db->trans_start();

            $ins = array(
                'receipt_date' => $this->input->post('receipt_date'),
                'customer_id' => $this->input->post('customer_id'),
                'receipt_mode' => $this->input->post('receipt_mode'),
                'bank_id' => $this->input->post('bank_id'),
                'receipt_type' => $this->input->post('receipt_type'),
                'cheque_date' => $this->input->post('cheque_date'),
                'cheque_no' => $this->input->post('cheque_no'),
                'cheque_bank' => $this->input->post('cheque_bank'),
                'amount' => $this->input->post('amount'),
                'remarks' => $this->input->post('remarks'),
                'status' => $this->input->post('status'),
                'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'created_date' => date('Y-m-d H:i:s')
            );
            $this->db->insert('tender_receipt_info', $ins);
            $tender_receipt_id = $this->db->insert_id();

            $selected_idxs = $this->input->post('selected_items') ?? [];
            $tender_enq_invoice_id = $this->input->post('tender_enq_invoice_id') ?? [];
            $tender_enquiry_id = $this->input->post('tender_enquiry_id') ?? [];
            $inv_amount = $this->input->post('inv_amount') ?? [];

            if (!empty($selected_idxs)) {
                foreach ($selected_idxs as $idx) {
                    $item_data = [
                        'tender_receipt_id' => $tender_receipt_id,
                        'tender_enquiry_id' => $tender_enquiry_id[$idx] ?? 0,
                        'tender_enq_invoice_id' => $tender_enq_invoice_id[$idx] ?? 0,
                        'inv_amount' => $inv_amount[$idx] ?? 0.000,
                        'status' => 'Active',
                    ];
                    $this->db->insert('tender_receipt_invoice_info', $item_data);
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Error saving data. Please try again.');
            } else {
                $this->session->set_flashdata('success', 'Customer Receipt saved successfully.');
            }

            redirect('customer-invoice-receipt');
        }

        // ===================== EDIT =====================
        if ($this->input->post('mode') == 'Edit') {

            $this->db->trans_start();

            $tender_receipt_id = $this->input->post('tender_receipt_id'); // ✅ SINGLE VALUE

            $upd = array(
                'receipt_date' => $this->input->post('receipt_date'),
                'customer_id' => $this->input->post('customer_id'),
                'receipt_mode' => $this->input->post('receipt_mode'),
                'bank_id' => $this->input->post('bank_id'),
                'receipt_type' => $this->input->post('receipt_type'),
                'cheque_date' => $this->input->post('cheque_date'),
                'cheque_no' => $this->input->post('cheque_no'),
                'cheque_bank' => $this->input->post('cheque_bank'),
                'amount' => $this->input->post('amount'),
                'remarks' => $this->input->post('remarks'),
                'status' => $this->input->post('status'),
                'updated_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'updated_date' => date('Y-m-d H:i:s')
            );

            $this->db->where('tender_receipt_id', $tender_receipt_id);
            $this->db->update('tender_receipt_info', $upd);

            // ✅ POST ARRAYS
            $selected_idxs = $this->input->post('selected_items') ?? [];
            $tender_receipt_invoice_id = $this->input->post('tender_receipt_invoice_id') ?? [];
            $tender_enq_invoice_id = $this->input->post('tender_enq_invoice_id') ?? [];
            $tender_enquiry_id = $this->input->post('tender_enquiry_id') ?? [];
            $inv_amount = $this->input->post('inv_amount') ?? [];

            $checked_existing_ids = [];

            // ✅ LOOP ONLY SELECTED ITEMS
            foreach ($selected_idxs as $idx) {

                $item_data = [
                    'tender_receipt_id' => $tender_receipt_id, // ✅ FIXED
                    'tender_enquiry_id' => $tender_enquiry_id[$idx] ?? 0,
                    'tender_enq_invoice_id' => $tender_enq_invoice_id[$idx] ?? 0,
                    'inv_amount' => $inv_amount[$idx] ?? 0.000,
                    'status' => 'Active',
                ];

                if (!empty($tender_receipt_invoice_id[$idx])) {

                    // ✅ UPDATE
                    $this->db->where('tender_receipt_invoice_id', $tender_receipt_invoice_id[$idx]);
                    $this->db->update('tender_receipt_invoice_info', $item_data);

                    $checked_existing_ids[] = $tender_receipt_invoice_id[$idx];

                } else {

                    // ✅ INSERT
                    $this->db->insert('tender_receipt_invoice_info', $item_data);
                }
            }

            // ✅ GET ALL EXISTING IDS (FROM FORM)
            $all_existing_ids = array_filter($tender_receipt_invoice_id, function ($v) {
                return !empty($v) && $v != 'null';
            });

            // ✅ FIND UNCHECKED → DELETE
            $ids_to_delete = array_diff($all_existing_ids, $checked_existing_ids);

            if (!empty($ids_to_delete)) {
                $this->db->where_in('tender_receipt_invoice_id', $ids_to_delete);
                $this->db->update('tender_receipt_invoice_info', ['status' => 'Deleted']);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Error updating data. Please try again.');
            } else {
                $this->session->set_flashdata('success', 'Customer Receipt updated successfully.');
            }

            redirect('customer-invoice-receipt');
        }

        $where = "1=1";

        // ===================== SEARCH FILTERS =====================
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

        if ($this->input->post('srch_enquiry_no') !== null) {
            $data['srch_enquiry_no'] = $srch_enquiry_no = $this->input->post('srch_enquiry_no');
            $this->session->set_userdata('srch_enquiry_no', $srch_enquiry_no);
        } elseif ($this->session->userdata('srch_enquiry_no')) {
            $data['srch_enquiry_no'] = $srch_enquiry_no = $this->session->userdata('srch_enquiry_no');
        } else {
            $data['srch_enquiry_no'] = $srch_enquiry_no = '';
        }



        if ($this->input->post('tender_enquiry_id_value_id') !== null) {
            $data['tender_enquiry_id_value_id'] = $tender_enquiry_id_value_id = $this->input->post('tender_enquiry_id_value_id');
            $this->session->set_userdata('tender_enquiry_id_value_id', $tender_enquiry_id_value_id);
        } elseif ($this->session->userdata('tender_enquiry_id_value_id')) {
            $data['tender_enquiry_id_value_id'] = $tender_enquiry_id_value_id = $this->session->userdata('tender_enquiry_id_value_id');
        } else {
            $data['tender_enquiry_id_value_id'] = $tender_enquiry_id_value_id = '';
        }

        if (!empty($tender_enquiry_id_value_id)) {
            $where .= " AND a.tender_enquiry_id = '" . $this->db->escape_str($tender_enquiry_id_value_id) . "'";
        }

        // ===================== PAGINATION =====================
        $this->load->library('pagination');

        $this->db->where('status != ', 'Delete');
        $this->db->from('tender_receipt_info as a');
        $this->db->where($where);
        $data['total_records'] = $cnt = $this->db->count_all_results();

        $data['sno'] = $this->uri->segment(2, 0);

        $config['base_url'] = trim(site_url('customer-invoice-receipt') . '/' . $this->uri->segment(2, 0));
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

        $offset = (int) $this->uri->segment(2, 0);
        $limit = (int) $config['per_page'];


        $data['record_list'] = array();

        $sql = "
            SELECT
                a.tender_receipt_id,
                a.receipt_no,
                a.receipt_date,
                a.customer_id,
                a.receipt_mode,
                a.amount,
                b.bank_name
            FROM tender_receipt_info AS a
            LEFT JOIN company_bank_info AS b ON b.bank_id = a.bank_id AND b.status = 'Active'
            WHERE a.status = 'Active'
            and $where
            ORDER BY a.tender_receipt_id DESC
            LIMIT ?, ?
        ";

        $query = $this->db->query($sql, array($offset, $limit));
        $data['record_list'] = $query->result_array();

        // ===================== DROPDOWNS =====================
        $data['customer_opt'] = [];
        $data['receipt_mode_opt'] = ['' => 'Select Receipt Type', 'Cash' => 'Cash', 'Bank' => 'Bank'];

        $sql = "
            SELECT customer_id, customer_name
            FROM customer_info
            WHERE status = 'Active'
            ORDER BY customer_name ASC
        ";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['customer_opt'][$row['customer_id']] = $row['customer_name'];
        }

        $data['bank_opt'] = [];
        $sql = "
            SELECT bank_id, bank_name
            FROM company_bank_info
            WHERE status = 'Active'
            ORDER BY bank_name ASC
        ";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['bank_opt'][$row['bank_id']] = $row['bank_name'];
        }

        $data['pagination'] = $this->pagination->create_links();

        $this->load->view('page/payment/customer-invoice-receipt', $data);
    }


    public function get_customer_invoice_list_ajax()
    {

        $customer_id = $this->input->post('customer_id');
        $sql = "
           SELECT
            a.tender_enq_invoice_id,
            a.tender_enquiry_id,
            DATE_FORMAT(a.invoice_date, '%d-%m-%Y') AS invoice_date,
            a.invoice_no,
            get_tender_info(a.tender_enquiry_id) as tender_details,
            a.total_amount,
            b.customer_name, 
            c.inv_amount as paid,
            (a.total_amount - IFNULL(SUM(c.inv_amount), 0)) AS balance_amount,
            CASE 
                WHEN a.total_amount = IFNULL(c.inv_amount, 0) THEN 'Paid'
                ELSE 'Unpaid'
            END AS payment_status
        FROM tender_enq_invoice_info AS a
        LEFT JOIN customer_info AS b
            ON a.customer_id = b.customer_id
            AND b.status = 'Active'
        left join tender_receipt_invoice_info as c on a.tender_enq_invoice_id = c.tender_enq_invoice_id and c.`status`='Active'
        WHERE a.status = 'Active' 
        AND a.customer_id = ? 
        group by a.tender_enq_invoice_id
       HAVING (a.total_amount - IFNULL(SUM(c.inv_amount), 0)) > 0
        ORDER BY a.invoice_date ASC, a.invoice_no ASC
        ";

        $query = $this->db->query($sql, array($customer_id));
        $data['record_list'] = $query->result_array();
        echo json_encode($data['record_list']);
    }


    public function load_receipt_customer_invoice_data()
    {
        $tender_receipt_id = $this->input->post('tender_receipt_id');
        $customer_id = $this->input->post('customer_id');

        $sql = "
            SELECT
                a.tender_enq_invoice_id,
                a.tender_enquiry_id,

                br.tender_receipt_id,
                br.tender_receipt_invoice_id,

                get_tender_info(a.tender_enquiry_id) AS tender_details,
                a.customer_id,
                a.invoice_no,
                DATE_FORMAT(a.invoice_date, '%d-%m-%Y') AS invoice_date,
                a.total_amount,

             
                IFNULL(SUM(b.inv_amount), 0) AS paid_amount,

               
                IFNULL(br.inv_amount, 0) AS current_paid_amount,

               
               (a.total_amount - IFNULL(SUM(b.inv_amount), 0)) AS balance

            FROM tender_enq_invoice_info AS a


            LEFT JOIN tender_receipt_invoice_info AS b 
                ON a.tender_enq_invoice_id = b.tender_enq_invoice_id 
                AND b.status = 'Active'


            LEFT JOIN tender_receipt_invoice_info AS br 
                ON a.tender_enq_invoice_id = br.tender_enq_invoice_id 
                AND br.tender_receipt_id = ?
                AND br.status = 'Active'

            WHERE
                a.status = 'Active'
                AND a.customer_id = ?
               
           
            GROUP BY
                a.tender_enq_invoice_id 

             HAVING balance > 0 or br.tender_receipt_id = ?
            ORDER BY
                a.tender_enq_invoice_id DESC;
        ";

        $query = $this->db->query($sql, array($tender_receipt_id, $customer_id, $tender_receipt_id));
        $data['data'] = $query->result_array();
        echo json_encode($data['data']);
    }


    public function get_data()
    {
        $table = $this->input->post('tbl');
        $rec_id = $this->input->post('id');

        $this->db->query('SET SQL_BIG_SELECTS=1');
        $rec_list = array();

        if ($table == 'vendor_payment_list_info_ajax') {

            $vendor_payment_id = $this->input->post('vendor_payment_id');

            $sql = "
                SELECT
                   a.*
                FROM vendor_payment_info AS a
                WHERE a.status = 'Active'
                AND a.vendor_payment_id = ?
                ORDER BY a.vendor_payment_id DESC
            ";

            $query = $this->db->query($sql, array($vendor_payment_id));
            $rec_list = $query->result_array();
        }
        if ($table == 'tender_receipt_info_invoice_list') {

            $tender_receipt_id = $this->input->post('tender_receipt_id');

            $sql = "
                SELECT
                    a.tender_receipt_id,
                    a.receipt_no,
                    a.receipt_date,
                    a.customer_id, 
                    a.receipt_mode,
                    a.bank_id,
                    a.amount,
                    a.remarks,
                    a.status,
                    a.receipt_type,
                    a.cheque_date,
                    a.cheque_no,
                    a.cheque_bank
                FROM tender_receipt_info AS a
                WHERE a.status = 'Active'
                AND a.tender_receipt_id = ?
                ORDER BY a.tender_receipt_id DESC
            ";

            $query = $this->db->query($sql, array($tender_receipt_id));
            $rec_list = $query->result_array();
        }

        echo json_encode($rec_list);
    }

    public function tender_enquiry_id_search()
    {
        $term = $this->input->post('search');


        $sql = "      
            SELECT 
            concat(a.tender_enquiry_id , ' || ', ifnull(b.company_code,'') , ' || ', ifnull(a.company_sno,'') ,  ' || ' , ifnull(c.customer_code,'') ,  ' || ' , ifnull(a.customer_sno,''),  ' || ' , ifnull(a.enquiry_no,'')) as tender_ref,
            concat(ifnull(b.company_code,'') , '/', ifnull(a.company_sno,'') ,  '/' , ifnull(c.customer_code,'') ,  '/' , ifnull(a.customer_sno,''),  '/' , ifnull(a.enquiry_no,'')) as enq1,
            concat(ifnull(b.company_code,'') , '/', ifnull(a.company_sno,'') ,  '/' , ifnull(c.customer_code,'') ,  '/' , ifnull(a.customer_sno,''),  '/' , DATE_FORMAT(a.enquiry_date,'%Y') ) as enq,
            a.company_id,
            a.customer_id,
            a.tender_enquiry_id,
            c.customer_name,
            a.enquiry_no
            FROM tender_enquiry_info AS a
            LEFT JOIN company_info AS b ON a.company_id = b.company_id AND b.status = 'Active'
            LEFT JOIN customer_info AS c ON a.customer_id = c.customer_id AND c.status = 'Active'
            WHERE  a.`status` = 'Active' 
            having enq like '%" . $this->db->escape_like_str($term) . "%'
            ORDER BY a.tender_enquiry_id desc, a.enquiry_no ASC  
        ";
        //and a.enquiry_no like '%" . $this->db->escape_like_str($term) . "%'

        $query = $this->db->query($sql);

        $result = [];

        foreach ($query->result() as $row) {
            $result[] = [
                'label' => $row->enq,       // what user sees 
                //'value' => $row->enq . ' [ ' . $row->enquiry_no . ' ]',        // filled in textbox
                'value' => $row->enq,        // filled in textbox
                'company_id' => $row->company_id,
                'customer_id' => $row->customer_id,
                'tender_enquiry_id' => $row->tender_enquiry_id,
                'customer_name' => $row->customer_name,
                'enquiry_no' => $row->enquiry_no
            ];
        }
        echo json_encode($result);

    }


    public function vendor_payment_list()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        if ($this->session->userdata(SESS_HD . 'level') != 'Admin' && $this->session->userdata(SESS_HD . 'level') != 'Staff') {
            echo "<h3 style='color:red;'>Permission Denied</h3>";
            exit;
        }

        $data['js'] = 'payment/vendor-payment-list.inc';
        $data['title'] = 'Supplier Payment List';

        // ===================== ADD =====================
        if ($this->input->post('mode') == 'Add') {
            // echo '<pre>';
            // print_r($_POST);
            // echo '</pre>';
            // exit;
            $this->db->trans_start();

            $ins = array(
                'payment_date' => $this->input->post('payment_date'),
                'vendor_id' => $this->input->post('vendor_id'),
                'payment_mode' => $this->input->post('payment_mode'),
                'bank_id' => $this->input->post('bank_id'),
                'payment_type' => $this->input->post('payment_type'),
                'cheque_date' => $this->input->post('cheque_date'),
                'cheque_no' => $this->input->post('cheque_no'),
                'cheque_bank' => $this->input->post('cheque_bank'),
                'amount' => $this->input->post('amount'),
                'remarks' => $this->input->post('remarks'),
                'status' => $this->input->post('status'),
                'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'created_date' => date('Y-m-d H:i:s')
            );
            $this->db->insert('vendor_payment_info', $ins);
            $vendor_payment_id = $this->db->insert_id();

            $selected_idxs = $this->input->post('selected_items') ?? [];
            $bill_id = $this->input->post('bill_id') ?? [];
            $bill_type = $this->input->post('bill_type') ?? [];
            $bill_amount = $this->input->post('bill_amount') ?? [];

            if (!empty($selected_idxs)) {
                foreach ($selected_idxs as $idx) {
                    $item_data = [
                        'vendor_payment_id' => $vendor_payment_id,
                        'bill_id' => $bill_id[$idx] ?? 0,
                        'bill_type' => $bill_type[$idx] ?? '',
                        'bill_amount' => $bill_amount[$idx] ?? 0.000,
                        'status' => 'Active',
                    ];
                    $this->db->insert('vendor_payment_bill_info', $item_data);
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Error saving data. Please try again.');
            } else {
                $this->session->set_flashdata('success', 'Supplier payment saved successfully.');
            }

            redirect('vendor-payment-list');
        }

        // ===================== EDIT =====================
        if ($this->input->post('mode') == 'Edit') {

            // echo '<pre>';
            // print_r($_POST);
            // echo '</pre>';
            // exit;

            $this->db->trans_start();

            $vendor_payment_id = $this->input->post('vendor_payment_id');

            $upd = array(
                'payment_date' => $this->input->post('payment_date'),
                'vendor_id' => $this->input->post('vendor_id'),
                'payment_mode' => $this->input->post('payment_mode'),
                'bank_id' => $this->input->post('bank_id'),
                'payment_type' => $this->input->post('payment_type'),
                'cheque_date' => $this->input->post('cheque_date'),
                'cheque_no' => $this->input->post('cheque_no'),
                'cheque_bank' => $this->input->post('cheque_bank'),
                'amount' => $this->input->post('amount'),
                'remarks' => $this->input->post('remarks'),
                'status' => $this->input->post('status'),
                'created_by' => $this->session->userdata(SESS_HD . 'user_id'),
                'created_date' => date('Y-m-d H:i:s')
            );
            $this->db->where('vendor_payment_id', $vendor_payment_id);
            $this->db->update('vendor_payment_info', $upd);

            $selected_idxs = $this->input->post('selected_items') ?? [];
            $vendor_payment_bill_id = $this->input->post('vendor_payment_bill_id') ?? [];
            $bill_id = $this->input->post('bill_id') ?? [];
            $bill_type = $this->input->post('bill_type') ?? [];
            $bill_amount = $this->input->post('inv_amount') ?? [];

            // Collect the IDs that were checked (selected) and already exist in DB
            $checked_existing_ids = [];

            foreach ($selected_idxs as $idx) {
                $item_data = [
                    'vendor_payment_id' => $vendor_payment_id,
                    'bill_id' => $bill_id[$idx] ?? 0,
                    'bill_type' => $bill_type[$idx] ?? '',
                    'bill_amount' => $bill_amount[$idx] ?? 0.000,
                    'status' => 'Active',
                ];

                if (!empty($vendor_payment_bill_id[$idx])) {
                    // Existing row → UPDATE
                    $this->db->where('vendor_payment_bill_id', $vendor_payment_bill_id[$idx]); 
                    $this->db->update('vendor_payment_bill_info', $item_data);

                    $checked_existing_ids[] = $vendor_payment_bill_id[$idx];
                } else {
                    // New row → INSERT
                    $this->db->insert('vendor_payment_bill_info', $item_data);
                }
            }


            $all_existing_ids = array_filter($vendor_payment_bill_id, function ($v) {
                return !empty($v);
            });

            $ids_to_delete = array_diff($all_existing_ids, $checked_existing_ids);

            if (!empty($ids_to_delete)) {
                $this->db->where_in('vendor_payment_bill_id', array_values($ids_to_delete));
                $this->db->update('vendor_payment_bill_info', ['status' => 'Deleted']);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Error updating data. Please try again.');
            } else {
                $this->session->set_flashdata('success', 'Customer Receipt updated successfully.');
            }

            redirect('vendor-payment-list');
        }

        $where = "1=1";

 
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

 


        $this->load->library('pagination');

        $this->db->where('status != ', 'Delete');
        $this->db->from('vendor_payment_info as a');
        $this->db->where($where);
        $data['total_records'] = $cnt = $this->db->count_all_results();

        $data['sno'] = $this->uri->segment(2, 0);

        $config['base_url'] = trim(site_url('vendor-payment-list') . '/' . $this->uri->segment(2, 0));
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

        $offset = (int) $this->uri->segment(2, 0);
        $limit = (int) $config['per_page'];


        $data['record_list'] = array();

        $sql = "
            SELECT
                date_format(a.payment_date, '%d-%m-%Y') as payment_date,
                a.payment_no,
                c.vendor_name,
                a.vendor_payment_id,
                a.payment_mode,
                a.cheque_date,
                a.cheque_no,
                a.cheque_bank,
                a.amount,
                a.remarks,
                a.status,  
                b.bank_name
            FROM vendor_payment_info AS a
            LEFT JOIN company_bank_info AS b ON b.bank_id = a.bank_id AND b.status = 'Active'
            left join vendor_info as c on c.vendor_id = a.vendor_id and c.status = 'Active'
            WHERE a.status = 'Active'
            and $where
            ORDER BY a.vendor_payment_id DESC
            LIMIT ?, ?
        ";

        $query = $this->db->query($sql, array($offset, $limit));
        $data['record_list'] = $query->result_array();

        $data['customer_opt'] = [];
        $data['vendor_opt'] = [];
        $data['receipt_mode_opt'] = ['' => 'Select Receipt Type', 'Cash' => 'Cash', 'Bank' => 'Bank'];


        $data['bank_opt'] = [];
        $sql = "
            SELECT bank_id, bank_name
            FROM company_bank_info
            WHERE status = 'Active'
            ORDER BY bank_name ASC
        ";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $data['bank_opt'][$row['bank_id']] = $row['bank_name'];
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

        $data['pagination'] = $this->pagination->create_links();

        $this->load->view('page/payment/vendor-payment-list', $data);
    }

    public function get_vendor_list_tender_enquiry_id()
    {
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            echo json_encode([]);
            return;
        }

        $tender_enquiry_id = $this->input->post('srch_tender_enquiry_id');
        $customer_id = $this->input->post('srch_customer_id');

        if (empty($tender_enquiry_id) || empty($customer_id)) {
            echo json_encode([]);
            return;
        }

        $sql = "
            SELECT 
                e.vendor_id,
                e.vendor_name
            FROM vendor_rate_enquiry_info AS a
            LEFT JOIN vendor_info AS e 
                ON a.vendor_id = e.vendor_id 
                AND e.status = 'Active'
            WHERE a.status = 'Active'
            AND a.tender_enquiry_id = ?
            AND a.customer_id = ?
            GROUP BY e.vendor_id, e.vendor_name
            ORDER BY e.vendor_name ASC
        ";

        $query = $this->db->query($sql, [$tender_enquiry_id, $customer_id]);

        echo json_encode($query->result_array());
    }

    public function get_vendor_payment_blil_list()
    {

        $vendor_id = $this->input->post('vendor_id');

        $sql = "
            SELECT 
                f.bill_id, 
                date_format(f.invoice_date, '%d-%m-%Y') as bill_date,
                f.invoice_no as bill_no,
                f.vendor_name,
              get_tender_info(f.tender_enquiry_id) as tender_details,
                f.bill_type,
                f.total_amount, 

                IFNULL(SUM(vrb.bill_amount), 0) AS paid_amount, 
                (f.total_amount - IFNULL(SUM(vrb.bill_amount), 0)) AS balance_amount, 

                CASE 
                    WHEN (f.total_amount - IFNULL(SUM(vrb.bill_amount), 0)) = 0 THEN 'Paid'
                    ELSE 'Unpaid'
                END AS payment_status

            FROM (

                -- Purchase Bill
                SELECT
                    a.vendor_purchase_invoice_id AS bill_id,
                    a.invoice_date,
                    a.tender_enquiry_id,
                    a.invoice_no,
                    b.vendor_name,
                    a.total_amount,
                    'Purchase Invoice' AS bill_type
                FROM vendor_purchase_invoice_info a
                LEFT JOIN vendor_info b 
                    ON a.vendor_id = b.vendor_id AND b.status = 'Active'
                WHERE a.status = 'Active' 
                    AND a.vendor_id = $vendor_id

                UNION ALL

                -- Local Bill
                SELECT
                    a.local_purchase_bill_id,
                    a.invoice_date, 
                    a.tender_enquiry_id,
                    a.invoice_no,
                    b.vendor_name,
                    a.tot_amt_with_tax,
                    'Local Bill'
                FROM local_purchase_bill_info a
                LEFT JOIN vendor_info b 
                    ON a.vendor_id = b.vendor_id AND b.status = 'Active'
                WHERE a.status = 'Active' 
                    AND a.vendor_id = $vendor_id

                UNION ALL

                -- Delivery Bill
                SELECT
                    a.dp_bill_id,
                    a.invoice_date,
                    a.tender_enquiry_id,
                    a.invoice_no,
                    b.vendor_name,
                    a.g_total,
                    'Delivery Bill'
                FROM dp_bill_info a
                LEFT JOIN vendor_info b 
                    ON a.vendor_id = b.vendor_id AND b.status = 'Active'
                WHERE a.status = 'Active' 
                    AND a.vendor_id = $vendor_id

                UNION ALL

                -- Customs Bill
                SELECT
                    a.customs_bill_id,
                    a.invoice_date,
                    a.tender_enquiry_id,
                    a.invoice_no,
                    b.vendor_name,
                    a.customs_tot_amt,
                    'Customs Bill'
                FROM customs_bill_info a
                LEFT JOIN vendor_info b 
                    ON a.vendor_id = b.vendor_id AND b.status = 'Active'
                WHERE a.status = 'Active' 
                    AND a.vendor_id = $vendor_id

            ) AS f

            LEFT JOIN vendor_payment_bill_info vrb 
                ON vrb.bill_id = f.bill_id 
                AND vrb.bill_type = f.bill_type
                AND vrb.status = 'Active'

            GROUP BY 
                f.bill_id,
                f.invoice_date,
                f.invoice_no,
                f.vendor_name,
                f.bill_type,
                f.total_amount
 				HAVING (f.total_amount - IFNULL(SUM(vrb.bill_amount), 0)) > 0
            ORDER BY f.invoice_date DESC;
        ";

        $query = $this->db->query($sql);
        echo json_encode($query->result_array());
    }
    public function load_vendor_payment_bill_edit_list_ajax()
    {

        $vendor_id = $this->input->post('vendor_id');
        $vendor_payment_id = $this->input->post('vendor_payment_id');

        $sql = "
            SELECT 
                f.bill_id, 
                get_tender_info(f.tender_enquiry_id) as tender_details,
                DATE_FORMAT(f.invoice_date, '%d-%m-%Y') AS bill_date,
                f.invoice_no as bill_no,
                f.vendor_name,
                f.bill_type,
                f.total_amount,  
                IFNULL(SUM(vrb_total.bill_amount), 0) AS paid_amount, 
                vrb_current.bill_amount AS current_paid_amount, 
                (f.total_amount - IFNULL(SUM(vrb_total.bill_amount), 0)) AS balance_amount,  
                vrb_current.vendor_payment_bill_id  
                
            FROM (

                -- Purchase Bill
                SELECT
                    a.vendor_purchase_invoice_id AS bill_id,
                    a.invoice_date,
                    a.tender_enquiry_id,
                    a.invoice_no, 
                    b.vendor_name,
                    a.total_amount,
                    'Purchase Invoice' AS bill_type
                FROM vendor_purchase_invoice_info a
                LEFT JOIN vendor_info b 
                    ON a.vendor_id = b.vendor_id AND b.status = 'Active'
                WHERE a.status = 'Active' 
                    AND a.vendor_id = $vendor_id

                UNION ALL

                -- Local Bill
                SELECT
                    a.local_purchase_bill_id,
                    a.invoice_date,
                    a.tender_enquiry_id,
                    a.invoice_no, 
                    b.vendor_name,
                    a.tot_amt_with_tax,
                    'Local Bill'
                FROM local_purchase_bill_info a
                LEFT JOIN vendor_info b 
                    ON a.vendor_id = b.vendor_id AND b.status = 'Active'
                WHERE a.status = 'Active' 
                    AND a.vendor_id = $vendor_id

                UNION ALL

                -- Delivery Bill
                SELECT
                    a.dp_bill_id,
                    a.invoice_date,
                    a.tender_enquiry_id,
                    a.invoice_no, 
                    b.vendor_name,
                    a.g_total,
                    'Delivery Bill'
                FROM dp_bill_info a
                LEFT JOIN vendor_info b 
                    ON a.vendor_id = b.vendor_id AND b.status = 'Active'
                WHERE a.status = 'Active' 
                    AND a.vendor_id = $vendor_id

                UNION ALL

                -- Customs Bill
                SELECT
                    a.customs_bill_id,
                    a.invoice_date,
                    a.tender_enquiry_id,
                    a.invoice_no, 
                    b.vendor_name,
                    a.customs_tot_amt,
                    'Customs Bill'
                FROM customs_bill_info a
                LEFT JOIN vendor_info b 
                    ON a.vendor_id = b.vendor_id AND b.status = 'Active'
                WHERE a.status = 'Active' 
                    AND a.vendor_id = $vendor_id

            ) AS f 
            LEFT JOIN vendor_payment_bill_info vrb_total
                ON vrb_total.bill_id = f.bill_id 
                AND vrb_total.bill_type = f.bill_type
                AND vrb_total.status = 'Active' 
            LEFT JOIN vendor_payment_bill_info vrb_current
                ON vrb_current.bill_id = f.bill_id 
                AND vrb_current.bill_type = f.bill_type
                AND vrb_current.status = 'Active'
                AND vrb_current.vendor_payment_id = $vendor_payment_id

            GROUP BY 
                f.bill_id,
                f.invoice_date,
                f.invoice_no,
                f.vendor_name,
                f.bill_type,
                f.total_amount
                
				HAVING 
				    (f.total_amount - IFNULL(SUM(vrb_total.bill_amount), 0)) > 0 
				    OR MAX(vrb_current.vendor_payment_bill_id) IS NOT NULL 
            ORDER BY f.invoice_date DESC
            ";
        $query = $this->db->query($sql);
        echo json_encode($query->result_array());
    }


    public function delete_record()
    {

        if (!$this->session->userdata(SESS_HD . 'logged_in'))
            redirect();

        date_default_timezone_set("Asia/Calcutta");


        $table = $this->input->post('tbl');
        $rec_id = $this->input->post('id');


        if ($table == 'vendor_payment_info_list') {

            $this->db->where('vendor_payment_id', $rec_id);
            $this->db->update('vendor_payment_info', array('status' => 'Delete'));

            $this->db->where('vendor_payment_id', $rec_id);
            $this->db->update('vendor_payment_bill_info', array('status' => 'Delete'));
            echo "Record Deleted Successfully";
        }

        if ($table == 'customer_invoice_receipt_info') {

            $this->db->where('tender_receipt_id', $rec_id);
            $this->db->update('tender_receipt_info', array('status' => 'Delete'));

            $this->db->where('tender_receipt_id', $rec_id);
            $this->db->update('tender_receipt_invoice_info', array('status' => 'Delete'));
            echo "Record Deleted Successfully";
        }

    }
}