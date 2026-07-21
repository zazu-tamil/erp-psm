<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CreditDebitNote extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata(SESS_HD . 'logged_in')) {
            redirect();
        }
        $this->load->model('Credit_debit_note_model');
    }

    public function index($offset = 0)
    {
        $data['title'] = "Credit / Debit Note List";
         $data['js'] = 'accounts/credit_debit_note.inc';
        
        $filters = [
            'from_date' => $this->input->post('srch_from_date'),
            'to_date' => $this->input->post('srch_to_date'),
            'party_type' => $this->input->post('srch_party_type'),
            'note_type' => $this->input->post('srch_note_type')
        ];

        $data['records'] = $this->Credit_debit_note_model->get_list(null, 0, $filters);
        $data['filters'] = $filters;
        
        $this->load->view('page/accounts/credit-debit-note-list', $data);
    }

    public function add()
    {
        $data['title'] = "Create Credit / Debit Note";
        $data['company_opt'] = $this->Credit_debit_note_model->get_companies();
        $data['customer_opt'] = $this->Credit_debit_note_model->get_customers();
        $data['supplier_opt'] = $this->Credit_debit_note_model->get_suppliers();
        $data['enquiry_opt'] = $this->Credit_debit_note_model->get_tender_enquiries();
        $data['currency_opt'] = $this->Credit_debit_note_model->get_currencies();
        $data['tax_opt'] = $this->Credit_debit_note_model->get_taxes();
        
        $data['js'] = 'accounts/credit_debit_note.inc';
        
        $this->load->view('page/accounts/credit-debit-note-add', $data);
    }

    public function edit($id)
    {
        $data['title'] = "Edit Credit / Debit Note";
        $data['company_opt'] = $this->Credit_debit_note_model->get_companies();
        $data['customer_opt'] = $this->Credit_debit_note_model->get_customers();
        $data['supplier_opt'] = $this->Credit_debit_note_model->get_suppliers();
        $data['enquiry_opt'] = $this->Credit_debit_note_model->get_tender_enquiries();
        $data['currency_opt'] = $this->Credit_debit_note_model->get_currencies();
        $data['tax_opt'] = $this->Credit_debit_note_model->get_taxes();
        
        $data['edit_data'] = $this->Credit_debit_note_model->get_note($id);
        if (!$data['edit_data']) {
            show_404();
        }
        $data['edit_items'] = $this->Credit_debit_note_model->get_items($id);
        $data['edit_adjustments'] = $this->Credit_debit_note_model->get_adjustments($id);
        $data['edit_attachments'] = $this->Credit_debit_note_model->get_attachments($id);
        
        $data['js'] = 'accounts/credit_debit_note.inc';
        $this->load->view('page/accounts/credit-debit-note-edit', $data);
    }

    public function save()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $note_id = $this->input->post('note_id');
            $party_type = $this->input->post('party_type');
            $status = $this->input->post('status') ? $this->input->post('status') : 'Draft';
            
            $note_no = $this->input->post('note_no');
            if (empty($note_no) || $note_no == 'Auto Generated') {
                $max_id = $this->db->select_max('credit_debit_note_id')->get('credit_debit_note_info')->row()->credit_debit_note_id;
                $note_no = 'CDN-' . str_pad(($max_id ? $max_id + 1 : 1), 4, '0', STR_PAD_LEFT);
            }

            $save_data = [
                'company_id' => $this->input->post('company_id'),
                'tender_enquiry_id' => $this->input->post('tender_enquiry_id'),
                'note_no' => $note_no,
                'note_type' => $this->input->post('note_type'),
                'party_type' => $party_type,
                'customer_id' => ($party_type == 'Customer') ? $this->input->post('customer_id') : NULL,
                'supplier_id' => ($party_type == 'Supplier') ? $this->input->post('supplier_id') : NULL,
                'note_date' => $this->input->post('note_date'),
                'reference_invoice_id' => $this->input->post('reference_invoice_id'),
                'reference_invoice_no' => $this->input->post('reference_invoice_no'),
                'currency_id' => $this->input->post('currency_id'),
                'exchange_rate' => $this->input->post('exchange_rate') ?: 1.000000,
                'reason' => $this->input->post('reason'),
                'remarks' => $this->input->post('remarks'),
                
                'subtotal' => $this->input->post('subtotal') ?: 0,
                'discount_amount' => $this->input->post('discount_amount') ?: 0,
                'taxable_amount' => $this->input->post('taxable_amount') ?: 0,
                'tax_amount' => $this->input->post('tax_amount') ?: 0,
                'roundoff' => $this->input->post('roundoff') ?: 0,
                'total_amount' => $this->input->post('total_amount') ?: 0,
                
                'adjustment_amount' => $this->input->post('adjustment_amount') ?: 0,
                'remaining_amount' => $this->input->post('remaining_amount') ?: 0,
                
                'status' => $status
            ];
            
            if ($note_id) {
                $save_data['modified_by'] = $this->session->userdata('cr_user_id');
                $save_data['modified_date'] = date('Y-m-d H:i:s');
            } else {
                $save_data['created_by'] = $this->session->userdata('cr_user_id');
                $save_data['created_date'] = date('Y-m-d H:i:s');
            }

            // Items
            $items = [];
            $item_codes = $this->input->post('item_code');
            if (!empty($item_codes)) {
                $descriptions = $this->input->post('description');
                $hsn_sacs = $this->input->post('hsn_sac');
                $qtys = $this->input->post('qty');
                $uoms = $this->input->post('uom');
                $rates = $this->input->post('rate');
                $discount_percents = $this->input->post('discount_percent');
                $discount_amounts = $this->input->post('item_discount_amount');
                $tax_percents = $this->input->post('tax_percent');
                $tax_amounts = $this->input->post('item_tax_amount');
                $line_totals = $this->input->post('line_total');

                for ($i = 0; $i < count($item_codes); $i++) {
                    if (!empty($item_codes[$i])) {
                        $items[] = [
                            'item_code' => $item_codes[$i],
                            'description' => $descriptions[$i],
                            'hsn_sac' => $hsn_sacs[$i],
                            'qty' => $qtys[$i],
                            'uom' => $uoms[$i],
                            'rate' => $rates[$i],
                            'discount_percent' => isset($discount_percents[$i]) && $discount_percents[$i] ? $discount_percents[$i] : 0,
                            'discount_amount' => isset($discount_amounts[$i]) && $discount_amounts[$i] ? $discount_amounts[$i] : 0,
                            'tax_percent' => isset($tax_percents[$i]) && $tax_percents[$i] ? $tax_percents[$i] : 0,
                            'tax_amount' => isset($tax_amounts[$i]) && $tax_amounts[$i] ? $tax_amounts[$i] : 0,
                            'line_total' => $line_totals[$i] ?: 0,
                        ];
                    }
                }
            }

            // Adjustment Info
            $adjustments = [];
            if ($this->input->post('adjustment_amount') > 0 && $this->input->post('reference_invoice_id')) {
                $adjustments = [
                    'invoice_id' => $this->input->post('reference_invoice_id'),
                    'invoice_no' => $this->input->post('reference_invoice_no'),
                    'invoice_amount' => $this->input->post('outstanding_invoice_amount') ?: 0,
                    'adjusted_amount' => $this->input->post('adjustment_amount') ?: 0,
                    'balance_amount' => $this->input->post('remaining_amount') ?: 0
                ];
            }

            // Attachments
            $attachments = [];
            if (!empty($_FILES['attachment_file']['name'])) {
                $config['upload_path'] = './uploads/credit_debit_notes/';
                if (!is_dir($config['upload_path'])) {
                    mkdir($config['upload_path'], 0777, TRUE);
                }
                $config['allowed_types'] = 'gif|jpg|jpeg|png|pdf|doc|docx|xls|xlsx';
                $config['max_size'] = 5048; // 5MB
                $config['encrypt_name'] = TRUE;

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('attachment_file')) {
                    $uploadData = $this->upload->data();
                    $attachments[] = [
                        'file_name' => $uploadData['orig_name'],
                        'file_path' => 'uploads/credit_debit_notes/' . $uploadData['file_name'],
                        'uploaded_by' => $this->session->userdata('cr_user_id'),
                        'uploaded_date' => date('Y-m-d H:i:s')
                    ];
                } else {
                    // Upload failed
                    echo json_encode(['status' => 'error', 'msg' => $this->upload->display_errors('','')]);
                    exit;
                }
            }

            if ($note_id) {
                $result_id = $this->Credit_debit_note_model->update_note($note_id, $save_data, $items, $adjustments, $attachments);
                $msg = 'Credit / Debit note updated successfully';
            } else {
                $result_id = $this->Credit_debit_note_model->insert_note($save_data, $items, $adjustments, $attachments);
                $msg = 'Credit / Debit note created successfully';
            }

            if ($result_id) {
                echo json_encode(['status' => 'success', 'msg' => $msg, 'note_id' => $result_id]);
            } else {
                echo json_encode(['status' => 'error', 'msg' => 'Database error occurred while saving the note.']);
            }
        }
    }
    
    public function get_reference_invoices()
    {
        $party_type = $this->input->post('party_type');
        $party_id = $this->input->post('party_id');
        $enquiry_id = $this->input->post('enquiry_id');
        
        if ($party_type == 'Customer' && $enquiry_id) {
            $this->db->select('tender_enq_invoice_id as invoice_id, invoice_no, invoice_date, total_amount');
            $this->db->from('tender_enq_invoice_info');
            if ($party_id) $this->db->where('customer_id', $party_id);
            $this->db->where('tender_enquiry_id', $enquiry_id);
            $query = $this->db->get();
            echo json_encode(['status'=>'success', 'data'=>$query->result()]);
        } else if ($party_type == 'Supplier' && $enquiry_id) {
            $this->db->select('vendor_purchase_invoice_id as invoice_id, invoice_no, invoice_date, total_amount');
            $this->db->from('vendor_purchase_invoice_info');
            if ($party_id) $this->db->where('vendor_id', $party_id);
            $query = $this->db->get();
            echo json_encode(['status'=>'success', 'data'=>$query->result()]);
        } else {
            echo json_encode(['status'=>'success', 'data'=>[]]);
        }
    }

    public function get_enquiry_details()
    {
        $enquiry_id = $this->input->post('enquiry_id');
        if ($enquiry_id) {
            $this->db->select('company_id, customer_id');
            $this->db->from('tender_enquiry_info');
            $this->db->where('tender_enquiry_id', $enquiry_id);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                echo json_encode(['status'=>'success', 'data'=>$query->row()]);
                return;
            }
        }
        echo json_encode(['status'=>'error']);
    }

    public function get_supplier_for_enquiry()
    {
        $enquiry_id = $this->input->post('enquiry_id');
        if ($enquiry_id) {
            $this->db->select('vendor_id');
            $this->db->distinct();
            $this->db->from('vendor_purchase_invoice_info');
            $this->db->where('tender_enquiry_id', $enquiry_id);
            $query = $this->db->get();
            
            // If there's exactly 1 supplier for this enquiry
            if ($query->num_rows() == 1) {
                $row = $query->row();
                echo json_encode(['status' => 'success', 'vendor_id' => $row->vendor_id]);
                return;
            }
        }
        echo json_encode(['status' => 'error']);
    }

    public function get_invoice_items()
    {
        $party_type = $this->input->post('party_type');
        $invoice_id = $this->input->post('invoice_id');
        
        if ($party_type == 'Customer' && $invoice_id) {
            $this->db->select('item_code, item_desc as description, uom, qty, rate, gst as tax_percent, amount as line_total');
            $this->db->from('tender_enq_invoice_item_info');
            $this->db->where('tender_enq_invoice_id', $invoice_id);
            $query = $this->db->get();
            echo json_encode(['status'=>'success', 'data'=>$query->result()]);
        } else if ($party_type == 'Supplier' && $invoice_id) {
            $this->db->select('item_code, item_desc as description, uom, qty, rate, gst as tax_percent, amount as line_total');
            $this->db->from('vendor_purchase_invoice_item_info');
            $this->db->where('vendor_purchase_invoice_id', $invoice_id);
            $query = $this->db->get();
            echo json_encode(['status'=>'success', 'data'=>$query->result()]);
        } else {
            echo json_encode(['status'=>'error', 'data'=>[]]);
        }
    }
    public function delete($id)
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $result = $this->Credit_debit_note_model->delete_note($id);
            if ($result) {
                echo json_encode(['status' => 'success', 'msg' => 'Credit / Debit note deleted successfully']);
            } else {
                echo json_encode(['status' => 'error', 'msg' => 'Database error occurred while deleting the note.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Invalid request']);
        }
    }
}
